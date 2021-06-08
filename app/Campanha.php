<?php namespace App;
namespace App;

use Illuminate\Database\Eloquent\Model;

class Campanha  extends Model{
    private $id;
    private $nome;

    protected $table = 'campanhas';
    public $timestamps = false;
    protected $fillable = ['dia',
                    'id',
                    'nome'];
    protected $hidden = [];
   
}