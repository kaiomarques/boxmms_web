<?php namespace App\Http\Dao;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Config;
use \App\TopicoPalavra;
use \App\TopicoPalavrasColecao;
use \App\TopicosColecao;
use App\Core\Strings\StringBuilder;

class TopicosDao
{
    private const TabelaTopicosDicionario = "topicos_dicionario";
    private const TabelaDicionario = "dicionario_tags";
    private const TabelaTopicos = "classes_cliente";
    private const TabelaClientes = "cliente";
    private $database = "";

    /**
     * @param string $database
     */
    public function __construct($database) {
        $this->database = $database;
    }

    /**
     * @param int $topicoId
     * @return TopicosColecao
     */

    public function match(string $sinopse, string $conteudo) {
        $result =  DB::select($this->matchQuery(), [$sinopse, $conteudo]);
        $topicos = new TopicosColecao();
        
        foreach($result as $item) {
            $topicos->adiciona($item->topicoId, $item->topicoNome, $item->clienteId, $item->clienteNome);
        }

        return $topicos;
    }

    private function listaTopicosPorClienteQuery()
    { 
        return (new StringBuilder())
            ->appendLine("SELECT ")
            ->appendLine("  `cc`.`id`, ")
            ->appendLine("  `cc`.`nome` ")
            ->appendLine("  FROM {$this->getTabelaTopicos()} AS `cc` ")
            ->appendLine("      INNER JOIN {$this->getTabelaClientes()} AS `c` ")
            ->appendLine("          ON `cc`.`id_cliente` = `c`.`id_registro_importado` AND `cc`.`ativo` = 1")
            ->appendLine("      WHERE  `c`.`id` = ? AND `c`.`ativo` = 1 ")
            ->appendLine("          ORDER BY nome ASC ")
            ->toString();
    }

    public function listaTopicosPorCliente($clienteId)
    {
        $result =  DB::select($this->listaTopicosPorClienteQuery(), [$clienteId]);
        $topicos = array();
        
        foreach($result as $item) {
            array_push($topicos, new Topico($item->id, $item->nome));
        }

        return $topicos;
    }

    /**
     * @param int $topicoId
     * @return TopicoPalavrasColecao
     */
    public function listaPalavras($topicoId) {
        $result =  DB::select($this->listaPalavrasQuery(), [$topicoId]);
        $palavras = array();
        foreach($result as $item) {
            array_push($palavras, new TopicoPalavra($item->nome, $item->id));
        }

        return new TopicoPalavrasColecao($palavras);
    }

    /**
     * @param int $topicoId
     * @param TopicoPalavrasColecao $palavras
     */
    public function adicionaPalavras($topicoId, TopicoPalavrasColecao $palavras)
    {
        if (!isset($palavras)) return;

        foreach($palavras->toArray() as $palavra) {
            DB::insert("insert into {$this->getTabelaTopicosDicionario()} (id_topico, id_dicionario) values (?, ?)", [$topicoId, $palavra->getId()]);
        }
    }

    /**
     * @param int $topicoId
     * @param TopicoPalavrasColecao $palavras
     */
    public function removePalavras($topicoId, TopicoPalavrasColecao $palavras)
    {
        if (!isset($palavras)) return;

        foreach($palavras->toArray() as $palavra) {
            DB::delete("delete from {$this->getTabelaTopicosDicionario()} where id_topico = ? and id_dicionario = ?", [$topicoId, $palavra->getId()]);
        }
    }

    private function tabela(string $tableName) {
        return empty($this->database) ? 
            "`{$tableName}`" : 
            "`{$this->database}`.`{$tableName}`";
    }

    private function getTabelaTopicosDicionario() {
        return $this->tabela(self::TabelaTopicosDicionario);
    }

    private function getTabelaDicionario() {
        return $this->tabela(self::TabelaDicionario);
    }

    private function getTabelaTopicos() {
        return $this->tabela(self::TabelaTopicos);
    }

    private function getTabelaClientes() {
        return $this->tabela(self::TabelaClientes);
    }

    private function listaPalavrasQuery() {
        return "select "
               ."`d`.`id` AS `id`, "
               ."`d`.`nome` AS `nome` "
               ."from {$this->getTabelaTopicosDicionario()} as `td` "
               ."    inner join {$this->getTabelaDicionario()} as `d` on `td`.`id_dicionario` = `d`.`id` "
               ."where `td`.`id_topico` = ? ";
    }

    private function matchQuery() {
        return "SELECT DISTINCT "
        ."`c`.`id` AS `clienteId`, "
        ."`c`.`nome` AS `clienteNome`, "
        ."`cc`.`id` AS `topicoId`, "
        ."`cc`.`nome` AS `topicoNome` "
        ."FROM {$this->getTabelaTopicosDicionario()} AS `td` "
        ."    INNER JOIN {$this->getTabelaDicionario()} AS `dt` ON `td`.`id_dicionario` = `dt`.`id` "
        ."    INNER JOIN {$this->getTabelaTopicos()} AS `cc` ON `td`.`id_topico` = `cc`.`id` "
        ."    INNER JOIN {$this->getTabelaClientes()} AS `c` ON `cc`.`id_cliente` = `c`.`id_registro_importado` "
        ."WHERE ? LIKE CONCAT('%', `dt`.`nome`, '%') OR ? LIKE CONCAT('%', `dt`.`nome`, '%'); ";
    }
}