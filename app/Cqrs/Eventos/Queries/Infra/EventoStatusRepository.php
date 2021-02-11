<?php namespace App\Cqrs\Eventos\Queries\Infra;

use App\Cqrs\Eventos\Queries\Model\EventoStatus;
use App\Cqrs\Eventos\Queries\Model\IEventoStatusRepository;
use Illuminate\Support\Facades\DB;
use App\Core\Strings\StringBuilder;

class EventoStatusRepository implements IEventoStatusRepository
{
    private const ATIVO = 1;
    private $connectionId;

    public function __construct(string $connectionId)
    {
        $this->connectionId = $connectionId;    
    }

    public function listaQuery() 
    {
        return (new StringBuilder())
            ->appendLine("SELECT ")
            ->appendLine("    `id`, ")
            ->appendLine("    `descricao` ")
            ->appendLine("    FROM ".config("app.DB_DATABASE").".`eventos_status` ")
            ->appendLine("        WHERE ativo = 1 ")
            ->appendLine("        ORDER BY `id` ASC ")
            ->toString();
    }


    public function lista($nome)
    {
        $result =  DB::connection($this->connectionId)->select($this->listaQuery());

        $evento_status = array();
        
        foreach($result as $item)
            array_push($evento_status, new EventoStatus($item->id, $item->descricao));            

        return $evento_status;
    }
}