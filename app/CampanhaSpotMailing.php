<?php namespace App;
namespace App;

use Illuminate\Database\Eloquent\Model;

class CampanhaSpotMailing extends Model{
    private $id_campanha;
    private $id_emissora;

    protected $table = 'campanha_spot_mailing';
    public $timestamps = false;
    protected $fillable = [
                    'id_campanha',
                    'id_emissora'
                ];
    protected $hidden = [];
   
}