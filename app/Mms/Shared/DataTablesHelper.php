<?php namespace App\Mms\Shared;

use Illuminate\Http\Request;

class DataTablesHelper
{
    private $orderBy;
    private $orderByDirection;

    public function __construct(Request $request)
    {
        $this->orderBy = null;
        $this->orderByDirection = null;        
        $this->setaValores($request);
    }
    private function setaValores(Request $request)
    {
        if (!$this->requestValida($request)) {
            return;
        }

        $parameteres = (object)$request->all();
        $order = $request->input("order");    
        $colunas = $parameteres->columns;
        $coluna =  $order[0]["column"];
        $this->orderBy = $colunas[$coluna]["data"];
        $this->orderByDirection  = $order[0]["dir"] ;
    }
    private function requestValida(Request $request)
    {
        $parameteres = (object)$request->all();
        $order = $request->input("order");

        return
            !is_null($parameteres) &&
            property_exists($parameteres, "columns") &&
            is_array($order) &&
            count($order) > 0;
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
