<?php

/**
 * Phplumber Process example implementation
 */
class CreateViews extends \Etouches\Phplumber\Process
{
    public function invoke(array $payload)
    {
        $database_name = $this->getPayloadAttribute($payload, 'database_name');
        echo "Connecting to database $database_name...\n";
        echo "Creating views for $database_name...\n";

        // Return payload for next process, here we can add anything we calculated and need to pass on.
        return $payload;
    }
}
