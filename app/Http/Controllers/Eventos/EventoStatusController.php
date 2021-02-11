<?php namespace App\Http\Controllers\Eventos;

use App\Cqrs\Eventos\Queries\Application\ListaEventoStatusQuery;
use App\Cqrs\Eventos\Queries\Application\EventoStatusQueriesHandler;
use App\Cqrs\Eventos\Queries\Infra\EventoStatusRepository;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\EventosStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use DateTime;

class EventoStatusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */

    private $queriesHandler;

    private const INTEGRACAO_CONNECTION_ID = "mysql_midiaclip";

    public function __construct() 
    {
        $this->queriesHandler = $this->criaQueriesHandler();
    }


    protected function criaQueriesHandler() 
    {
        return new EventoStatusQueriesHandler($this->criaEventoStatusRepository());
    }

    public function listarTodosStatus()
    {
        $result = $this->queriesHandler->lista(new ListaEventoStatusQuery());

        return $this->sendResponse(array("data"=> $result->items()));
    }
  
    public function getStatusById(Request $request)
    {
        $header = $request->header();
        $id = $request->input("id");
        $eventoStatus = \App\EventoStatus::find($id);

        $saida = array(
            "qtde"=> count($eventoStatus),
            "data" => $eventoStatus
        );
                         
        return $saida;
    }

    protected function criaEventoStatusRepository()
    {
        return new EventoStatusRepository(self::INTEGRACAO_CONNECTION_ID);
    }

}
