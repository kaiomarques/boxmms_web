<?php namespace App\Mms\Infra\Materias;

use Illuminate\Support\Facades\DB;
use App\Mms\Domain\Materias\IMateriasDAO;
use App\Mms\Domain\Materias\Materia;
use App\Core\Strings\StringBuilder;

class MateriasDAO implements IMateriasDAO
{
    private const ATIVO = 1;
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

    private function template($colunas) 
    {
        return (new StringBuilder())
            ->appendLine("SELECT ")
            ->append($colunas)
            ->appendLine("    FROM `materia_rascunho` `p` ")
            ->appendLine("        LEFT JOIN `eventos` AS `ev` ")
            ->appendLine("            ON `ev`.`id` = `p`.`id_projeto` ")
            ->appendLine("            AND `ev`.`tipo` = 'filho' ")
            ->appendLine("        INNER JOIN {$this->integracaoDb}.`programa` AS `pro` ")
            ->appendLine("            ON `pro`.`id` = `p`.`id_programa` ")
            ->appendLine("            AND `pro`.`ativo` = :programa_ativo ")
            ->appendLine("            AND (:programa_id_null IS NULL OR `pro`.`id` = :programa_id) ")
            ->appendLine("            AND (:programa_midia_null IS NULL OR `pro`.`id_meio_comunicacao` = :programa_midia) ")
            ->appendLine("        INNER JOIN {$this->integracaoDb}.`emissora` AS `emi` ")
            ->appendLine("            ON `emi`.`id` = `ev`.`id_emissora` ")
            ->appendLine("            AND `emi`.`ativo` = :emissora_ativa ")
            ->appendLine("            AND (:emissora_id_null IS NULL OR `emi`.`id` = :emissora_id) ")
            ->appendLine("            AND (:emissora_midia_null IS NULL OR `emi`.`id_veiculo` = :emissora_midia) ")
            ->appendLine("        INNER JOIN {$this->integracaoDb}.`usuario` AS `us` ")
            ->appendLine("            ON `us`.`id` = `p`.`id_operador` ")
            ->appendLine("        WHERE (:cliente_null IS NULL OR `p`.`cliente_list` LIKE :cliente) ")
            ->appendLine("            AND `p`.`status` = :status ")
            ->appendLine("            AND (:inicio_null IS NULL OR `p`.`data` >= :inicio) ")
            ->appendLine("            AND (:fim_null IS NULL OR `p`.`data` <= :fim) ")
            ->toString();
    }

    private function countQuery() 
    {
        return $this->template("    COUNT(`p`.`id`) as `total`");
    }

    public function count($inicio, $fim, $cliente, $midia, $status, $emissora, $programa)
    {
        $result =  DB::connection($this->connectionId)
            ->select($this->countQuery(), [
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
                "cliente_null" => "%{$cliente}%",
                "cliente" => "%{$cliente}%",
                "status" => $status,
                "inicio_null" => $inicio,
                "inicio" => $inicio,
                "fim_null" => $fim,
                "fim" => $fim
            ]);

        return $result[0]->total;
    }

    private function listaQuery($orderBy, $orderByDirection, $limit, $offset) 
    {
        $colunas = (new StringBuilder())
            ->appendLine("    `p`.`id`                          AS `id`, ")
            ->appendLine("    `p`.`id_projeto`                  AS `id_projeto`, ")
            ->appendLine("    `p`.`data`                        AS `data`, ")
            ->appendLine("    `p`.`titulo`                      AS `titulo`, ")
            ->appendLine("    `p`.`id_operador`                 AS `id_operador`, ")
            ->appendLine("    `p`.`data_cadastro`               AS `data_cadastro`, ")
            ->appendLine("    `p`.`status`                      AS `status`, ")
            ->appendLine("    `p`.`id_materia_radiotv_jornal`   AS `id_materia_radiotv_jornal`, ")
            ->appendLine("    `p`.`cliente_list`                AS `cliente_list`, ")
            ->appendLine("    `p`.`id_programa`                 AS `id_programa`, ")
            ->appendLine("    `p`.`dia`                         AS `dia`, ")
            ->appendLine("    `pro`.`nome`                      AS `programa_nome`, ")
            ->appendLine("    `emi`.`nome`                      AS `emissora_nome`, ")
            ->appendLine("    `us`.`nome`                       AS `nome_operador` ")
            ->toString();

        return (new StringBuilder())
            ->append($this->template($colunas))	
            ->appendLine("        ORDER BY {$orderBy} {$orderByDirection} ")
            ->appendLine("        LIMIT {$limit} OFFSET {$offset} ")
            ->toString();
    }

    public function lista($inicio, $fim, $cliente, $midia, $status, $emissora, $programa, $orderBy, $orderByDirection, $limit, $offset)
    {
        $result =  DB::connection($this->connectionId)
            ->select($this->listaQuery($orderBy, $orderByDirection, $limit, $offset), [
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
                "cliente_null" => "%{$cliente}%",
                "cliente" => "%{$cliente}%",
                "status" => $status,
                "inicio_null" => $inicio,
                "inicio" => $inicio,
                "fim_null" => $fim,
                "fim" => $fim
            ]);

        $materias = array();
        
        foreach($result as $item)
            array_push($materias, new Materia(
                $item->id,
                $item->id_projeto,
                $item->data,
                $item->titulo,
                $item->id_operador,
                $item->nome_operador,
                $item->data_cadastro,
                $item->status,
                $item->id_materia_radiotv_jornal,
                $item->cliente_list,
                $item->id_programa,
                $item->dia,
                $item->programa_nome,
                $item->emissora_nome));            

        return $materias;
    }
}
