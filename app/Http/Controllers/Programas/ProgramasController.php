<?php
namespace App\Http\Controllers\Programas;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Cqrs\Queries\Application\Programas\ListarProgramasQuery;
use App\Cqrs\Queries\Infra\Programas\ProgramasRepository;
use App\Cqrs\Queries\Application\Programas\ProgramasQueriesHandler;

class ProgramasController extends Controller
{
    private $queriesHandler;

    public function __construct() {
        $this->queriesHandler = $this->createQueriesHandler();
    }

    protected function createRepository()
    {
        return new ProgramasRepository("mysql_midiaclip");
    }

    protected function createQueriesHandler()
    {
        return new ProgramasQueriesHandler($this->createRepository());
    }

    protected function createListarProgramasQuery(Request $request)
    {
        //var_dump($request->input());die;
        return new ListarProgramasQuery(
            $request->input("veiculo_id"), 
            $request->input("praca_id"), 
            $request->input("id_emissora"));
    }

    public function index(Request $request)
    {

        $query = $this->createListarProgramasQuery($request);

        $result = $this->queriesHandler->handle($query);

        return $this->sendResponse(array("data"=>$result));
    }
}

