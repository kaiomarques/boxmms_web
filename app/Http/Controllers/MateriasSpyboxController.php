<?php namespace App\Http\Controllers;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Dao\ImageDao;
use DateTime;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;


class MateriasSpyboxController  extends Controller
{
    public function gerarXLS(Request $request) {



        Excel::create('relatorio_spybox', function($excel) {
            $excel->sheet('Sheetname', function($sheet) {
                $sql = "
                SELECT 
                        ms.id,
                        ms.titulo,
                        ms.data_hora_materia,
                        ms.id_boxnet,
                        e.nome as emissora_nome,
                        cb.descricao as praca_nome,
                        ms.hora_processo_inicio,
                        ms.hora_processo_fim,
                        c.nome as campanha_nome,
                        s.nome as spot_nome
                    FROM boxintegra.materia_radiotv_spybox ms
                    INNER JOIN boxintegra.cadastro_basico cb ON cb.id = ms.id_praca
                    INNER JOIN boxintegra.emissora e ON e.id = ms.id_emissora
                    INNER JOIN boxmmsdb.campanha_spot cs ON cs.id = ms.id_boxnet
                    INNER JOIN boxmmsdb.campanhas c ON c.id = cs.id_campanha
                    INNER JOIN boxmmsdb.spots s ON s.id = cs.id_spot
                ORDER BY id DESC";
        
                $itens = DB::select($sql);
        
                $dados = array();
        
                foreach($itens as $item) {
                    $dados[] = array(
                        $item->id,
                        $item->data_hora_materia,
                        $item->titulo,
                        $item->emissora_nome,
                        $item->praca_nome,
                        $item->hora_processo_inicio,
                        $item->hora_processo_fim,
                        $item->campanha_nome,
                        $item->spot_nome
                    );
                }

                $sheet->fromArray($dados);
            });
        
        })->export('xls');
    }

    public function index(Request $request)
    {
        $sql = "
        SELECT 
            ms.id,
            ms.data_hora_materia,
            ms.titulo,
            ms.id_boxnet,
            e.nome as emissora_nome,
            cb.descricao as praca_nome,
            ms.hora_processo_inicio,
            ms.hora_processo_fim,
            c.nome as campanha_nome,
            s.nome as spot_nome
        FROM boxintegra.materia_radiotv_spybox ms
        INNER JOIN boxintegra.cadastro_basico cb ON cb.id = ms.id_praca
        INNER JOIN boxintegra.emissora e ON e.id = ms.id_emissora
        INNER JOIN boxmmsdb.campanha_spot cs ON cs.id = ms.id_boxnet
        INNER JOIN boxmmsdb.campanhas c ON c.id = cs.id_campanha
        INNER JOIN boxmmsdb.spots s ON s.id = cs.id_spot";

        $itens = DB::select($sql);
                
        $saida = array(
            "qtde"=> count($itens),
            "data" => $itens, 
            "sql"=> $sql
        );
                    
        return $saida;
    }
}