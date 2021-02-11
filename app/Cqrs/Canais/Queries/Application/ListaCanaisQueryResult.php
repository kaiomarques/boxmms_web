<?php namespace App\Cqrs\Canais\Queries\Application;

use App\Cqrs\Shared\Queries\Application\CollectionQueryResult;

class ListaCanaisQueryResult extends CollectionQueryResult
{
    public function __construct(array $canais) 
    {
        parent::__construct($canais);
    }

    protected function handle($item) {
        return array("id" => $item->getId(), "nome" => $item->getNome());
    }
}