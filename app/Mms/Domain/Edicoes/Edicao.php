<?php namespace App\Mms\Domain\Edicoes;

class Edicao
{
    private $id;
    private $data;
    private $programa;
    private $emissora;
    private $prioridadeTextual;
    private $duracao;
    private $prioridade;
    private $dia;
    private $inicio;
    private $fim;
    private $tempoRealizado;
    private $tempoRealizadoEmminutos;
    private $tempoTotal;
    private $tempoTotalTextual;
    private $pasta;

    public function __construct(
        $id,
        $data,
        $programa,
        $emissora,
        $prioridadeTextual,
        $prioridade,
        $duracao,
        $dia,
        $inicio,
        $fim,
        $tempoRealizado,
        $tempoRealizadoEmminutos,
        $tempoTotal,
        $tempoTotalTextual,
        $pasta,
        $eventoStatus
    ) {
        $this->id = $id;
        $this->data = $data;
        $this->programa = $programa;
        $this->emissora = $emissora;
        $this->prioridadeTextual  = $prioridadeTextual;
        $this->duracao = $duracao;
        $this->prioridade = $prioridade;
        $this->dia = $dia;
        $this->inicio = $inicio;
        $this->fim = $fim;
        $this->tempoRealizado = $tempoRealizado;
        $this->tempoRealizadoEmminutos = $tempoRealizadoEmminutos;
        $this->tempoTotal = $tempoTotal;
        $this->tempoTotalTextual = $tempoTotalTextual;
        $this->pasta = $pasta;
        $this->eventoStatus = $eventoStatus;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getData()
    {
        return $this->data;
    }
    public function getPrograma()
    {
        return $this->programa;
    }
    public function getEmissora()
    {
        return $this->emissora;
    }
    public function getPrioridadeTextual()
    {
        return $this->prioridadeTextual;
    }
    public function getDuracao()
    {
        return $this->duracao;
    }
    public function getPrioridade()
    {
        return $this->prioridade;
    }
    public function getDia()
    {
        return $this->dia;
    }
    public function getInicio()
    {
        return $this->inicio;
    }
    public function getFim()
    {
        return $this->fim;
    }
    public function getTempoRealizado()
    {
        return $this->tempoRealizado;
    }
    public function getTempoRealizadoEmMinutos()
    {
        return $this->tempoRealizadoEmminutos;
    }
    public function getTempoTotal()
    {
        return $this->tempoTotal;
    }
    public function getTempoTotalTextual()
    {
        return $this->tempoTotalTextual;
    }
    public function getPasta()
    {
        return $this->pasta;
    }
    public function getEventoStatus()
    {
        return $this->eventoStatus;
    }

}
