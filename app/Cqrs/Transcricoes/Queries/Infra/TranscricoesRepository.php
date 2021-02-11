<?php namespace App\Cqrs\Transcricoes\Queries\Infra;

use App\Cqrs\Transcricoes\Queries\Model\ITranscricoesRepository;
use App\Cqrs\Transcricoes\Queries\Model\TranscricaoVinculo;
use Illuminate\Support\Facades\DB;
use App\Core\Strings\StringBuilder;

class TranscricoesRepository implements ITranscricoesRepository
{
    private $connectionId;
    private $integracaoDb;

    public function __construct(string $connectionId, $integracaoDb)
    {
        $this->connectionId = $connectionId;
        $this->integracaoDb = $integracaoDb;
    }

    private function listaVinculosQuery(array $ids) 
    {
        $in = implode(",", $ids);

        return (new StringBuilder())
            ->appendLine("SELECT ")
            ->appendLine("  `c`.`id` as `clienteId`, ")
            ->appendLine("  `c`.`nome` as `clienteNome`, ")
            ->appendLine("  `cc`.`id` as `topicoId`, ")
            ->appendLine("  `cc`.`nome` as `topicoNome` ")
            ->appendLine("  FROM `eventos_arquivos` AS `ea` ")
            ->appendLine("      INNER JOIN `eventos_arquivos_palavras` AS `eap` ")
            ->appendLine("          ON `ea`.`id` = `eap`.`id_evento_arquivo` ")
            ->appendLine("      INNER JOIN `{$this->integracaoDb}`.`dicionario_tags` AS `dt` ")
            ->appendLine("          ON `eap`.`id_dicionario_tag` = `dt`.`id` ")
            ->appendLine("      INNER JOIN `{$this->integracaoDb}`.`classes_cliente` AS `cc` ")
            ->appendLine("          ON `dt`.`id_registro_importado` = `cc`.`id_registro_importado` ")
            ->appendLine("      INNER JOIN `{$this->integracaoDb}`.`cliente` AS `c` ")
            ->appendLine("          ON `eap`.`id_cliente` = `c`.`id` ")
            ->appendLine("      WHERE `ea`.`id` IN ({$in}) ")
            ->appendLine("          ORDER BY c.nome ASC ")
            ->toString();
    }

    private function extraiParametros(array $ids) 
    {
        $result = array();

        foreach($ids as $id)
            array_push($result, "?");

        return $result;
    }

    public function listaVinculos(array $ids)
    {
        $result =  DB::connection($this->connectionId)->select($this->listaVinculosQuery($this->extraiParametros($ids)), $ids);

        $vinculos = array();
        
        foreach($result as $item)
            array_push($vinculos, new TranscricaoVinculo($item->clienteId, $item->clienteNome, $item->topicoId, $item->topicoNome));            

        return $vinculos;
    }
}

