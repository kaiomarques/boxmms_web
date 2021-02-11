<?php
namespace App\Core\Strings;

class StringBuilder
{
    private $strings = array();

    public function append(string $string)
    {
        array_push($this->strings, $string);

        return $this;
    }
    
    public function appendLine(string $string)
    {
        return $this->append($string . PHP_EOL);
    }

    public function toString()
    {
        return implode("", $this->strings);
    }
}