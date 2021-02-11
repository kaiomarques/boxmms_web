<?php namespace App\Mms\Application\Edicoes;

use App\Mms\Application\Edicoes\Queries\ListarEdicoesQuery;
use App\Mms\Application\Edicoes\Queries\ListarEdicoesQueryResult;
use App\Mms\Domain\Edicoes\IEdicoesDAO;

class EdicoesService
{
    private $edicoesDAO;

    public function __construct(IEdicoesDAO $edicoesDAO)
    {
        $this->edicoesDAO = $edicoesDAO;
    }

    public function handleListarEdicoesQuery(ListarEdicoesQuery $query)
    {
        $edicoes = $this->edicoesDAO->lista(
            $query->getInicio(), 
            $query->getFim(), 
            $query->getCliente(), 
            $query->getMidia(), 
            $query->getPraca(), 
            $query->getEmissora(), 
            $query->getPrograma(),
            $query->getEventoStatus()
        );

        return new ListarEdicoesQueryResult($edicoes);
    }
}