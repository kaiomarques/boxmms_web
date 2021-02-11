<?php namespace App\Cqrs\Canais\Queries\Application;

use App\Cqrs\Canais\Queries\Model\ICanaisRepository;

class CanaisQueriesHandler
{
    private $canaisRepository;

    public function __construct(ICanaisRepository $canaisRepository)
    {
        $this->canaisRepository = $canaisRepository;
    }

    public function lista(ListaCanaisQuery $query)
    {
        $canais = $this->canaisRepository->lista($query->getClienteId());

        return new ListaCanaisQueryResult($canais);
    }
}