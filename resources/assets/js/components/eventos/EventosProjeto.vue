<template>
  <div>
    <div class="col-xs-12" style="margin-top: 10px">
      <div v-if="loading">..Carregando</div>

      <section class="col-lg-12">
        <finaliza_evento
          v-if="form != null && form.id_evento_pai != null"
          :id="form.id_evento_pai"
          :botao_voltar="botao_voltar"
          :msg_botao_voltar="msg_botao_voltar"
          :onCarga="onCargaSearch"
        ></finaliza_evento>
      </section>

      <div class="col-xs-12 col-lg-5">
        <div class="box box-primary">
          <div class="box-bodyS" style="overflow:auto">
            <div id="arquivos-container" style="max-height: 300px; overflow-y: scroll">
              <table class="table table-striped table-bordered transcricoes">
                <thead>
                  <tr>
                    <th colspan="3">
                      Programa
                      <span
                        v-if="form != null && form.meta != null "
                      >: {{form.meta.programa}} - {{form.meta.emissora}}</span>
                    </th>
                  </tr>
                </thead>

                <tbody v-if="form != null && form.arquivos != null " >
                  <tr
                    v-for="(item,index) in form.arquivos"
                    :key="index"
                    v-bind:video_id="item.id"
                    v-bind:class="current_video != null && current_video.id == item.id ? 'bg-light-blue-active {{ item.id }}': ''"
                    v-bind:style="item.utilizado == true ? 'background-color: rgb(175 235 185)': ''"
                  >
                    <input type="hidden" class="hora_inicio_seg" v-bind:value="item.hora_inicio_seg" />
                    <input type="hidden" class="utilizado" nome = "utilizado"
                      v-bind:value="item.utilizado" />
                    <td>{{item.nome}}</td>

                    <td style="width: 30px">
                      <a
                        style="cursor:pointer"
                        v-if="item.temClientes"
                        v-on:click="abrir_clientes(item, index)"
                      >
                        <i
                          class="fa fa-bell"
                          v-bind:style="current_video != null && current_video.id == item.id ? 'color: white': '' "
                        ></i>
                      </a>
                    </td>

                    <td style="width: 30px">
                      <a class="play_video" style="cursor:pointer" v-on:click="openVideo(item, index)">
                        <span class="glyphicon glyphicon-play-circle"></span>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="current_video != null" style="margin:2px" id="bloco_nome_video">
              <!--  v-bind:height="current_video.url_load.indexOf('.mp3') > -1 ? 100 : 330" -->
              <video
                id="video_main"
                v-if="show_video"
                v-bind:video_id="current_video.id"
                v-bind:src="current_video.url_load"
                width="99%"
                
                preload="auto"
                v-on:timeupdate="setCurrentTime"
              ></video>

              <div>
                <b>Vídeo em reprodução:</b>
                {{current_video.nome}}
              </div>
            </div>

            <!--table
              class="table"
              v-if="current_video != null && ( current_video.tipo == null || current_video.tipo == 'pai' || current_video.tipo == 'join' )"
            -->

            <div class="col-xs-12" id="bloco_de_captura"
              v-if="current_video != null && ( current_video.tipo == null || current_video.tipo == 'pai' || current_video.tipo == 'join' )"
              style="padding-bottom: 10px;padding-left: 0px;padding-top: 10px;"
            >

              <!-- --------- TEMPO ATUAL --------- -->
              <div class="col-xs-12 col-lg-4" style="padding-left:5px;padding-right:0" >
                <div style="width: 82px;margin: 0 auto;display: block;">
                  <label>Tempo Atual:</label>
                  <input
                    type="text"
                    name="tx_currentTime"
                    id="tx_currentTime"
                    class="t_readonly"
                    readonly="readonly"
                    style="width: 70px;padding:0"
                    :value="duracao_atual"
                  />

                  <input type="hidden" id="txtDuracao" v-bind:value="current_video.duracao" />  
                </div>
              </div>

              <!-- --------- BLOCO DE BOTÃO DE INÍCIO E FIM --------- -->  
              <div class="col-xs-12 col-lg-4" style="padding:0;padding-top:5px">
                <div style="width: fit-content;margin: 0 auto;display: block;">
                  <input
                    type="button"
                    name="bt_catch_start"
                    onclick="obj_corteaudiovideo.catchTime('start')"
                    id="bt_catch_start"
                    value="Capturar Início"
                    data-toggle="tooltip"
                    title="Pega o tempo atual e indica como o início do corte"
                    class="btn btn-default btn-xs"
                    style="width: 120px"
                  />
                  <input
                    type="button"
                    name="bt_catch_end"
                    id="bt_catch_end"
                    onclick="obj_corteaudiovideo.catchTime('end')"
                    value="Capturar Fim"
                    data-toggle="tooltip"
                    title="Pega o tempo atual e indica como o fim do corte"
                    class="btn btn-default btn-xs"
                    style="width: 120px"
                  />
                </div>
              </div>

              <!-- div class="col-xs-12 col-lg-4">
                <button
                  type="button"
                  class="btn btn-default btn-xs"
                  title="Pausa o vídeo ou áudio"
                  data-toggle="tooltip"
                  id="btPausar"
                  onclick="obj_corteaudiovideo.pauseVideo()"
                >
                  <span class="glyphicon glyphicon-pause"></span> Pausa
                </button>
              </div -->

              <!-- --------- BLOCO DE VELOCIDADE --------- -->
              <div class="col-xs-12 col-lg-4" style="padding-left:5px;padding-right:0" >
                <div style="width: 72px;margin: 0 auto;display: block;">
                  <label>Velocidade:</label>
                  <select
                    name="video_velocidade"
                    id="video_velocidade"
                    onchange="obj_corteaudiovideo.setaVelocidade(this.value)"
                                        style="width: 70px"
                  >
                    <option value="1">Normal</option>
                    <option value="1.25">1,25</option>
                    <option value="1.5">1,5</option>
                    <option value="2">2</option>
                  </select>
                </div>
              </div>
          
          </div>

            <!-- && current_video.tipo == 'join' -->
          
          
          <div
            v-if="current_video != null && ( current_video.tipo == null || current_video.tipo == 'pai' || current_video.tipo == 'join' ) "
            id="bloco_inicio_fim" class="col-xs-12 col-lg-12"
            >
            <div class="col-xs-12"  style="padding: 0;">
              <div class="col-xs-2" style="padding-left: 5px;padding=right:0">Início:</div>
              <div class="col-xs-7" style="padding: 0;">
                <input
                  type="range"
                  name="rg_start"
                  id="rg_start"
                  min="0"
                  v-bind:max="max_video_time"
                  style="width: 99%"
                  onchange="obj_corteaudiovideo.sendTime(this)"
                />
              </div>
              <div class="col-xs-3" style="padding-left: 2%;padding-right: 2%;padding-top: 1px;padding-bottom: 1px;">
                  <input
                    type="text"
                    id="sp_start"
                    name="sp_start"
                    style="width: 100%"
                    onchange="obj_corteaudiovideo.changeTextTime(this)"
                  />
              </div>
            </div>
            <div class="col-xs-12 col-lg-12"  style="padding: 0;">
              <div class="col-xs-2"  style="padding-left: 5px;padding=right:0">Fim:</div>

              <div class="col-xs-7"  style="padding: 0;">
                <input
                  type="range"
                  name="rg_end"
                  id="rg_end"
                  min="0"
                  v-bind:max="max_video_time"
                  style="width: 99%"
                  onchange="obj_corteaudiovideo.sendTime(this)"
                />
              </div>
              <div class="col-xs-3" style="padding-left: 2%;padding-right: 2%;padding-top: 1px;padding-bottom: 1px;">
                <input
                  type="text"
                  id="sp_end"
                  name="sp_end"
                  style="width: 100%"
                  onchange="obj_corteaudiovideo.changeTextTime(this)"
                />
              </div>
            </div>
            <div class="col-xs-12 col-lg-12" style="padding-bottom: 12px;padding-left: 0;padding-right: 0;padding-top: 5px;">
              <div class="col-xs-12 input-group">
                <input
                  type="text"
                  placeholder="Nome do recorte"
                  id="nome_projeto"
                  v-model="nome_projeto"
                  class="form-control"
                  maxlength="100"
                />
                <span class="input-group-btn">
                  <button
                    type="button"
                    v-on:click="gera_corte"
                    class="btn btn-info btn-sm pull-right"
                    v-on:disabled="!enableCorte"
                  >
                    <i class="fa fa-cut"></i> Gerar Recorte
                  </button>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
        <div class="box box-info">
          <div class="box-body">
            <table
              v-if="form != null && form.arquivos_cortados != undefined  && form.arquivos_cortados != null "
              class="table table-striped table-bordered"
            >
              <thead>
                <tr>
                  <th colspan="2">
                    Recortes
                    <i class="fa fa-spinner" v-if="loading2"></i>
                  </th>
                  <th colspan="4">
                    <button
                      type="button"
                      v-on:click="nova_materia"
                      :disabled="salvando_materia"
                      class="btn btn-default pull-right"
                    >
                      <i class="fa fa-tv"></i> Criar Matéria
                    </button>
                  </th>
                </tr>
              </thead>
            </table>

            <div style="max-height: 400px; overflow-y: scroll">
              <table
                v-if="form != null && form.arquivos_cortados != undefined  && form.arquivos_cortados != null "
                class="table table-striped table-bordered recortes"
              >
                <tbody>
                  <tr
                    v-for="(item,index) in form.arquivos_cortados"
                    :key="index"
                    v-bind:class="current_video != null && current_video.id == item.id ? 'bg-light-blue-active': ''"
                  >
                    <input type="hidden" name="hora_inicio_seg" v-bind:value="item.hora_inicio_seg"  />
                    <input type="hidden" class="id" nome = "id"
                      v-bind:value="item.id" />
                    <td>{{item.titulo}}</td>
                    <td>{{item.nome}}</td>
                    <td>{{item.duracao}}</td>
                    <td>{{item.hora_inicio}}</td>

                    <td style="width: 30px">
                      <input type="checkbox" :value="item.id" class="chk_recorte" />
                    </td>

                    <td style="width: 30px">
                      <a style="cursor:pointer" v-on:click="openVideo(item, index)">
                        <span
                          v-bind:style="current_video != null &&  current_video.id == item.id ? 'color: white': '' "
                          class="glyphicon glyphicon-play-circle"
                        ></span>
                      </a>
                    </td>
                    <td style="width: 30px">
                      <a style="cursor:pointer" v-on:click="removeVideo(item, index)">
                        <span
                          v-bind:style="current_video != null &&  current_video.id == item.id ? 'color: white': '' "
                          class="glyphicon glyphicon-remove-circle"
                        ></span>
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="box box-info">
          <div class="box-body" style="max-height: 400px; overflow-y: scroll">
            <ListMateriaRascunho
              v-if="form!= null  && form.id != null && mostra_materia_rascunho"
              :id_projeto="form.id"
              :onSelect="SelectMateriaRascunho"
            ></ListMateriaRascunho>
          </div>
        </div>
      </div>

      <div class="col-xs-12 col-lg-7">
        <div class="box box-primary">
          <div class="box-body">
            <div class="box-header with-border">
              <h3 class="box-title">Texto transcrito</h3>
            </div>
            <div
              id="'divTexto'"
              class="col-xs-12"
              v-if="current_video != null && current_text_list != null "
              style="max-height: 500px; overflow-y: scroll;"
            >
              <span
                v-for="(item,index) in current_text_list"
                :key="index"
                v-bind:style="getStyle(item)"
              >
                <a
                  href="#!"
                  v-if="item.alternatives[0] != undefined "
                  class="link_video_legenda"
                  v-on:click="click_time(item)"
                  v-html="item.alternatives[0].text + ' '"
                ></a>
              </span>
            </div>
          </div>
        </div>

        <div class="box box-info">
          <div class="box-body">
            <div class="box-header with-border">
              <h3 class="box-title">Rascunho</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-default" v-on:click="copiar_transcricao_para_sinopse">
                  <i class="fa fa-copy"></i> Copiar transcrição
                </button>
              </div>
            </div>
            <div>
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Título</label>
                  <input
                    type="text"
                    name="materia_titulo"
                    id="materia_titulo"
                    class="form-control"
                    v-model="form.titulo_temp"
                  />
                </div>
              </div>
              <div class="col-xs-12">
                <div class="form-group">
                  <label>Sinopse</label>
                  <textarea class="form-control" rows="10" v-model="form.sinopse_temp"></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="box box-default" v-if="current_video != null && current_video.tipo == 'cut' ">
          <div class="box-body">
            <div class="box-header with-border">
              <h3 class="box-title">Dados do programa</h3>
            </div>
            <div class="col-xs-12">
              <div class="form-group">
                <b>Hora Início:</b>
                {{current_video.hora_inicio}}
                &nbsp;&nbsp;
                <b>Duração:</b>
                {{current_video.duracao}}
                &nbsp;&nbsp;
                <b>Veículo:</b>
                {{form.meta.emissora}}
                &nbsp;&nbsp;
                <b>Programa:</b>
                {{form.meta.programa}}
              </div>
            </div>
          </div>
        </div>

        <div class="box box-default" v-if="modo_materia">
          <div class="box-body">
            <div class="box-header with-border">
              <h3 class="box-title">Cadastro de Matéria</h3>
              <div class="box-tools pull-right">
                <button class="btn btn-default" v-on:click="buscarCanais">
                  <i class="fa fa-copy"></i> Buscar palavras chave
                </button>
              </div>
            </div>
            <CadMateria 
              ref="cad_materia"
              :form="form"
              :topicos_adicionais="topicos_adicionais"
              :ids_recortes="ids_recortes_selecionados"
              :id_materia_rascunho="id_materia_rascunho"
              :start_clientes="start_clientes"
              :onSave="OnSaveMateriaRascunho"
              :onRemove="onRemoveMateriaRascunho"
              :onMounted="onMateriaMounted"
            ></CadMateria>
          </div>
        </div>
        <div
          class="box box-primary"
          v-if="current_video != null && current_text_list != null && clientes.length > 0 "
        >
          <div class="box-body" style="max-height: 250px; overflow-y:scroll">
            <grid_cliente :items="clientes"></grid_cliente>
          </div>
        </div>
      </div>
    </div>

    <!--

 onloadedmetadata="obj_corteaudiovideo.loadControls(this)" onplay="obj_corteaudiovideo.setPlayVideo(this)" onpause="obj_corteaudiovideo.onpause(this)"

    -->

    <div class="modal" id="myModal" tabindex="-1" role="dialog" v-if="show_modal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Notificações</h5>
          </div>
          <div class="modal-body">
            <div v-if="meta_dados_visualizar">
              <grid_cliente :items="meta_dados_visualizar.clientes"></grid_cliente>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              v-on:click="closeModal"
              data-dismiss="modal"
            >Fechar</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style>
