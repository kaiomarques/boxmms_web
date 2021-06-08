<?php namespace App;
namespace App;

use Illuminate\Database\Eloquent\Model;

class Spot  extends Model{
    private $id;
    private $nome;
    private $s3_path;

    protected $table = 'spots';
    public $timestamps = false;
    protected $fillable = ['dia',
                    'id',
                    'nome',
                    's3_path'];
    protected $hidden = [];
   
}