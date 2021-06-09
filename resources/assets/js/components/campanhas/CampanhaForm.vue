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

    <form class="box" enctype="multipart/form-data" method="PUT" action="#">
      <div class="box-header with-border">
        <h3>Campanha</h3>
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
                :disabled="!nome_enabled"
              />
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Spot</label>
              <multiselect 
                name="id_spot"
                v-model="id_spot"
                :options="spots"
                :multiple = true
                placeholder = "Selecione..."
                :loading="!cliente_enabled"
                :internal-search="false"
                label="name"
                language="pt-BR"
                track-by="key"
            ></multiselect>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Adicionar Cliente</label>
              <multiselect 
                name="id_cliente"
                v-model="id_cliente"
                :options="clientes"
                placeholder = "Selecione..."
                :loading="!cliente_enabled"
                :internal-search="false"
                label="name"
                language="pt-BR"
                track-by="key"
              ></multiselect >
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Adicionar Emissoras</label>
              <multiselect 
                name="id_emissora"
                v-model="id_emissora"
                :options="emissoras"
                :multiple = true
                placeholder = "Selecione..."
                :loading="!emissora_enabled"
                :internal-search="false"
                label="name"
                language="pt-BR"
                track-by="id_emissora"
              ></multiselect >
            </div>
          </div>
        </div>


        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Período Inicial</label>
              <input
                type="text"
                class="form-control"
                id="filtro_dtinicio"
                v-model="filtro_dtinicio"
                placeholder="Data Início"
              />
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Período Final</label>
              <input
                type="text"
                class="form-control"
                id="filtro_dtfim"
                v-model="filtro_dtfim"
                placeholder="Data Fim"
              />
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
                  v-on:click.prevent="salvar()"
                >{{publicar_titulo}}</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
  export default {
    // OR register locally
    components: { Multiselect },
    data () {
      return {
        value: null,
        options: ['list', 'of', 'options']
      }
    }
  }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>
<script>
import checkbox_int from "../../library/checkbox/checkbox_int";
import vueSelect from "../../library/vue-select/src/Select";
import Util from "../../library/Util";
//import vueMultiSelect from "../../library/vue-multiselect/src/Select";



