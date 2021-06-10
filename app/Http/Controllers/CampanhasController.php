<?php namespace App\Http\Controllers;

use App\Campanha;
use App\CampanhaSpot;
use App\CampanhaSpotMailing;
use App\CampanhaSpotCliente;
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
        campanha_spot.id as id_boxnet, campanha_spot.id_spot,
        spots.nome as nome, spots.s3_path as s3_path
        FROM boxmmsdb.campanha_spot 
        INNER JOIN boxmmsdb.spots ON spots.id = campanha_spot.id_spot
        WHERE id_campanha = {$id}";

        $sql3 = "
        SELECT 
            id_emissora
        FROM boxmmsdb.campanha_spot_mailing
        WHERE id_campanha = {$id}";

        $sql4 = "
        SELECT 
            id_cliente
        FROM boxmmsdb.campanha_spot_clientes
        WHERE id_campanha = {$id}";

        $itens = DB::select($sql);
        $spotItens = DB::select($sql2);
        $emissoraItens = DB::select($sql3);
        $clienteItens = DB::select($sql4);
                
        $saida = array(
            "qtde"=> count($itens),
            "data" => $itens, 
            "spot_data" => $spotItens,
            "emissora_data" => $emissoraItens,
            "cliente_data" => $clienteItens,
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
        $reg_campanha_spot_cliente = null;

        $ids_spots = [];
        $ids_emissoras = [];
        $ids_clientes = null;

        try {
            DB::beginTransaction();
            if(isset($_POST["data_inicial"]) && $_POST["data_inicial"] != null) {
                $data_inicial = $_POST["data_inicial"];
            }
            
            if(isset($_POST["data_final"]) && $_POST["data_final"] != null) {
                $data_final = $_POST["data_final"];
            }
            
            if(count(json_decode($_POST["id_clientes"])) > 0) {
                foreach(json_decode($_POST["id_clientes"]) as $obj) {
                    $ids_clientes[] = $obj->key;
                }
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
                    foreach ($ids_clientes as $id_cliente) {
                        $reg_campanha_spot_cliente =  new CampanhaSpotCliente;
                        $reg_campanha_spot_cliente->id_cliente = $id_cliente;
                        $reg_campanha_spot_cliente->id_campanha = $reg_campanha->id;
                        $ret3 = $reg_campanha_spot_cliente->save();
                    }
                }
                $campanhaDados = $this->getById($reg_campanha->id);

                $dadosParaEnvio = array (
                    "campaign" => $campanhaDados["data"][0]->nome,
                    "start_date" => $campanhaDados["data"][0]->periodo_inicial,
                    "end_date" => $campanhaDados["data"][0]->periodo_final,
                    "jsonstring_id_broadcaster" => "[".implode("," ,$ids_emissoras)."]"
                );

                foreach($campanhaDados["spot_data"] as $spot_data) {
                    $dados = $dadosParaEnvio;
                    $dados["spot"] =  $spot_data->nome;
                    $dados["audio"] =  $spot_data->s3_path;
                    $dados["id_boxnet"] =  $spot_data->id_boxnet;

                    $this->callSpyBox($dados);
                }
            }
            DB::commit();
            $msg = "sucesso!";
            $code = 1;
        } catch (Exception $e) {
            DB::rollBack();
            $msg = "Erro ao cadastrar a campanha";
            $code = 0;
        } catch (RequestException $e) {
            if ($e->hasResponse()){
                if ($e->getResponse()->getStatusCode() == '400') {
                    echo "Got response 400";
                }
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

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    private function callSpyBox($parametros)
    {
        $url = "http://10.1.20.69/prototypeideas.spybox.api/Campaign";

        $response = new \GuzzleHttp\Client();
        $json = json_encode($parametros);
        $response->post($url, ['form_params' => $parametros]);
    }
}