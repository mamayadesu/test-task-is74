<?php

namespace test_is74\Helpers;

use test_is74\Database\Database;
use test_is74\DTO\CsvSession;

class CsvSessionsHelper
{
    public static function createSession() : int
    {
        if (!file_exists(CSV_SESSIONS_DIR))
        {
            mkdir(CSV_SESSIONS_DIR);
        }
        $expires = time() + 86400;
        $session_id = Database::getInstance()->insert("INSERT INTO csv_sessions (filename, expires) VALUES
        ('" . Database::getInstance()->escapeString($_FILES["csv_file"]["name"]) . "', $expires);");
        $new_file = CSV_SESSIONS_DIR . "session_$session_id.csv";
        $source = $_FILES["csv_file"]["tmp_name"];
        move_uploaded_file($source, $new_file);
        return $session_id;
    }

    public static function clearExpiredSessions() : void
    {
        $rows = Database::getInstance()->select("SELECT * FROM csv_sessions WHERE expires < " . time());
        $ids_to_delete = [];
        foreach ($rows as $row)
        {
            $session = CsvSession::fromArray($row);
            $ids_to_delete[] = $session->id;
        }

        if (count($ids_to_delete) == 0)
        {
            return;
        }

        Database::getInstance()->execute("DELETE FROM csv_sessions WHERE id IN (" . implode(", ", $ids_to_delete) . ")");
        foreach ($ids_to_delete as $id)
        {
            $filename = CSV_SESSIONS_DIR . "session_$id.csv";
            if (file_exists($filename))
            {
                unlink($filename);
            }
        }
    }

    public static function CsvToArray($filename, $delimiter = ",")
    {
        $data = array();
        if (($f = fopen($filename, "r")) !== false)
        {
            while (($row = fgetcsv($f, 1000, $delimiter)) !== false)
            {
                $data[] = $row;
            }
            fclose($f);
        }
        return $data;
    }
}