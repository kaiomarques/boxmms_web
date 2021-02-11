<?php namespace App\Cqrs\Clientes\Queries\Application;

use App\Cqrs\Shared\Queries\Application\Query;

class ListaClientesQuery extends Query
{
    private $nome;

    public function __construct($nome) 
    {
        $nome = trim($nome);
        $this->nome = isset($nome) && !empty($nome) ? $nome : null;
    }

    public function getNome()
    {
        return $this->nome;
    }
}