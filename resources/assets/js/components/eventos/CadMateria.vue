<template>
  <div>
    <div v-if="materia.id_materia_radiotv_jornal != null ">
      <materia_gerada :id="materia.id_materia_radiotv_jornal.toString()"></materia_gerada>
    </div>
    <div v-if="materia.id_materia_radiotv_jornal == null ">
      <div class="col-xs-12" v-if="idsDosRecortes != null">
        <div v-if="idsDosRecortes.length == 1">1 Arquivo selecionado</div>
        <div v-if="idsDosRecortes.length  > 1">{{idsDosRecortes.length}} Arquivos selecionados</div>
        <div v-if="idsDosRecortes.length == 0">Nenhum arquivo selecionado</div>
      </div>

      <div class="col-xs-6" v-if="idsDosRecortes.length > 0 ">
        <div class="form-group">
          <label>Hora Início:</label>
          {{materia.hora_inicio}}
        </div>
      </div>

      <div class="col-xs-6" v-if="idsDosRecortes.length > 0 ">
        <div class="form-group">
          <label>Duração:</label>
          {{materia.duracao}}
        </div>
      </div>

      <div class="col-xs-6">
        <div class="form-group">
          <label>Título</label>
          <input
            type="text"
            name="materia_titulo"
            id="materia_titulo"
            class="form-control"
            v-model="materia.titulo"
          />
        </div>
      </div>
      <div class="col-xs-6">
        <div class="form-group">
          <label>Apresentador</label>
          <select
            name="materia_id_apresentador"
            id="materia_id_apresentador"
            class="form-control"
            v-model="materia.id_apresentador"
          >
            <option
              v-for="(item,index) in list_apresentador"
              :key="index"
              :value="item.id"
            >{{item.nome}}</option>
          </select>
        </div>
      </div>

      <div class="col-xs-12">
        <div class="form-group">
          <label>Sinopse</label>
          <textarea v-model="materia.sinopse" class="form-control" rows="10"></textarea>
        </div>
      </div>

      <div class="col-xs-12">
        <div class="form-group">
          <label>Conteúdo</label>
          <textarea v-model="materia.conteudo" class="form-control" rows="20"></textarea>
        </div>
      </div>

      <vinculosComClientes
        :clientes="listaDeClientes" 
        :vinculos="vinculos"
        ref="todosVinculos">
      </vinculosComClientes>

      <div class="col-xs-10">
        <div class="form-group">
          <!-- <label>Adicionar Cliente</label> -->
          <!-- :onChange="loadUnidade" -->
          <vue_select
            class="vue-select2"
            name="select2"
            :options="items_clientes"
            v-model="id_cliente_add"
            v-if="false"
            :searchable="true"
            language="pt-BR"
            :placeholder="placeholder_cliente"
          ></vue_select>
        </div>
      </div>
      <div class="col-xs-2" style="padding-top: 25px">
        <button
          type="button"
          v-on:click="adicionar_novo_cliente"
          v-if="false"
          class="btn btn-default pull-right"
        >
          <i class="fa fa-plus"> </i> Adicionar
        </button>
      </div>

      <div class="col-xs-12">
        <table
          class="table table-striped table-bordered"
          v-if="false"
        >
          <thead>
            <tr>
              <th>ID</th>
              <th>Cliente</th>
              <th>Canal</th>
              <!-- <th>Impacto</th> -->
              <th style="text-align: center;">Citação Direta</th>
              <th style="width: 30px"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item,index) in todosClientes" :key="index">
              <!-- {{verifica_se_tem_index_cliente(item)}} -->
              <td>{{item.id}}</td>
              <td>{{item.nome}}</td>
              <td>
                <select
                  v-model="item.id_topico"
                  class="form-control"
                  :key="item.loaded_topico_index"
                  v-if="item.loaded_topicos"
                >
                  <option
                    v-for="(item2, index2) in item.list_topicos"
                    :key="index2"
                    :value="item2.id"
                    :style="item2.pai == 1 ? 'background: #E1E1E1' : 'background: white; padding-left: 10px;'"
                  >{{item2.nome}}</option>
                </select>
              </td>
              <!-- <td>
                <select v-model="item.id_impacto" class="form-control">
                  <option
                    v-for="(item2, index2) in list_impacto"
                    :key="index2"
                    :value="item2.id"
                  >{{item2.nome}}</option>
                </select>
              </td>-->
              <td style="text-align: center;">{{item.cita_diretamente == "1" ? "Sim" : "Não"}}</td>
              <td>
                <button
                  type="button"
                  v-on:click="remover_cliente(item, index)"
                  class="btn btn-default pull-right"
                >
                  <i class="fa fa-remove"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="col-xs-12" v-if="idsDosRecortes!= null && idsDosRecortes.length > 0 ">
        <grid_arquivos :ids="idsDosRecortes.join(',')"></grid_arquivos>
      </div>

      <div
        class="col-xs-12"
        v-if="materia.id_materia_radiotv_jornal == null || materia.id_materia_radiotv_jornal == ''"
      >
        <button
          type="button"
          v-if="materia.id_rascunho != null"
          v-on:click="excluir_materia"
          :disabled="salvando_materia"
          class="btn btn-default pull-right"
          style="margin-left: 20px"
        >
          <i class="fa fa-trash"></i>
        </button>

        <button
          type="button"
          v-on:click="gera_materia"
          :disabled="salvando_materia"
          class="btn btn-danger pull-right"
        >
          <i class="fa fa-tv"></i>
          {{msg_salvando_materia}}
        </button>

        <button
          type="button"
          v-on:click="salva_materia_rascunho"
          :disabled="salvando_materia"
          class="btn btn-info pull-right"
        >
          <i class="fa fa-radio"></i>
          {{ !salvando_materia ? "Salvar Rascunho" : msg_salvando_materia }}
        </button>
      </div>
      <div
        class="col-xs-12"
        v-if="materia.id_materia_radiotv_jornal != null && materia.id_materia_radiotv_jornal.toString() != ''"
      >
        <envio_email :clientes="todosClientes" :id_materia="materia.id_materia_radiotv_jornal"></envio_email>
      </div>
    </div>
  </div>
