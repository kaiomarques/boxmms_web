<?php
namespace App\Cqrs\Queries\Model\Emissoras;

interface IEmissorasRepository
{
    public function listaPor($veiculoId, $pracaId);
}