<?php
namespace App\Core\Strings;

class StringConverter
{
    public function toIntOrNull($value)
    {
        return !isset($value) || empty($value) || !is_numeric($value) ? null : intval($value);
    }
}