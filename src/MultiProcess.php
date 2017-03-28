<?php
/**
 * Copyright (c) etouches.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;

/**
 * Class MultiProcess
 *
 * Represents a process that can be split into multiple concurrent processes by payload.
 *
 * @package DataServices\ETL\Process
 */
abstract class MultiProcess extends Process
{
    /**
     * Get multiple async process payloads from the "main" process payload.
     *
     * @param $payload
     * @return array
     */
    abstract public function getAsyncPayloads(array $payload);
}
