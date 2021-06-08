<?php namespace App;
namespace App;

use Illuminate\Database\Eloquent\Model;

class CampanhaSpot  extends Model{
    private $id;
    private $id_campanha;
    private $id_spot;
    private $periodo;
    private $canal_id;

    protected $table = 'campanha_spot';
    public $timestamps = false;
    protected $fillable = [
                    'id',
                    'id_campanha',
                    'id_spot',
                    'periodo',
                    'id_canal'
                ];
    protected $hidden = [];
   
}