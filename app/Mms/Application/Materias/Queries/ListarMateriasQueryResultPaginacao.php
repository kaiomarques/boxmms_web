<?php namespace App\Mms\Application\Materias\Queries;

class ListarMateriasQueryResultPaginacao
{
    private $inicio;
    private $quantidade;
    private $paginaAtual;
    private $total;

    public function __construct(
        $inicio,
        $quantidade,
        $paginaAtual,
        $total)
    {
        $this->inicio = $inicio;
        $this->quantidade = $quantidade;
        $this->paginaAtual = $paginaAtual;
        $this->total = $total;
    }

    public function getInicio()
    {
        return $this->inicio > $this->total ? 0 : $this->inicio;
    }
    public function getFim()
    {
        return $this->getInicio() + $this->quantidade;
    }
    public function getPaginaAtual()
    {
        return $this->inicio > $this->total ? 1 : $this->paginaAtual;
    }
    public function getQuantidade()
    {
        return $this->quantidade;        
    }
    public function getTotal()
    {
        return $this->total;
    }
}