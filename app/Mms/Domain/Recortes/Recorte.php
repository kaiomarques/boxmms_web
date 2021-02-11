<?php namespace App\Mms\Domain\Recortes;

class Recorte
{
    private $id;
    private $edicao;
    private $programa;
    private $data;
    private $inicio;
    private $tempoRealizado;
    private $tempoRealizadoTextual;
    private $materia;
    private $materiaTitulo;
    private $metaDados;

    public function __construct(
        $id,
        $edicao,
        $programa,
        $data,
        $inicio,
        $tempoRealizado,
        $tempoRealizadoTextual,
        $materia,
        $materiaTitulo,
        $metaDados)
    {
        $this->id = $id;
        $this->edicao = $edicao;
        $this->programa = $programa;
        $this->data = $data;
        $this->inicio = $inicio;
        $this->tempoRealizado = $tempoRealizado;
        $this->tempoRealizadoTextual = $tempoRealizadoTextual;
        $this->materia = $materia;
        $this->materiaTitulo = $materiaTitulo;
        $this->metaDados = $metaDados;
    }
    public function getId()
    {
        return $this->id;
    }
    public function getEdicao()
    {
        return $this->edicao;
    }
    public function getPrograma()
    {
        return $this->programa;
    }
    public function getData()
    {
        return $this->data;
    }
    public function getInicio()
    {
        return $this->inicio;
    }
    public function getTempoRealizado()
    {
        return $this->tempoRealizado;
    }
    public function getTempoRealizadoTextual()
    {
        return $this->tempoRealizadoTextual;
    }
    public function getMateria()
    {
        return $this->materia;
    }
    public function getMateriaTitulo()
    {
        return $this->materiaTitulo;
    }
    public function getMetaDados()
    {
        return $this->metaDados;
    }
}
