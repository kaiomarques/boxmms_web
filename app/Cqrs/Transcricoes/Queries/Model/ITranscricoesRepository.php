<?php namespace App\Cqrs\Transcricoes\Queries\Model;

interface ITranscricoesRepository
{
    public function listaVinculos(array $ids);
}