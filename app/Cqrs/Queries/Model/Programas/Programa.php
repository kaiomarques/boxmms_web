<?php
namespace App\Cqrs\Queries\Model\Programas;

class Programa
{
    private $id;
    private $nome;

    public function __construct($id, string $nome) 
    {
        $this->id = $id;
        $this->nome = $nome;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getNome()
    {
        return $this->nome;
    }
}