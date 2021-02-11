<?php namespace App\Mms\Application\Recortes\Queries;

use App\Mms\Application\Shared\Queries\CollectionQueryResult;

class ListarRecortesQueryResult extends CollectionQueryResult
{
    public function __construct(array $recortes)
    {
        parent::__construct($recortes);
    }

    protected function handle($recorte)
    {
        return array(
            "id" => $recorte->getId(),
            "id_evento" => $recorte->getEdicao(),
            "programa_nome" => $recorte->getPrograma(),
            "data" => $recorte->getData(),
            "hora_inicio" => $recorte->getInicio(),
            "tempo_h_realizado" => $recorte->getTempoRealizadoTextual(),
            "id_materia_radiotv_jornal" => $recorte->getMateria(),
            "titulo_materia" => $recorte->getMateriaTitulo(),
            "meta_dados" => $recorte->getMetaDados(),
            "blnk" => "");
    }
}