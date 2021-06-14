<?php
namespace App\Http\Controllers\Emissoras;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Cqrs\Queries\Application\Emissoras\ListarEmissorasQuery;
use App\Cqrs\Queries\Infra\Emissoras\EmissorasRepository;
use App\Cqrs\Queries\Application\Emissoras\EmissorasQueriesHandler;

class EmissorasController extends Controller
{
    private $queriesHandler;

    public function __construct() {
        $this->queriesHandler = $this->createQueriesHandler();
    }

    protected function createRepository()
    {
        return new EmissorasRepository("mysql_midiaclip");
    }

    protected function createQueriesHandler()
    {
        return new EmissorasQueriesHandler($this->createRepository());
    }

    protected function createListarEmissorasQuery(Request $request)
    {
        return new ListarEmissorasQuery(
            $request->input("veiculo_id"), 
            $request->input("praca_id"));
    }

    public function index(Request $request) 
    {
        $query = $this->createListarEmissorasQuery($request);

        $result = $this->queriesHandler->handle($query);

        return $this->sendResponse(array("data"=> $result));
    }

    public function selectByIdMidia($id_midia) {
        $sql = "
        SELECT 
            id, nome
        FROM boxintegra.emissora
        WHERE id_veiculo = {$id_midia}";

        $itens = DB::select($sql);
                
        $saida = array(
            "qtde"=> count($itens),
            "data" => $itens, 
            "sql"=> $sql
        );
                    
        return $saida;
    }

    public function selectByIdPraca($id_praca) {
        $sql = "
        SELECT 
            id, nome
        FROM boxintegra.emissora
        WHERE id_praca = {$id_praca}";

        $itens = DB::select($sql);
                
        $saida = array(
            "qtde"=> count($itens),
            "data" => $itens, 
            "sql"=> $sql
        );
                    
        return $saida;
    }
}