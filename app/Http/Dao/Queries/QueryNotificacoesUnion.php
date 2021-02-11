<?php 
namespace App\Http\Dao\Queries;

class QueryNotificacoesUnion
{
    private $limit;
    private $offset;
    private $orderBy;
    private $unionDistinctQueryUm;
    private $unionDistinctQueryDois;
    private $isCount;


    public function __construct(QueryNotificacoes $queryUm, QueryNotificacoes $queryDois)
    {
        $this->unionDistinctQueryUm = $queryUm;
        $this->unionDistinctQueryDois = $queryDois;
    }

    public function count($isCount)
    {
        $this->isCount = $isCount;

        return $this;
    }

    private function orderByWithDirection($coluna, $direction)
    {
        $this->orderBy = "{$coluna} {$direction}";
        return $this;
    }

    public function orderByAsc($coluna)
    {
        return $this->orderByWithDirection($coluna, "ASC");
    }

    public function orderByDesc($coluna)
    {
        return $this->orderByWithDirection($coluna, "DESC");
    }

    public function limit($limit, $offset)
    {
        $this->limit = $limit;
        $this->offset = $offset;

        return $this;
    }
    
    public function iniciandoEm($data)
    {
        $this->unionDistinctQueryUm->iniciandoEm($data);
        $this->unionDistinctQueryDois->iniciandoEm($data);
        
        return $this;

    }

    public function terminandoEm($data)
    {
        $this->unionDistinctQueryUm->terminandoEm($data);
        $this->unionDistinctQueryDois->terminandoEm($data);
        
        return $this;        
    }

    public function doPrograma($programa)
    {
        $this->unionDistinctQueryUm->doPrograma($programa);
        $this->unionDistinctQueryDois->doPrograma($programa);
        
        return $this;        
    }

    public function daEmissora($emissora)
    {
        $this->unionDistinctQueryUm->daEmissora($emissora);
        $this->unionDistinctQueryDois->daEmissora($emissora);
        
        return $this;        
    }

    public function comStatusDasNotificacoes($status)
    {
        $this->unionDistinctQueryUm->comStatusDasNotificacoes($status);
        $this->unionDistinctQueryDois->comStatusDasNotificacoes($status);
        
        return $this;        
    }

    public function comStatusDosEventos($status)
    {
        $this->unionDistinctQueryUm->comStatusDosEventos($status);
        $this->unionDistinctQueryDois->comStatusDosEventos($status);
        
        return $this;        
    }

    public function doStatus($id_evento_status)
    {
        $this->unionDistinctQueryUm->doStatus($id_evento_status);
        $this->unionDistinctQueryDois->doStatus($id_evento_status);
        
        return $this;        
    }

    public function tinder($tinder)
    {
        $this->unionDistinctQueryUm->tinder($tinder);
        $this->unionDistinctQueryDois->tinder($tinder);
        
        return $this;        
    }

    public function adicionaPalavras($palavras)
    {
        $this->unionDistinctQueryUm->adicionaPalavras($palavras);
        $this->unionDistinctQueryDois->adicionaPalavras($palavras);
        
        return $this;        
    }

    public function adicionaCliente($cliente)
    {
        $this->unionDistinctQueryUm->adicionaCliente($cliente);
        $this->unionDistinctQueryDois->adicionaCliente($cliente);
        
        return $this;        
    }

    public function comIdsMaioresQue($id)
    {
        $this->unionDistinctQueryUm->comIdsMaioresQue($id);
        $this->unionDistinctQueryDois->comIdsMaioresQue($id);
        
        return $this;        
    }

    public function daPraca($praca)
    {
        $this->unionDistinctQueryUm->daPraca($praca);
        $this->unionDistinctQueryDois->daPraca($praca);

        return $this;
    }

    public function daMidia($midia)
    {
        $this->unionDistinctQueryUm->daMidia($midia);
        $this->unionDistinctQueryDois->daMidia($midia);
        
        return $this;
    }

    private function getUnionDistinct()
    {
        return "{$this->unionDistinctQueryUm->count($this->isCount)->sql()} UNION DISTINCT {$this->unionDistinctQueryDois->count($this->isCount)->sql()}";
    }

    private function getOrderBy()
    {
        return isset($this->orderBy) && !empty($this->orderBy) ? " ORDER BY {$this->orderBy}" : "";
    }

    private function getLimitOffset()
    {
        $limit = "";

        if (isset($this->limit) && is_numeric($this->limit)) {
                
            $limit = " LIMIT {$this->limit}";
              
            if (isset($this->offset) && is_numeric($this->offset))
                $limit = "{$limit} OFFSET {$this->offset}";
        }

        return $limit;
    }

    private function getColunas()
    {
        return $this->isCount ? " COUNT(1) AS res " : " * ";
    }

    public function sql()
    {
        return "SELECT{$this->getColunas()}FROM ({$this->getUnionDistinct()}) AS T{$this->getOrderBy()}{$this->getLimitOffset()}";
    }
}