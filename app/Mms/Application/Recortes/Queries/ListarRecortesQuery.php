<?php namespace App\Mms\Application\Recortes\Queries;

use App\Mms\Application\Shared\Queries\Query;
use App\Mms\Shared\Tempo;

class ListarRecortesQuery extends Query
{    
    private $inicio;
    private $fim;
    private $cliente;
    private $midia;
    private $emissora;
    private $programa;

    public function __construct(    
        $inicio,
        $fim,
        $cliente,
        $midia,
        $emissora,
        $programa)
    {
        $this->inicio = $this->parseDateOr($inicio, $this->defaultDate(), Tempo::primeiroSegundoDoDia());
        $this->fim = $this->parseDateOr($fim, date(parent::DATE_FORMAT), Tempo::ultimoSegundoDoDia());
        $this->cliente = $this->parsePositiveInt($cliente);
        $this->midia = $this->parsePositiveInt($midia);
        $this->emissora = $this->parsePositiveInt($emissora);
        $this->programa = $this->parsePositiveInt($programa);
    }

    private function defaultDate()
    {
        return (new \DateTime(date(parent::DATE_FORMAT)))
            ->modify("-7 days")
            ->format(parent::DATE_FORMAT);
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
    public function getEmissora()
    {
        return $this->emissora;
    }
    public function getPrograma()
    {
        return $this->programa;
    }
}