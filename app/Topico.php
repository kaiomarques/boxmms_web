<?php namespace App;

class Topico {
    private $id;
    private $nome;
    private $clienteId;
    private $clienteNome;

    public function __construct($id, $nome, $clienteId, $clienteNome) {
        $this->id = $id;
        $this->nome = $nome;
        $this->clienteId = $clienteId;
        $this->clienteNome = $clienteNome;
    }

    public function getId() {
        return $this->id;
    }

    public function getNome() {
        return $this->nome;
    }

    public function getClienteId() {
        return $this->clienteId;
    }

    public function getClienteNome() {
        return $this->clienteNome;
    }

    public function toJson() {
        return array(
            "topicoId" => $this->getId(),
            "topicoNome" => $this->getNome(),
            "clienteId" => $this->getClienteId(),
            "clienteNome" => $this->getClienteNome()
        );
    }

    public function equals(Topico $other) {
        return $this == $other;
    }

    public function areValuesEquals(Topico $other) {
        return 
            $this->getId() == $other->getId() &&
            $this->getNome() == $other->getNome() &&
            $this->getClienteId() == $other->getClienteId() &&
            $this->getClienteNome() == $other->getClienteNome();
    }         
     
}