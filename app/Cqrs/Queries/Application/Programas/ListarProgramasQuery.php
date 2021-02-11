<?php
namespace App\Cqrs\Queries\Application\Programas;

use App\Cqrs\Queries\Application\Shared\Query;

class ListarProgramasQuery extends Query
{
    private $veiculoId = null;
    private $pracaId = null;
    private $emissoraId = null;

    public function __construct($veiculoId, $pracaId, $emissoraId)
    {
        parent::__construct();

        $this->veiculoId = $this->toIntOrNull($veiculoId);
        $this->pracaId = $this->toIntOrNull($pracaId);
        $this->emissoraId = $this->toIntOrNull($emissoraId);
    }

    public function getVeiculoId()
    {
        return $this->veiculoId;
    }

    public function getPracaId()
    {
        return $this->pracaId;
    }

    public function getEmissoraId()
    {
        return $this->emissoraId;
    }
}