<?php
namespace App\Cqrs\Queries\Application\Emissoras;

use App\Cqrs\Queries\Application\Shared\Query;

class ListarEmissorasQuery extends Query
{
    private $veiculoId = null;
    private $pracaId = null;

    public function __construct($veiculoId, $pracaId)
    {
        parent::__construct();
        $this->veiculoId = $this->toIntOrNull($veiculoId);
        $this->pracaId = $this->toIntOrNull($pracaId);
    }

    public function getVeiculoId()
    {
        return $this->veiculoId;
    }

    public function getPracaId()
    {
        return $this->pracaId;
    }
}