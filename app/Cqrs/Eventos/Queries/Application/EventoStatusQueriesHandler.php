<?php namespace App\Cqrs\Eventos\Queries\Application;

use App\Cqrs\Eventos\Queries\Model\IEventoStatusRepository;

class EventoStatusQueriesHandler
{
    private $eventoStatusRepository;

    public function __construct(IEventoStatusRepository $eventoStatusRepository)
    {
        $this->eventoStatusRepository = $eventoStatusRepository;
    }

    public function lista(ListaEventoStatusQuery $query)
    {
        $evento_status = $this->eventoStatusRepository->lista($query->getDescricao());

        return new ListaEventoStatusQueryResult($evento_status);
    }
}