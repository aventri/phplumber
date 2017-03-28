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

    public function __construct(ProcessFactoryInterface $processFactory)
    {
        $this->processFactory = $processFactory;
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
                if (!array_key_exists('process', $message)) {
                    throw new \Exception("Message missing process name");
                }
                if (!array_key_exists('payload', $message)) {
                    throw new \Exception("Message missing payload");
                }
                $process = $this->processFactory->make($message['process']);
                $process->invoke($message['payload']);
            }
        }
    }
}
