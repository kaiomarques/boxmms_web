<template>
  <div v-if="data_filtro != null ">
    <div class="form-row" style="padding-top:10px;">
      <div class="col-md-2">
        <div class="form-group">
          <label>Data Início</label>
          <input
            name="dtinicio"
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
            name="dtfim"
            type="text"
            class="form-control"
            id="filtro_dtfim"
            v-model="data_filtro.filtro_dtfim"
            placeholder="Data Fim"
          />
        </div>
      </div>
      </div>

    <div class="form-row">
      <div class="col-md-2">
        <div class="form-group">
          <label>Mídia</label>
          <select
            id = "veiculo_id"
            name = "veiculo_id"
            v-model="data_filtro.id_veiculo"
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
            class="form-control"
          >
            <option v-for="(item, index) in emissoras" :key="index" :value="item.id">{{item.nome}}</option>
          </select>
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          <label>Campanha</label>
          <select
            id="id_campanha"
            name="id_campanha"
            v-model="data_filtro.id_campanha"
            class="form-control"
          >
            <option v-for="(item, index) in campanhas" :key="index" :value="item.id">{{item.nome}}</option>
          </select>
        </div>
      </div>
            <div class="col-md-6">
        <div class="form-group">
          <label>Spot</label>
          <select
            id="id_spot"
            name="id_spot"
            v-model="data_filtro.id_spot"
            class="form-control"
          >
            <option v-for="(item, index) in spots" :key="index" :value="item.id">{{item.nome}}</option>
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
      emissoras: [],
      pracas: [],
      midias: [
        { id: -1, nome: "--TODAS--"},
        { id: 13, nome: "TV" },
        { id: 14, nome: "Rádio" }
      ],
      campanhas: [],
      spots: [],
      id_veiculo: null,
      id_praca: null,
      id_evento_status: null,
      id_campanha: null,
      id_spot: null,
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
      });
    },

    carregarCampanhas() {
      var self = this;
      self.campanhas = [];
      self.campanhas.push({ id: -1, nome: "Carregando..." });
      self.data_filtro.id_campanha = -1;

      obj_api.call("campanhas", "GET", null, function(retorno) {
        self.campanhas = retorno.data;
        self.campanhas.unshift({ id: -1, nome: "--TODAS--" });
        self.data_filtro.id_campanha = -1;
      });
    },

    carregarSpots() {
      var self = this;
      self.spots = [];
      self.spots.push({ id: -1, nome: "Carregando..." });
      self.data_filtro.id_spot = -1;

      obj_api.call("select_spots", "GET", null, function(retorno) {
        self.spots = retorno.data;
        self.spots.unshift({ id: -1, nome: "--TODAS--" });
        self.data_filtro.id_spot = -1;
      });
    },
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
    //this.exibirCarregamentoDeEmissoras();
    this.carregarEmissoras();
    this.carregarPracas();
    this.carregarCampanhas();
    this.carregarSpots();

    $(document).ready(function() {
      obj_editor.loadCalendar("#filtro_dtinicio");
      obj_editor.loadCalendar("#filtro_dtfim");
      console.log("URL: " + window.URL_API + "eventos_arquivos2");
      console.log("Type: " + self.type);
    });
  }
};
</script>