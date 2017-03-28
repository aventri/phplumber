<?php

class Storage implements \Etouches\Phplumber\StorageInterface
{
    /** @var SQLite3 */
    protected $db;

    public function storeSemaphore(\Etouches\Phplumber\Semaphore $semaphore)
    {
        if ($semaphore->id) {
            $statement = $this->db->prepare(
                "UPDATE semaphore 
                SET list = :list,
                    process = :process,
                    count = :count,
                    list_payload = :list_payload
                WHERE id = :id"
            );
            $statement->bindValue(':id', $semaphore->id, SQLITE3_INTEGER);
        } else {
            $statement = $this->db->prepare(
                "INSERT INTO semaphore (list, process, count, list_payload) 
                VALUES (:list, :process, :count, :list_payload)"
            );
        }
        $statement->bindValue(':list', $semaphore->list, SQLITE3_TEXT);
        $statement->bindValue(':process', $semaphore->process, SQLITE3_TEXT);
        $statement->bindValue(':count', $semaphore->count, SQLITE3_INTEGER);
        $statement->bindValue(':list_payload', json_encode($semaphore->listPayload), SQLITE3_TEXT);
        $statement->execute();
        if ($semaphore->id) {
            return $semaphore->id;
        } else {
            return $this->db->lastInsertRowID();
        }
    }

    public function getSemaphore($id)
    {
        $statement = $this->db->prepare(
            "SELECT list, process, count, list_payload
            FROM semaphore
            WHERE id = :id"
        );
        $statement->bindValue(':id', $id, SQLITE3_INTEGER);
        $result = $statement->execute();
        while ($row = $result->fetchArray()) {
            $semaphore = new \Etouches\Phplumber\Semaphore();
            $semaphore->id = (int) $id;
            $semaphore->list = $row['list'];
            $semaphore->process = $row['process'];
            $semaphore->count = (int) $row['count'];
            $semaphore->listPayload = json_decode($row['list_payload'], true);
            return $semaphore;
        }
        return null;
    }

    public function deleteSemaphore($id)
    {
        $statement = $this->db->prepare("DELETE semaphore WHERE id = :id");
        $statement->bindValue(':id', $id, SQLITE3_INTEGER);
    }

    public function connect()
    {
        $this->db = new SQLite3(realpath(__DIR__ . '/..') . '/sqlite_storage.db');
        $this->db->exec(
            "CREATE TABLE IF NOT EXISTS semaphore (
                id INTEGER PRIMARY KEY,
                list VARCHAR(255),
                process VARCHAR(255),
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