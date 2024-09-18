<?php

namespace test_is74\DTO;

abstract class DTO
{
    public static function getNullObject() : DTO
    {
        $obj = new static();

        // get_class_vars может вернуть неинициализированные поля, get_object_vars не может
        $properties = array_keys(get_class_vars(get_class($obj)));
        foreach ($properties as $property)
        {
            $obj->$property = null;
        }

        return $obj;
    }
}