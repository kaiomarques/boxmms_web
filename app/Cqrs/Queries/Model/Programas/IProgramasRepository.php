<?php
namespace App\Cqrs\Queries\Model\Programas;

interface IProgramasRepository
{
    public function listaPor($veiculoId, $pracaId, $emissoraId);
}