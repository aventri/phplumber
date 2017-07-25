<?php
/**
 * Copyright (c) etouches, Inc.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;

/**
 * Interface ProcessFactoryInterface
 *
 * Contract that calls for creating every type of process in a system.
 *
 * @package Etouches\Phplumber
 */
interface ProcessFactoryInterface
{
    /**
     * Create a new process object based on its name.
     *
     * @param string $processName
     * @return ProcessInterface
     */
    public function make($processName);
}
