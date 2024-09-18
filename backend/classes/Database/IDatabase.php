<?php

namespace test_is74\Database;

interface IDatabase
{
    public function select(string $sql) : ?array;

    public function selectOne(string $sql) : ?array;

    public function insert(string $sql);

    public function execute(string $sql) : void;

    public function close() : void;
}