<?php
/**
 * Copyright (c) etouches.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;


/**
 * Interface ProcessInterface
 *
 * Implement this interface to define one process within a ProcessList.
 *
 * @package Etouches\Phplumber
 */
interface ProcessInterface
{
    /**
     * Invoke this process (as part of a process list).
     *
     * Returns false when the sequence of processes should not continue (e.g. on error).
     * Otherwise return a payload array to pass to the next process in the list.
     *
     * @param array $payload
     * @return bool|array False on failure or payload array
     */
    public function invoke(array $payload);
}
