<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;

use App\Mms\Infra\Edicoes\EdicoesDAO;
use App\Mms\Application\Edicoes\EdicoesService;
use App\Mms\Application\Edicoes\Queries\ListarEdicoesQuery;

class EdicoesController extends Controller
{
    private $edicoesService;

    public function __construct()
    {
        $this->edicoesService = $this->criaEdicoesService();
    }

    protected function criaEdicoesService()
    {
        return new EdicoesService(new EdicoesDAO("mysql", config("app.DB_MIDIACLIP")));
    }

    public function index(Request $request)
    {
        $query = new ListarEdicoesQuery(                
            $request->input("dt_inicio"),
            $request->input("dt_fim"),
            $request->input("id_cliente"),
            $request->input("veiculo_id"),
            $request->input("id_praca"),
            $request->input("id_emissora"),
            $request->input("id_programa"),
            $request->input("id_evento_status")
        );

        $edicoes = $this->edicoesService->handleListarEdicoesQuery($query);

        return $this->sendResponse(array(
            "data" => $edicoes->items(), 
            "dt_inicio"=> $query->getInicio()->format("Y-m-d"), 
            "dt_fim" => $query->getFim()->format("Y-m-d")));
    }
}