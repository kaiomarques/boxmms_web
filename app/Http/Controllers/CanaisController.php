<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Cqrs\Canais\Queries\Application\CanaisQueriesHandler;
use App\Cqrs\Canais\Queries\Application\ListaCanaisQuery;
use App\Cqrs\Canais\Queries\Infra\CanaisRepository;
use Illuminate\Support\Facades\DB;


class CanaisController extends Controller
{
    private const INTEGRACAO_CONNECTION_ID = "mysql_midiaclip";
    private $queriesHandler;
    
    public function __construct() {
        $this->queriesHandler = $this->criaCanaisQueriesHandler();
    }

    protected function criaCanaisQueriesHandler()
    {
        return new CanaisQueriesHandler(new CanaisRepository(self::INTEGRACAO_CONNECTION_ID));
    
    
    }
    
    public function index($clienteId) {

        $canais = $this->queriesHandler->lista(new ListaCanaisQuery($clienteId));

        return $this->sendResponse(array("data" => $canais->items() , "qtde" => $canais->count()));
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function selectAll()
    {
        $sql = "
        SELECT 
            id, nome
        FROM boxintegra.classes_cliente";

        $itens = DB::select($sql);
                
        $saida = array(
            "qtde"=> count($itens),
            "data" => $itens, 
            "sql"=> $sql
        );
                    
        return $saida;
    }
}