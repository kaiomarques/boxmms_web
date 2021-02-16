<?php namespace App\Mms\Infra\Edicoes;

use Illuminate\Support\Facades\DB;
use App\Mms\Domain\Edicoes\IEdicoesDAO;
use App\Mms\Domain\Edicoes\Edicao;
use App\Core\Strings\StringBuilder;
use App\Mms\Shared\Tempo;

class EdicoesDAO implements IEdicoesDAO
{
    private const ATIVO = 1;
    private const TIPO_PRACA = 4;
    private const TIPO_PAI = "pai";
    private const DECORATOR = "`";
    private $connectionId;
    private $integracaoDb;

    public function __construct(string $connectionId, string $integracaoDb)
    {
        $this->connectionId = $connectionId;
        $this->integracaoDb = (new StringBuilder())
            ->append(self::DECORATOR)
            ->append(trim(trim($integracaoDb), self::DECORATOR))
            ->append(self::DECORATOR)
            ->toString();
    }

    private function listaQuery() 
    {
        return (new StringBuilder())
            ->appendLine("SELECT DISTINCT ")
            ->appendLine("    `ev`.`id`                                                 as `id`, ")
            ->appendLine("    `ev`.`data`                                               as `data`, ")
            ->appendLine("    ifNull(`pr`.`nome`, 'PROGRAMA EXCLUÍDO')                  as `programa`, ")
            ->appendLine("    `em`.`nome`                                               as `emissora`, ")
            ->appendLine("    ''                                                        as `form_pai`, ")
            ->appendLine("    `pr`.`transcricao_prioridade`                             as `prioridade`, ")
            ->appendLine("    `ev`.`duracao`                                            as `duracao`, ")
            ->appendLine("    `ev`.`tempo_realizado_minutos`                            as `tempo_realizado_minutos`, ")
            ->appendLine("    case ")
            ->appendLine("        when `pr`.`transcricao_prioridade`  = 'Alta'      then 3 ")
            ->appendLine("        when `pr`.`transcricao_prioridade`  = 'Normal'    then 2 ")
            ->appendLine("        else                                                   1 ")
            ->appendLine("    end                                                       as `prioridade_int`, ")
            ->appendLine("    `ev`.`dia`                                                as `dia`, ")
            ->appendLine("    `ev`.`hora_inicio`                                        as `hora_inicio`, ")
            ->appendLine("    `ev`.`hora_fim`                                           as `hora_fim`, ")
            ->appendLine("    `ev`.`tempo_realizado_minutos`                            as `tempo_realizado`, ")
            ->appendLine("    `ev`.`tempo_total_minutos`                                as `tempo_total`, ")
            ->appendLine("    `ev`.`status`                                             as `evento_status`, ")
            ->appendLine("    `em`.`transcricao_url`                                    as `path`, ")
            ->appendLine("    `em`.`transcricao_url2`                                   as `alt_path`, ")
            ->appendLine("    ''                                                        as `blnk`, ")
            ->appendLine("    ''                                                        as `tempo_h` ")
            ->appendLine("    FROM `eventos` AS `ev` ")
            ->appendLine("        INNER JOIN {$this->integracaoDb}.`programa` AS `pr` ")
            ->appendLine("            ON `pr`.`id` = `ev`.`id_programa` ")
            ->appendLine("                AND `pr`.`ativo` = :programa_ativo ")
            ->appendLine("                AND (:programa_id_null IS NULL OR `pr`.`id` = :programa_id) ")
            ->appendLine("                AND (:programa_midia_null IS NULL OR `pr`.`id_meio_comunicacao` = :programa_midia) ")
            ->appendLine("        INNER JOIN {$this->integracaoDb}.`emissora` AS `em` ")
            ->appendLine("            ON `ev`.`id_emissora` = `em`.`id` ")
            ->appendLine("                AND `pr`.`id_emissora` = `em`.`id` ")
            ->appendLine("                AND `em`.`ativo` = :emissora_ativa ")
            ->appendLine("                AND (:emissora_id_null IS NULL OR `em`.`id` = :emissora_id) ")
            ->appendLine("                AND (:emissora_midia_null IS NULL OR `em`.`id_veiculo` = :emissora_midia) ")
            ->appendLine("        INNER JOIN {$this->integracaoDb}.`cadastro_basico` AS `cb` ")
            ->appendLine("            ON `em`.`id_praca` = `cb`.`id` ")
            ->appendLine("                AND `cb`.`ativo` = :praca_ativa ")
            ->appendLine("                AND `cb`.`tipo_cadastro_basico` = :cadastro_tipo ")
            ->appendLine("                AND (:praca_id_null IS NULL OR `cb`.`id` = :praca_id) ")
            ->appendLine("        LEFT JOIN `eventos_arquivos` AS `ea` ")
            ->appendLine("            ON `ev`.`id` = `ea`.`id_evento` ")
            ->appendLine("        LEFT JOIN `eventos_arquivos_palavras` AS `eap` ")
            ->appendLine("            ON `ea`.`id` = `eap`.`id_evento_arquivo` ")
            ->appendLine("                AND `ev`.`id` = `eap`.`id_evento` ")
            ->appendLine("        LEFT JOIN {$this->integracaoDb}.`cliente` AS `c` ")
            ->appendLine("            ON `eap`.`id_cliente` = `c`.`id` ")
            ->appendLine("                AND `c`.`ativo` = :cliente_ativo ")
            ->appendLine("                AND (:cliente_id_null IS NULL OR `c`.`id` = :cliente_id) ")
            ->appendLine("        WHERE IFNULL(`ev`.`tipo`, 'tipo') = :evento_tipo ")
            ->appendLine("            AND (:inicio_null IS NULL OR `ev`.`data` >= :inicio) ")
            ->appendLine("            AND (:fim_null IS NULL OR `ev`.`data` <= :fim)")
            ->appendLine("            AND (:evento_status_null IS NULL OR `ev`.`status` <= :evento_status)")                    
            ->appendLine("            AND (:cliente_id_null_where IS NULL OR `c`.`id` IS NOT NULL) ")
            ->appendLine("        ORDER BY `ev`.`id` DESC; ")
            ->toString();
    }
    public function lista($inicio, $fim, $cliente, $midia, $praca, $emissora, $programa, $eventoStatus)
    {
        $result =  DB::connection($this->connectionId)
            ->select($this->listaQuery(), [
                "programa_ativo" => self::ATIVO,
                "programa_id_null" => $programa,
                "programa_id" => $programa,
                "programa_midia_null" => $midia,
                "programa_midia" => $midia,
                "emissora_ativa" => self::ATIVO,
                "emissora_id_null" => $emissora,
                "emissora_id" => $emissora,
                "emissora_midia_null" => $midia,
                "emissora_midia" => $midia,
                "praca_ativa" => self::ATIVO,
                "cadastro_tipo" => self::TIPO_PRACA,
                "praca_id_null" => $praca,
                "praca_id" => $praca,
                "evento_status" => $eventoStatus,
                "evento_status_null" => $eventoStatus,
                "cliente_ativo" => self::ATIVO,
                "cliente_id_null" => $cliente,
                "cliente_id" => $cliente,
                "evento_tipo" => self::TIPO_PAI,
                "inicio_null" => $inicio,
                "inicio" => $inicio,
                "fim_null" => $fim,
                "fim" => $fim,
                "cliente_id_null_where" => $cliente
            ]);

        $edicoes = array();
        
        foreach($result as $item)
            array_push($edicoes, new Edicao(
                $item->id,
                $item->data,
                $item->programa,
                $item->emissora,
                $item->prioridade,
                $item->prioridade_int,
                $item->duracao,
                $item->dia,
                $item->hora_inicio,
                $item->hora_fim,
                $item->tempo_realizado,
                $item->tempo_realizado_minutos,
                $item->tempo_total,
                Tempo::minutos($item->tempo_realizado_minutos)->toString(),
                $item->path,
                $item->evento_status));            

        return $edicoes;
    }
}