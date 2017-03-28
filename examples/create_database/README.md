Example
-------

In this example we will pretend we are creating and populating a database.  For demo purposes we'll simply 
output SQL strings to the console.

This example is writing all data to disk to keep it independent and simple. **In real systems you should write to a 
queueing system such as RabbitMQ and to a reliable storage system such as Redis.**  

To run this example:

1. Execute `php start.php` to run the first sequential steps of the process.
1. Execute `php queue_worker.php` to run concurrent and subsequent steps.

You can run multiple workers to operate on the queue concurrently.

### Dependencies

This example requires the [SQLite 3 extension](http://php.net/manual/en/book.sqlite3.php) for PHP.
