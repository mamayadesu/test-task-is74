<?php

namespace test_is74\Database;

use \SQLite3;
use \Exception;
use test_is74\Database\Exceptions\SQLite3Exception;

class SQLite3Database extends Database implements IDatabase
{
    private SQLite3 $db;

    protected function __construct(array $config)
    {
        $need_installation = !file_exists($config["path"]);
        $this->db = new SQLite3($config["path"], SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

        if ($need_installation)
        {
            try
            {
                $this->execute("CREATE TABLE tariffs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    price REAL NOT NULL,
    speed INTEGER NOT NULL,
    created INTEGER NOT NULL,
    end INTEGER NOT NULL,
    updated INTEGER NULL
)");

                $this->execute("CREATE TABLE options (
    name VARCHAR(255) PRIMARY KEY,
    value VARCHAR(255) NOT NULL
)");

                $this->insert("INSERT INTO options (name, value) VALUES ('pdf_file_version', '');");
            }
            catch (SQLite3Exception $e)
            {
                // удаляем базу при неудачной установке
                unlink($config["path"]);
                throw $e;
            }
        }
    }

    /**
     * @param string $sql
     * @return array
     * @throws SQLite3Exception
     */
    public function select(string $sql) : array
    {
        $db_result = @$this->db->query($sql);
        $this->checkForErrors();

        $result = [];
        while ($row = $db_result->fetchArray())
        {
            $newRow = [];
            foreach ($row as $column => $value)
            {
                if (gettype($column) == "integer")
                {
                    continue;
                }
                $newRow[$column] = $value;
            }
            $result[] = $newRow;
        }
        return $result;
    }

    /**
     * @param string $sql
     * @return array
     * @throws SQLite3Exception
     */
    public function selectOne(string $sql) : array
    {
        $result = @$this->db->querySingle($sql, true);
        $this->checkForErrors();

        if (!$result)
        {
            return [];
        }
        return $result;
    }

    /**
     * @param string $sql
     * @return int
     * @throws SQLite3Exception
     */
    public function insert(string $sql) : int
    {
        @$this->db->exec($sql);
        $this->checkForErrors();
        return $this->db->lastInsertRowID();
    }

    /**
     * @param string $sql
     * @return void
     * @throws SQLite3Exception
     */
    public function execute(string $sql) : void
    {
        @$this->db->exec($sql);
        $this->checkForErrors();
    }

    /**
     * @return void
     * @throws SQLite3Exception
     */
    public function close() : void
    {
        @$this->db->close();
        $this->checkForErrors();
    }

    private function checkForErrors() : void
    {
        if ($this->db->lastErrorCode())
        {
            throw new SQLite3Exception($this->db->lastErrorMsg(), $this->db->lastErrorCode());
        }
    }

    public function escapeString(string $value) : string
    {
        return SQLite3::escapeString($value);
    }
}