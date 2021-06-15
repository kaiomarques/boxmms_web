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
              <label>Adicionar Clientes</label>
              <multiselect 
                name="id_cliente"
                v-model="id_cliente" 
                :options="clientes" 
                placeholder = "Selecione..."
                :loading="!cliente_enabled"
                :internal-search="false"
                :multiple = true
                label="name"
                language="pt-BR"
                track-by="key"
              ></multiselect >
            </div>
          </div>
        </div>


        <div class="row">
          <div class="col-xs-3">
            <div class="form-group"  style="float:left">
              <label>Adicionar Mídia</label>
              <multiselect 
                id="midia"
                name="id_midia"
                v-model="id_midia"
                :options="midias"
                @select="recarregarPorMidia"
                placeholder = "Selecione..."
                :loading="!midia_enabled"
                :internal-search="false"
                label="name"
                language="pt-BR"
                track-by="id_midia"
                style="width:200px"
              ></multiselect >
            </div>
          </div>
          <div class="col-xs-3">
            <div class="form-group" style="float:right">
              <label>Adicionar Praça</label>
              <multiselect 
                id="praca"
                name="id_praca"
                v-model="id_praca"
                :options="pracas"
                @select="recarregarPorPracas"
                placeholder = "Selecione..."
                :loading="!praca_enabled"
                :internal-search="false"
                label="name"
                language="pt-BR"
                track-by="id_praca"
                style="width:220px"
              ></multiselect >
            </div>
          </div>
        </div>


        <!--div class="row">

        </div-->


        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Adicionar Emissoras</label>
              <multiselect 
                id="emissora"
                name="id_emissora"
                v-model="id_emissora"
                :options="emissoras"
                :multiple = true
                @input="checarEmissorasSelecionada"
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
      id_midia: [],
      id_campanha:[],
      id_praca: [],
      s3_path: "",
      nome_enabled: false,
      midia_enabled: false,
      praca_enabled: false,
      spot_enabled: false,
      cliente_enabled: false,
      emissora_enabled: true,
      show_info_message: false,
      campanhas: [],
      pracas: [],
      canais: [],
      spots: [],
      clientes: [],
      midias: [],
      emissoras: [],

      emissoras_selecionadas: [],
      spots_selecionados: [],
      clientes_selecionados: [],
      midia_selecionada: [],
      praca_selecionada: [],

      filtro_dtinicio: "",
      filtro_dtfim: "",
      carregando_spots: false,
      carregando_clientes: false,
      carregando_midia: false,
      carregando_canais: false,
      carregando_pracas: false,
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
      opcao_13: { id_emissora: -99, name: "[TODAS AS EMISSORAS DE TV]" },
      opcao_14: { id_emissora: -99, name: "[TODAS AS EMISSORAS DE RÁDIO]" }, 

      
      //todas_praca_opcao: { id_emissora: -99, name: "[TODAS AS PRAÇAS]" },
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
      this.nome_enabled = true;
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

      self.cliente_enabled = true;
      if (self.id_load) {
          if(self.clientes_selecionados.length > 0) {
            $.each(self.clientes_selecionados, function (index,id_cliente) {
              self.id_cliente.push(self.clientes.find(cliente => cliente.id_cliente === id_cliente));
            });
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

    obj_api.call("pracas", "GET", null, function(response) {
      $.each(response.data,function (index, value) {
        self.pracas.push({ id_praca: value.id, name: value.nome });
      });
      self.praca_enabled = true;
      if (self.id_load) {
          if(self.praca_selecionada.length > 0) {
            self.id_praca = self.praca_selecionada;
          }
      } else {
        self.id_praca = null;
        self.praca_enabled = true;
      }
    });

    obj_api.call("midias", "GET", null, function(response) {
      $.each(response.data,function (index, value) {
        self.midias.push({ id_midia: value.id, name: value.nome });
      });
      self.midia_enabled = true;
      if (self.id_load) {
          if(self.midia_selecionada.length > 0) {
            self.id_midia = self.midia_selecionada;
          }
      } else {
        self.id_midia = null;
        self.midia_enabled = true;
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

          $.each(response.cliente_data, function (index,value) {
            self.clientes_selecionados.push(value.key);
          });

          if(response.data[0].id_praca != null) {
            self.praca_selecionada = response.data[0].id_praca;
          }

          if(response.data[0].id_midia != null) {
            self.midia_selecionada = response.data[0].id_midia;
          }

          if(response.data[0].todos != null) {
            self.todos = response.data[0].todos;
          }

          if(self.clientes.length > 0) {
            $.each(self.clientes_selecionados, function (index,id_cliente) {
              self.id_cliente.push(self.clientes.find(cliente => cliente.id_cliente === id_cliente));
            });
          }

          if(self.emissoras.length > 0) {
            if(self.todos == 1) {
              if(self.id_midia != null && self.id_midia != 0) {
                if(self.id_midia.id_midia == 13) self.id_emissora = self.opcao_13;
                if(self.id_midia.id_midia == 14) self.id_emissora = self.opcao_14;
              }
            } else {
              $.each(self.emissoras_selecionadas, function (index,id_emissora) {
                self.id_emissora.push(self.emissoras.find(emissora => emissora.id_emissora === id_emissora));
              });
            }
          }
          
          if(self.spots.length > 0) {
            $.each(self.spots_selecionados, function (index,id_spot) {
              self.id_spot.push(self.spots.find(spot => spot.id_spot === id_spot));
            });
          }

          if(self.pracas.length > 0 && self.id_praca != null && self.praca_selecionada != 0) {
            self.id_praca = self.pracas.find(praca => praca.id_praca === self.praca_selecionada);
            self.recarregarPorPracas(self.id_praca);
          }

          if(self.midias.length > 0 && self.id_midia != null ) {
              self.id_midia = self.midias.find(midia => midia.id_midia === self.midia_selecionada);
              self.recarregarPorMidia(self.id_midia);
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
      var todos = 0;

      if(
        self.id_emissora.find(emissora => emissora == self.opcao_13 ) !== undefined ||
        self.id_emissora.find(emissora => emissora == self.opcao_14 ) !== undefined 
      ) {
        todos = 1;
      }

      var arquivo = new FormData();
      arquivo.append('nome', this.nome);
      arquivo.append('id', this.id_load);
      arquivo.append('id_emissoras', JSON.stringify(this.id_emissora));
      arquivo.append('id_clientes', JSON.stringify(this.id_cliente));
      arquivo.append('id_spots', JSON.stringify(this.id_spot));
      arquivo.append('id_midia', JSON.stringify(this.id_midia));
      arquivo.append('id_praca', JSON.stringify(this.id_praca));
      arquivo.append('data_inicial', Util.BrDateToUS($("#filtro_dtinicio").val()));
      arquivo.append('data_final', Util.BrDateToUS($("#filtro_dtfim").val()));
      arquivo.append('todos', todos);

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
    },
    checarEmissorasSelecionada(opcao) {
      var self = this;
      var opcao_selecionada = null;
      if(opcao != null) {
        opcao_selecionada = opcao[opcao.length - 1];
      }

      if(self.id_emissora != null) {
        if(opcao_selecionada != null && opcao_selecionada == self.opcao_13) {
          self.id_emissora = [self.opcao_13];
        } else if(opcao_selecionada != null && opcao_selecionada == self.opcao_14) {
          self.id_emissora = [self.opcao_14];
        } else {
          for( var i = 0; i < self.id_emissora.length; i++)
                if ( self.id_emissora[i] == self.opcao_13 || self.id_emissora[i] == self.opcao_14) 
                    self.id_emissora.splice(i, 1);
        }
      } 
    },
    recarregarPorPracas(opcao) {
      var self = this;
      self.emissoras = [];
      self.emissora_enabled = false;
      var id_praca = opcao.id_praca;
      self.id_midia = null;
      self.id_emissora = null;

      obj_api.call("emissoras/porPraca/" + id_praca, "GET", null, function(response) {   
        $.each(response.data,function (index, value) {
          self.emissoras.push({ id_emissora: value.id, name: value.nome });
        });

        self.emissora_enabled = true;
        if (self.id_load) {
            if(self.emissoras_selecionadas.length > 0) {
              self.id_emissora = [];
              $.each(self.emissoras_selecionadas, function (index,id_emissora) {
                self.id_emissora.push(self.emissoras.find(emissora => emissora.id_emissora === id_emissora));
              });
            }
        } else {
          self.id_emissora = null;
          self.emissora_enabled = true;
        }
      });
    },
    recarregarPorMidia(opcao) {
      var self = this;
      self.emissoras = [];
      self.emissora_enabled = false;
      var id_midia = opcao.id_midia;
      self.id_praca = null;
      self.id_emissora = null;

      obj_api.call("emissoras/porMidia/" + id_midia, "GET", null, function(response) {
        if(id_midia == 13) {
          self.emissoras.push(self.opcao_13); 
        }
        if(id_midia == 14) {
          self.emissoras.push(self.opcao_14);
        }

        $.each(response.data,function (index, value) {
          self.emissoras.push({ id_emissora: value.id, name: value.nome });
        });

        self.emissora_enabled = true;
        if (self.id_load) {
            if(self.todos == 1) {
              if(self.id_midia != null && self.id_midia != 0) {
                if(self.id_midia.id_midia == 13) self.id_emissora = self.opcao_13;
                if(self.id_midia.id_midia == 14) self.id_emissora = self.opcao_14;
              }
            } else {
              if(self.emissoras_selecionadas.length > 0) {
                self.id_emissora = [];
                $.each(self.emissoras_selecionadas, function (index,id_emissora) {
                  self.id_emissora.push(self.emissoras.find(emissora => emissora.id_emissora === id_emissora));
                });
              }
            }
        } else {
          self.id_emissora = null;
          self.emissora_enabled = true;
        }
      });      
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
