<?php namespace App\Cqrs\Clientes\Queries\Model;

interface IClientesRepository
{
    public function lista($nome);
}