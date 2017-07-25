<?php
/**
 * Copyright (c) etouches, Inc.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;

/**
 * Interface QueueInterface
 *
 * Contract that calls for providing functionality for connecting, publishing messages, and consuming messages.
 *
 * @package Etouches\Phplumber
 */
interface QueueInterface
{
    /**
     * Publish a message to the queue.
     *
     * @param array $message
     */
    public function publishMessage(array $message);

    /**
     * Listen to the queue for incoming messages. Must call consumeMessage() in the abstract Queue class.
     *
     * This is the loop called by the worker daemon. Implementations must run indefinitely until terminated.
     *
     * @see Queue::consumeMessage()
     */
    public function consume();

    /**
     * Connect to queue system.
     */
    public function connect();

    /**
     * Close connection to queue system.
     */
    public function close();
}
