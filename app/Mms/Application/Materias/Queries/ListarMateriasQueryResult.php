<?php namespace App\Mms\Application\Materias\Queries;

use App\Mms\Application\Shared\Queries\CollectionQueryResult;

class ListarMateriasQueryResult extends CollectionQueryResult
{
    private $paginacao;

    public function __construct(array $materias, ListarMateriasQueryResultPaginacao $paginacao)
    {
        parent::__construct($materias);
        $this->paginacao = $paginacao;
    }

    protected function handle($materia)
    {
        return array(
            "blnk" => "",
            "cliente_list" => $this->handleClientes($materia->getClientes()),
            "data" => $materia->getData(),
            "data_cadastro" => $materia->getDataDeCadasttro(),
            "dia" => $materia->getDia(),
            "emissora_nome" => $materia->getEmissora(),
            "id" => $materia->getId(),
            "id_materia_radiotv_jornal" => $materia->getMateriaId(),
            "id_operador" => $materia->getOperador(),
            "id_programa" => $materia->getPrograma(),
            "id_projeto" => $materia->getProjeto(),
            "nome_operador" => $materia->getOperadorNome(),
            "programa_nome" => $materia->getProgramaNome(),
            "status" => $materia->getStatus(),
            "titulo" => $materia->getTitulo());
    }

    private function handleClientes($clientes)
    {
        if (!isset($clientes) || empty($clientes))
            return "";

        $lista = explode(",", $clientes);

        if (count($lista) < 1)
            return "";

        $outrosClientesQuantidade = count($lista) - 1;
        $complemento = $outrosClientesQuantidade < 1 ? "" : " + {$outrosClientesQuantidade} cliente(s)";

        return "{$lista[0]}{$complemento}";

    }

    public function getPaginacao()
    {
        return $this->paginacao;
    }
}
