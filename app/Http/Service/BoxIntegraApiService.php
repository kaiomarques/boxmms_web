<?php namespace App\Http\Service;

use Illuminate\Http\Request;
use \GuzzleHttp\Client;
use \App\TopicoPalavrasColecao;

class BoxIntegraApiService
{
    private $boxIntegraApi = "";

    public function __construct($boxIntegraApi)
    {
        $this->boxIntegraApi = $boxIntegraApi;
    }

    public function salvaPalavrasChavePorCliente($clienteId, TopicoPalavrasColecao $palavras)
    {
        if ($palavras->count() < 1 || !isset($clienteId)) return;

        $client = new Client();
        $response = $client->request('POST', $this->salvaPalavrasChavePorClienteEndpoint($clienteId), [ 'json' => array("data" => json_encode($palavras->toJson())) ]);
        $resultado = json_decode($response->getBody()->getContents());

        if (!isset($resultado->data)) return;

        foreach($resultado->data as $item) {
            $palavra = $palavras->encontraPorNome($item->nome);
            if ($palavra != null) {
                $palavra->setId($item->id);
            }
        }
    }

    private function salvaPalavrasChavePorClienteEndpoint($clienteId) {
        return "{$this->boxIntegraApi}v1/clientes/{$clienteId}/dicionario";
    }
}
