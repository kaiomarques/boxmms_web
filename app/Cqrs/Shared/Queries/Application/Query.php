<?php namespace App\Cqrs\Shared\Queries\Application;

class Query
{
    protected function extraiId($id)
    {
        $result = isset($id) && !empty($id) && is_numeric($id) ? intval($id) : 0;
        
        return $result > 0 ? $result : null;
    }
}