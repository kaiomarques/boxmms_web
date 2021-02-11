<?php namespace App\Cqrs\Clientes\Queries\Infra;

use App\Cqrs\Clientes\Queries\Model\Cliente;
use App\Cqrs\Clientes\Queries\Model\IClientesRepository;
use Illuminate\Support\Facades\DB;
use App\Core\Strings\StringBuilder;

class ClientesRepository implements IClientesRepository
{
    private const ATIVO = 1;
    private $connectionId;

    public function __construct(string $connectionId)
    {
        $this->connectionId = $connectionId;    
    }

    private function listaQuery() 
    {
        return (new StringBuilder())
            ->appendLine("SELECT ")
            ->appendLine("    `id`, ")
            ->appendLine("    `nome` ")
            ->appendLine("    FROM `cliente` ")
            ->appendLine("        WHERE (:nome_null IS NULL OR `nome` LIKE :nome) ")
            ->appendLine("          AND `ativo` = :ativo ")
            ->appendLine("        ORDER BY `nome` ASC ")
            ->toString();
    }

    public function lista($nome)
    {
        $result =  DB::connection($this->connectionId)->select($this->listaQuery(),[
            "nome_null" => "%{$nome}%",
            "nome" => "%{$nome}%",
            "ativo" => self::ATIVO
        ]);

        $clientes = array();
        
        foreach($result as $item)
            array_push($clientes, new Cliente($item->id, $item->nome));            

        return $clientes;
    }
}