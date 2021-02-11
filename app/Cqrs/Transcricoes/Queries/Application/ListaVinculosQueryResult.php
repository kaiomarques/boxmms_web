<?php namespace App\Cqrs\Transcricoes\Queries\Application;

use App\Cqrs\Shared\Queries\Application\CollectionQueryResult;

class ListaVinculosQueryResult extends CollectionQueryResult
{
    public function __construct(array $vinculos) 
    {
        parent::__construct($vinculos);
    }

    protected function handle($item) {
        return array(
            "clienteId" => $item->getClienteId(), 
            "clienteNome" => $item->getClienteNome(),
            "topicoId" => $item->getCanalId(), 
            "topicoNome" => $item->getCanalNome());
    }
}