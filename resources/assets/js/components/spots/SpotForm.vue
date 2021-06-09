<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.jquery.min.js"></script>
<link href="https://cdn.rawgit.com/harvesthq/chosen/gh-pages/chosen.min.css" rel="stylesheet"/>

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
                :disabled="!nome_enabled"
              />
            </div>
          </div>
        </div>



        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <label>Arquivo</label>

              <a :href="s3_path" v-if="s3_path">Link</a>

              
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-6">
            <div class="form-group">
              <input type="file" name="s3_path" class="file" />
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


<div class="progress" style="display:none">
  <div class="progress-bar" role="progressbar" aria-valuenow="70"
  aria-valuemin="0" aria-valuemax="100" style="width:0%">
    <span class="sr-only">70% Complete</span>
  </div>
</div>
        
      </div>
    </form>
  </div>
</template>

<script>
import checkbox_int from "../../library/checkbox/checkbox_int";
import vueSelect from "../../library/vue-select/src/Select";

export default {
  props: [
    "id_load",
    "post_type",
    "show_back_button",
    "onBack",
    "onSave",
    "onEdit"
  ],
  components: {
    checkbox_int
    ,vueSelect
  },
  data: function() {
    return {
      id: "",
      id_cliente: "",
      nome: "",
      s3_path: "",
      campanha_enabled: true,
      canal_enabled: true,
      nome_enabled: true,
      show_info_message: false,
      campanhas: [],
      canais: [],
      carregando_topico: false,
      carregando_campanhas: true,
      carregando_canais: true,
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
      this.nome = "";
      this.s3_path = "";
    }

    this.loading = true;
    this.carregando_campanha = true;
    this.id_campanha = 0;
    this.id_canal = 0;
    this.nome = "";
    this.s3_path = "";
    this.campanha_enabled = false;
    this.canal_enabled = false;

    if (self.id_load) {
      self.nome = "Carregando...";
      self.nome_enabled = false;
    }

    if(this.id_load != "" && this.id_load != null && this.id_load != undefined) {
      obj_api.call("spot/"+this.id_load, "GET", null, function(response) {
        self.loading = false;
        if (self.id_load) {
          self.nome_enabled = true;
          self.nome = response.data[0].nome;
          self.s3_path = response.data[0].s3_path;
          self.id_campanha = response.data[0].id_campanha;
          self.id_canal = response.data[0].id_canal;
        } else {
          self.id_cliente = null;
          self.cliente_enabled = true;
        }
      });
    }
  },
  methods: {
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
      var url = window.URL_API + "spot/update/"+this.id_load;

      self.show_info_message = true;
      self.message_text = "Salvando spot. Por favor, aguarde.";

      var arquivo = new FormData();
      arquivo.append('file', $('.file').prop('files')[0]); 
      arquivo.append('nome', this.nome);
      if(this.id_load != null) arquivo.append('id', this.id_load);
      arquivo.append('id_campanha', self.id_campanha);
      if(self.id_canal != null) arquivo.append('add_canal', self.id_canal);
      $('.progress').show();
      $.ajax({
        xhr: function() {
          var xhr = new window.XMLHttpRequest();
          xhr.upload.addEventListener("progress", function(evt) {
          if (evt.lengthComputable) {
            var percentComplete = evt.loaded / evt.total * 100;
              $('.progress-bar').attr('aria-valuenow', percentComplete).css('width', percentComplete+'%');
              console.log(percentComplete);
            }
          }, false);

          return xhr;
        },
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
          return;
          //document.location.reload(true);
        }
      });
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
