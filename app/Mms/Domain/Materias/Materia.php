<?php namespace App\Mms\Domain\Materias;

class Materia
{
    private $id;
    private $projeto;
    private $data;
    private $titulo;
    private $operador;
    private $operadorNome;
    private $dataDeCadasttro;
    private $status;
    private $materiaId;
    private $clientes;
    private $programa;
    private $dia;
    private $programaNome;
    private $emissora;

    public function __construct(
        $id,
        $projeto,
        $data,
        $titulo,
        $operador,
        $operadorNome,
        $dataDeCadasttro,
        $status,
        $materiaId,
        $clientes,
        $programa,
        $dia,
        $programaNome,
        $emissora)
    {
        $this->id = $id;
        $this->projeto = $projeto;
        $this->data = $data;
        $this->titulo = $titulo;
        $this->operador = $operador;
        $this->operadorNome = $operadorNome;
        $this->dataDeCadasttro = $dataDeCadasttro;
        $this->status = $status;
        $this->materiaId = $materiaId;
        $this->clientes = $clientes;
        $this->programa = $programa;
        $this->dia = $dia;
        $this->programaNome = $programaNome;
        $this->emissora = $emissora;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getProjeto()
    {
        return $this->projeto;
    }
    public function getData()
    {
        return $this->data;
    }
    public function getTitulo()
    {
        return $this->titulo;
    }
    public function getOperador()
    {
        return $this->operador;
    }
    public function getOperadorNome()
    {
        return $this->operadorNome;
    }
    public function getDataDeCadasttro()
    {
        return $this->dataDeCadasttro;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function getMateriaId()
    {
        return $this->materiaId;
    }
    public function getClientes()
    {
        return $this->clientes;
    }
    public function getPrograma()
    {
        return $this->programa;
    }
    public function getDia()
    {
        return $this->dia;
    }
    public function getProgramaNome()
    {
        return $this->programaNome;
    }
    public function getEmissora()
    {
        return $this->emissora;
    }
}
