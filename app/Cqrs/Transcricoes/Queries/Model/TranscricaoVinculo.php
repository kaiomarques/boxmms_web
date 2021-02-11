<?php namespace App\Cqrs\Transcricoes\Queries\Model;

class TranscricaoVinculo
{
    private $clienteId;
    private $clienteNome;
    private $canalId;
    private $canalNome;

    public function __construct($clienteId, $clienteNome, $canalId, $canalNome) {
        $this->clienteId = $clienteId;
        $this->clienteNome = $clienteNome;
        $this->canalId = $canalId;
        $this->canalNome = $canalNome;
    }

    public function getClienteId() {
        return $this->clienteId;
    }

    public function getClienteNome() {
        return $this->clienteNome;
    }

    public function getCanalId() {
        return $this->canalId;
    }

    public function getCanalNome() {
        return $this->canalNome;
    }
}