<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use App\Cqrs\Queries\Application\Emissoras\ListarEmissorasQuery;
use App\Cqrs\Queries\Infra\Emissoras\EmissorasRepository;
use App\Cqrs\Queries\Application\Emissoras\EmissorasQueriesHandler;
use App\Cqrs\Queries\Application\Programas\ListarProgramasQuery;
use App\Cqrs\Queries\Infra\Programas\ProgramasRepository;
use App\Cqrs\Queries\Application\Programas\ProgramasQueriesHandler;

class TranscricaoController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
    }

    public function getServers(Request $request)
    {       
            //collate utf8_general_ci
        $sql = "select id, convert(nome using utf8) as nome , convert(url using utf8) as url , '' as ip,  licencas as max_requests  from servidor_transcricao ";
            
        $lista = DB::connection("mysql_midiaclip")->select($sql);
            
        for ($i= 0; $i < count($lista); $i++) {
            $item = &$lista[$i];
                
            $arp = explode("/", $item->url);
                
            $item->ip = \App\Http\Service\UtilService::left($arp[2], strpos($arp[2], ":"));
        }
            
            
        return $this->sendResponse($lista);
    }
        
        
    public function getClientes(Request $request)
    {
        $DB_MIDIACLIP = config('app.DB_MIDIACLIP', 'mdclipweb');

        if ($request->input("filesIds")) {
            $clientesSql = "SELECT "
                ."  `c`.`id` as `clienteId`, "
                ."  `c`.`nome` as `clienteNome`, "
                ."  `cc`.`id` as `topicoId`, "
                ."  `cc`.`nome` as `topicoNome` "
                ."  FROM `eventos_arquivos` AS `ea` "
                ."      INNER JOIN `eventos_arquivos_palavras` AS `eap` "
                ."          ON `ea`.`id` = `eap`.`id_evento_arquivo` "
                ."      INNER JOIN `". $DB_MIDIACLIP. "`.`dicionario_tags` AS `dt` "
                ."          ON `eap`.`id_dicionario_tag` = `dt`.`id` "
                ."      INNER JOIN `". $DB_MIDIACLIP. "`.`classes_cliente` AS `cc` "
                ."          ON `dt`.`id_registro_importado` = `cc`.`id_registro_importado` "
                ."      INNER JOIN `". $DB_MIDIACLIP. "`.`cliente` AS `c` "
                ."          ON `eap`.`id_cliente` = `c`.`id` "
                ."      WHERE `ea`.`id` IN (".$request->input("filesIds").") ORDER BY c.nome ASC";
                
            return DB::select($clientesSql);
        }

        $todos = $request->input("todos");
           
        $sql  = "select c.id, convert(c.nome using utf8) as nome from ". $DB_MIDIACLIP. ".cliente c "
                    . " where ifNull(status, 1) = 1  ";

        if (!$todos) {
            $sql  .= " and c.id in ( select distinct id_cliente from eventos_arquivos_palavras ) order by c.nome ";
        } else {
            $sql .= " order by c.nome ";
        }
            
            
        $lista = DB::select($sql);
            
        return $this->sendResponse(array("data"=>$lista));
    }

    public function getPrograms(Request $request)
    {
        $w = date("w");
        $dia = date("Ymd");
              
        $sql = "select pr.id, convert(pr.nome using utf8) as nome,  convert(emi.nome using utf8) as emissora, '' as dia, pr.hora_inicio as hora, pr.hora_fim ,
                       pr.transcricao_prioridade as prioridade, emi.transcricao_url as caminho
                       from 
                       programa pr inner join 
                       associacao_cadastros ac on ( ac.id_pai = pr.id and ac.classificacao='programaxcanal_comunicacao' and tabela_pai='programa' ) 
                       inner join emissora emi on emi.id = ac.id_filho 
                       where 1 = 1 and pr.transcricao_ativar = 1 and transcricao_dias like '%".$w."%'";
        //where 1 = 1 and pr.transcricao_ativar = 1 and transcricao_dias like '%".$w."%'";
                        
              
        // die ( $sql );
        $lista = DB::select($sql);
            
        for ($i= 0; $i < count($lista); $i++) {
            $item = &$lista[$i];
            $item->dia=$dia;
            $item->duracao = 0 ; //$dia;
                
            if ($item->hora != "" && $item->hora_fim != "") {
                $time_inicio = \App\Http\Service\UtilService::time_to_seconds($item->hora);
                $time_fim =  \App\Http\Service\UtilService::time_to_seconds($item->hora_fim);
                         
                $minutos = ($time_fim - $time_inicio) / 60 ;
                $item->duracao = $minutos;
            }
                
            $item->prioridade = strtolower($item->prioridade) ;
        }
              
        return $this->sendResponse($lista);
    }
}
