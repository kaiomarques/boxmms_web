<?php namespace App\Mms\Application\Materias;

use App\Mms\Application\Materias\Queries\ListarMateriasQuery;
use App\Mms\Application\Materias\Queries\ListarMateriasQueryResult;
use App\Mms\Application\Materias\Queries\ListarMateriasQueryResultPaginacao;
use App\Mms\Domain\Materias\IMateriasDAO;

class MateriasService
{
    private $materiasDAO;

    public function __construct(IMateriasDAO $materiasDAO)
    {
        $this->materiasDAO = $materiasDAO;
    }

    public function handleListarMateriasQuery(ListarMateriasQuery $query)
    {
        $total = $this->materiasDAO->count(
            $query->getInicio(), 
            $query->getFim(), 
            $query->getCliente(), 
            $query->getMidia(), 
            $query->getStatus(), 
            $query->getEmissora(),
            $query->getPrograma()
        );

        $materias = $this->materiasDAO->lista(
            $query->getInicio(), 
            $query->getFim(), 
            $query->getCliente(), 
            $query->getMidia(), 
            $query->getStatus(), 
            $query->getEmissora(),
            $query->getPrograma(),
            $query->getOrderBy(), 
            $query->getOrderByDirection(), 
            $query->getLimit(), 
            $query->getOffset());

        $paginacao = new ListarMateriasQueryResultPaginacao(
            $query->getOffset(),
            $query->getLimit(),
            $query->getPaginaAtual(),
            $total);

        return new ListarMateriasQueryResult($materias, $paginacao);
    }
}