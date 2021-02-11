<?php namespace App\Mms\Application\Recortes;

use App\Mms\Application\Recortes\Queries\ListarRecortesQuery;
use App\Mms\Application\Recortes\Queries\ListarRecortesQueryResult;
use App\Mms\Domain\Recortes\IRecortesDAO;

class RecortesService
{
    private $recortesDAO;

    public function __construct(IRecortesDAO $recortesDAO)
    {
        $this->recortesDAO = $recortesDAO;
    }

    public function handleListarRecortesQuery(ListarRecortesQuery $query)
    {
        $recortes = $this->recortesDAO->lista(
            $query->getInicio(), 
            $query->getFim(), 
            $query->getCliente(), 
            $query->getMidia(), 
            $query->getEmissora(), 
            $query->getPrograma());

        return new ListarRecortesQueryResult($recortes);
    }
}


