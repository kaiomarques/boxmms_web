<template>
  <div>
    <div v-bind:style="style_list()">
      <div style="padding-top: 10px">
        <div class="col-xs-1" style="padding-top: 20px">
          <button class="btn btn-primary btn-lg" v-on:click="reload_table_search">
            <i class="fa fa-search" v-if="!loading"></i>
            <i class="fa fa-spinner" v-if="loading"></i>
            Criar
          </button>
          <button
            v-if="mostra_add"
            class="btn btn-default btn-lg"
            v-on:click="open_form"
            v-html="button_new_text"
          ></button>
        </div>
      </div>

      <div class="col-xs-12">
        <table
          id="table_data"
          class="table table-bordered table-striped display"
          style="width: 100%"
        >
          <thead>
            <tr>
              <th>ID</th>
              <th>Nome</th>
              <th>Link</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>

    <div v-if="action =='form'" class="col-xs-12">
      <evento_transcricao
        v-bind:id_load="id_evento"
        v-bind:id_load_arquivo="id_evento_arquivo"
        v-bind:onSave="onSave"
        show_back_button="true"
        v-bind:onBack="onBack"
        v-bind:tempo_inicio="tempo_seg"
        v-bind:onSelectFilho="onSelectFilho"
      ></evento_transcricao>
    </div>

    <div v-if="action =='projeto' && item_filho != null " class="col-xs-12">
      <evento_projeto
        v-bind:id_load="item_filho.id"
        v-bind:id_load_arquivo="id_evento_arquivo"
        v-bind:tempo_inicio="tempo_seg"
        show_back_button="true"
        v-bind:onBack="onBackProjeto"
      ></evento_projeto>
    </div>

    <div v-if="action =='form' && false" class="col-xs-12">
      <agrupamento_notificacoes_form
        v-bind:id_load="id"
        v-bind:onSave="onSave"
        show_back_button="true"
        v-bind:onBack="onBack"
      ></agrupamento_notificacoes_form>
    </div>
  </div>
</template>

<script>
import Util from "../../library/Util";
import evento_projeto from "../eventos/EventosProjeto.vue";
import evento_transcricao from "../eventos/EventosTranscricao.vue";

