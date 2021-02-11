<?php
namespace App\Http\Controllers\Pracas;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

use App\Cqrs\Queries\Infra\Pracas\PracasRepository;
use App\Cqrs\Queries\Application\Pracas\ListarPracasQuery;
use App\Cqrs\Queries\Application\Pracas\PracasQueriesHandler;

class PracasController extends Controller
{
    private $queriesHandler;

    public function __construct() {
        $this->queriesHandler = $this->createQueriesHandler();
    }

    protected function createRepository()
    {
        return new PracasRepository("mysql_midiaclip");
    }

    protected function createQueriesHandler()
    {
        return new PracasQueriesHandler($this->createRepository());
    }

    protected function createListarPracasQuery(Request $request)
    {
        return new ListarPracasQuery();
    }

    public function index(Request $request)
    {
        $query = $this->createListarPracasQuery($request);

        $result = $this->queriesHandler->handle($query);

		return array(
			"qtde"=> count($result),
			"data" => $result);
    }
}