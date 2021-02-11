<?php namespace App\Cqrs\Canais\Queries\Model;

interface ICanaisRepository
{
    public function lista($clienteId);
}

