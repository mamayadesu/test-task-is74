<?php

namespace test_is74\Controllers;

interface IController
{
    public function handleRequest(array $parameters) : void;
}