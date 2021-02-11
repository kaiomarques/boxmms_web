<?php namespace App\Cqrs\Eventos\Queries\Application;

use App\Cqrs\Shared\Queries\Application\CollectionQueryResult;

class ListaEventoStatusQueryResult extends CollectionQueryResult
{
    public function __construct(array $evento_) 
    {
        parent::__construct($evento_);
    }

    protected function handle($item) {
        return array("id" => $item->getId(), "nome" => $item->getDescricao());
    }
}