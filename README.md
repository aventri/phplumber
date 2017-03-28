Phplumber
=========

Phplumber is a very simple pipelining library for PHP which allows a mix of asynchronous and synchronous processes. 
Use it to break a large process into multiple steps using a queue and multi-processing.  Phplumber contains no 
hard-coded dependencies and can be backed by any queue setup and any storage mechanism.

Requirements
------------

- PHP 5.3+
- A queue, such as RabbitMQ or Redis
- Storage, such as a relational database table or Redis

Example
-------

Let's take creating and filling a database as an example.  It takes multiple steps and some can be done concurrently.

1. Create database (one process)
1. Create and populate tables (one process per table)
1. Create views dependent on multiple tables (one process, dependent on all tables existing)


```
                         Create table 1
                       /                \
    Create database ->   Create table 2   -> Create views
                       \                /
                         Create table 3
```

First we would define our processes.  Sequential steps extend `Process`.  A step that can run multiple times with 
different data extends `MultiProcess`.

```php
class CreateDatabase extends Process
{
    public function invoke($payload)
    {
        $database_name = $payload['database_name'];
        echo "Drop database $database_name if it exists...\n";
        echo "Creating database $database_name...\n";
    }
}
```
```php
class CreateTable extends MultiProcess
{
    // Determine the data we need to queue for async processes
    public function getAsyncPayloads($payload)
    {
        $database_name = $payload['database_name'];
        $table_names = array('first_table', 'second_table', 'third_table');
        
        $payloads = array();
        foreach ($table_names as $table) {
            $payloads[] = array('database_name' => $database_name, 'table_name' => $table);
        }
        return $payloads;
    }
    
    public function invoke($payload)
    {
        $database_name = $payload['database_name'];
        $table_name = $payload['table'];
        echo "Connecting to database $database_name...\n";
        echo "Creating and populating $table_name...\n";
        
        switch ($payload['table']) {
            case 'first_table':
                // Create table and insert rows...
                break;
            // ...
        }
    }
}
```

Then we define the sequence of processes.

```php
class CreateAndFillDatabase extends ProcessList
{
    protected function setup()
    {
        $this
            ->add('CreateDatabase')
            ->add('CreateTable')
            ->add('CreateViews');
    }
}
```

We can now kick off the process sequence. 

```php
$equation = new CreateAndFillDatabase();
$equation->process(array('database_name' => 'test_db'));
```

See the `examples` directory for complete, working demos.

Getting Started
---------------

1. Implement `StorageInterface`.  This will hold semaphore data.  Appropriate storage engines include any 
relational database, nosql, or key-value stores such as Redis.
1. Extend the `Queue` class to integrate with a queue system.  Appropriate queue engines include Redis and RabbitMQ.
1. Write each process as a class that extends `Process` (for synchronous) or `MultiProcess` (for asynchronous).
1. Implement `ProcessFactoryInterface`. This will create each instance of `ProcessInterface`, allowing you to set 
up your processes with any prerequisites, such as a database connection or configuration options.
1. Put the processes together by extending `ProcessList`.
1. Implement a worker daemon which instantiates your `Queue` implementation and calls `consume()` to listen for 
incoming messages.  Each message is to invoke a single part of a multi-process.  Run multiple workers to execute 
processes concurrently.
1. Choose a place in your system to start the entire workflow, instantiate your `ProcessList`, and call `process()`, 
passing it the initial payload.
