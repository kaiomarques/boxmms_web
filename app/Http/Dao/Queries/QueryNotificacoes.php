<?php 
namespace App\Http\Dao\Queries;

class QueryNotificacoes
{
    private $tabela;
    private $alias;
    private $colunas = array();
    private $colecaoDeColunas;
    private $joins = array();

    private $filtrosWhere = array();
    private $cliente;
    private $palavras = array();
    private $isCount = false;
    private $dataDeInicio;
    private $dataDoFim;
    private $idDoArquivo;
    private $programa;
    private $emissora;
    private $praca;
    private $midia;
    private $idsMaioresQue;
    private $statusDasNotificacoes;
    private $statusDosEventos;
    private $tinder;
    private $orderBy;

    public function __construct(string $tabela, string $alias)
    {
        $this->tabela = $tabela;
        $this->alias = $alias;
    }

    protected function colunaPalavras()
    {
        return "";
    }

    protected function adicionaColuna($coluna, $alias)
    {
        if (isset($alias) && !empty($alias))        
            $coluna = "{$coluna} AS {$alias}";

        array_push($this->colunas, trim($coluna));

        return $this;
    }

    protected function adicionaJoin($join)
    {
        array_push($this->joins, $join);

        return $this;
    }

    public function adicionaCliente($cliente)
    {
        if (isset($cliente) && !empty($cliente))
            $this->cliente = $cliente;

        return $this;
    }

    public function adicionaPalavras($palavras)
    {
        if (isset($palavras) && !empty($palavras))
            $this->palavras = explode(",", $palavras);

        return $this;
    }

    public function iniciandoEm($data)
    {
        if (isset($data) && !is_null($data))
            $this->dataDeInicio = $data;

        return $this;
    }

    public function terminandoEm($data)
    {
        if (isset($data) && !is_null($data))
            $this->dataDoFim = $data;

        return $this;
    }

    public function adicionaIdDoArquivo($id)
    {
        if (isset($id) && is_numeric($id))
            $this->idDoArquivo = $id;

        return $this;
    }

    public function doPrograma($programa)
    {
        if (isset($programa) && is_numeric($programa) && $programa != "-1" && $programa != "0")
            $this->programa = $programa;

        return $this;
    }

    public function daEmissora($emissora)
    {
        if (isset($emissora) && is_numeric($emissora) && $emissora != "-1" && $emissora != "0")
            $this->emissora = $emissora;

        return $this;
    }

    public function daPraca($praca)
    {
        if (isset($praca) && is_numeric($praca) && $praca != "-1" && $praca != "0")
            $this->praca = $praca;

        return $this;
    }

    public function daMidia($midia)
    {
        if (isset($midia) && is_numeric($midia))
            $this->midia = $midia;

        return $this;
    }

    public function comIdsMaioresQue($id)
    {
        if (isset($id) && is_numeric($id))
            $this->idsMaioresQue = $id;

        return $this;
    }

    public function count($isCount)
    {
        $this->isCount = $isCount;

        return $this;
    }

    public function comStatusDasNotificacoes($status)
    {
        if (isset($status) && !empty($status))
            $this->statusDasNotificacoes = $status;

        return $this;
    }

    public function comStatusDosEventos($status)
    {
        if (isset($status) && !empty($status))
            $this->statusDosEventos = $status;

        return $this;
    }

    public function doStatus($id_evento_status)
    {
        if (isset($id_evento_status) && !empty($id_evento_status))
            $this->statusDosEventos = $id_evento_status;

        return $this;
    }

    public function tinder($tinder)
    {
        if (isset($tinder) && is_numeric($tinder) && $tinder == "1")
            $this->tinder = $tinder;

        return $this;
    }    

    public function orderByAsc($order)
    {
        return $this->orderByWithDirection($order, "ASC");
    }
    
    public function orderByDesc($order)
    {
        return $this->orderByWithDirection($order, "DESC");
    }

    private function orderByWithDirection($order, $direction)
    {
        if (isset($order) && !empty($order))
        {

            $this->orderBy = "{$order} {$direction}";
        }

        return $this;
    }

    protected function getColumnsForCount()
    {
        return "1";
    }
    
    private function setColunas()
    {
        if ($this->isCount)
            $this->colecaoDeColunas = $this->getColumnsForCount();
        else if (is_array($this->colunas) && count($this->colunas) >= 1)
            $this->colecaoDeColunas = implode(", ", $this->colunas);
        else 
            $this->colecaoDeColunas = "*";        
        
        $this->colecaoDeColunas = " {$this->colecaoDeColunas} ";
    }

    private function getJoins()
    {
        $joins = trim(implode(" ", $this->joins));
        
        return !empty($joins) ? "{$joins} " : "";
    }

    private function setFiltroCliente()
    {
        if (isset($this->cliente) && !empty($this->cliente))
            array_push($this->filtrosWhere, "`p`.`clientes` LIKE '%{$this->cliente}%'");
    }

