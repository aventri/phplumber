<?php

/**
 * Phplumber MultiProcess example implementation
 */
class CreateTable extends \Etouches\Phplumber\MultiProcess
{
    public function getAsyncPayloads(array $payload)
    {
        $database_name = $this->getPayloadAttribute($payload, 'database_name');
        $table_names = array('person', 'team', 'organization');

        // Create one payload for each table to be handled as an independent process
        $payloads = array();
        foreach ($table_names as $table) {
            $payloads[] = array('database_name' => $database_name, 'table_name' => $table);
        }
        return $payloads;
    }

    public function invoke(array $payload)
    {
        $database_name = $this->getPayloadAttribute($payload, 'database_name');
        $table_name = $this->getPayloadAttribute($payload, 'table_name');
        echo "Connecting to database $database_name...\n";

        switch ($table_name) {
            case 'person':
                echo "CREATE TABLE person (id INT, fname VARCHAR(255), lname VARCHAR(255))\n";
                echo "INSERT INTO person (fname, lname) VALUES ...\n\n";
                break;
            case 'team':
                echo "CREATE TABLE team (id INT, name VARCHAR(255))\n";
                echo "INSERT INTO team (name) VALUES ...\n\n";
                break;
            case 'organization':
                echo "CREATE TABLE organization (id INT, name VARCHAR(255))\n";
                echo "INSERT INTO organization (name) VALUES ...\n\n";
                break;
        }

        // Return main payload for next process
        return array('database_name' => $database_name);
    }
}
