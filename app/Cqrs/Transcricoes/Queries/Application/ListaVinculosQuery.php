<?php namespace App\Cqrs\Transcricoes\Queries\Application;

use App\Cqrs\Shared\Queries\Application\Query;

class ListaVinculosQuery extends Query
{
    private $idsDasTranscricoes = [];

    public function __construct(array $idsDasTranscricoes)
    {
        foreach($idsDasTranscricoes as $id) {
            array_push($this->idsDasTranscricoes,  $this->extraiId($id));
        }
    }

    public function getIdsDasTranscricoes() {
        $result = array();

        foreach($this->idsDasTranscricoes as $id) {
            array_push($result, $id);
        }

        return $result;
    }
}