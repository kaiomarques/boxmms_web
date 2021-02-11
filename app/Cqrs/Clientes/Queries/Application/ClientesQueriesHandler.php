<?php namespace App\Cqrs\Clientes\Queries\Application;

use App\Cqrs\Clientes\Queries\Model\IClientesRepository;

class ClientesQueriesHandler
{
    private $clientesRepository;

    public function __construct(IClientesRepository $clientesRepository)
    {
        $this->clientesRepository = $clientesRepository;
    }

    public function lista(ListaClientesQuery $query)
    {
        $clientes = $this->clientesRepository->lista($query->getNome());

        return new ListaClientesQueryResult($clientes);
    }
}