<?php namespace App\Cqrs\Clientes\Queries\Application;

use App\Cqrs\Shared\Queries\Application\CollectionQueryResult;

class ListaClientesQueryResult extends CollectionQueryResult
{
    public function __construct(array $clientes) 
    {
        parent::__construct($clientes);
    }

    protected function handle($item) {
        return array("id" => $item->getId(), "nome" => $item->getNome());
    }
}