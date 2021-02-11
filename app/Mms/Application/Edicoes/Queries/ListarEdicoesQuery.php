<?php namespace App\Mms\Application\Edicoes\Queries;

use App\Mms\Application\Shared\Queries\Query;
use App\Mms\Shared\Tempo;

class ListarEdicoesQuery extends Query
{
    private $inicio;
    private $fim;
    private $cliente;
    private $midia;
    private $praca;
    private $emissora;
    private $programa;
    private $eventoStatus;

    public function __construct(    
        $inicio,
        $fim,
        $cliente,
        $midia,
        $praca,
        $emissora,
        $programa,
        $eventoStatus)
    {
        $this->inicio = $this->parseDateOr($inicio, date(parent::DATE_FORMAT), Tempo::primeiroSegundoDoDia());
        $this->fim = $this->parseDateOr($fim, date(parent::DATE_FORMAT), Tempo::ultimoSegundoDoDia());
        $this->cliente = $this->parsePositiveInt($cliente);
        $this->midia = $this->parsePositiveInt($midia);
        $this->praca = $this->parsePositiveInt($praca);
        $this->emissora = $this->parsePositiveInt($emissora);
        $this->programa = $this->parsePositiveInt($programa);
        $this->eventoStatus = $this->parsePositiveInt($eventoStatus);
    }
    public function getInicio()
    {
        return $this->inicio;
    }
    public function getFim()
    {
        return $this->fim;
    }
    public function getCliente()
    {
        return $this->cliente;
    }
    public function getMidia()
    {
        return $this->midia;
    }
    public function getPraca()
    {
        return $this->praca;
    }
    public function getEmissora()
    {
        return $this->emissora;
    }
    public function getPrograma()
    {
        return $this->programa;
    }

    public function getEventoStatus() {
        return $this->eventoStatus;
    }
}
