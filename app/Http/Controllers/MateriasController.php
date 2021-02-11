<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;

use App\Mms\Shared\DataTablesHelper;
use App\Mms\Infra\Materias\MateriasDAO;
use App\Mms\Application\Materias\MateriasService;
use App\Mms\Application\Materias\Queries\ListarMateriasQuery;

class MateriasController extends Controller
{
    private $materiasService;

    public function __construct()
    {
        $this->materiasService = $this->criaMateriasService();
    }

    protected function criaMateriasService()
    {
        return new MateriasService(new MateriasDAO("mysql", config("app.DB_MIDIACLIP")));
    }

    public function index(Request $request)
    {
        $helper = new DataTablesHelper($request);
        
        $query = new ListarMateriasQuery(  
            $request->input("dt_inicio"),
            $request->input("dt_fim"),
            $request->input("veiculo_id"),
            $request->input("id_programa"),
            $request->input("id_emissora"),
            $request->input("cliente_nome"),
            $request->input("status"),
            $request->input("length"),
            $request->input("draw"),
            $request->input("start"),
            $helper->getOrderBy(),
            $helper->getOrderByDirection());

        $materias = $this->materiasService->handleListarMateriasQuery($query);

        return array(
            "qtde"=> $materias->count(),
            "total"=> $materias->getPaginacao()->getTotal(),
            "data" => $materias->items(), 
            "dt_inicio"=> $query->getInicio()->format("Y-m-d"), 
            "dt_fim" => $query->getFim()->format("Y-m-d"),
            "pagging" => array(
                "inicio"=> $materias->getPaginacao()->getInicio(), 
                "pagesize"=> $materias->getPaginacao()->getQuantidade(), 
                "fim" => $materias->getPaginacao()->getFim(), 
                "page"=> $materias->getPaginacao()->getPaginaAtual()),
            "cliente_nome" => $query->getCliente());
    }
}