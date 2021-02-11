<?php namespace App\Cqrs\Canais\Queries\Infra;

use App\Cqrs\Canais\Queries\Model\ICanaisRepository;
use App\Cqrs\Canais\Queries\Model\Canal;
use Illuminate\Support\Facades\DB;
use App\Core\Strings\StringBuilder;

class CanaisRepository implements ICanaisRepository
{
    private $connectionId;

    public function __construct(string $connectionId)
    {
        $this->connectionId = $connectionId;
    }
    private function listaQuery()
    {
        return (new StringBuilder())
            ->appendLine("SELECT ")
            ->appendLine("  `cc`.`id`, ")
            ->appendLine("  `cc`.`nome` ")
            ->appendLine("  FROM `classes_cliente` AS `cc` ")
            ->appendLine("      INNER JOIN `cliente` AS `c` ")
            ->appendLine("          ON `cc`.`id_cliente` = `c`.`id_registro_importado` ")
            ->appendLine("      WHERE  (? IS NULL OR `c`.`id` = ?) ")
            ->appendLine("          ORDER BY `cc`.`nome` ASC ")
            ->toString();
    }

    public function lista($clienteId)
    {
        $result =  DB::connection($this->connectionId)->select($this->listaQuery(), [$clienteId, $clienteId]);

        $canais = array();
        
        foreach ($result as $item) {
            array_push($canais, new Canal($item->id, $item->nome));
        }

        return $canais;
    }
}
