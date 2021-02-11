<?php namespace App\Mms\Shared;

class Tempo
{
    private $segundos;

    private function __construct($segundos)
    {
        $this->segundos = $segundos;
    }

    public static function minutos($minutos)
    {
        return new Tempo($minutos * 60);
    }
    
    public static function horas($horas)
    {
        return self::minutos($horas * 60);
    }

    public static function primeiroSegundoDoDia()
    {
        return new Tempo(0);
    }
    
    public static function ultimoSegundoDoDia()
    {
        $horas = self::horas(23);
        $minutos = self::minutos(59);
        $segundos = 59;
        return new Tempo($horas->getSegundos() + $minutos->getSegundos() + $segundos);
    }

    public function getSegundos()
    {
        return $this->segundos;
    }    

    private function calculaHoras()
    {
        return floor($this->segundos / 3600);
    }

    private function calculaMinutos()
    {
        $segundos = $this->segundos - $this->calculaHoras() * 3600;

        return floor($segundos / 60);
    }

    private function calculaSegundos()
    {
        $segundos = $this->segundos - $this->calculaHoras() * 3600; 
        $segundos -=  $this->calculaMinutos() * 60;      
        $segundos = round($segundos, 0);
        return $segundos >= 60 ? 59 : $segundos;
    }

    private function textual($valor)
    {
        return str_pad($valor, 2, '0', STR_PAD_LEFT);
    }

    public function toString()
    {
        return  "{$this->textual($this->calculaHoras())}:{$this->textual($this->calculaMinutos())}:{$this->textual($this->calculaSegundos())}"; 
    }
}
