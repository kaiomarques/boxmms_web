<?php namespace App\Cqrs\Canais\Queries\Application;

use App\Cqrs\Shared\Queries\Application\Query;

class ListaCanaisQuery extends Query
{
    private $clienteId;

    public function __construct($clienteId) 
    {
        $this->clienteId = $this->extraiId($clienteId);
    }

    public function getClienteId()
    {
        return $this->clienteId;
    }
}