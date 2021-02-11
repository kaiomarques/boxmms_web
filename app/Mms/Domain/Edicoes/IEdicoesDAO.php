<?php namespace App\Mms\Domain\Edicoes;

interface IEdicoesDAO 
{
    public function lista($inicio, $fim, $cliente, $midia, $praca, $emissora, $programa, $eventoStatus);
}