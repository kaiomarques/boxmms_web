<?php
namespace App\Http\Controllers\Emissoras;

use App\Http\Requests;
use App\Http\Controllers\Controller;

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
}