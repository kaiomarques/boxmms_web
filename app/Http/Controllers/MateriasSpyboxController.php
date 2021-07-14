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

    var $filtros = "";
    
    public function gerarXLS(Request $request) 
    {

        $this->filtros = $this->setFiltros($request);

        Excel::create('relatorio_spybox', function($excel) {
            $excel->sheet('Sheetname', function($sheet) {
                $sql = "
                SELECT 
                            ms.id,
                            ms.titulo,
                            DATE_FORMAT(ms.data_hora_materia, '%d/%m/%Y') AS data_materia,
                            DATE_FORMAT(ms.data_hora_materia, '%H:%i:%s') AS hora_materia,
                            ms.id_boxnet,
                            e.nome as emissora_nome,
                            cb.descricao as praca_nome,
                            m.nome as midia_nome,
                            DATE_FORMAT(ms.hora_processo_inicio, '%d/%m/%Y %H:%i:%s') AS hora_processo_inicio,
                            DATE_FORMAT(ms.hora_processo_fim, '%d/%m/%Y %H:%i:%s') AS hora_processo_fim,
                            c.nome as campanha_nome,
                            s.nome as spot_nome,
                            ms.nome_arquivo as nome_arquivo
                        FROM boxintegra.materia_radiotv_spybox ms
                        INNER JOIN boxintegra.cadastro_basico cb ON cb.id = ms.id_praca
                        INNER JOIN boxintegra.emissora e ON e.id = ms.id_emissora
                        INNER JOIN boxmmsdb.campanha_spot cs ON cs.id = ms.id_boxnet
                        INNER JOIN boxmmsdb.campanhas c ON c.id = cs.id_campanha
                        INNER JOIN boxmmsdb.spots s ON s.id = cs.id_spot
                        INNER JOIN boxintegra.midias m ON m.id = e.id_veiculo
                    WHERE 1 = 1 {$this->filtros}
                ORDER BY id DESC";
        
                $itens = DB::select($sql);
        
                $dados = array();
        
                foreach($itens as $item) {
                    $dados[] = array(
                        "Id" => $item->id,
                        "Data" => $item->data_materia,
                        "Hora" => $item->hora_materia,
                        "Título" => $item->titulo,
                        "Emissora" => $item->emissora_nome,
                        "Praça" => $item->praca_nome,
                        "Mídia" => $item->midia_nome,
                        "Início do processo" => $item->hora_processo_inicio,
                        "Fim do processo" => $item->hora_processo_fim,
                        "Campanha" => $item->campanha_nome,
                        "Spot" => $item->spot_nome,
                        "Nome do arquivo" => $item->nome_arquivo
                    );
                }

                $sheet->fromArray($dados);
            });
        
        })->export('xls');
    }

    public function index(Request $request)
    {

        $sql_filtro = "
        SELECT 
            ms.id,
            DATE_FORMAT(ms.data_hora_materia, '%d/%m/%Y %H:%i:%s') AS data_hora_materia,
            ms.titulo,
            ms.id_boxnet,
            e.nome as emissora_nome,
            cb.descricao as praca_nome,
            m.id as midia_id,
            m.nome as midia_nome,
            DATE_FORMAT(ms.hora_processo_inicio, '%d/%m/%Y %H:%i:%s') AS hora_processo_inicio,
            DATE_FORMAT(ms.hora_processo_fim, '%d/%m/%Y %H:%i:%s') AS hora_processo_fim,
            c.nome as campanha_nome,
            s.nome as spot_nome
        FROM boxintegra.materia_radiotv_spybox ms
        INNER JOIN boxintegra.cadastro_basico cb ON cb.id = ms.id_praca
        INNER JOIN boxintegra.emissora e ON e.id = ms.id_emissora
        INNER JOIN boxmmsdb.campanha_spot cs ON cs.id = ms.id_boxnet
        INNER JOIN boxmmsdb.campanhas c ON c.id = cs.id_campanha
        INNER JOIN boxmmsdb.spots s ON s.id = ms.id_spot
        INNER JOIN boxintegra.midias m ON m.id = e.id_veiculo
        WHERE 1 = 1 ".$this->setFiltros($request);

        $sql_pagina = $sql_filtro.$this->setPage($request);

        $itens_total = DB::select($sql_filtro);
        $itens_paginado = DB::select($sql_pagina);
                
        $saida = array(
            "draw" => $request["draw"],
            "recordsTotal"=> count($itens_total),
            "recordsFiltered"=> count($itens_total),
            "data" => $itens_paginado, 
            "sql"=> $sql_pagina
        );
                    
        return $saida;
    }


    private function setPage($request) {
        $limit_sql = "";

        if (($request->has('length')  && $request->input('length') != "") &&
            ($request->has('start')  && $request->input('start') != "")) {
            $length = $request->input('length');
            $start = $request->input('start');

            $limit_sql = " LIMIT $start,$length";
        } else {
            $limit_sql = " LIMIT 0,10";
        }

        return $limit_sql;
        
    }

    private function setFiltros($request) {
        $filtros = "";

        if( $request->has('dtinicio')  && $request->input('dtinicio') != "") {
            $date = DateTime::createFromFormat('d/m/Y', $request->input('dtinicio'));
            $data_inicio = $date->format('Y-m-d 00:00:00');

            $filtros.= " and ms.data_hora_materia >= \"".$data_inicio."\" ";
        }

        if($request->has('dtfim')  && $request->input('dtfim') != "") {

            $date = DateTime::createFromFormat('d/m/Y', $request->input('dtfim'));
            $data_fim = $date->format('Y-m-d 23:59:59');

            $filtros.= " and ms.data_hora_materia <= \"".$data_fim."\" ";
        }

        if( $request->has('id_emissora') && $request->input('id_emissora') != -1 ) {
            $filtros.= " and e.id = \"".$request->input('id_emissora')."\" ";
        }

        if( $request->has('id_praca') && $request->input('id_praca') != -1 ) {
            $filtros.= " and ms.id_praca = \"".$request->input('id_praca')."\" ";
        }

        if( $request->has('veiculo_id') && $request->input('veiculo_id') != -1 ) {
            $filtros.= " and m.id = \"".$request->input('veiculo_id')."\" ";
        }

        if( $request->has('id_campanha') && $request->input('id_campanha') != -1  ) {
            $filtros.= " and cs.id_campanha = \"".$request->input('id_campanha')."\" ";
        }

        if( $request->has('id_spot') && $request->input('id_spot') != -1  ) {
            $filtros.= " and ms.id_spot = \"".$request->input('id_spot')."\" ";
        }

        return $filtros;
    }
}