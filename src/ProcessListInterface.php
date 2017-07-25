<?php
/**
 * Copyright (c) etouches, Inc.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;

/**
 * Interface ProcessListInterface
 *
 * Contract required for handling synchronous and asynchronous processes.
 *
 * @package Etouches\Phplumber
 */
interface ProcessListInterface
{
    /**
     * Start the series of processes.
     *
     * @param array $payload
     * @return bool
     */
    public function process(array $payload);

    /**
     * Resumes a pause async multi-process
     *
     * @param string|int $id
     * @param string $processName
     * @param array $payload
     * @return bool
     */
    public function resume($id, $processName, array $payload);
}
