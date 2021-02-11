<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Service\ErrorsService;
use App\Http\Service\BoxIntegraApiService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\TopicoPalavra;
use App\TopicoPalavrasColecao;
use App\Http\Dao\TopicosDao;
use App\Http\Dao\ClientesDao;

use App\Cqrs\Canais\Queries\Application\CanaisQueriesHandler;
use App\Cqrs\Canais\Queries\Application\ListaCanaisQuery;
use App\Cqrs\Canais\Queries\Infra\CanaisRepository;

class TopicosController extends Controller
{
    private const INTEGRACAO_CONNECTION_ID = "mysql_midiaclip";
    /**
     * @var TopicosDao $dao
     */
    private $dao;

    /**
     * @var ClientesDao $clientesDao
     */
    private $clientesDao;

    /**
     * @var BoxIntegraApiService $apiService
     */
    private $apiService;

    private $queriesHandler;

    public function __construct() {
        $database = \App\Http\Dao\ConfigDao::getSchemaMidiaClip(); 
        $this->dao = new TopicosDao($database);
        $this->clientesDao = new ClientesDao($database);
        $this->apiService = new BoxIntegraApiService(config("app.url_midiaclip"));
        $this->queriesHandler = $this->criaCanaisQueriesHandler();
    }

    protected function criaCanaisQueriesHandler()
    {
        return new CanaisQueriesHandler(new CanaisRepository(self::INTEGRACAO_CONNECTION_ID));
    }

    public function index($clienteId) {

        $canais = $this->queriesHandler->lista(new ListaCanaisQuery($clienteId));

        return $this->sendResponse(array("data" => $canais->items() , "qtde" => $canais->count()));
    }
    
    public function listaPalavras($id)
    {
        $palavras = $this->dao->listaPalavras($id);

        return $this->sendResponse(array("data" => $palavras->toJson() , "qtde" => $palavras->count()));
    }

    public function match(Request $request)
    {
        $sinopse = $request->input("sinopse");
        $conteudo = $request->input("conteudo");
        $topicos = $this->dao->match($sinopse, $conteudo);

        return $this->sendResponse(array("data" => $topicos->toJson() , "qtde" => $topicos->count()));
    }

    public function salvaPalavras($id, Request $request)
    {
        $clienteId = $request->input("cliente_id");
        $topicoId = $request->input("topico_id");
        $palavrasAdicionadas = $request->input("palavras_adicionadas");
        $palavrasRemovidas = $request->input("palavras_removidas");
        
        if (isset($palavrasAdicionadas) && is_array($palavrasAdicionadas)) {
            $palavras = new TopicoPalavrasColecao();

            foreach ($palavrasAdicionadas as $palavraAdicionada) {
                $palavras->adiciona($palavraAdicionada["nome"]);
            }
            $idOrigem = $this->clientesDao->getIdOrigemById($clienteId);

            $this->apiService->salvaPalavrasChavePorCliente($idOrigem, $palavras);            

            $this->dao->adicionaPalavras($topicoId, $palavras);
        }

        if (isset($palavrasRemovidas) && is_array($palavrasRemovidas)) {
            $palavras = new TopicoPalavrasColecao();

            foreach ($palavrasRemovidas as $palavraRemovida) {
                $palavras->adiciona($palavraRemovida["nome"], $palavraRemovida["id"]);
            }

            $this->dao->removePalavras($topicoId, $palavras);
        }

        $response = array(
            "palavras_adicionadas" => $request->input("palavras_adicionadas"),
            "palavras_removidas" => $request->input("palavras_removidas"),
            "palavras_existentes" => $request->input("palavras_existentes"));

        return $this->sendResponse(array("data" => $response));
    }
}
