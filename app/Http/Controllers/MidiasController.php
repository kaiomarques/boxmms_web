<?php namespace App\Http\Controllers;

use App\Midia;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\AgrupamentoNotificacoes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Http\Dao\PostsDao;
use DateTime;
use App\Http\Dao\Queries\QueryNotificacoesOriginal;
use App\Http\Dao\Queries\QueryNotificacoesPorPalavrasChave;
use App\Http\Dao\Queries\QueryNotificacoesUnion;

class MidiasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function select()
    {
        $sql = "
        SELECT 
            id, nome
        FROM boxintegra.midias";

        $itens = DB::select($sql);
                
        $saida = array(
            "qtde"=> count($itens),
            "data" => $itens, 
            "sql"=> $sql
        );
                    
        return $saida;
    }

}