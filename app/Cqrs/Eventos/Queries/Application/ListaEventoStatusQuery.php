<?php namespace App\Cqrs\Eventos\Queries\Application;

use App\Cqrs\Shared\Queries\Application\Query;

class ListaEventoStatusQuery extends Query
{
    private $descricao;

    public function getDescricao()
    {
        return $this->descricao;
    }
}