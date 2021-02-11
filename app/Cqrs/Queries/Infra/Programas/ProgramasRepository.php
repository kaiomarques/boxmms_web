<?php
namespace App\Cqrs\Queries\Infra\Programas;

use App\Cqrs\Queries\Model\Programas\IProgramasRepository;
use App\Cqrs\Queries\Model\Programas\Programa;
use Illuminate\Support\Facades\DB;
use App\Core\Strings\StringBuilder;

class ProgramasRepository implements IProgramasRepository
{
    private $connectionId;
    private const CLASSIFICACAO = "programaxcanal_comunicacao";
    private const TABELA_FILHO = "emissora";
    private const TABELA_PAI = "programa";

    public function __construct(string $connectionId)
    {
        $this->connectionId = $connectionId;
    }

    private function listarQuery() 
    {
        return (new StringBuilder())
            ->appendLine("SELECT DISTINCT ")
            ->appendLine("    `p` .`id`, ")
            ->appendLine("    convert(`p`.nome using utf8) as nome ")
            ->appendLine("    FROM `programa` AS `p` ")
            ->appendLine("        INNER JOIN `associacao_cadastros` AS `ac` ")
            ->appendLine("            ON `p`.`id` = `ac`.`id_pai` ")
            ->appendLine("                AND `ac`.`classificacao` = ? ")
            ->appendLine("                AND `ac`.`tabela_filho` = ? ")
            ->appendLine("                AND `ac`.`tabela_pai` = ? ")
            ->appendLine("        INNER JOIN `emissora` AS `e` ")
            ->appendLine("            ON `ac`.`id_filho` = `e`.`id` ")
            ->appendLine("                AND `p`.`id_emissora` = `e`.`id` ")
            ->appendLine("        WHERE (? IS NULL OR `e`.`id` = ?) ")
            ->appendLine("            AND (? IS NULL OR `p`.`id_meio_comunicacao` = ?) ")
            ->appendLine("            AND (? IS NULL OR `e`.`id_praca` = ?) ")
            ->appendLine("        ORDER BY convert(`p`.nome using utf8) ")
            ->toString();
    }

    public function listaPor($veiculoId, $pracaId, $emissoraId)
    {
        $result =  DB::connection($this->connectionId)
            ->select($this->listarQuery(), [self::CLASSIFICACAO, self::TABELA_FILHO, self::TABELA_PAI, $emissoraId, $emissoraId, $veiculoId, $veiculoId, $pracaId, $pracaId]);

        $programas = array();
        
        foreach($result as $item)
            array_push($programas, new Programa($item->id, $item->nome));            

        return $programas;
    }
}