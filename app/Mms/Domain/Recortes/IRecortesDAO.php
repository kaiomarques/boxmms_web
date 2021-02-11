<?php namespace App\Mms\Domain\Recortes;

interface IRecortesDAO 
{
    public function lista($inicio, $fim, $cliente, $midia, $emissora, $programa);
}