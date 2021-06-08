<?php namespace App\Http\Controllers;

use App\Campanha;
use App\CampanhaSpot;
use App\CampanhaSpotMailing;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

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

class CampanhasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $sql = "
        SELECT 	id, nome
        FROM boxmmsdb.campanhas campanhas ";

        $itens = DB::select($sql);
                
        $saida = array(
            "qtde"=> count($itens),
            "data" => $itens, 
            "sql"=> $sql
        );
                    
        return $saida;
    }


    public function getById($id) {
        $sql = "
        SELECT 
            id, nome, id_cliente, periodo_inicial, periodo_final
        FROM boxmmsdb.campanhas
            WHERE id = {$id}";

        $sql2 = "
        SELECT 
            id_spot
        FROM boxmmsdb.campanha_spot
        WHERE id_campanha = {$id}";

        $sql3 = "
        SELECT 
            id_emissora
        FROM boxmmsdb.campanha_spot_mailing
        WHERE id_campanha = {$id}";

        $itens = DB::select($sql);
        $spotItens = DB::select($sql2);
        $emissoraItens = DB::select($sql3);
                
        $saida = array(
            "qtde"=> count($itens),
            "data" => $itens, 
            "spot_data" => $spotItens,
            "emissora_data" => $emissoraItens,
            "sql"=> $sql
        );

        return $saida;
    }

        /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $ret1 = null;
        $ret2 = null;
        $ret3 = null;

        $reg_campanha = null;
        $reg_campanha_spot = null;
        $reg_campanha_spot_mailing = null;

        $ids_spots = [];
        $ids_emissoras = [];
        $id_cliente = null;

        if(isset($_POST["data_inicial"]) && $_POST["data_inicial"] != null) {
            $data_inicial = $_POST["data_inicial"];
        }
        
        if(isset($_POST["data_final"]) && $_POST["data_final"] != null) {
            $data_final = $_POST["data_final"];
        }


        if(isset($_POST["id_cliente"]) && $_POST["id_cliente"] != null) {
            $id_cliente = json_decode($_POST["id_cliente"]);
            $id_cliente = $id_cliente->key;
        }
        
        if(count(json_decode($_POST["id_spots"])) > 0) {
            foreach(json_decode($_POST["id_spots"]) as $obj) {
                $ids_spots[] = $obj->key;
            }
        }

        if (count(json_decode($_POST["id_emissoras"])) > 0) {
            foreach (json_decode($_POST["id_emissoras"]) as $obj) {
                $ids_emissoras[] = $obj->id_emissora;
            }
        }

        $nome = $_POST["nome"];

        if(isset($_POST["id"]) && $_POST["id"] != null && $_POST["id"] != 0) {
            $reg_campanha = Campanha::find($id);
            $reg_campanha_spot = new CampanhaSpot;
            $reg_campanha->nome = $nome;
            $reg_campanha->id_cliente = $id_cliente;
            $reg_campanha->periodo_inicial = $data_inicial;
            $reg_campanha->periodo_final = $data_final;
            $ret1 = $reg_campanha->save();
        } else {
            $reg_campanha = new Campanha;
            $reg_campanha->nome = $nome;
            $reg_campanha->id_cliente = $id_cliente;
            $reg_campanha->periodo_inicial = $data_inicial;
            $reg_campanha->periodo_final = $data_final;
            $ret1 = $reg_campanha->save();
            if ($ret1) {
                foreach ($ids_spots as $id_spot) {
                    $reg_campanha_spot =  new CampanhaSpot;
                    $reg_campanha_spot->id_campanha = $reg_campanha->id;
                    $reg_campanha_spot->id_spot = $id_spot;
                    $ret2 = $reg_campanha_spot->save();
                }
                foreach ($ids_emissoras as $id_emissora) {
                    $reg_campanha_spot_mailing =  new CampanhaSpotMailing;
                    $reg_campanha_spot_mailing->id_emissora = $id_emissora;
                    $reg_campanha_spot_mailing->id_campanha = $reg_campanha->id;
                    $ret3 = $reg_campanha_spot_mailing->save();
                }
            }
        }
        
        $msg = "sucesso!";
        $code = 1;
        if (!isset($ret1)) {
            $code = 0;
            $msg = "Erro ao cadastrar campanha";
        }

        if(isset($_POST["id"]) && $_POST["id"] != null && $_POST["id"] != 0) {
            if (!isset($ret2)) {
                $code = 0;
                $msg = "Erro ao cadastrar campanha-spot";
            }
            if (!isset($ret3)) {
                $code = 0;
                $msg = "Erro ao cadastrar emissora";
            }     
        }
        return array("msg"=>$msg, "code" =>  $code , "success" => [$ret1, $ret2, $ret3], "results"=> [$reg_campanha_spot, $reg_campanha_spot, $reg_campanha_spot_mailing]);
    }

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
        FROM boxmmsdb.campanhas";

        $itens = DB::select($sql);
                
        $saida = array(
            "qtde"=> count($itens),
            "data" => $itens, 
            "sql"=> $sql
        );
                    
        return $saida;
    }
}