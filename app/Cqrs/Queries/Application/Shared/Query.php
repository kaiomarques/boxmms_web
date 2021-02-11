<?php
namespace App\Cqrs\Queries\Application\Shared;

use App\Core\Strings\StringConverter;

abstract class Query
{
    private $converter;
    
    public function __construct()
    {
        $this->converter = new StringConverter();
    }

    protected function toIntOrNull($value)
    {
        $result = $this->converter->toIntOrNull($value);
        
        return $result == null || $result == -1  ? null : $result;
    }
}