a.link_video_legenda {
  color: #333;
}
a.link_video_legenda:hover {
  background-color: #c3dcde;
}
</style>

<script>
import grid_cliente from "./GridCliente.vue";
import obj_cliente from "../../library/obj_cliente";
import Util from "../../library/Util";
import vue_select from "../../library/vue-select/src/Select";
import CadMateria from "./CadMateria.vue";
import finaliza_evento from "./finaliza_evento.vue";
import ListMateriaRascunho from "../materia_rascunho/List.vue";

export default {
  components: {
    grid_cliente,
    vue_select,
    CadMateria,
    ListMateriaRascunho,
    finaliza_evento
  },
  props: [
    "id_load",
    "id_pai",
    "post_type",
    "show_back_button",
    "onBack",
    "onSave",
    "id_load_arquivo",
    "tempo_seg",
    "tempo_inicio"
  ],
  data: function() {
    return {
      topicos_adicionais: [],
      form_sinopse_temp: "",
      form_titulo_temp: "",             
      form: {
        sinopse_temp: "",
        titulo_temp: ""
      },
      texto_transcricao: "",
      current_time: -1,
      duracao_atual: "00:00:00",
      interval_id: null,
      current_video: null,
      current_text_list: null,

      disableButton: false,
      publicar_titulo: "Salvar",
      titulo_acao: "eventos",
      botao_voltar_visible: false,

      show_message: "off",
      message_text: "",
      message_type: "success",
      interval_message: null,

      obj_video: null,
      loading: false,
      max_video_time: 100,
      current_video_index: -1,

      arquivos_ids: [],
      enableCorte: true,

      salvando_materia: false,

      list_apresentador: [],
      clientes: [],
      show_video: false,
      msg_botao_voltar: "Voltar para o programa",

      nome_projeto: "",

      index_arquivos: 0,
      meta_dados_visualizar: null,
      show_modal: false,

      loading2: false,

      ids_recortes_selecionados: [],
      start_clientes: [],
      modo_materia: false,

      id_materia_rascunho: null,
      mostra_materia_rascunho: true,

      bloqueado: false,
      form_pai: null,
      id_operador_atual: -1
    };
  },
  mounted() {
    console.log("seleciona projeto? ");
    if (this.interval_id != null) {
      clearInterval(this.interval_id);
    }

    let self = this;
    if ($("#id_operador").val() != "") {
      this.id_operador_atual = $("#id_operador").val();
    }

    if (this.show_back_button != null && this.show_back_button != undefined) {
      this.botao_voltar_visible = this.show_back_button;
    }

    if (this.id_load == null || this.id_load == "") {
      console.log("Id_load vazio então parando aqui");
      return;
    }

    var url = "eventos/" + this.id_load + "?clean=1";
    console.log("projeto monted post url: " + url);
    var method = "get";

    this.disableButton = true;
    self.loading = true;

    var data = {};

    var fn_return = function(retorno) {
                
      console.log("Retorno? ");
      console.log(retorno);
      console.log("id_load_arquivo? ", self.id_load_arquivo);

      self.start_clientes = retorno.item.start_clientes;
      self.form = retorno.item;
      self.disableButton = false;
      self.loading = false;

      if (self.form.status == null) {
        self.form.status = 1;
      }

      if (self.form.status != 2) {
        self.load_status_parent();
      }

      for(const arquivo of self.form.arquivos) {
        arquivo["temClientes"] = self.tem_clientes(arquivo);
      }

      if (
        self.id_load_arquivo != null &&
        self.id_load_arquivo != undefined &&
        self.id_load_arquivo != ""
      ) {
        for (var o = 0; o < self.form.arquivos.length; o++) {          
          if (
            self.form.arquivos[o].id.toString() ==
            self.id_load_arquivo.toString()
          ) {
            self.openVideo(self.form.arquivos[o], o);

            if (
              self.tempo_inicio != null &&
              self.tempo_inicio != undefined &&
              self.tempo_inicio > -1
            ) {
              setTimeout(function() {
                var video_main = document.getElementById("video_main");
                console.log("video_main?", self.tempo_inicio);
                if (video_main != null) {
                  video_main.currentTime = self.tempo_inicio;
                  // video_main.play();

                  //break;
                }
              }, 500);
            } else {
            }
            break;
          }
        }

        self.msg_botao_voltar = "Voltar para a lista";
      } else {
        if (self.form.arquivos.length == 1) {
          self.openVideo(self.form.arquivos[0], 0);
        }
      }

      if (false && self.form.id_programa != "") {
        obj_api.call(
          "midiaclip_cadastros?acao=apresentador&id_programa=" +
            self.form.id_programa,
          "GET",
          {},
          function(retorno) {
            self.list_apresentador = retorno.data;
          }
        );
      }
      
      self.scroll();
      self.interval_id = setInterval(function() { self.atualizaArquivos();}, 30000);
    };

    obj_api.call(url, method, data, fn_return);

    $(".content-wrapper").css({ "max-height": "auto", height: "1500px" });



    function doc_keyUp(e) {

        if (e.altKey && e.ctrlKey && e.keyCode === 73) {            
            obj_corteaudiovideo.catchTime('start');
        }
      
        if (e.altKey && e.ctrlKey && e.keyCode === 70) {
            obj_corteaudiovideo.catchTime('end');
        }
    }

    document.addEventListener('keyup', doc_keyUp, false);

  },
  computed: {},
  methods: {
    atualizaArquivos() {
      const self = this;

      obj_api.call("eventos/" + this.id_load + "?clean=1", "GET", {}, function(retorno) {
                
        const arquivos = [];

        for(const arquivo of retorno.item.arquivos) {
          arquivo["temClientes"]  = self.tem_clientes(arquivo);
          arquivos.push(arquivo);
        }
        self.form.arquivos = arquivos;
        self.scroll();
        
        
      });
    },
    scroll() {
      if (!this.id_load_arquivo) {
        const element = $("#arquivos-container");
        element[0].scrollTop = element[0].scrollHeight;      
      }
    },
    buscarCanais() {
      var self = this;
        
      obj_api.call("topicos/match", "post", {sinopse: self.$refs.cad_materia.materiaSinopse, conteudo: self.$refs.cad_materia.materiaConteudo}, function(retorno) {
        self.topicos_adicionais = retorno.data;
      });
    },
    onCargaSearch() {
      var self = this;

      if (this.form != null && this.form.arquivos != null) {
        for (var i = 0; i < this.form.arquivos.length; i++) {
          if (self.form.arquivos[i] == undefined) {
            continue;
          }

          if (i == this.current_video_index) {
            var id_arquivo = self.form.arquivos[i].id;

            obj_api.call(
              "eventos_arquivos_simples/" + id_arquivo,
              "get",
              {},
              function(retorno) {
                console.log("this.form.arquivos[i]", i, self.form.arquivos[i]);
                console.log("eventos_arquivos_simples?", retorno);
                self.form.arquivos[self.current_video_index].json =
                  retorno.item.json;
                self.current_text_list = JSON.parse(retorno.item.json);
                self.texto_transcricao = self.textoDaTranscricao();

                self.setaPalavrasChave();

                obj_alert.show(
                  "Sucesso!",
                  "Carga realizada neste programa com sucesso!",
                  "success"
                );
              }
            );
          } else {
            self.form.arquivos[i].json = null; //os outros podem ficar vazios e serem chamados na medida que alguém clicar..
          }
        }
      }
    },

    load_status_parent(id) {
      var self = this;
      console.log("load_status_parent " + self.form.id_evento_pai);

      var url = "eventos/" + self.form.id_evento_pai;

      var fn_return = function(retorno) {
        console.log("retorno load_status_parent", retorno);
        self.form_pai = retorno.item;

        if (retorno.item.status == null || retorno.item.status == 1) {
          //Não bloqueado...
          self.acao_bloqueio(2);
        }
      };

      obj_api.call(url, "get", { simples: 1 }, fn_return);
    },
    acao_bloqueio(status, fn_final) {
      let self = this;

      if (self.form == null) {
        return;
      }

      var data = {
        id: self.form.id_evento_pai,
        status: status,
        id_operador: $("#id_operador").val()
      };

      // console.log(data); return false;

      var fn_return = function(retorno) {
        //  self.form.bloqueado_por = retorno.form.bloqueado_por;
        //  self.form.bloqueado_por_id = retorno.form.bloqueado_por_id;
        //  self.form.status = retorno.form.status;
        //  self.form.data_status_change = retorno.form.data_status_change;

        if (retorno.form.status == null || retorno.form.status == 1) {
          //    self.bloqueado = false;
        }
        if (retorno.form.status != null && retorno.form.status == 2) {
          //  self.bloqueado = true;
        }

        if (fn_final != null) {
          fn_final();
        }
      };

      obj_api.call("eventos_status", "POST", data, fn_return);
    },

    removeVideo(item, index) {
      var self = this;

      this.loading2 = true;

      obj_alert.confirm(
        "Atenção",
        "Deseja remover este recorte?",
        "question",
        function(result) {
          if (result.value) {
            obj_api.call("recorte_delete", "POST", { id: item.id }, function(
              retorno2
            ) {
              if (
                self.current_video != null &&
                self.current_video.id.toString() == item.id.toString()
              ) {
                self.current_video = null;
              }

              if (retorno2.code == 1) {
                self.form.arquivos_cortados.splice(index, 1);
                self.loading2 = false;
              }
            });
          }
        }
      );
    },

    abrir_clientes(item, index) {
      this.index_arquivos = index;

      if (
        item.meta_dados != null &&
        item.meta_dados != undefined &&
        item.meta_dados.indexOf("{") > -1
      ) {
        var obj = JSON.parse(item.meta_dados);

        this.meta_dados_visualizar = obj;
      }

      this.show_modal = true;

      setTimeout(function() {
        $("#myModal").modal({ show: true });
      }, 200);
    },

    closeModal() {
      this.show_modal = false;
    },

    tem_clientes(item) {
      if (
        item.meta_dados != null &&
        item.meta_dados != undefined &&
        item.meta_dados.indexOf("{") > -1
      ) {
        var obj = JSON.parse(item.meta_dados);

        if (obj.clientes.length <= 0) {
          return false;
        }

        if (obj.clientes.length > 0) {
          return true;
        }
      }

      return false;
    },
    textoDaTranscricao() {
      var text = " ";

      if (this.current_text_list) {
        for (var i = 0; i < this.current_text_list.length; i++) {
          if (this.current_text_list[i].alternatives) {
            for (
              var j = 0;
              j < this.current_text_list[i].alternatives.length;
              j++
            ) {
              if (this.current_text_list[i].alternatives[j].text) {
                text = text.concat(
                  this.current_text_list[i].alternatives[j].text,
                  " "
                );
              }
            }
          }
        }
      }

      return text
        .replace(/<span class='palavra_destaque'>/g, "")
        .replace(/<\/span>/g, "");
    },

    copiar_transcricao_para_sinopse() {
      if (!this.form.sinopse_temp) {
        this.$set(this.form, "sinopse_temp", "");
      }

      this.$set(
        this.form,
        "sinopse_temp",
        this.form.sinopse_temp + this.textoDaTranscricao()
      );
    },
    nova_materia() {
      var self = this;

      this.modo_materia = false;
      var favorite = [];
      $.each($(".chk_recorte:checked"), function() {
        favorite.push($(this).val());
      });

      if (favorite.length <= 0) {
        obj_alert.show(
          "Atenção",
          "Selecione pelo menos um recorte!",
          "warning"
        );
        return false;
      }

      this.ids_recortes_selecionados = favorite;
      this.start_clientes = [];

      this.id_materia_rascunho = null;

      setTimeout(function() {
        self.modo_materia = true;
      }, 100);
    },

    OnSaveMateriaRascunho() {
      var self = this;
      this.mostra_materia_rascunho = false;

      setTimeout(function() {
        self.mostra_materia_rascunho = true;
      }, 100);
    },
    onMateriaMounted() {
      var self = this;

      this.$set(this.form, "titulo_temp", "");
      this.$set(this.form, "sinopse_temp", "");
    },
    onRemoveMateriaRascunho() {
      var self = this;
      this.mostra_materia_rascunho = false;
      this.modo_materia = false;

      setTimeout(function() {
        self.mostra_materia_rascunho = true;
      }, 100);
    },

    SelectMateriaRascunho(item, index) {
      var self = this;

      this.modo_materia = false;
      this.id_materia_rascunho = item.id;

      setTimeout(function() {
        self.modo_materia = true;
      }, 100);
    },

    gera_corte() {
      var self = this;
      this.enableCorte = false;
      self.loading2 = true;

      if (document.getElementById("rg_start") == null) {
        // alert("Não localizei o campo de tempo início");
        obj_alert.show(
          "Atenção",
          "Campo de tempo início não localizado!",
          "warning"
        );
        false;
      }

      if (this.nome_projeto == "") {
        //   obj_alert.show("Atenção", "Informe o nome do recorte", "warning");
        //   false;
      }

      // console.log("Gera_corte: "); console.log(data); return false;

      var data = {
        id_evento: this.id_load,
        inicio: $("#rg_start").val(),
        fim: $("#rg_end").val(),
        id_arquivo_pai: this.current_video.id,
        titulo: this.nome_projeto
      };

      var fn_return = function(retorno) {
                
        self.form.arquivos_cortados.push(retorno.data);
        self.enableCorte = true;
        self.nome_projeto = "";
        self.loading2 = false;
        console.log("Retorno do corte!");
        console.log(retorno);

        if($("#rg_end").val() == $("#rg_end").attr("max")) {
          $(".bg-light-blue-active")
              .closest('tr')
              .next()
              .find("td a.play_video span")
              .trigger("click");
        }
      };

      obj_api.call("project_corte", "POST", data, fn_return);
    },

    novoProjeto() {
      var arquivos = [];

      for (var i = 0; i < this.form.arquivos.length; i++) {
        var item = this.form.arquivos[i];

        if (this.arquivos_ids.indexOf(item.id) > -1) {
          arquivos[arquivos.length] = { id: item.id, nome: item.nome };
        }
      }

      var data = {
        arquivos: JSON.stringify(arquivos),
        id_operador: $("#id_operador").val()
      };

      // console.log(data); return false;

      var fn_return = function(retorno) {
        console.log("Retorno? ");
        console.log(retorno);
      };

      obj_api.call("project/" + this.form.id, "POST", data, fn_return);

      //obj_api.callFormData("project/" +this.form.id,"POST", data, fn_return );
    },

    add_video(id, index) {
      var check = document.getElementById("chk_" + id);

      if (!check.checked) {
        var inde = this.arquivos_ids.indexOf(id);
        if (inde > -1) {
          this.arquivos_ids.splice(inde, 1);
        }
      } else {
        var inde = this.arquivos_ids.indexOf(id);
        if (inde < 0) {
          this.arquivos_ids.push(id);
        }
      }

      console.log("add? ");
      console.log(id);
      console.log(this.arquivos_ids);
      console.log(check.checked);
    },

    getStyle(item) {
      if (
        item.start_time < this.current_time &&
        item.end_time >= this.current_time
      ) {
        return "background-color: #CCCCCC";
      }

      return "";
    },

    click_time(item) {
      if (this.obj_video == null) {
        this.obj_video = document.getElementById("video_main");
      }

      this.obj_video.currentTime = item.start_time;
      this.obj_video.play();
    },

    setCurrentTime() {
      if (this.obj_video == null) {
        this.obj_video = document.getElementById("video_main");
      }

      this.current_time = this.obj_video.currentTime;
      this.duracao_atual = this.duracaoAtual();
    },

    duracaoAtual() {
      var segundos = this.current_time;

      if (segundos == null) segundos = 0;

      var horas = parseInt(parseInt(segundos) / 3600);
      var minutos = parseInt((parseInt(segundos) % 3600) / 60);
      var seg = parseInt((parseInt(segundos) % 3600) % 60);

      var tempo =
        obj_corteaudiovideo.sTempo(horas) +
        ":" +
        obj_corteaudiovideo.sTempo(minutos) +
        ":" +
        obj_corteaudiovideo.sTempo(seg);

      return tempo;
    },

    openVideo(item, index) {
      var self = this;
      $(this).siblings().remove();

      this.show_video = false;

      if (self.obj_video != null) {
        self.obj_video.pause();
      }

      this.current_text_list = null;
      this.texto_transcricao = "";

      this.clientes = [];

      if (item.meta_dados != null && item.meta_dados != undefined) {
        var obj = JSON.parse(item.meta_dados);

        if (obj.clientes != null) {
          this.clientes = obj.clientes;
        }
      }

      if (item.duracao != undefined && item.duracao != null) {
        this.max_video_time = obj_corteaudiovideo.ConverteTextoParaSegundos(
          item.duracao
        );
      }
      this.current_video = item;
      this.current_video_index = index;
      if (item.json != null) {
        this.current_text_list = JSON.parse(item.json);
        this.texto_transcricao = this.textoDaTranscricao();
        this.setaPalavrasChave();
      } else {
        obj_api.call("eventos_arquivos_simples/" + item.id, "get", {}, function(
          retorno
        ) {
          item.json = retorno.item.json;
          console.log("eventos_arquivos_simples para bvuscar json do arquivo");
          self.current_text_list = JSON.parse(item.json);
          self.texto_transcricao = self.textoDaTranscricao();
          self.setaPalavrasChave();
        });
      }

      if (item.tipo == null || item.tipo == "pai" || item.tipo == "join") {
        this.modo_materia = false;
      }

      setTimeout(function() {
        self.show_video = true;
        self.obj_video = document.getElementById("video_main");

        if (self.obj_video != null) {
          self.obj_video.play();
        }

        $("#bloco_nome_video").on('mouseenter', '#video_main',function () {
          $(this).attr("controls", 1); 
        });
        $("#bloco_nome_video").on('mouseleave', '#video_main',function () {
          $(this).removeAttr("controls"); 
        });

      }, 1300);
    

    },

    setaPalavrasChave() {
      console.log("seta palavras chave? ");
      console.log(this.clientes);

      if (this.current_text_list == null) {
        return;
      }

      for (var o = 0; o < this.current_text_list.length; o++) {
        if (
          this.clientes.length > 0 &&
          this.current_text_list[o].alternatives.length > 0 &&
          this.current_text_list[o].alternatives[0].text != null &&
          this.current_text_list[o].alternatives[0].text != undefined
        ) {
          for (var i = 0; i < this.clientes.length; i++) {
            if (
              this.clientes[i].palavras != null &&
              this.clientes[i].palavras != undefined
            ) {
              for (var zz = 0; zz < this.clientes[i].palavras.length; zz++) {
                if (
                  this.current_text_list[o].alternatives == null ||
                  this.current_text_list[o].alternatives == undefined
                ) {
                  continue;
                }

                if (this.current_text_list[o].alternatives.length <= 0) {
                  continue;
                }
                this.current_text_list[
                  o
                ].alternatives[0].text = this.current_text_list[
                  o
                ].alternatives[0].text.replace(
                  this.clientes[i].palavras[zz].nome.toLowerCase() + "",
                  "<span class='palavra_destaque'>" +
                    this.clientes[i].palavras[zz].nome.toLowerCase() +
                    "</span> "
                );
              }
            } else {
              this.current_text_list[
                o
              ].alternatives[0].text = this.current_text_list[
                o
              ].alternatives[0].text.replace(
                this.clientes[i].nome.toLowerCase() + "",
                "<span class='palavra_destaque'>" +
                  this.clientes[i].nome.toLowerCase() +
                  "</span> "
              );
            }
          }
        }
      }
    },

    carregaForm(item) {
      var self = this;
    },

    exibe_error(tipo) {},

    getClassFirstSection() {
      if (this.id != "") return "col-xs-9";

      return "col-xs-12";
    },

    validar() {
      if (obj_alert.isvazioInput("f_data", "Informe o Data!")) return false;

      return true;
    },

    botao_voltar() {
      var self = this;
      
      if (self.interval_id != null)
        clearInterval(self.interval_id);

      self.interval_id = null;
      

      if (
        self.form_pai != null &&
        self.form_pai.bloqueado_por_id != null &&
        self.id_operador_atual.toString() ==
          self.form_pai.bloqueado_por_id.toString()
      ) {
        console.log("acao_ desbloqueio ? ");
        this.acao_bloqueio(1, function() {
          //Vou dar um segundo pra ele não ficar tão empatado numa tela só...

          if (self.onBack != null && self.onBack != undefined) {
            console.log("clique no voltar!");
            self.onBack(self);
          }
        });
      } else {
        if (self.onBack != null && self.onBack != undefined) {
          console.log("clique no voltar!");
          self.onBack(self);
        }
      }
    },

    salvar(tipo) {},

    clear_message() {
      this.show_message = "off";
      clearInterval(this.interval_message);
    },

    excluir() {
      let self = this;
    }
  }
};
</script>
