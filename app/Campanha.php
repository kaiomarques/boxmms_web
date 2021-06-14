<?php namespace App;
namespace App;

use Illuminate\Database\Eloquent\Model;

class Campanha  extends Model{
    private $id;
    private $nome;
    private $id_cliente;
    private $periodo_inicial;
    private $periodo_final;
    private $id_praca;
    private $id_midia;
    private $todos;

    protected $table = 'campanhas';
    public $timestamps = false;
    protected $fillable = [
                    'id',
                    'nome',
                    'id_cliente',
                    'periodo_inicial',
                    'periodo_final',
                    'id_praca',
                    'id_midia',
                    'todos'
                ];
    protected $hidden = [];
   
}