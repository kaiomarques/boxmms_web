<?php namespace App\Cqrs\Transcricoes\Queries\Application;

use App\Cqrs\Transcricoes\Queries\Model\ITranscricoesRepository;

class TranscricoesQueriesHandler
{
    private $repository;

    public function __construct(ITranscricoesRepository $repository)
    {
        $this->repository = $repository;
    }

    public function listaVinculos(ListaVinculosQuery $query)
    {
        $transcricoes = $this->repository->listaVinculos($query->getIdsDasTranscricoes());

        return new ListaVinculosQueryResult($transcricoes);
    }
}