export default {
  props: [
    "id_load",
    "post_type",
    "show_back_button",
    "onBack",
    //"nome",
    //"s3_path",
    "onSave",
    "onEdit"
  ],
  components: {
    checkbox_int, 
    vueSelect
  },
  data: function() {
    return {
      id: "",
      id_cliente: [],
      nome: "",
      id_emissora: [],
      id_campanha:[],
      s3_path: "",
      nome_enabled: true,
      spot_enabled: true,
      cliente_enabled: true,
      emissora_enabled: true,
      show_info_message: false,
      campanhas: [],
      canais: [],
      spots: [],
      clientes: [],
      emissoras: [],

      emissoras_selecionadas: [],
      spots_selecionados: [],
      cliente_selecionado: [],

      filtro_dtinicio: "",
      filtro_dtfim: "",
      carregando_spots: false,
      carregando_clientes: false,
      carregando_canais: false,
      carregando_emissoras: false,
      topicos_enabled: false,
      palavras_texto: "",
      palavras_texto_enabled: true,
      palavras_carregadas: [],
      id_spot: [],
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
      this.nome = "";
      this.s3_path = "";
    }

    this.loading = true;
    this.carregando_campanha = true;
    this.nome = "";
    this.s3_path = "";
    self.nome = "";

    obj_api.call("clientes", "GET", null, function(response) {

      $.each(response.data,function (index, value) {
        self.clientes.push({ key: value.id, name: value.nome });
      });

      self.loading = false;
      self.carregando_clientes = false;
      self.cliente_enabled = true;
      if (self.id_load) {
          if(self.cliente_selecionado.length > 0) {
            //alert("self.cliente_selecionado: " + self.cliente_selecionado);
            //alert("self.clientes.find: " + self.clientes.find);
            self.id_cliente = self.clientes.find(cliente => cliente.key === self.cliente_selecionado);
            alert("self.id_cliente" + JSON.stringify(self.id_cliente));
          }
      } else {
        self.id_cliente = null;
        self.cliente_enabled = true;
      }
    });

    obj_api.call("select_spots", "GET", null, function(response) {
      $.each(response.data,function (index, value) {
        self.spots.push({ key: value.id, name: value.nome });
      });

      self.loading = false;
      self.carregando_spots = false;
      self.spot_enabled = true;
      if (self.id_load) {
          if(self.spots_selecionados.length > 0) {
            $.each(self.spots_selecionados, function (index,id_spot) {
              self.id_spot.push(self.spots.find(spot => spot.id_spot === id_spot));
            });
          }
      } else {
        self.id_spot = null;
        self.spot_enabled = true;
      }
    });

    obj_api.call("emissoras", "GET", null, function(response) {
      $.each(response.data,function (index, value) {
        self.emissoras.push({ id_emissora: value.id, name: value.nome });
      });
      self.loading = false;
      self.carregando_emissoras = false;
      self.emissora_enabled = true;
      if (self.id_load) {
          if(self.emissoras_selecionadas.length > 0) {
            $.each(self.emissoras_selecionadas, function (index,id_emissora) {
              self.id_emissora.push(self.emissoras.find(emissora => emissora.id_emissora === id_emissora));
            });
          }
      } else {
        self.id_emissora = null;
        self.emissora_enabled = true;
      }
    });

    if(this.id_load != "" && this.id_load != null && this.id_load != undefined) {
      this.nome_enabled = false;
      obj_api.call("campanha/" + this.id_load, "GET", null, function(response) {
        self.loading = false;
        
        if (self.id_load) {
          self.nome = response.data[0].nome;

          $.each(response.emissora_data, function (index,value) {
            self.emissoras_selecionadas.push(value.id_emissora);
          });

          $.each(response.spot_data, function (index,value) {
            self.spots_selecionados.push(value.key);
          });

          self.cliente_selecionado = response.data[0].id_cliente;

          if(self.clientes.length > 0) {
            //alert("Opção 1: " + self.clientes);
            self.id_cliente = self.clientes.find(cliente => cliente.key === response.data[0].id_cliente);
            alert(JSON.stringify(self.id_cliente));
          }

          if(self.emissoras.length > 0) {
            $.each(self.emissoras_selecionadas, function (index,id_emissora) {
              self.id_emissora.push(self.emissoras.find(emissora => emissora.id_emissora === id_emissora));
            });
          }
          
          if(self.spots.length > 0) {
            $.each(self.spots_selecionados, function (index,id_spot) {
              self.id_spot.push(self.spots.find(spot => spot.id_spot === id_spot));
            });
          }

          self.filtro_dtinicio = Util.dateToBR(response.data[0].periodo_inicial);
          self.filtro_dtfim = Util.dateToBR(response.data[0].periodo_final);

          self.nome_enabled = true;
          self.spot_enabled = true;
          self.cliente_enabled = true;
        } else {
          self.id_cliente = null;
          self.nome_enabled = true;
        }
      });
    }
    obj_editor.loadCalendar("#filtro_dtinicio");
    obj_editor.loadCalendar("#filtro_dtfim");
  },
  'methods': {
    carregaForm(item) {
      var self = this;
      self.id = item.id;
      self.nome = item.nome;
      self.s3_path = item.s3_path;
    },
    botao_voltar() {
      var self = this;

      if (this.onBack != null && this.onBack != undefined) {
        console.log("clique no voltar!");
        this.onBack(self);
      }
    },
    salvar() {
      let self = this;
      var url = window.URL_API + "campanha/update/"+this.id_load;

      self.show_info_message = true;
      self.message_text = "Salvando mudanças. Por favor, aguarde.";

      var arquivo = new FormData();
      arquivo.append('nome', this.nome);
      arquivo.append('id', this.id_load);
      arquivo.append('id_emissoras', JSON.stringify(this.id_emissora));
      arquivo.append('id_cliente', JSON.stringify(this.id_cliente));
      arquivo.append('id_spots', JSON.stringify(this.id_spot));
      arquivo.append('data_inicial', Util.BrDateToUS($("#filtro_dtinicio").val()));
      arquivo.append('data_final', Util.BrDateToUS($("#filtro_dtfim").val()));

      $.ajax({
          url: url,
          data: arquivo,
          type: 'POST',
          headers: {
            "Authorization": window.API_AUTHORIZATION,
            "apiauth": window.API_MYAUTH
          },
          dataType: 'text', 
          cache: false,
          contentType: false,
          processData: false,

          success: function (data) {
            data = JSON.parse(data);
            if(data.msg == "sucesso!") {
              document.location.reload(true);
            } else {
              alert("Erro");
            }
          }
      });


      /*obj_api.callFormData(
        url,
        "PUT",
        data,
        function(retorno) {
          alert("Voltou aqui");
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
      );*/
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