</template>
<script type="text/javascript">
import vinculosComClientes from "../materias/VinculosComClientes";
import Util from "../../library/Util";
import obj_cliente from "../../library/obj_cliente";
import vue_select from "../../library/vue-select/src/Select";
import grid_arquivos from "../eventos_arquivos/grid_arquivos";
import materia_gerada from "../materia_rascunho/Gerada";
import envio_email from "./EnvioEmail";
import ClientesService from "../../services/ClientesService";

export default {
  components: {
    vinculosComClientes,
    vue_select,
    grid_arquivos,
    materia_gerada,
    envio_email
  },
  props: {
    form: {
      default: null,
      type: Object
    },

    ids_recortes: {
      default: [],
      type: Array
    },
    topicos_adicionais: {
      default: () => [],
      type: Array
    },
    start_clientes: {
      default: [],
      type: Array
    },
    id_materia_rascunho: {
      default: null,
      type: Number
    },
    onSave: {
      default: null,
      type: Function
    },
    onMounted: {
      default: null,
      type: Function
    },
    onRemove: {
      default: null,
      type: Function
    }
  },
  watch: {
    start_clientes(clientes) {
      console.log(JSON.stringify(clientes));
    },
    idsDosRecortes: function(val) {
      this.gera_duracao();
    },
    topicos_adicionais(topicos) {
      var self = this;
      for(const item of topicos) {
        self.vinculos.push({
          clienteId: item.clienteId,
          clienteNome: item.clienteNome,
          canalId: item.topicoId,
          canalNome: item.topicoNome
        });
      }
    }
  },
  data: function() {
    return {
      list_apresentador: [],
      clientes: [],
      vinculos: [],

      loading2: false,
      show_items_clientes: true,

      items_clientes: [],
      listaDeClientes: [],

      placeholder_cliente: "Carregando clientes..",
      id_cliente_add: null,
      msg_salvando_materia: "Gerar Matéria",
      salvando_materia: false,

      list_impacto: [],
      modo_email: false,
      show_clientes: true,
      todosClientes: [],
      recortes_ids: [],

      materia: {
        id_rascunho: null,
        id_apresentador: null,
        titulo: "",
        sinopse: "",
        conteudo: "",
        id: null,
        hora_inicio: "",
        duracao: "",
        clientes: []
      }
    };
  },
  computed: {  
    idsDosRecortes() {
      const ids = [];
      
      for(const id of this.ids_recortes) {
        if (ids.indexOf(id) == -1)
          ids.push(id);
      }

      for(const id of this.recortes_ids) {
        if (ids.indexOf(id) == -1)
          ids.push(id);
      }

      return ids;
    },
    materiaSinopse() {
      return this.materia.sinopse;
    },
    materiaConteudo() {
      return this.materia.conteudo;
    }
  },
  async mounted() {
    var self = this;

    this.list_impacto = [
      { id: 1201231, nome: "POSITIVO" },
      { id: 1201232, nome: "NEUTRO" },
      { id: 1201233, nome: "NEGATIVO" }
    ];
    const service = new ClientesService();
    const clientes = await service.listaAsync();

    for(const cliente of clientes) {        
      self.listaDeClientes.push(cliente);
    }     

    // obj_api.call("clientes", "GET", {}, function(response){
    //   const clientes = [];
    //   for(const cliente of response.data) {        
    //     clientes.push(cliente);
    //   }
    //   self.listaDeClientes = clientes;
    // });    

    obj_api.call("midiaclip_cadastros?acao=impacto", "GET", {}, function(
      retorno
    ) {
      console.log("list impacto");
      self.list_impacto = retorno.data;
      console.log(self.list_impacto);
    });

    if (self.form != null && self.form.titulo_temp != null) {
      self.materia.titulo = self.form.titulo_temp;
    }

    if (self.form != null && self.form.sinopse_temp != null) {
      self.materia.sinopse = self.form.sinopse_temp;
    }

    if (self.form != null && self.form.id_programa != null) {
      obj_api.call(
        "midiaclip_cadastros?acao=apresentador&id_programa=" +
          self.form.id_programa,
        "GET",
        {},
        function(retorno) {
          console.log("list apresentador");
          console.log(self.list_apresentador);
          self.list_apresentador = retorno.data;

          var tmp_apresentador = obj_programa.get_last_apresentador(
            self.form.id_programa
          );

          console.log("tmp_apresentador? ", tmp_apresentador);
          console.log(
            "form_id_programa? ",
            window.sessionStorage.getItem("form_id_programa")
          );
          console.log(
            "form_id_apresentador? ",
            window.sessionStorage.getItem("form_id_apresentador")
          );

          if (tmp_apresentador != null && tmp_apresentador != undefined) {
            self.materia.id_apresentador = parseInt(tmp_apresentador);
          }
        }
      );
    }

    obj_api.call("transcricoes/vinculos", "GET", { filesIds: this.idsDosRecortes.join(",") }, function(resultado) {

      for(const vinculo of resultado.data) {
        self.vinculos.push({
          clienteId: vinculo.clienteId,
          clienteNome: vinculo.clienteNome,
          canalId: vinculo.topicoId,
          canalNome: vinculo.topicoNome
        });
      }
    });

    // obj_cliente.getListByFilesIds(this.idsDosRecortes.join(","), function(
    //   resultado
    // ) {
    //   if (!resultado) return;
      
    //   for (var i = 0; i < resultado.length; i++) {
    //     var item = resultado[i];
    //     self.vinculos.push({
    //       clienteId: item.clienteId,
    //       clienteNome: item.clienteNome,
    //       canalId: item.topicoId,
    //       canalNome: item.topicoNome
    //     });
    //     // self.materia.clientes.push({
    //     //   id: item.clienteId,
    //     //   nome: item.clienteNome,
    //     //   id_impacto: null,
    //     //   cita_diretamente: "1",
    //     //   loaded_topicos: true,
    //     //   list_topicos:
    //     //     item.topicoId && item.topicoNome
    //     //       ? [{ id: item.topicoId, nome: item.topicoNome }]
    //     //       : [],
    //     //   id_topico: item.topicoId && item.topicoNome ? item.topicoId : null,
    //     //   loaded_topico_index: self.criaTopicoIndex(
    //     //     item.clienteId,
    //     //     item.topicoId
    //     //   ),
    //     //   loaded_topico_span: self.criaTopicoSpan(item.clienteId, item.topicoId)
    //     // });
    //   }
    // });

    if (this.id_materia_rascunho == null) {
      //Se o id da matéria já esta salvo, não precisamos adicionar os clientes.
      console.log("start clientes? ", this.start_clientes);

      if (this.start_clientes != null && this.start_clientes != undefined) {
        this.adicionar_clientes(this.start_clientes);
        this.reload_topicos_clientes();
      }
    }

    if (this.id_materia_rascunho == null) {
      this.gera_duracao();
    } else {
      //obj_json

      obj_api.call(
        "materia_rascunho/" + this.id_materia_rascunho,
        "GET",
        {},
        function(retorno2) {
          ///console.log("Consegui retornar o rascunho? ");
          //console.log(retorno2);
          self.materia = retorno2.item.obj_json;
          if (retorno2.item.ids_arquivos != null) {
            self.recortes_ids = retorno2.item.ids_arquivos.split(",");
            //self.$emit(
            //  "update-ids_recortes",
            //  retorno2.item.ids_arquivos.split(",")
            //);
          }


          for(const cliente of self.materia.clientes) {
            for(const canal of cliente.list_topicos) {
              if (cliente.id_topico == canal.id) {
                self.vinculos.push({
                  clienteId: cliente.id,
                  clienteNome: cliente.nome,
                  canalId: canal.id,
                  canalNome: canal.nome
                });
              }
            }
          }

          self.reload_topicos_clientes();
        }
      );
    }

    if (self.onMounted != null) self.onMounted();
  },
  methods: {
    todosVinculos() {
      return this.$refs.todosVinculos.todosVinculos;
    },
    todosClientesAdicionados() {
      var vinculos = this.todosVinculos();
      var result = [];
      for (const vinculo of vinculos) {
        result.push({
            id: vinculo.clienteId,
            nome: vinculo.clienteNome,
            id_impacto: null,
            cita_diretamente: vinculo.citaDiretamente,
            loaded_topicos: true,
            list_topicos: vinculo.canais,
            id_topico: vinculo.canal,
            loaded_topico_index: this.criaTopicoIndex(vinculo.clienteId,vinculo.topicoId),
            loaded_topico_span: this.criaTopicoSpan(vinculo.clienteId,vinculo.topicoId)
          });
      }
      return result;
      // var clientes = this.materia.clientes;
      // for (var i = 0; i < clientes.length; i++) {
      //   var item = clientes[i];

      //   var found = false;
      //   for (var j = 0; j < result.length; j++) {
      //     if (
      //       result[j].id == item.id &&
      //       result[j].nome == item.nome &&
      //       result[j].id_topico == item.id_topico
      //     ) {
      //       found = true;
      //       break;
      //     }
      //   }

      //   if (!found) result.push(clientes[i]);
      // }

      // for (var i = 0; i < this.topicos_adicionais.length; i++) {
      //   var item = this.topicos_adicionais[i];

      //   var found = false;
      //   for (var j = 0; j < result.length; j++) {
      //     if (
      //       result[j].id == item.clienteId &&
      //       result[j].nome == item.clienteNome &&
      //       result[j].id_topico == item.topicoId
      //     ) {
      //       found = true;
      //       break;
      //     }
      //   }

      //   if (!found)
      //     result.push({
      //       id: item.clienteId,
      //       nome: item.clienteNome,
      //       id_impacto: null,
      //       cita_diretamente: "1",
      //       loaded_topicos: true,
      //       list_topicos:
      //         item.topicoId && item.topicoNome
      //           ? [{ id: item.topicoId, nome: item.topicoNome }]
      //           : [],
      //       id_topico: item.topicoId && item.topicoNome ? item.topicoId : null,
      //       loaded_topico_index: this.criaTopicoIndex(
      //         item.clienteId,
      //         item.topicoId
      //       ),
      //       loaded_topico_span: this.criaTopicoSpan(
      //         item.clienteId,
      //         item.topicoId
      //       )
      //     });
      // }

      // return result;
    },
    logTodosOsVinculos() {
      console.log(JSON.stringify(this.todosVinculos));
    },
    criaTopicoIndex(clienteId, topicoId) {
      if (!topicoId) {
        return `${clienteId}_default_index`;
      }

      return `${clienteId}_${topicoId}_index`;
    },
    criaTopicoSpan(clienteId, topicoId) {
      if (!topicoId) {
        return `${clienteId}_default_span`;
      }

      return `${clienteId}_${topicoId}_span`;
    },
    gera_duracao() {
      var int_inicio = -1;
      var duracao_segundos = 0;

      if (this.form == null) {
        return;
      }

      if (this.id_materia_rascunho != null) {
        return;
      }

      if (this.form.arquivos_cortados.length <= 0) {
        this.materia.hora_inicio = "";
        this.materia.duracao = "";
        return;
      }

      var texto = "";

      for (var i = 0; i < this.form.arquivos_cortados.length; i++) {
        if (
          this.idsDosRecortes.indexOf(
            this.form.arquivos_cortados[i].id.toString()
          ) > -1
        ) {
          duracao_segundos +=
            this.form.arquivos_cortados[i].tempo_realizado_minutos * 60;

          if (texto != "") {
            texto += "\n";
          }
          texto += this.form.arquivos_cortados[i].texto;

          if (
            int_inicio == -1 ||
            this.form.arquivos_cortados[i].hora_inicio_seg < int_inicio
          ) {
            int_inicio = this.form.arquivos_cortados[i].hora_inicio_seg;
          }

          var meta_dados = this.form.arquivos_cortados[i].meta_dados;
          if (meta_dados != null) {
            var obj_meta_dados = JSON.parse(meta_dados);
            if (
              obj_meta_dados.clientes != null &&
              obj_meta_dados.clientes.length > 0
            ) {
              this.adicionar_clientes(obj_meta_dados.clientes);
              this.reload_topicos_clientes();
            }
          }
        }
      }

      this.materia.sinopse =
        this.materia.sinopse == "" ? texto : this.materia.sinopse;

      this.materia.conteudo = this.materia.conteudo
        ? this.materia.conteudo
        : texto;

      this.materia.hora_inicio = obj_corteaudiovideo.segundoParaTexto(
        int_inicio
      );

      this.materia.duracao = obj_corteaudiovideo.segundoParaTexto(
        duracao_segundos
      );
    },

    get_item_cliente(id) {
      for (var i = 0; i < this.items_clientes.length; i++) {
        if (this.items_clientes[i].value.toString() == id.toString()) {
          var retorno = this.items_clientes[i];

          return { id: retorno.value, nome: retorno.label }; // this.items_clientes[i];
        }
      }

      return null;
    },
    excluir_materia() {
      var self = this;

      var id_materia_rascunho = this.materia.id_rascunho;

      obj_alert.confirm(
        "Atenção",
        "Deseja realmente excluir este rascunho?",
        "question",
        function(result) {
          if (result.value) {
            obj_api.call(
              "materia_rascunho_del/" + id_materia_rascunho,
              "GET",
              {},
              function(resultado2) {
                if (self.onRemove != null) {
                  self.onRemove(resultado2);
                }
              }
            );
          }
        }
      );
    },
    adicionar_novo_cliente() {
      var self = this;

      if (this.id_cliente_add == null) {
        obj_alert.show("Atenção", "Selecione o cliente!", "warning");
        return;
      }

      var item = this.get_item_cliente(this.id_cliente_add);
      console.log("cliente para adicionar???");
      console.log(item);
      this.show_items_clientes = false;
      this.adicionar_cliente(item);
      this.reload_topicos_clientes();
      this.id_cliente_add = null;
      setTimeout(function() {
        self.show_items_clientes = true;
      }, 100);
    },

    reload_topicos_clientes() {
      console.log("reload_topicos_clientes? ");
      var self = this;
      var conta = 0;
      var clientes = self.materia.clientes;

      for (var i = 0; i < clientes.length; i++) {
        if (
          clientes[i].loaded_topicos == null ||
          clientes[i].loaded_topicos == false
        ) {
          var id_cliente = clientes[i].id;
          console.log("load_cliente_topico? " + id_cliente);
          self.load_cliente_topico(id_cliente, self, conta);
          console.log("aqui");
          conta++;
        }
      }
    },
    get_index_cliente(id_cliente) {
      var clientes = this.materia.clientes;
      for (var i = 0; i < clientes.length; i++) {
        if (clientes[i].id.toString() == id_cliente.toString()) {
          return i;
        }
      }

      return -1;
    },
    load_cliente_topico(id_cliente, self, conta) {
      setTimeout(function() {
        console.log("load_cliente_topico? " + id_cliente);
        obj_api.call(
          "midiaclip_cadastros?acao=topicos&id_cliente=" + id_cliente,
          "GET",
          {},
          function(response) {
            var index_cliente = self.get_index_cliente(id_cliente);
            console.log(
              "load_cliente_topico? index_cliente ? " + index_cliente
            );
            if (index_cliente < 0) {
              return;
            }
            var clientes = self.materia.clientes;
            var o_cliente = clientes[index_cliente];

            o_cliente.loaded_topicos = true;
            o_cliente.list_topicos = response.data;
            o_cliente.topico_required = response.required;

            var topico_id =
              o_cliente.list_topicos && o_cliente.list_topicos[0]
                ? o_cliente.list_topicos[0].id
                : null;

            o_cliente.loaded_topico_index =
              self.criaTopicoIndex(id_cliente, topico_id) +
              new Date().getTime(); //mudando o key força ele recarregar...
            o_cliente.loaded_topico_span =
              self.criaTopicoSpan(id_cliente, topico_id) + new Date().getTime();

            Vue.set(self.materia.clientes, parseInt(index_cliente), o_cliente);
          }
        );
      }, 20 * conta + 50);
    },

    remover_cliente(item, index) {
      this.materia.clientes.splice(index, 1);
    },
    verifica_se_tem_index_cliente(item) {
      if (item.loaded_topico_index == null) {
        item.loaded_topico_index = this.criaTopicoIndex(item.id);
      }

      if (item.loaded_topico_span == null) {
        item.loaded_topico_span = this.criaTopicoSpan(item.id);
      }

      return "";
    },
    adicionar_clientes(itens) {
      for (var i = 0; i < itens.length; i++) {
        this.adicionar_cliente(itens[i]);
      }

      this.id_cliente_add = null;
    },
    adicionar_cliente(item) {
      if (item == null) return null;

      if (!this.tem_cliente(item.id)) {
        var cita_diretamente = 0;
        if (item.citacao_direta != null && item.citacao_direta == 1) {
          cita_diretamente = 1;
        }
        this.materia.clientes.push({
          id: item.id,
          nome: item.nome,
          id_impacto: null,
          cita_diretamente: cita_diretamente,
          loaded_topicos: false,
          list_topicos: null,
          id_topico: null,
          loaded_topico_index: this.criaTopicoIndex(item.id),
          loaded_topico_span: this.criaTopicoSpan(item.id)
        });
      }
    },
    tem_cliente(id) {
      if (id == undefined) return false;

      var clientes = this.materia.clientes;
      for (var i = 0; i < clientes.length; i++) {
        var item_cliente = clientes[i];

        if (item_cliente.id.toString() == id.toString()) {
          return true;
        }
      }
      return false;
    },
    get_str_nome_clientes() {
      var str_clientes = "";
      var clientes = this.todosClientesAdicionados();
      for (var i = 0; i < clientes.length; i++) {
        if (str_clientes != "") {
          str_clientes += ", ";
        }
        str_clientes += clientes[i].nome;
      }

      return str_clientes;
    },
    salva_materia_rascunho() {
      var self = this;

      self.salvando_materia = true;
      self.msg_salvando_materia = "Salvando...";

      var str_clientes = this.get_str_nome_clientes();

      var materia = self.materia;
      materia.clientes = self.todosClientesAdicionados();

      var data = {
        id: this.materia.id_rascunho,
        dados_materia: JSON.stringify(materia),
        cliente_list: str_clientes,
        id_programa: this.form.id_programa,
        id_projeto: this.form.id,
        dia: this.form.dia,
        id_operador: $("#id_operador").val(),
        titulo: this.materia.titulo,
        ids_arquivos: this.idsDosRecortes.join(","),
        clientes: JSON.stringify(self.todosClientesAdicionados())
      };

      obj_programa.set_last_apresentador(
        this.form.id_programa,
        this.materia.id_apresentador
      );

      obj_api.call("materia_rascunho", "post", data, function(response) {
        self.materia.id_rascunho = response.data.id;
        self.salvando_materia = false;
        self.msg_salvando_materia = "Gerar Matéria";
        var materiaAtual = self.materia;
        materiaAtual.clientes = self.todosClientesAdicionados();
        if (self.onSave != null) {
          self.onSave(materiaAtual);
        }
        obj_alert.show(
          "Sucesso!",
          "Rascunho salvo com sucesso!",
          "success",
          null,
          1500
        );
      });
    },
    gera_materia() {
      var self = this;

      if (this.materia.titulo == "") {
        obj_alert.show("Atenção", "Informe o título", "warning");
        return false;
      }

      if (this.idsDosRecortes.length <= 0) {
        obj_alert.show("Atenção", "Selecione um ou mais arquivos", "warning");
        return false;
      }

      var clientes = this.todosClientesAdicionados();
      if (clientes.length > 0) {
        var str_obrigatorio_topico = "";
        console.log("materia clientes?" + clientes.length);

        for (var i = 0; i < clientes.length; i++) {
          var o_cliente = clientes[i];
          console.log(o_cliente);
          if (
            o_cliente.topico_required != undefined &&
            o_cliente.topico_required != null &&
            o_cliente.topico_required.toString() == "1" &&
            o_cliente.list_topicos != null &&
            o_cliente.list_topicos.length > 0
          ) {
            console.log("topico required?", o_cliente.id_topico);
            if (
              o_cliente.id_topico == undefined ||
              o_cliente.id_topico == null ||
              o_cliente.id_topico.toString() == ""
            ) {
              if (str_obrigatorio_topico != "") {
                str_obrigatorio_topico += ", " + o_cliente.nome;
              } else {
                str_obrigatorio_topico += o_cliente.nome;
              }
            }
          }
        }

        if (str_obrigatorio_topico != "") {
          obj_alert.show(
            "Atenção",
            "O tópico é obrigatório para o(s) cliente(s): " +
              str_obrigatorio_topico,
            "warning"
          );
          return false;
        }
      }
      var new_id = "";

      self.salvando_materia = true;
      self.msg_salvando_materia = "Salvando...";

      var str_clientes = this.get_str_nome_clientes();

      var compl_api4 = "";

      if (clientes != null && clientes.length > 0) {
        compl_api4 = "&clientes=" + clientes.length.toString();
      }

      obj_programa.set_last_apresentador(
        this.form.id_programa,
        this.materia.id_apresentador
      );

      obj_api.call_midiaclip("midiaclip", "?acao=path_rtv", "GET", function(
        retorno3
      ) {
        obj_api.call_midiaclip(
          "api4",
          "materiartv/getnewid?arquivos=1" + compl_api4,
          "GET",
          function(retorno) {
            retorno.path = retorno3.Codigo;
            var materia = self.materia;
            materia.clientes = self.todosClientesAdicionados();
            var data = {
              id_materia: retorno.id,
              id_materia_frags: JSON.stringify(retorno),
              json_data: JSON.stringify(materia),
              clientes: JSON.stringify(clientes),
              ids_arquivos: self.idsDosRecortes.join(","),
              id_programa: self.form.id_programa,
              id_projeto: self.form.id,
              id_rascunho: self.materia.id_rascunho,
              cliente_list: str_clientes,
              dia: self.form.dia,
              id_operador: $("#id_operador").val(),
              titulo: self.materia.titulo
            };

            obj_api.call("materia_new", "POST", data, function(retorno2) {
              self.materia.id_rascunho = retorno2.data.id_rascunho;
              self.materia.id_materia_radiotv_jornal =
                retorno2.data.id_materia_radiotv_jornal;

              if (self.onSave != null) {
                var materia = self.materia;
                materia.clientes = self.todosClientesAdicionados();

                self.onSave(materia);
              }

              obj_api.call(
                "materia_gerada/" + self.materia.id_materia_radiotv_jornal,
                "get",
                {},
                function(retorno3) {
                  self.materia.clientes = retorno3.clientes;
                  self.topicos_adicionais = [];
                }
              );

              self.salvando_materia = false;
              self.msg_salvando_materia = "Gerar Matéria";

              obj_alert.show(
                "Sucesso!",
                "Matéria criada com sucesso!",
                "success"
              );
            });
          }
        );
      });
    }
  }
};
</script>