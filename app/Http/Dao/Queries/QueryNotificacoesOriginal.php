<?php 
namespace App\Http\Dao\Queries;

class QueryNotificacoesOriginal extends QueryNotificacoes
{
    public function __construct(string $dbIntegracao)
    {
        parent::__construct("agrupamento_notificacoes", "p");
        $this
            ->adicionaColuna("p.*", null)
            ->adicionaColuna("ifNull(eve.status, 1 )", " status_evento")
            ->adicionaColuna("arq.id_evento", null)
            ->adicionaColuna("pro.nome", "programa")
            ->adicionaColuna("emi.nome", "emissora")
            ->adicionaColuna("''", "blnk")
            ->adicionaColuna("''", "user_link")
            ->adicionaColuna("concat('row_data_', p.id)", "id_row");
        $this
            ->adicionaJoin("INNER JOIN eventos_arquivos arq on arq.id = p.id_evento_arquivo")
            ->adicionaJoin("INNER JOIN eventos eve on eve.id = arq.id_evento")
            ->adicionaJoin("LEFT JOIN {$dbIntegracao}.programa pro on pro.id = p.id_programa")
            ->adicionaJoin("LEFT JOIN {$dbIntegracao}.emissora emi on emi.id = p.id_emissora");
    }

    protected function getColumnsForCount()
    {
        return "p.id";
    }

    protected function colunaPalavras()
    {
        return "`p`.`palavras`";
    }
}