<?php 
namespace App\Http\Dao\Queries;

class QueryNotificacoesPorPalavrasChave extends QueryNotificacoes
{public function __construct(string $dbIntegracao)
    {
        parent::__construct("`agrupamento_notificacoes_palavras_chave`", "p");
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
            ->adicionaJoin("INNER JOIN `eventos_arquivos_palavras` AS `eap` ON `arq`.`id` = `eap`.`id_evento_arquivo` AND `eve`.`id` = `eap`.`id_evento`")
            ->adicionaJoin("INNER JOIN {$dbIntegracao}.`dicionario_tags` AS `dt` ON `eap`.`id_dicionario_tag` = `dt`.`id`")
            ->adicionaJoin("INNER JOIN {$dbIntegracao}.`classes_cliente` AS `cc` ON `dt`.`id_registro_importado` = `cc`.`id_registro_importado`")
            ->adicionaJoin("INNER JOIN {$dbIntegracao}.`topicos_dicionario` AS `td` ON `cc`.`id` = `td`.`id_topico`")
            ->adicionaJoin("INNER JOIN {$dbIntegracao}.`dicionario_tags` AS `dtt` ON `td`.`id_dicionario` = `dtt`.`id`")
            ->adicionaJoin("LEFT JOIN {$dbIntegracao}.programa pro on pro.id = p.id_programa")
            ->adicionaJoin("LEFT JOIN {$dbIntegracao}.emissora emi on emi.id = p.id_emissora");
    }

    protected function getColumnsForCount()
    {
        return "p.id";
    }

    protected function colunaPalavras()
    {
        return "`dtt`.`nome`";
    }
}