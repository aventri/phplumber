<?php
/**
 * The queue_worker example consumes messages as they come out of the queue and runs one process at a time
 * as defined by queue messages.
 *
 * This is a very simplified example.  In a real application you will want to gracefully catch errors,
 * retry on timed-out queue connections, etc.
 */
require_once __DIR__ . '/include/autoload.php';

echo "This listens on the queue indefinitely until you hit ^C\n";

$processFactory = new ProcessFactory();
$storage = new Storage();
$storage->connect();
$queue = new Queue($processFactory, $storage);
$queue->connect();
$queue->consume();
