<?php
namespace App\Cqrs\Queries\Infra\Pracas;

use App\Cqrs\Queries\Model\Pracas\IPracasRepository;
use App\Cqrs\Queries\Model\Pracas\Praca;
use Illuminate\Support\Facades\DB;
use App\Core\Strings\StringBuilder;

class PracasRepository implements IPracasRepository
{
    private $connectionId;
    private const TIPO_PRACA = 4;

    public function __construct(string $connectionId)
    {
        $this->connectionId = $connectionId;
    }

    private function listaQuery() 
    {
        return (new StringBuilder())
            ->appendLine("SELECT ")
            ->appendLine("    id, ")
            ->appendLine("    `descricao` AS `nome` ")
            ->appendLine("    FROM cadastro_basico ")
            ->appendLine("        WHERE (tipo_cadastro_basico = ?) ")
            ->appendLine("        ORDER BY descricao ASC ")
            ->toString();
    }

    public function lista()
    {       
        $result =  DB::connection($this->connectionId)
            ->select($this->listaQuery(), [self::TIPO_PRACA]);

        $pracas = array();
        
        foreach($result as $item)
            array_push($pracas, new Praca($item->id, $item->nome));            

        return $pracas;
    }
}

