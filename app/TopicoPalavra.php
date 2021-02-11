<?php namespace App;

class TopicoPalavra
{
    private $id = null;
    private $valor = "";

    public function __construct($valor, $id = null) {
        $this->valor = $valor;
        $this->id = $id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getId() {
        return $this->id;
    }

    public function getValor() {
        return $this->valor;
    }

    public function equals(TopicoPalavra $other) {
        return $this == $other;
    }

    public function areValuesEquals(TopicoPalavra $other) {
        return 
            $this->getId() == $other->getId() &&
            $this->getValor() == $other->getValor();
    }
}