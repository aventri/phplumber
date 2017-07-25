<?php
/**
 * Copyright (c) etouches, Inc.  All rights reserved.
 * Licensed under the MIT License.  See LICENSE.md file in project root for complete license.
 */

namespace Etouches\Phplumber;


/**
 * Contract that calls for providing functionality for storing and retrieving data across processes.
 *
 * Interface StorageInterface
 *
 * @package Etouches\Phplumber
 */
interface StorageInterface
{
    /**
     * Record a semaphore in storage.
     *
     * @param Semaphore $semaphore Data to store
     * @return string|int Key created or updated
     */
    public function storeSemaphore(Semaphore $semaphore);

    /**
     * Get values from storage.
     *
     * @param string|int $id
     * @return Semaphore
     */
    public function getSemaphore($id);

    /**
     * Decrement the count property of a semaphore.
     *
     * @param string|int $id
     */
    public function decrementSemaphoreCount($id);

    /**
     * Remove from storage.
     *
     * @param string|int $id
     */
    public function deleteSemaphore($id);

    /**
     * Connect to a storage provider.
     */
    public function connect();

    /**
     * Disconnect from a storage provider.
     */
    public function close();
}
