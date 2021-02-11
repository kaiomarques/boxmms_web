<?php
namespace App\Cqrs\Queries\Application\Emissoras;

use App\Cqrs\Queries\Model\Emissoras\IEmissorasRepository;
use App\Cqrs\Queries\Application\Shared\QueriesHandler;

class EmissorasQueriesHandler extends QueriesHandler
{
    private $repository;

    public function __construct(IEmissorasRepository $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    protected function registerHandlers()
    {
        $this->register(ListarEmissorasQuery::class, function ($query) { return $this->listarEmissoras($query); });
    }

    private function listarEmissoras(ListarEmissorasQuery $query)
    {
        $emissoras = $this->repository->listaPor($query->getVeiculoId(), $query->getPracaId());
        $result = array();

        foreach ($emissoras as $emissora) {
            array_push($result, array(
                "id" => $emissora->getId(),
                "nome" => $emissora->getNome()));
        }

        return $result;
    }
}
