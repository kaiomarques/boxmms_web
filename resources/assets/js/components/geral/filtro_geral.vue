<template>
  <div v-if="data_filtro != null ">
    <div class="form-row" style="padding-top:10px;">
      <div class="col-md-2">
        <div class="form-group">
          <label>Data Início</label>
          <input
            type="text"
            class="form-control"
            id="filtro_dtinicio"
            v-model="data_filtro.filtro_dtinicio"
            placeholder="Data Início"
          />
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          <label>Data Fim</label>
          <input
            type="text"
            class="form-control"
            id="filtro_dtfim"
            v-model="data_filtro.filtro_dtfim"
            placeholder="Data Fim"
          />
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <label>Cliente</label>
          <input
            type="text"
            class="form-control"
            id="filtro_nome_cliente"
            v-model="data_filtro.cliente_nome"
            placeholder="Nome do cliente"
          />
        </div>
      </div>

      <div class="col-md-2" v-if="show_palavras">
        <div class="form-group">
          <label>Palavras Chaves</label>
          <input
            type="text"
            class="form-control"
            name="filtro_palavra"
            id="filtro_palavra"
            v-model="data_filtro.palavras"
            placeholder="Palavra Chave"
          />
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          <label>Status</label>
          <select 
          
            id="id_evento_status"
            name="id_evento_status"
            v-model="data_filtro.id_evento_status"
            class="form-control"
            
            >
            <option
              v-for="(item, index) in evento_status"
              :key="index"
              :value="item.id"
            >{{item.nome}}</option>
          </select>
        </div>
      </div>
    </div>

    <div class="form-row">
      <div class="col-md-2">
        <div class="form-group">
          <label>Mídia</label>
          <select
            v-model="data_filtro.id_veiculo"
            v-on:change="change_veiculo"
            class="form-control"
          >
            <option v-for="(item, index) in midias" :key="index" :value="item.id">{{item.nome}}</option>
          </select>
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          <label>Praça</label>
          <select
            id="id_praca"
            name="id_praca"
            v-model="data_filtro.id_praca"
            v-on:change="change_praca" 
            class="form-control">
            <option v-for="(item, index) in pracas" :key="index" :value="item.id">{{item.nome}}</option>
          </select>
        </div>
      </div>

      <div class="col-md-2">
        <div class="form-group">
          <label>Emissora</label>
          <select
            id="id_emissora"
            name="id_emissora"
            v-model="data_filtro.id_emissora"
            v-on:change="change_emissora"
            class="form-control"
          >
            <option v-for="(item, index) in emissoras" :key="index" :value="item.id">{{item.nome}}</option>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label>Programa</label>
          <select
            id="id_programa"
            name="id_programa"
            v-model="data_filtro.id_programa"
            class="form-control"
          >
            <option v-for="(item, index) in programas" :key="index" :value="item.id">{{item.nome}}</option>
          </select>
        </div>
      </div>
    </div>
    <div class="form-row" v-if="show_status">
      <div class="col-md-1">
        <div class="form-group">
          <label>Status</label>
          <select
            id="filtro_status"
            name="filtro_status"
            v-model="data_filtro.status"
            class="form-control"
          >
            <option value="-1">--</option>
            <option value="1">Mat. Criada</option>
            <option value="0 ">Rascunho</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    mostra_add: {
      type: Boolean,
      default() {
        return false;
      }
    },
    show_status: {
      type: Boolean,
      default() {
        return false;
      }
    },
    show_palavras: {
      type: Boolean,
      default() {
        return false;
      }
    },
    onSearch: {
      type: Function
    },
    onNovo: {
      type: Function,
      default() {
        return null;
      }
    },

    data_filtro: {
      default() {
        return null;
      },
      type: Object
    }
  },
  model: {
    prop: "data_filtro",
    event: "input"
  },
  watch: {
    data_filtro: {
      deep: true,
      handler: function(val) {
        this.$emit("input", this.data_filtro);
      }
    }
  },
  data: function() {
    return {
      show_new_button: true,

      item_filho: null,
      clientes: [],
      pracas: [],
      midias: [
        { id: -1, nome: "--TODAS--"},
        { id: 13, nome: "TV" },
        { id: 14, nome: "Rádio" }
      ],
      id_veiculo: null,
      id_praca: null,
      id_evento_status: null,
      programas: [],
      programas: [],
      evento_status: [],
      palavras: null,
      loading: false,

      button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
    };
  },
  methods: {
    open_form() {
      if (this.onNovo != null) {
        this.onNovo();
      }
    },
    exibirCarregamentoDeProgramas() {
      this.programas = [];
      this.programas.push({id: -1, nome:"Carregando..."})
      this.data_filtro.id_programa = -1;
    },
    carregarProgramas() {
      var self = this;
      var data_programa = {};
      this.exibirCarregamentoDeProgramas();

      if (
        this.data_filtro.id_emissora != null &&
        this.data_filtro.id_emissora != "" &&
        this.data_filtro.id_emissora != " "
      ) {
        data_programa["id_emissora"] = this.data_filtro.id_emissora;
      }
      if (this.data_filtro.id_veiculo) {
        data_programa["veiculo_id"] = this.data_filtro.id_veiculo;
      }
      if (this.data_filtro.id_praca) {
        data_programa["praca_id"] = this.data_filtro.id_praca;
      }

      obj_api.call("programas", "POST", data_programa, function(retorno) {        
        
        retorno.data.unshift({id: -1, nome:"--TODOS--"});

        self.programas = retorno.data;
        self.data_filtro.id_programa = -1;
      });

    },
    exibirCarregamentoDeEmissoras() {
      this.emissoras = [];
      this.emissoras.push({id: -1, nome: "Carregando..."});
      this.data_filtro.id_emissora = -1;
    },
    carregarEmissoras() {
      var self = this;
      this.exibirCarregamentoDeEmissoras();

      var data_emissora = {};
      data_emissora["veiculo_id"] = this.data_filtro.id_veiculo;
      data_emissora["praca_id"] = this.data_filtro.id_praca | "";

      obj_api.call("emissoras", "POST", data_emissora, function(retorno) {
        self.emissoras = retorno.data;
        self.emissoras.unshift({id: -1, nome: "--TODAS--"});
        self.data_filtro.id_emissora = -1;
        self.change_emissora();
      });
    },
    carregarPracas() {
      var self = this;
      self.pracas = [];
      self.pracas.push({id: -1, nome: "Carregando..."});      
      self.data_filtro.id_praca = -1;

      obj_api.call("pracas", "GET", {}, function(retorno) {
        self.pracas = retorno.data;
        self.pracas.unshift({id: -1, nome:"--TODAS--"});
        self.data_filtro.id_praca = -1;
        self.change_praca();
      });
    },

    carregarStatus() {
      var self = this;
      self.evento_status = [];
      self.evento_status.push({ id: -1, nome: "Carregando..." });
      self.data_filtro.id_evento_status = -1;

      obj_api.call("eventos_status", "GET", null, function(retorno) {
        self.evento_status = retorno.data;
        self.evento_status.unshift({ id: -1, nome: "--TODAS--" });
        self.data_filtro.id_evento_status = -1;
      });
    },

    change_veiculo() {
      this.carregarEmissoras();
    },
    change_praca() {
      this.carregarEmissoras();
    },
    change_emissora() {
      this.carregarProgramas();
    }
  },
  mounted() {
    let self = this;

    if (this.data_filtro == null) {
      this.data_filtro = {
        id_cliente: null,

        nome_cliente: "",
        id_praca: null,
        id_veiculo: null,
        id_programa: null,
        id_emissora: null,
        if_evento_status: null,
        palavras: "",
        filtro_dtinicio: "",
        filtro_dtfim: ""
      };
    }

    self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';
    this.data_filtro.id_veiculo = -1;
    this.exibirCarregamentoDeEmissoras();
    this.exibirCarregamentoDeProgramas();
    this.carregarPracas();
    this.carregarStatus();

    $(document).ready(function() {
      obj_editor.loadCalendar("#filtro_dtinicio");
      obj_editor.loadCalendar("#filtro_dtfim");
      console.log("URL: " + window.URL_API + "eventos_arquivos2");
      console.log("Type: " + self.type);
    });
  }
};
</script>