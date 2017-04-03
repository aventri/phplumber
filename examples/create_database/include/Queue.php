<?php

/**
 * Phplumber Queue example implementation
 *
 * DO NOT USE THIS EXAMPLE IN REAL PROJECTS.
 *
 * This writes to a database file just to prove the concept. In a real system a dedicated queue application
 * must be used, such as RabbitMQ or Redis.
 */
class Queue extends \Etouches\Phplumber\Queue
{
    /** @var SQLite3 */
    protected $db;

    public function __destruct()
    {
        $this->close();
    }

    public function publishMessage(array $message)
    {
        $statement = $this->db->prepare("INSERT INTO queue (message) VALUES (:message)");
        $statement->bindValue(':message', json_encode($message), SQLITE3_TEXT);
        $statement->execute();
    }

    public function consume()
    {
        while (true) {
            // Read one message out of the queue
            $result = $this->db->query("SELECT id, message FROM queue ORDER BY id DESC LIMIT 1");
            if ($row = $result->fetchArray()) {
                // Remove the message from the queue
                $statement = $this->db->prepare("DELETE FROM queue WHERE id = :id");
                $statement->bindValue(':id', $row['id']);
                $statement->execute();

                $this->consumeMessage(json_decode($row['message'], true));
            }
        }
    }

    public function connect()
    {
        $this->db = new SQLite3(realpath(__DIR__ . '/..') . '/queue.db');
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS queue (
                id INTEGER PRIMARY KEY,
                message TEXT
            )"
        );
    }

    public function close()
    {
        $this->db->close();
    }
}
