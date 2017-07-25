<?php
/**
 * Copyright (c) etouches, Inc.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;

class Semaphore
{
    /** @var string|int */
    public $id;
    /** @var int */
    public $count;
    /** @var array */
    public $listPayload = array();
}
