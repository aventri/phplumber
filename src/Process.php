<?php
/**
 * Copyright (c) etouches, Inc.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;


abstract class Process implements ProcessInterface
{
    /**
     * Get a value from the payload. Raises an exception if it's not found.
     *
     * @param array $payload
     * @param string $attribute
     * @return mixed
     * @throws \Exception
     */
    protected function getPayloadAttribute($payload, $attribute)
    {
        if (!is_array($payload)) {
            throw new \Exception("Payload must be an array");
        }
        if (!array_key_exists($attribute, $payload)) {
            throw new \Exception("Payload missing attribute $attribute");
        }
        return $payload[$attribute];
    }

    /**
     * @inheritdoc
     */
    abstract public function invoke(array $payload);
}
