<?php

namespace test_is74\Database;

use \SQLite3;

class SQLite3Database extends Database implements IDatabase
{
    private SQLite3 $db;

    public function __construct(array $config)
    {
        $this->db = new SQLite3($config["path"], SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    }

    public function select(string $sql) : array
    {
        $db_result = $this->db->query($sql);

        $result = [];
        while ($row = $db_result->fetchArray())
        {
            $result[] = $row;
        }
        return $result;
    }

    public function selectOne(string $sql): array
    {
        return $this->db->querySingle($sql, true);
    }

    public function insert(string $sql) : int
    {
        $this->db->exec($sql);
        return $this->db->lastInsertRowID();
    }

    public function execute(string $sql) : void
    {
        $this->db->exec($sql);
    }

    public function close() : void
    {
        $this->db->close();
    }
}