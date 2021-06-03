<template>
  <div>
    <section class="container">
      <section class="col-lg-9" style="padding-left: 0px; margin-left: 0px">
        <h1 style="padding-left: 0px; margin-left: 0px"></h1>
        <ol class="breadcrumb" style="display: none">
          <li>
            <a href="#">
              <i class="fa fa-dashboard"></i> Home
            </a>
          </li>
          <li>
            <a href="#">Forms</a>
          </li>
          <li class="active">General Elements</li>
        </ol>
      </section>

      <section class="col-lg-3" style="padding-top: 30px">
        <a href="#" v-on:click="botao_voltar" v-if="botao_voltar_visible">
          <i class="fa fa-arrow-left"></i> Voltar para lista
        </a>
      </section>
    </section>

    <div class="row">
      <div class="col-xs-12">
        <div v-if="show_message == 'on' " class="alert alert-success">{{message_text}}</div>
        <div v-if="show_info_message" class="alert alert-warning">{{message_text}}</div>
      </div>
    </div>

    <div class="box">
      <div class="box-header with-border">
        <h3>Spot</h3>
      </div>
      <div class="box-body">
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Nome</label>
              <input
                id="nome"
                name="nome"
                v-model="nome"
                type="text"
                class="form-control"
                :disabled="!palavras_texto_enabled"
              />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Cliente</label>
              <select
                id="id_cliente"
                name="id_cliente"
                v-model="id_cliente"
                class="form-control"
                v-on:change="cliente_alterado"
                :disabled="!cliente_enabled"
              >
                <option v-if="carregando_cliente" :value="0">Carregando...</option>
                <option
                  v-for="(item, index) in clientes"
                  :key="index"
                  :value="item.id"
                >{{item.nome}}</option>
              </select>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Arquivo</label>
              <input type="file" name="s3_path" />
            </div>
          </div>
        </div>


        <div class="row">
          <div class="col-xs-12">
            <div class="form-group">
              <div class="btn-group pull-right">
                <button
                  type="submit"
                  class="btn btn-info"
                  v-bind:disabled="disableButton"
                  v-on:click="salvar()"
                >{{publicar_titulo}}</button>

                <!-- <button
                  type="submit"
                  class="btn btn-danger"
                  v-bind:disabled="disableButton"
                  v-if="id!='' && parseInt(id) > 0"
                  v-on:click="excluir()"
                >Excluir</button> -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import checkbox_int from "../../library/checkbox/checkbox_int";

