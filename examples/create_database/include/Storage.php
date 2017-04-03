<?php

/**
 * Phplumber Storage example implementation
 */
class Storage implements \Etouches\Phplumber\StorageInterface
{
    /** @var SQLite3 */
    protected $db;

    public function storeSemaphore(\Etouches\Phplumber\Semaphore $semaphore)
    {
        $statement = $this->db->prepare(
            "INSERT INTO semaphore (count, list_payload) 
            VALUES (:count, :list_payload)"
        );
        $statement->bindValue(':count', $semaphore->count, SQLITE3_INTEGER);
        $statement->bindValue(':list_payload', json_encode($semaphore->listPayload), SQLITE3_TEXT);
        $statement->execute();
        return $this->db->lastInsertRowID();
    }

    public function getSemaphore($id)
    {
        $statement = $this->db->prepare(
            "SELECT count, list_payload
            FROM semaphore
            WHERE id = :id"
        );
        $statement->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $statement->execute();
        while ($row = $result->fetchArray()) {
            $semaphore = new \Etouches\Phplumber\Semaphore();
            $semaphore->id = (int) $id;
            $semaphore->count = (int) $row['count'];
            $semaphore->listPayload = json_decode($row['list_payload'], true);
            return $semaphore;
        }
        return null;
    }

    public function decrementSemaphoreCount($id)
    {
        $statement = $this->db->prepare(
            "UPDATE semaphore
            SET count = count - 1
            WHERE id = :id"
        );
        $statement->bindValue(':id', $id, SQLITE3_INTEGER);
        $statement->execute();
    }

    public function deleteSemaphore($id)
    {
        $statement = $this->db->prepare("DELETE FROM semaphore WHERE id = :id");
        $statement->bindValue(':id', $id, SQLITE3_INTEGER);
    }

    public function connect()
    {
        $this->db = new SQLite3(realpath(__DIR__ . '/..') . '/storage.db');
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS semaphore (
                id INTEGER PRIMARY KEY,
                count INT,
                list_payload  TEXT
            )"
        );
    }

    public function close()
    {
        $this->db->close();
    }
}
