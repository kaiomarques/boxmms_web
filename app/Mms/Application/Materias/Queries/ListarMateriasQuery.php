<?php namespace App\Mms\Application\Materias\Queries;

use App\Mms\Application\Shared\Queries\Query;
use App\Mms\Shared\Tempo;

class ListarMateriasQuery extends Query
{
    private const DEFAULT_ORDER_BY = "id";
    private const DEFAULT_ORDER_BY_DIRECTION = "ASC";
    private $inicio;
    private $fim;
    private $midia;
    private $programa;
    private $emissora;
    private $cliente;
    private $status;
    private $limit;
    private $paginaAtual;
    private $offset;
    private $orderBy;
    private $orderByDirection;

    public function __construct(
        $inicio,
        $fim,
        $midia,
        $programa,
        $emissora,
        $cliente,
        $status,
        $limit,
        $paginaAtual,
        $offset,
        $orderBy,
        $orderByDirection)
    {
        $this->inicio = $this->parseDateOr($inicio, $this->defaultDate(), Tempo::primeiroSegundoDoDia());
        $this->fim = $this->parseDateOr($fim, date(parent::DATE_FORMAT), Tempo::ultimoSegundoDoDia());
        $this->midia = $this->parsePositiveInt($midia);
        $this->programa = $this->parsePositiveInt($programa);
        $this->emissora = $this->parsePositiveInt($emissora);
        $this->cliente = $this->parseString($cliente);
        $this->status = $this->parseNonNegativeInt($status);
        $this->limit = $this->parseNonNegativeInt($limit);
        $this->paginaAtual = $this->parseNonNegativeInt($paginaAtual);
        $this->offset = $this->parseNonNegativeInt($offset);
        $this->orderBy = $this->parseStringOr($orderBy, self::DEFAULT_ORDER_BY);
        $this->orderByDirection = $this->parseStringOr($orderByDirection, self::DEFAULT_ORDER_BY_DIRECTION);        
    }
    private function defaultDate()
    {
        return (new \DateTime(date(parent::DATE_FORMAT)))
            ->modify("-10 days")
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
    public function getMidia()
    {
        return $this->midia;
    }
    public function getPrograma()
    {
        return $this->programa;
    }
    public function getEmissora()
    {
        return $this->emissora;
    }
    public function getCliente()
    {
        return $this->cliente;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function getPaginaAtual()
    {
        return $this->paginaAtual;
    }
    public function getOffset()
    {
        return $this->offset;
    }
    public function getOrderBy()
    {
        return $this->orderBy;
    }
    public function getOrderByDirection()
    {
        return $this->orderByDirection;
    }
}
