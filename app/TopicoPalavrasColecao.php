<?php namespace App;

use App\TopicoPalavra;

class TopicoPalavrasColecao
{
    private $colecao;

    public function __construct($colecao = array()) {
        $this->colecao = array();

        foreach($colecao as $item) {
            array_push($this->colecao, $item);
        }
    }

    public function adiciona(string $palavra, $id = null) {
        if ($this->containsName($palavra))
            return;
        
        array_push($this->colecao, new TopicoPalavra($palavra, $id));
    }

    public function encontraPorNome(string $palavra) {
        foreach($this->colecao as $item) {
            if ($item->getValor() == $palavra)
                return $item;
        }
        return null;
    }

    public function containsName(string $palavra) {
        foreach($this->colecao as $item) {
            if ($item->getValor() == $palavra)
                return true;
        }
        return false;
    }

    public function contains(TopicoPalavra $palavra) {
        foreach($this->colecao as $item) {
            if ($item->equals($palavra))
                return true;
        }
        return false;
    }

    public function containsByValues(TopicoPalavra $palavra) {
        foreach($this->colecao as $item) {
            if ($item->areValuesEquals($palavra))
                return true;
        }
        return false;
    }

    public function toArray() {
        $copy = array();

        foreach($this->colecao as $item) {
            array_push($copy, $item);
        }

        return $copy;
    }

    public function count() {
        return count($this->colecao);
    }

    public function toJson() {
        $resultado = array();

        foreach( $this->colecao as $item) {
            array_push($resultado, array(
                "id" => $item->getId() == null ? "" : $item->getId(),
                "nome" => $item->getValor()
            ));
        }

        return $resultado;
    }
}