    private function setFiltroPalavras()
    {
        if (isset($this->palavras) && is_array($this->palavras) && count($this->palavras) > 0) {
            $palavras = array();

            foreach ($this->palavras as $palavra) {
                $palavra = str_replace("'", "''", trim($palavra));

                array_push($palavras, "{$this->colunaPalavras()} LIKE '%{$palavra}%'");
            }
            $palavrasFiltro = implode(" OR ", $palavras);

            array_push($this->filtrosWhere, "({$palavrasFiltro})");
        }
    }

    private function setFiltroIniciandoEm()
    {
        if (isset($this->dataDeInicio) && !is_null($this->dataDeInicio) && !(isset($this->idDoArquivo) && is_numeric($this->idDoArquivo)))
            array_push($this->filtrosWhere, "`p`.`data` >= '{$this->dataDeInicio} 00:00:00'");
    }

    private function setFiltroTerminandoEm()
    {
        if (isset($this->dataDoFim) && !is_null($this->dataDoFim) && !(isset($this->idDoArquivo) && is_numeric($this->idDoArquivo)))
            array_push($this->filtrosWhere, "`p`.`data` <= '{$this->dataDoFim} 23:59:59'");
    }

    private function setFiltroIdDoArquivo()
    {
        if (isset($this->idDoArquivo) && is_numeric($this->idDoArquivo))
            array_push($this->filtrosWhere, "`p`.`id_evento_arquivo` = {$this->idDoArquivo}");
    }

    private function setFiltroPrograma()
    {
        if (isset($this->programa) && is_numeric($this->programa))
            array_push($this->filtrosWhere, "`p`.`id_programa` = {$this->programa}");
    }

    private function setFiltroEmissora()
    {
        if (isset($this->emissora) && is_numeric($this->emissora))
            array_push($this->filtrosWhere, "`p`.`id_emissora` = {$this->emissora}");
    }

    private function setFiltroPraca()
    {
        if (isset($this->praca) && is_numeric($this->praca))
            array_push($this->filtrosWhere, "`emi`.`id_praca` = {$this->praca}");        
    }

    private function setFiltroMidia()
    {
        if (isset($this->midia) && is_numeric($this->midia)) {
            array_push($this->filtrosWhere, "(`pro`.`id` IS NULL OR `pro`.`id_meio_comunicacao` = {$this->midia})");
            array_push($this->filtrosWhere, "(`emi`.`id` IS NULL OR `emi`.`id_veiculo` = {$this->midia})");
        }
    }

    private function setIdsMaioresQue()
    {
        if (isset($this->idsMaioresQue) && is_numeric($this->idsMaioresQue)) 
            array_push($this->filtrosWhere, "`p`.`id` > {$this->idsMaioresQue}");
    }

    private function setStatusDasNotificacoes()
    {
        if (isset($this->statusDasNotificacoes) && !empty($this->statusDasNotificacoes)) 
            array_push($this->filtrosWhere, "IFNULL(`p`.`status`, 1) IN ({$this->statusDasNotificacoes})");
    }

    private function setStatusDosEventos()
    {
        if (isset($this->statusDosEventos) && !empty($this->statusDosEventos)) 
            array_push($this->filtrosWhere, "IFNULL(`eve`.`status`, 1) = {$this->statusDosEventos}");
    }

    private function setTinder()
    {
        if (isset($this->tinder) && $this->tinder == "1") {
            array_push($this->filtrosWhere, "IFNULL(`p`.`status`, 1) IN ({$this->tinder})");
            array_push($this->filtrosWhere, "IFNULL(`eve`.`status`, 1) IN ({$this->tinder})");
        }
        else {
            /*
            array_push($this->filtrosWhere, "IFNULL(`p`.`status`, 1) IN (1,3,4)");
            array_push($this->filtrosWhere, "IFNULL(`eve`.`status`, 1) IN (1,2)");
            */
            $this->setStatusDosEventos();
        }
    }

    private function setFiltroStatus()
    {
        if (!isset($this->statusDasNotificacoes) && !isset($this->statusDosEventos))
            $this->setTinder();
        else
        {
            $this->setStatusDasNotificacoes();
            $this->setStatusDosEventos();
        }
    }
    
    private function getFiltrosWhere()
    {
        $this->setFiltroCliente();
        $this->setFiltroPalavras();
        $this->setFiltroIniciandoEm();
        $this->setFiltroTerminandoEm();
        $this->setFiltroIdDoArquivo();
        $this->setFiltroPrograma();
        $this->setFiltroEmissora();
        $this->setFiltroPraca();
        $this->setFiltroMidia();
        $this->setIdsMaioresQue();
        $this->setFiltroStatus();

        if (count($this->filtrosWhere) < 1) 
            return "";

        $filtros = trim(implode(" AND ", $this->filtrosWhere));

        return "WHERE {$filtros}";
    }

    private function orderByClause()
    {
        if (isset($this->orderBy) && !empty($this->orderBy))
            return " ORDER BY {$this->orderBy}";
    }

    private function tabelaComAlias()
    {
        return " {$this->tabela} AS {$this->alias} ";
    }

    public function sql()
    {
        $this->setColunas();

        return "SELECT{$this->colecaoDeColunas}FROM{$this->tabelaComAlias()}{$this->getJoins()}{$this->getFiltrosWhere()}{$this->orderByClause()}";
    }
}