<?php namespace App\Http\Dao;

class Topico
{
    private $id;
    private $nome;

    public function __construct($id, $nome)
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