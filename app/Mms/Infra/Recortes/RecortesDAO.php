<?php namespace App\Mms\Infra\Recortes;

use Illuminate\Support\Facades\DB;
use App\Mms\Domain\Recortes\IRecortesDAO;
use App\Mms\Domain\Recortes\Recorte;
use App\Core\Strings\StringBuilder;
use App\Mms\Shared\Tempo;

class RecortesDAO implements IRecortesDAO
{
    private const ATIVO = 1;
    private const TIPO_RECORTE = "cut";
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
            ->appendLine("    `ea`.`id`                         AS `recorte_id`, ")
            ->appendLine("    `ev`.`id`                         AS `edicao_id`, ")
            ->appendLine("    CONVERT( `pr`.`nome` USING utf8)  AS `programa`, ")
            ->appendLine("    `ev`.`data`                       AS `data`, ")
            ->appendLine("    `ea`.`hora_inicio`                AS `inicio`, ")
            ->appendLine("    `ea`.`tempo_realizado_minutos`    AS `tempo_realizado`, ")
            ->appendLine("    `m`.`id`                          AS `materia_id`, ")
            ->appendLine("    CONVERT(`m`.`titulo` USING utf8)  AS `materia_titulo`, ")
            ->appendLine("    `ea`.`meta_dados`                 AS `meta_dados` ")
            ->appendLine("    FROM `eventos_arquivos` AS `ea` ")
            ->appendLine("        INNER JOIN `eventos` AS `ev` ")
            ->appendLine("            ON `ev`.`id` = `ea`.`id_evento` ")
            ->appendLine("        INNER JOIN {$this->integracaoDb}.`emissora` AS `e` ")
            ->appendLine("            ON `ev`.`id_emissora` = `e`.`id` ")
            ->appendLine("            AND `e`.`ativo` = :emissora_ativa ")
            ->appendLine("            AND (:emissora_id_null IS NULL OR `e`.`id` = :emissora_id) ")
            ->appendLine("            AND (:emissora_midia_null IS NULL OR `e`.`id_veiculo` = :emissora_midia) ")
            ->appendLine("        INNER JOIN {$this->integracaoDb}.`programa` AS `pr` ")
            ->appendLine("            ON `ev`.`id_programa`= `pr`.`id` ")
            ->appendLine("            AND `pr`.`ativo` = :programa_ativo ")
            ->appendLine("            AND (:programa_id_null IS NULL OR `pr`.`id` = :programa_id) ")
            ->appendLine("            AND (:programa_midia_null IS NULL OR `pr`.`id_meio_comunicacao` = :programa_midia) ")
            ->appendLine("        LEFT JOIN {$this->integracaoDb}.`materia_radiotv_jornal` AS `m` ")
            ->appendLine("            ON `ea`.`id_materia_radiotv_jornal` = `m`.`id` ")
            ->appendLine("        LEFT JOIN `eventos_arquivos_palavras` AS `eap` ")
            ->appendLine("            ON `ea`.`id` = `eap`.`id_evento_arquivo` ")
            ->appendLine("            AND `ev`.`id`= `eap`.`id_evento` ")
            ->appendLine("            AND (:cliente_id_null IS NULL OR `eap`.`id_cliente` = :cliente_id) ")
            ->appendLine("        LEFT JOIN {$this->integracaoDb}.`dicionario_tags` AS `dt` ")
            ->appendLine("            ON `eap`.`id_dicionario_tag` = `dt`.`id` ")
            ->appendLine("            AND `dt`.`ativo` = :palavra_chave_ativa ")
            ->appendLine("        LEFT JOIN {$this->integracaoDb}.`cliente` AS `c` ")
            ->appendLine("            ON `eap`.`id_cliente` = `c`.`id` ")
            ->appendLine("            AND `c`.`ativo` = :cliente_ativo ")
            ->appendLine("        WHERE (:inicio_null IS NULL OR `ev`.`data` >= :inicio) ")
            ->appendLine("            AND (:fim_null IS NULL OR `ev`.`data` <= :fim) ")
            ->appendLine("            AND (`ea`.`tipo` = :evento_arquivo_tipo) ")
            ->appendLine("            AND (:cliente_id_null_where IS NULL OR `c`.`id` IS NOT NULL) ")
            ->appendLine("        ORDER BY `ea`.`id` DESC ")
            ->toString();
    }

    public function lista($inicio, $fim, $cliente, $midia, $emissora, $programa)
    {
        $result =  DB::connection($this->connectionId)
            ->select($this->listaQuery(), [
                "emissora_ativa" => self::ATIVO,
                "emissora_id_null" => $emissora,
                "emissora_id" => $emissora,
                "emissora_midia_null" => $midia,
                "emissora_midia" => $midia,
                "programa_ativo" => self::ATIVO,
                "programa_id_null" => $programa,
                "programa_id" => $programa,
                "programa_midia_null" => $midia,
                "programa_midia" => $midia,
                "cliente_id_null" => $cliente,
                "cliente_id" => $cliente,
                "palavra_chave_ativa" => self::ATIVO,
                "cliente_ativo" => self::ATIVO,
                "inicio_null" => $inicio,
                "inicio" => $inicio,
                "fim_null" => $fim,
                "fim" => $fim,
                "evento_arquivo_tipo" => self::TIPO_RECORTE,
                "cliente_id_null_where" => $cliente
            ]);

        $recortes = array();
        
        foreach($result as $item)
            array_push($recortes, new Recorte(
                $item->recorte_id,
                $item->edicao_id,
                $item->programa,
                $item->data,
                $item->inicio,
                $item->tempo_realizado,
                Tempo::minutos($item->tempo_realizado)->toString(),
                $item->materia_id,
                $item->materia_titulo,
                $item->meta_dados));            

        return $recortes;
    }
}