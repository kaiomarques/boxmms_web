<?php namespace App;
namespace App;

use Illuminate\Database\Eloquent\Model;

class Midia  extends Model{
    private $id;
    private $nome;

    protected $table = 'midias';
    public $timestamps = false;
    protected $fillable = [,
                    'id',
                    'nome'];
    protected $hidden = [];
   
}