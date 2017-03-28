<?php
/**
 * Copyright (c) etouches.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;

/**
 * Class ProcessList
 *
 * Setup and hand-off between running processes.
 *
 * @package Etouches\Phplumber
 */
abstract class ProcessList implements ProcessListInterface
{
    /** @var int|null */
    protected $runningIndex;

    /** @var ProcessInterface[] */
    protected $processes = array();

    /** @var ProcessFactoryInterface */
    protected $processFactory;

    /** @var Queue */
    protected $queue;

    /** @var StorageInterface */
    protected $storage;

    /**
     * Creates the semaphone table, and adds the various processes via setup. Requires the DB Connection, factory and
     * manager
     *
     * ProcessList constructor.
     *
     * @param ProcessFactoryInterface $factory
     * @param Queue $queue
     * @param StorageInterface $storage
     */
    public function __construct(ProcessFactoryInterface $factory, Queue $queue, StorageInterface $storage)
    {
        $this->processFactory = $factory;
        $this->queue = $queue;
        $this->storage = $storage;
        $this->setup();
    }

    /**
     * Adds the processes in the list
     *
     * @return mixed
     */
    abstract protected function setup();

    /**
     * Adds a process sequentially to the list.
     *
     * @param $processName
     * @return $this
     */
    protected function add($processName)
    {
        $this->processes[$processName] = $this->processFactory->make($processName);
        return $this;
    }

    /**
     * Start the series of processes.
     *
     * @param array $payload
     * @return bool
     */
    public function process(array $payload)
    {
        $this->runningIndex = null;
        return $this->processNext($payload);
    }

    /**
     * Invoke a process, if it's a multi-process, a semaphore is added and the processes are added to the queue
     *
     * @param array $payload
     * @return bool
     */
    protected function processNext(array $payload)
    {
        if ($this->runningIndex === null) {
            $this->runningIndex = 0;
        } else {
            $this->runningIndex++;
        }

        $processes = array_values($this->processes);
        $processNames = array_keys($this->processes);
        if (array_key_exists($this->runningIndex, $processNames)) {
            /** @var ProcessInterface $process */
            $process = $processes[$this->runningIndex];
            $processName = $processNames[$this->runningIndex];

            if ($process instanceof MultiProcess) {
                $payloads = $process->getAsyncPayloads($payload);
                $listName = get_class($this);
                $id = $this->pause($listName, $processName, count($payloads), $payload);
                foreach ($payloads as $payload) {
                    $this->queue->enqueueProcessAsync($id, $listName, $processName, $payload);
                }
            } else {
                $result = $process->invoke($payload);
                if ($result === false) {
                    return false;
                }
                if (!$result) {
                    $result = array();
                }
                $this->processNext($result);
            }
        }
        return true;
    }

    /**
     * When the process list hits a multi-process, a pause is issued. A semaphore is stored. All async processes
     * must run to completion for the pause to be resumed
     *
     * @param string $listName
     * @param string $processName
     * @param int $count
     * @param array $payload
     * @return null
     */
    protected function pause($listName, $processName, $count, array $payload)
    {
        $semaphore = new Semaphore();
        $semaphore->list = $listName;
        $semaphore->process = $processName;
        $semaphore->count = $count;
        $semaphore->listPayload = $payload;
        return $this->storage->storeSemaphore($semaphore);
    }

    /**
     * Resumes a pause async multi-process
     *
     * @param string|int $id
     * @param string $processName
     * @param array $payload
     * @return bool
     */
    public function resume($id, $processName, array $payload)
    {
        $success = true;
        $index = 0;
        foreach ($this->processes as $name => $process) {
            if ($name === $processName) {
                $this->runningIndex = $index;
                if ($process->invoke($payload) === false) {
                    $success = false;
                }
                break;
            }
            $index++;
        }

        if (!$id) {
            $asyncComplete = true;
        } else {
            // check if async is complete, so we can continue with next process
            $asyncComplete = false;

            $semaphore = $this->storage->getSemaphore($id);
            if (!$semaphore->count || $semaphore->count <= 1) {
                $asyncComplete = true;
                $this->storage->deleteSemaphore($id);
            } else {
                $semaphore->count--;
                $this->storage->storeSemaphore($semaphore);
            }
        }

        if ($asyncComplete && $success) {
            /** @noinspection PhpUndefinedVariableInspection */
            return $this->processNext($semaphore->listPayload);
        } else {
            return $success;
        }
    }
}
