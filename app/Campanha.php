<?php namespace App;
namespace App;

use Illuminate\Database\Eloquent\Model;

class Campanha  extends Model{
    private $id;
    private $nome;
    private $id_cliente;
    private $periodo_inicial;
    private $periodo_final;

    protected $table = 'campanhas';
    public $timestamps = false;
    protected $fillable = [
                    'id',
                    'nome',
                    'id_cliente',
                    'periodo_inicial',
                    'periodo_final' 
                ];
    protected $hidden = [];
   
}