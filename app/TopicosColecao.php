<?php namespace App;

use \App\Topico;

class TopicosColecao {
    private $colecao = array();    

    public function adiciona($id, string $nome, $clienteId, string $clienteNome) {
        $topico = new Topico($id, $nome, $clienteId, $clienteNome);

        if ($this->containsByValues($topico))
            return;
        
        array_push($this->colecao, $topico);
    }

    private function containsByValues(Topico $topico) {
        foreach($this->colecao as $item) {
            if ($item->areValuesEquals($topico))
                return true;
        }
        return false;
    }

    public function count() {
        return count($this->colecao);
    }

    public function toJson() {
        $result = array();

        foreach($this->colecao as $item) {
            array_push($result, $item->toJson());
        }

        return $result;
    }
}