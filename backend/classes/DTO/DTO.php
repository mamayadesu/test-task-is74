<?php

namespace test_is74\DTO;

abstract class DTO
{
    public bool $_loaded = false;

    public static function getNullObject() : DTO
    {
        $obj = new static();

        // get_class_vars может вернуть неинициализированные поля, get_object_vars не может
        $properties = array_keys(get_class_vars(get_class($obj)));
        foreach ($properties as $property)
        {
            if ($property == "_loaded")
            {
                continue;
            }
            $obj->$property = null;
        }

        return $obj;
    }

    public static function fromArray(array $arr) : DTO
    {
        $obj = new static();

        foreach ($arr as $key => $value)
        {
            $obj->$key = $value;
        }

        $obj->_loaded = true;

        return $obj;
    }
}