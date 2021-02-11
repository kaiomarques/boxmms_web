<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Cqrs\Clientes\Queries\Application\ListaClientesQuery;
use App\Cqrs\Clientes\Queries\Application\ClientesQueriesHandler;
use App\Cqrs\Clientes\Queries\Infra\ClientesRepository;

use Illuminate\Http\Request;
use App\ClienteConfiguracao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;

class ClientesController extends Controller
{
    private const INTEGRACAO_CONNECTION_ID = "mysql_midiaclip";

    private $queriesHandler;

    public function __construct() 
    {
        $this->queriesHandler = $this->criaQueriesHandler();
    }

    protected function criaQueriesHandler() 
    {
        return new ClientesQueriesHandler($this->criaClientesRepository());
    }

    protected function criaClientesRepository()
    {
        return new ClientesRepository(self::INTEGRACAO_CONNECTION_ID);
    }

    public function index(Request $request)
    {
        $result = $this->queriesHandler->lista(new ListaClientesQuery($request->input("filtro")));

        return $this->sendResponse(array("data"=> $result->items()));
    }
}