export default {
  components: {
    evento_projeto: evento_projeto,
    evento_transcricao: evento_transcricao
  },
  props: {
    prop_status: {
      type: String,
      default() {
        return null;
      }
    }
  },
  data: function() {
    return {
      action: "list",
      id: "",

      id_evento: null,
      id_evento_arquivo: null,
      tempo_seg: null,
      item_filho: null,

      table: null,
      filtro_titulo: "",
      filtro_status: "",

      show_new_button: true,

      show_modal: false,
      loading: false,
      mostra_add: false,

      tempo_seg: -1,
      interval: null,
      o_interval: null,

      data_filtro: {
        id_cliente: null,

        nome_cliente: "",
        id_programa: null,
        id_emissora: null,
        id_evento_status: null,
        palavra: "",

        filtro_dtinicio: "",
        filtro_dtfim: "",
        status: ""
      },

      button_new_text: "" //<i class=\"fa fa-file\" ></i> NOVA POST"
    };
  },
  methods: {
    getObjFiltro() {
      var data = {
        dt_inicio: Util.BrDateToUS($("#filtro_dtinicio").val()),
        dt_fim: Util.BrDateToUS($("#filtro_dtfim").val())
      };

      if (this.prop_status != null) {
        this.data_filtro.status = this.prop_status;
      }

      if (this.data_filtro != null) {
        data["id_programa"] = this.data_filtro.id_programa;
        data["cliente_nome"] = this.data_filtro.cliente_nome;
        data["status"] = this.data_filtro.status;
        data["palavra"] = this.data_filtro.palavra;
        data["id_emissora"] = this.data_filtro.id_emissora;
        data["id_evento_status"] = this.data_filtro.id_evento_status;
        data["veiculo_id"] = this.data_filtro.id_veiculo;

        this.data_filtro.filtro_dtinicio = $("#filtro_dtinicio").val();
        this.data_filtro.filtro_dtfim = $("#filtro_dtfim").val();
      }

      console.log("filtro?");
      console.log(data);

      return data;
    },
    closeModal() {
      this.show_modal = false;
    },
    onBack(objPost) {
      //Clicou no back button.
      this.id = ""; //Voltando para a lista
      this.action = "list";
    },

    onBackProjeto(form) {
      this.id = ""; //Voltando para a lista
      this.action = "list";
      //this.action = "form";
    },

    onSelectFilho(item, index) {
      this.item_filho = item;
      this.action = "projeto";
    },

    open_form() {
      this.id = "";
      this.action = "form";
    },

    editar(datarow) {
      this.id = datarow.id;

      this.id_evento_arquivo = datarow.id_evento_arquivo;
      this.id_evento = datarow.id_evento;
      this.tempo_seg = datarow.tempo_seg;

      this.action = "form";
    },
    onSave() {
      this.refresh_table();
    },
    callEventosBloqueados() {
      var self = this;

      if (self.interval == null) {
        window.o_interval = setInterval(function() {
          //code goes here that will be run every 5 seconds.
          self.getEventosBloqueados();
          self.interval = 1;
        }, 15000);
      }
    },

    getEventosBloqueados() {
      var self = this;
      console.log("getEventosBloqueados " + this.$route.name);

      if (
        this.$route.name != "notificacoes2" &&
        this.$route.name != "home" &&
        window.o_interval != null &&
        window.o_interval != undefined
      ) {
        clearInterval(window.o_interval);
        console.log("clear interval");
        return;
      }

      if (self.table == null) {
        return;
      }

      if (self.action != "list") {
        return;
      }

      var ids = new Array();
      $(".hd_input_id").each(function() {
        var input = $(this); // This is the jquery object of the input, do what you will
        ids[ids.length] = input.val();
      });

      if (ids.length <= 0) {
        return;
      }

      var id_operador = $("#id_operador").val();

      /*obj_api.call(
        "agrupamento_status",
        "POST",
        { ids: ids.join(",") },
        function(response) {
          //console.log(response);
          var data2 = response.data;

          for (var i = 0; i < data2.length; i++) {
            $("#row_data_" + data2[i].id.toString()).removeClass(
              "background_bloqueado"
            );
            $("#row_data_" + data2[i].id.toString()).removeClass(
              "background_azul"
            );

            if (
              data2[i].status == 2 &&
              (data2[i].bloqueado_por_id == null ||
                data2[i].bloqueado_por_id.toString() != id_operador)
            ) {
              $("#row_data_" + data2[i].id.toString()).addClass(
                "background_bloqueado"
              );
            } else {
              if (data2[i].status_notificacao == 3) {
                $("#row_data_" + data2[i].id.toString()).addClass(
                  "background_azul"
                );
              }
            }

            var campodid = "div_evento_palavras_" + data2[i].id.toString();
            $("#" + campodid).html(data2[i].palavras);
          }
        }
      );*/
    },

    refresh_table() {
      var page = this.table.page.info().page;
      this.reload_table_search(page);
    },

    reload_table_search(page) {
      var self = this;
      self.loading = true;
      if (this.table != null) {
        var filtro = this.getObjFiltro();

        obj_api.call(
          "agrupamento_notificacoes_filtro",
          "POST",
          filtro,
          function(retorno) {
            var dataSet = retorno.data;

            self.table.clear().draw();
            self.table.rows.add(dataSet); // Add new data

            if (page != null && page != undefined && page > 0) {
              // self.table.displayStart = page; //fnPageChange(page, true); //this.table.displayStart
              self.table.columns.adjust().draw(); // Redraw the DataTable
            } else {
              self.table.columns.adjust().draw();
            }
            self.loading = false;
          }
        );
      }
    },

    style_list() {
      if (this.action == "form" || this.action == "projeto") {
        return "display:none";
      }
      return "";
    }
  },
  computed: {},
  mounted() {
    let self = this;

    self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';
    var filtro = {}; //this.data_filtro;

    if (this.prop_status != null) {
      filtro["status"] = this.prop_status;
    }

    obj_api.call("lista_campanhas", "POST", filtro, function(
      retorno
    ) {
      var dataSet = retorno.data;

      var table = obj_datatable.dataTable("#table_data", {
        //"dom" : "Bfrtip",
            pageLength: obj_datatable.getPageLength(),
            pagingType: "full_numbers",
            language: obj_datatable.getLanguage(),
            responsive: true,
            processing: true,
            lengthChange: false,
            searching: false,
            rowId: "id_row",

            data: dataSet,
            columns: [
              { data: "id" },
              { data: "nome" },
              { data: "id_cliente" }
            ],
            order: [[0, "desc"]],
          });
      
      

      self.callEventosBloqueados();

      setTimeout(function() {
        self.getEventosBloqueados();
      }, 1000);

      table.on("draw", function() {
        self.getEventosBloqueados();
      });

      self.table = table;
    });
    self.button_new_text = '<i class="fa fa-user" ></i> CADASTRAR';
  }
};
</script>
