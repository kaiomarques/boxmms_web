<?php namespace App\Cqrs\Eventos\Queries\Model;

class EventoStatus
{
    private $id;
    private $descricao;

    public function __construct($id, $descricao)
    {
        $this->id = $id;
        $this->descricao = $descricao;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getDescricao()
    {
        return $this->descricao;
    }
}