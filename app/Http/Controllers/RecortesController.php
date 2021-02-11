<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;

use App\Mms\Infra\Recortes\RecortesDAO;
use App\Mms\Application\Recortes\RecortesService;
use App\Mms\Application\Recortes\Queries\ListarRecortesQuery;

class RecortesController extends Controller
{
    private $recortesService;

    public function __construct()
    {
        $this->recortesService = $this->criaRecortesService();
    }

    protected function criaRecortesService()
    {
        return new RecortesService(new RecortesDAO("mysql", config("app.DB_MIDIACLIP")));
    }

    public function index(Request $request)
    {
        $query = new ListarRecortesQuery(                
            $request->input("dt_inicio"),
            $request->input("dt_fim"),
            $request->input("id_cliente"),
            $request->input("veiculo_id"),
            $request->input("id_emissora"),
            $request->input("id_programa"));

        $recortes = $this->recortesService->handleListarRecortesQuery($query);

        return $this->sendResponse(array(
            "data" => $recortes->items(), 
            "qtde" => $recortes->count(),
            "dt_inicio"=> $query->getInicio()->format("Y-m-d"), 
            "dt_fim" => $query->getFim()->format("Y-m-d")));
    }
}