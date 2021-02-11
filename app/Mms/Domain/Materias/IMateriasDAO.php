<?php namespace App\Mms\Domain\Materias;

interface IMateriasDAO
{
    public function count($inicio, $fim, $cliente, $midia, $status, $emissora, $programa);
    public function lista($inicio, $fim, $cliente, $midia, $status, $emissora, $programa, $orderBy, $orderByDirection, $limit, $offset);
}