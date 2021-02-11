<?php
namespace App\Cqrs\Queries\Application\Pracas;

use App\Cqrs\Queries\Model\Pracas\IPracasRepository;
use App\Cqrs\Queries\Application\Shared\QueriesHandler;

class PracasQueriesHandler extends QueriesHandler
{
    private $repository;

    public function __construct(IPracasRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    protected function registerHandlers()
    {
        $this->register(ListarPracasQuery::class, function ($query) { return $this->listarPracas($query); });
    }

    private function listarPracas(ListarPracasQuery $query)
    {
        $pracas = $this->repository->lista();
        $result = array();

        foreach ($pracas as $praca) {
            array_push($result, array(
                "id" => $praca->getId(),
                "nome" => $praca->getNome()));
        }
        
        return $result;
    }

}