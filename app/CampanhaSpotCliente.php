<?php namespace App;
namespace App;

use Illuminate\Database\Eloquent\Model;

class CampanhaSpotCliente extends Model{
    private $id;
    private $id_campanha;
    private $id_cliente;

    protected $table = 'campanha_spot_clientes';
    public $timestamps = false;
    protected $fillable = [
                    'id',
                    'id_campanha',
                    'id_cliente'
                ];
    protected $hidden = [];
   
}