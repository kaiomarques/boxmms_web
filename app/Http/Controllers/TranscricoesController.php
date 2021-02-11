<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Cqrs\Transcricoes\Queries\Application\TranscricoesQueriesHandler;
use App\Cqrs\Transcricoes\Queries\Application\ListaVinculosQuery;
use App\Cqrs\Transcricoes\Queries\Infra\TranscricoesRepository;

class TranscricoesController extends Controller
{
    private const INTEGRACAO_CONNECTION_ID = "mysql_midiaclip";
    private const CONNECTION_ID = "mysql";
    
    private $queriesHandler;
    
    public function __construct() {
        $this->queriesHandler = $this->criaQueriesHandler();
    }

    protected function criaQueriesHandler()
    {
        return new TranscricoesQueriesHandler(new TranscricoesRepository(self::CONNECTION_ID, \App\Http\Dao\ConfigDao::getSchemaMidiaClip()));
    }
    
    public function vinculos(Request $request) 
    {
        $ids = explode(",", $request->input("filesIds"));
        $vinculos = $this->queriesHandler->listaVinculos(new ListaVinculosQuery($ids));

        return $this->sendResponse(array("data" => $vinculos->items() , "qtde" => $vinculos->count()));
    }
}