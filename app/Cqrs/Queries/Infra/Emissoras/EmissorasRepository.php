<?php
namespace App\Cqrs\Queries\Infra\Emissoras;

use App\Cqrs\Queries\Model\Emissoras\IEmissorasRepository;
use App\Cqrs\Queries\Model\Emissoras\Emissora;
use Illuminate\Support\Facades\DB;
use App\Core\Strings\StringBuilder;

class EmissorasRepository implements IEmissorasRepository
{
    private $connectionId;

    public function __construct(string $connectionId)
    {
        $this->connectionId = $connectionId;
    }

    private function listarQuery() 
    {
        return (new StringBuilder())
            ->appendLine("SELECT ")
            ->appendLine("    id, ")
            ->appendLine("    nome ")
            ->appendLine("    FROM emissora ")
            ->appendLine("        WHERE (? IS NULL OR id_veiculo = ?) ")
            ->appendLine("            AND (? IS NULL OR id_praca = ?)")
            ->appendLine("        ORDER BY nome ASC ")
            ->toString();
    }

    public function listaPor($veiculoId, $pracaId)
    {
        $result =  DB::connection($this->connectionId)
            ->select($this->listarQuery(), [$veiculoId, $veiculoId, $pracaId, $pracaId]);

        $emissoras = array();
        
        foreach($result as $item)
            array_push($emissoras, new Emissora($item->id, $item->nome));            

        return $emissoras;
    }
}