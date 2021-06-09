<?php namespace App\Http\Controllers;

use App\Spot;
use App\CampanhaSpot;
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
use App\Http\Dao\SpotsDao;
use DateTime;
use App\Http\Dao\Queries\QueryNotificacoesOriginal;
use App\Http\Dao\Queries\QueryNotificacoesPorPalavrasChave;
use App\Http\Dao\Queries\QueryNotificacoesUnion;


use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class SpotsController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */


    var $path;
    var $spot_path;
    var $url;

    public function __construct() {
        $this->url = "http://boxmms.boxnet.com.br/files/";
        $this->path = "/var/www/boxmms/files/";
        $this->spot_path = "spots/";
        //$this->path = "c:/var/www/boxmms/files/spots/";
    }

    public function index(Request $request)
    {
        $sql = "
        SELECT 
            s.id as id, s.nome as nome,  s.s3_path
        FROM boxmmsdb.spots s ORDER BY s.id DESC";

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
    public function select(Request $request)
    {
        $sql = "
            SELECT 	
                id, nome
            FROM boxmmsdb.spots";

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
            s.id as id, s.nome as nome, s.s3_path
        FROM boxmmsdb.spots s
            WHERE s.id = {$id}";

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
    public function update($id, Request $request)
    {
        $nome = $_POST["nome"];
        $id = isset($_POST["id"])?$_POST["id"]:null;
        $id_campanha = $_POST['id_campanha'];

        if($id == null || $id == 0) {
            $reg = new Spot;
        } else {
            $reg = Spot::find($id);
        }
        $reg->nome = $nome;

        if(isset($_FILES) && count($_FILES) > 0) {
            $s3_path = $this->path . $this->spot_path . $_FILES["file"]["name"];
            move_uploaded_file($_FILES["file"]["tmp_name"],
            $s3_path);
            $reg->s3_path = $this->url . $this->spot_path . $_FILES["file"]["name"];
        }

        $ret = $reg->save();

        $msg = "sucesso!";
        $code = 1;
        if (! $ret) {
            $code = 0;
            $msg = "erro";
        }
            
        return array("msg"=>$msg, "code" =>  $code , "success" => $ret, "results"=> $reg, "item" => $reg);
    }
}