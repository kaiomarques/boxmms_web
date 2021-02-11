<?php
namespace App\Cqrs\Queries\Application\Programas;

use App\Cqrs\Queries\Model\Programas\IProgramasRepository;
use App\Cqrs\Queries\Application\Shared\QueriesHandler;

class ProgramasQueriesHandler extends QueriesHandler
{
    private $repository;

    public function __construct(IProgramasRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    protected function registerHandlers()
    {
        $this->register(ListarProgramasQuery::class, function ($query) { return $this->listarProgramas($query); });
    }

    private function listarProgramas(ListarProgramasQuery $query)
    {
        $programas = $this->repository->listaPor($query->getVeiculoId(), $query->getPracaId(), $query->getEmissoraId());
        $result = array();

        foreach ($programas as $programa) {
            array_push($result, array(
                "id" => $programa->getId(),
                "nome" => $programa->getNome()));
        }
        
        return $result;
    }
}