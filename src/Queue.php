<?php
/**
 * Copyright (c) etouches.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;


/**
 * Class Queue
 *
 * Abstract Queue that pushes and consumes messages for async processes.
 *
 * @package Etouches\Phplumber
 */
abstract class Queue implements QueueInterface
{
    /** @var ProcessFactoryInterface */
    protected $processFactory;

    /** @var StorageInterface */
    protected $storage;

    public function __construct(ProcessFactoryInterface $processFactory, StorageInterface $storage)
    {
        $this->processFactory = $processFactory;
        $this->storage = $storage;
    }

    /**
     * @param string|int $id
     * @param string $listName
     * @param string $processName
     * @param array $payload
     */
    public function enqueueProcessAsync($id, $listName, $processName, array $payload)
    {
        $this->publishMessage(array(
            'type' => 'process_async',
            'semaphore' => $id,
            'list' => $listName,
            'process' => $processName,
            'payload' => $payload
        ));
    }

    /**
     * Process one message from the queue.
     *
     * @see QueueInterface::consume()
     *
     * @param array $message
     * @throws \Exception
     */
    protected function consumeMessage(array $message)
    {
        if (array_key_exists('type', $message)) {
            if ($message['type'] === 'process_async') {
                $requiredKeys = array('semaphore', 'process', 'payload', 'list');
                foreach ($requiredKeys as $key) {
                    if (!array_key_exists($key, $message)) {
                        throw new \Exception("Message missing key $key");
                    }
                }
                $listName = $message['list'];
                if (!class_exists($listName)) {
                    throw new \Exception("List class $listName not found");
                }
                /** @var ProcessList $list */
                $list = new $listName($this->processFactory, $this, $this->storage);
                $list->resume($message['semaphore'], $message['process'], $message['payload']);
            }
        }
    }
}