export default {
  props: [
    "id_load",
    "post_type",
    "show_back_button",
    "onBack",
    "nome_cliente",
    "onSave",
    "onEdit"
  ],
  components: {
    checkbox_int
  },
  data: function() {
    return {
      id: "",
      id_cliente: "",
      nome: "",
      carregando_cliente: false,
      cliente_enabled: true,
      show_info_message: false,
      clientes: [],
      carregando_topico: false,
      topicos: [],
      topicos_enabled: false,
      palavras_texto: "",
      palavras_texto_enabled: true,
      palavras_carregadas: [],
      id_spot: "",
      consulta_comum: "",
      consulta_elastic: "",
      grid_palavras: null,
      disableButton: false,
      publicar_titulo: "Salvar",
      titulo_acao: "Cliente Configuração",
      botao_voltar_visible: false,

      show_message: "off",
      message_text: "",
      message_type: "success",
      interval_message: null,

      loading: false
    };
  },
  mounted() {
    let self = this;

    if (this.show_back_button != null && this.show_back_button != undefined) {
      this.botao_voltar_visible = this.show_back_button;
    }

    if (this.id_load == null || this.id_load == "") {
      this.id_load = 0;
      this.nome_cliente = "";
    }

    this.loading = true;
    this.carregando_cliente = true;
    this.id_cliente = 0;
    this.cliente_enabled = false;

    obj_api.call("clientes", "GET", { todos: 1 }, function(response) {
      self.clientes = response.data;
      self.loading = false;
      self.carregando_cliente = false;
      if (self.id_load) {
        self.id_cliente = self.id_load;
        self.cliente_alterado();
      } else {
        self.id_cliente = null;
        self.cliente_enabled = true;
      }
    });
  },
  methods: {
    isAttributeEquals(obj, obj2, attribute) {
      return (
        attribute &&
        obj[attribute] &&
        obj2[attribute] &&
        obj[attribute] == obj2[attribute]
      );
    },
    collectionContains(collection, item, attribute) {
      if (!collection || !Array.isArray(collection)) return false;

      for (var i = 0; i < collection.length; i++) {
        if (
          this.isAttributeEquals(collection[i], item, attribute) ||
          collection[i] == item
        ) {
          return true;
        }
      }

      return false;
    },
    criaSet(collection, attribute) {
      if (!collection || !Array.isArray(collection)) return [];

      var result = [];

      for (var i = 0; i < collection.length; i++) {
        if (!this.collectionContains(result, collection[i], attribute)) {
          result.push(collection[i]);
        }
      }

      return result;
    },
    palavras() {
      if (!this.palavras_texto) return [];

      return this.criaSet(this.palavras_texto.split(","));
    },
    foiRemovida(palavra) {
      return this.collectionContains(
        this.palavrasRemovidas(),
        { id: 0, nome: palavra },
        "nome"
      );
    },
    foiAdicionada(palavra) {
      return this.collectionContains(
        this.palavrasAdicionadas(),
        { id: 0, nome: palavra },
        "nome"
      );
    },
    palavrasRemovidas() {
      if (!this.palavras_carregadas) return [];

      var palavrasAtuais = this.palavras();
      var removidas = [];

      for (var i = 0; i < this.palavras_carregadas.length; i++) {
        var found = false;

        for (var j = 0; j < palavrasAtuais.length; j++) {
          if (palavrasAtuais[j] == this.palavras_carregadas[i].nome) {
            found = true;
            break;
          }
        }

        if (!found) removidas.push(this.palavras_carregadas[i]);
      }

      return this.criaSet(removidas, "nome");
    },
    palavrasAdicionadas() {
      if (!this.palavras_carregadas && !this.palavras_texto) return [];

      var palavrasAtuais = this.palavras();
      var adicionadas = [];

      for (var j = 0; j < palavrasAtuais.length; j++) {
        var found = false;

        for (var i = 0; i < this.palavras_carregadas.length; i++) {
          if (palavrasAtuais[j] == this.palavras_carregadas[i].nome) {
            found = true;
            break;
          }
        }

        if (!found) adicionadas.push({ id: null, nome: palavrasAtuais[j] });
      }

      return this.criaSet(adicionadas, "nome");
    },
    palavrasExistentes() {
      if (!this.palavras_carregadas) return [];

      var palavrasAtuais = this.palavras();
      var existentes = [];

      for (var j = 0; j < palavrasAtuais.length; j++) {
        if (
          !this.foiRemovida(palavrasAtuais[j]) &&
          !this.foiAdicionada(palavrasAtuais[j])
        ) {
          for (var i = 0; i < this.palavras_carregadas.length; i++) {
            if (this.palavras_carregadas[i].nome == palavrasAtuais[j]) {
              existentes.push(this.palavras_carregadas[i]);
              break;
            }
          }
        }
      }

      return this.criaSet(existentes, "nome");
    },
    clienteSelecionado() {
      if (!this.id_cliente || !this.clientes) return null;

      for (var i = 0; i < this.clientes.length; i++) {
        if (this.clientes[i].id == this.id_cliente) return this.clientes[i];
      }

      return null;
    },
    topico_alterado() {
      var self = this;

      if (!self.id_spot) return;

      self.palavras_texto = "Carregando...";
      self.palavras_texto_enabled = false;

      var url = `topicos/${self.id_spot}/palavras`;

      obj_api.call(url, "GET", {}, function(response) {
        self.palavras_carregadas = response.data;

        var palavras = [];

        for (var i = 0; i < self.palavras_carregadas.length; i++) {
          palavras.push(self.palavras_carregadas[i].nome);
        }

        self.palavras_texto = palavras.join();
        self.palavras_texto_enabled = true;
      });
    },
    cliente_alterado() {
      var self = this;

      if (!self.id_cliente) {
        return;
      }

      var cliente = self.clienteSelecionado();
      self.id_load = cliente.id;
      self.nome_cliente = cliente.nome;
      self.topicos = [];
      self.carregaTopicos();
    },
    carregaTopicos() {
      var self = this;

      if (!self.id_cliente) {
        return;
      }

      var cliente = self.clienteSelecionado();

      self.loading = true;
      self.carregando_topico = true;
      self.id_spot = 0;
      self.topicos_enabled = false;
      self.disableButton = true;

      obj_api.call(
        "midiaclip_cadastros?acao=topicos&id_cliente=" + cliente.id,
        "GET",
        {},
        function(response) {
          self.carregando_topico = false;
          self.topicos = response.data;
          self.topicos_enabled = true;
          self.loading = false;
          self.id_spot = null;

          self.disableButton = false;
        }
      );
    },
    carregaForm(item) {
      var self = this;
      self.id = item.id;
      self.id_cliente = item.id_cliente;
      self.consulta_comum = item.consulta_comum;
      self.consulta_elastic = item.consulta_elastic;
    },
    editar_elastic() {
      console.log("onEdit?", this.onEdit);
      if (this.onEdit != null) {
        this.onEdit("editar_elastic");
      }
    },
    editar_palavra() {
      if (this.onEdit != null) {
        this.onEdit("editar_palavra");
      }
    },

    exibe_error(tipo) {},

    getClassFirstSection() {
      if (this.id != "") return "col-xs-9";

      return "col-xs-12";
    },

    validar() {
      return true;
    },

    botao_voltar() {
      var self = this;

      if (this.onBack != null && this.onBack != undefined) {
        console.log("clique no voltar!");
        this.onBack(self);
      }
    },
    salvar() {
      //if (!this.validar()) return false;

      let self = this;
      var data = {
        cliente_id: this.id_cliente,
        nome: this.nome,
        spot_id : this.id_spot,
        palavras_adicionadas: this.palavrasAdicionadas(),
        palavras_removidas: this.palavrasRemovidas(),
        palavras_existentes: this.palavrasExistentes()
      };

      var url = `lista_spot/${this.id_spot}/palavras`;

      self.show_info_message = true;
      self.message_text = "Salvando palavras-chave. Por favor, aguarde.";

      obj_api.call(
        "topicos/" + this.id_spot + "/palavras",
        "PUT",
        data,
        function(retorno) {
          console.log("========================================");
          console.log("Response");
          console.log(`Data: ${JSON.stringify(retorno)}`);
          console.log("========================================");

          self.show_info_message = false;
          self.show_message = "on";
          self.message_text = "Palavras-chave salvas com sucesso!";
          self.interval_message = setInterval(function() 
          {
             self.show_message = "off";
             //self.botao_voltar();
             clearInterval(self.interval_message);
          }, 6000);
          self.topico_alterado();
          
        }
      );
    },
    salvar_old(tipo) {
      if (!this.validar()) return false;

      let self = this;
      var url = "cliente_configuracao";
      console.log("url: " + window.URL_API + url);
      var method = "POST";

      if (this.id != null && this.id != "") {
        method = "PUT";
        url = url + "/" + this.id;
      }

      var data = {
        id: this.id,
        id_cliente: this.id_cliente,
        nome: this.nome,
        consulta_comum: this.consulta_comum,
        consulta_elastic: this.consulta_elastic
      };

      var fn_return = function(retorno) {
        var item = retorno.item;

        self.carregaForm(item);

        self.show_message = "on";
        self.message_text = "Cliente Configuração salvo com sucesso!";

        self.interval_message = setInterval(self.clear_message, 6000);

        if (self.onSave != null && self.onSave != undefined) {
          self.onSave(retorno, "save");
        }
      };

      obj_api.call(url, method, data, fn_return);
    },

    clear_message() {
      this.show_message = "off";
      clearInterval(this.interval_message);
    },

    excluir() {
      let self = this;
      var fn_return = function(retorno, tipo) {
        self.onSave(retorno, tipo);
        self.botao_voltar();
      };
      obj_api.call_delete("cliente_configuracao", this.id, fn_return);
    }
  }
};
</script>
