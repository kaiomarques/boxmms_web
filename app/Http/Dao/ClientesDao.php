<?php
namespace App\Http\Dao;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use \App\Config;


class ClientesDao {    
    private const TabelaClientes = "cliente";
    private $database = "";

    /**
     * @param string $database
     */
    public function __construct($database) {
        $this->database = $database;
    }

    /**
     * @param int $id
     * @return int
     */

    public function getIdOrigemById($id) {
        $result =  DB::select($this->getIdOrigemByIdQuery(), [$id]);
        
        if ($result) {
            return $result[0]->idOrigem;
        }

        return null;
    }

    private function tabela(string $tableName) {
        return empty($this->database) ? 
            "`{$tableName}`" : 
            "`{$this->database}`.`{$tableName}`";
    }

    private function getTabelaClientes() {
        return $this->tabela(self::TabelaClientes);
    }

    private function getIdOrigemByIdQuery() {
        return "select "
               ."`c`.`id_registro_importado` AS `idOrigem` "
               ."from {$this->getTabelaClientes()} as `c` "
               ."where `c`.`id` = ? AND `c`.`ativo` = 1"
               ."LIMIT 1 ";
    }
}