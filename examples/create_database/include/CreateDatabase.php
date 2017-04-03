<?php

/**
 * Phplumber Process example implementation
 */
class CreateDatabase extends \Etouches\Phplumber\Process
{
    public function invoke(array $payload)
    {
        $database_name = $this->getPayloadAttribute($payload, 'database_name');
        echo "Drop database $database_name if it exists...\n";
        echo "Create database $database_name...\n";

        // Return payload for next process, here we can add anything we calculated and need to pass on.
        return $payload;
    }
}
