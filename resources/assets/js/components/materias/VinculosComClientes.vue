<template>
  <div>
    <div class="col-xs-10">
      <div class="form-group">
        <label>Adicionar Cliente</label>
        <vueSelect
          name="clientes"
          :options="listaDeClientes"
          v-model="clienteSelecionadoId"
          v-if="visivel"
          :searchable="true"
          language="pt-BR"
          :placeholder="placeholder"
          ref="vueSelect"
        ></vueSelect>
      </div>
    </div>

    <div class="col-xs-2" style="padding-top: 25px">
      <button type="button" v-on:click="adicionaCliente" class="btn btn-default pull-right">
        <i class="fa fa-plus"></i> Adicionar
      </button>
    </div>

    <div class="col-xs-12">
      <table class="table table-striped table-bordered" v-if="exibirVinculos">
        <thead>
          <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Canal</th>
            <th style="text-align: center;">Citação Direta</th>
            <th style="width: 30px"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="vinculo in vinculosAdicionados" :key="vinculo.id">
            <td>{{vinculo.clienteId}}</td>
            <td>{{vinculo.clienteNome}}</td>
            <td>
              <select
                v-model="vinculo.canal"
                class="form-control"
                v-if="vinculo.canais"
                v-on:change="canalAlterado(vinculo.id)"
                :disabled="!vinculo.canalHabilitado"
              >
                <option
                  v-for="canal in vinculo.canaisExibidos"
                  :key="canal.id"
                  :value="canal.id"
                  :style="canal.impar ? 'background: #E1E1E1' : 'background: white; padding-left: 10px;'"
                >{{canal.nome}}</option>
              </select>
            </td>
            <td style="text-align: center;">{{vinculo.citaDiretamente == "1" ? "Sim" : "Não"}}</td>
            <td>
              <button
                type="button"
                v-on:click="salvaVinculo(vinculo)"
                v-if="vinculo.canalHabilitado"
                class="btn btn-default pull-right"
              >
                <i class="fa fa-check"></i>
              </button>
              <button
                type="button"
                v-on:click="removeVinculo(vinculo)"
                v-if="!vinculo.canalHabilitado"
                class="btn btn-default pull-right"
              >
                <i class="fa fa-remove"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
<script type="text/javascript">
import vueSelect from "../../library/vue-select/src/Select";
import CanaisService from "../../services/CanaisService";
import Vinculo from "./VinculoComClientesViewModel";

export default {
  components: {
    vueSelect
  },
  props: {
    clientes: {
      default: () => [],
      type: Array
    },
    vinculos: {
      default: () => [],
      type: Array
    }
  },
  service: null,
  data() {
    return {
      idAtual: 1,
      todosVinculos: [],
      listaDeClientes: [],
      vinculosAdicionados: [],
      clientesCarregados: [],
      clienteSelecionadoId: null,
      visivel: true,
      placeholder: "Digite o nome do cliente..."
    };
  },
  mounted() {
    this.service = new CanaisService();
  },
  watch: {
    vinculosAdicionados(vinculos) {
      this.todosVinculos = [];

      for (const vinculo of vinculos) {
        this.todosVinculos.push(vinculo);
      }
    },
    clientes(clientes) {
      for (const cliente of clientes) {
        this.listaDeClientes.push({ value: cliente.id, label: cliente.nome });
      }
    },
    vinculos(vinculos) {
      
      for(const vinculo of vinculos) {
        const novoVinculo = new Vinculo(
          this.idAtual,
          vinculo.clienteId,
          vinculo.clienteNome,
          0
        );

        this.idAtual = this.idAtual + 1;
        this.vinculosAdicionados.push(novoVinculo);
        novoVinculo.adicionaCanal({ id: vinculo.canalId, nome: vinculo.canalNome});
        novoVinculo.canal = vinculo.canalId;
        this.salvaVinculo(novoVinculo);
        novoVinculo.desabilitaCanal();
      }
    }
  },
  methods: {
    buscaCliente(id) {
      for (const cliente of this.clientesCarregados) {
        if (cliente.id == id) return cliente;
      }

      return null;
    },
    clienteCarregado(cliente) {
      for (const vinculo of this.vinculosAdicionados) {
        if (vinculo.clienteId == cliente.id) {
          for (const canal of cliente.canaisDisponiveis) {
            vinculo.adicionaCanal(canal);
          }
        }
      }
    },
    buscaVinculo(id) {
      for (const vinculo of this.vinculosAdicionados) {
        if (vinculo.id == id) return vinculo;
      }

      return null;
    },
    canalAlterado(vinculoId) {
      const vinculo = this.buscaVinculo(vinculoId);
      if (vinculo == null) return;

      const cliente = this.buscaCliente(vinculo.clienteId);
      cliente.marcaComoSelecionado(vinculo.canal);
    },
    async adicionaCliente() {
      const self = this;
      const clienteSelecionado = this.clienteSelecionado;
      const cliente = this.buscaCliente(clienteSelecionado.id);
      const vinculo = new Vinculo(
        this.idAtual,
        clienteSelecionado.id,
        clienteSelecionado.nome,
        0
      );
      this.idAtual = this.idAtual + 1;
      this.vinculosAdicionados.push(vinculo);
      this.$refs.vueSelect.limpar();

      if (cliente != null) {
        for (const canal of cliente.canaisDisponiveis) {
          vinculo.adicionaCanal(canal);
        }
      } else {
        const canais = await this.service.listaAsync(clienteSelecionado.id);
        const novoCliente = {
          id: clienteSelecionado.id,
          nome: clienteSelecionado.nome,
          canais: [],
          adicionaCanal(canal) {
            this.canais.push({ canal: canal, disponivel: true });
          },
          get canaisDisponiveis() {
            const canais = [];
            for (const canal of this.canais) {
              if (canal.disponivel) {
                canais.push(canal.canal);
              }
            }
            return canais;
          },
          marcaComoSelecionado(canalId) {
            for (const canal of this.canais) {
              if (canal.canal.id == canalId) {
                canal.disponivel = false;
              }
            }
          }
        };
        self.clientesCarregados.push(novoCliente);

        for (const canal of canais) {
          novoCliente.adicionaCanal(canal);
        }

        self.clienteCarregado(novoCliente);
      }
    },
    removeVinculo(vinculo) {
      const vinculos = [];

      for(let i = 0; i < this.vinculosAdicionados.length; i++) {
        const item = this.vinculosAdicionados[i];

        if (item.id != vinculo.id) {
          vinculos.push(item);      
        }
      }
      
      this.vinculosAdicionados = vinculos;
    },
    salvaVinculo(vinculo) {
      vinculo.desabilitaCanal();
    }
  },
  computed: {
    exibirVinculos() {
      return this.vinculosAdicionados.length > 0;
    },
    clienteSelecionado() {
      if (this.clienteSelecionadoId == null) return null;

      for (const cliente of this.clientes) {
        if (cliente.id == this.clienteSelecionadoId) return cliente;
      }

      return null;
    }
  }
};
</script>