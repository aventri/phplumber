<?php
/**
 * This example begins the process list defined by CreateAndFillDatabase.
 */
require_once __DIR__ . '/include/autoload.php';

$storage = new Storage();
$storage->connect();
$factory = new ProcessFactory();
$queue = new Queue($factory, $storage);
$queue->connect();

$list = new CreateAndFillDatabase($factory, $queue, $storage);
$list->process(array('database_name' => 'phplumber_example'));

$queue->close();
$storage->close();
