<?php namespace App\Mms\Application\Edicoes\Queries;

use App\Mms\Application\Shared\Queries\CollectionQueryResult;

class ListarEdicoesQueryResult extends CollectionQueryResult
{
    public function __construct(array $edicoes)
    {
        parent::__construct($edicoes);
    }

    protected function handle($edicao)
    {
        return array(
            "alt_path" => null,
            "blnk" => "",
            "data" => $edicao->getData(),
            "status" => $edicao->getEventoStatus() ,
            "dia" => $edicao->getDia(),
            "duracao" => $edicao->getDuracao(),
            "emissora" => $edicao->getEmissora(),
            "form_pai" => "",
            "hora_fim" => $edicao->getFim(),
            "hora_inicio" => $edicao->getInicio(),
            "id" => $edicao->getId(),
            "path" => $edicao->getPasta(),
            "prioridade" => $edicao->getPrioridadeTextual(),
            "prioridade_int" => $edicao->getPrioridade(),
            "programa" => $edicao->getPrograma(),
            "tempo_h" => $edicao->getTempoTotalTextual(),
            "tempo_realizado" => $edicao->getTempoRealizado(),
            "tempo_realizado_minutos" => $edicao->getTempoRealizadoEmMinutos(),
            "tempo_total" => $edicao->getTempoTotal());
    }
}
