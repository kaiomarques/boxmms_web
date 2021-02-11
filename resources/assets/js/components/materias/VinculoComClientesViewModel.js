export default class VinculoComClientesViewModel {
    constructor(id, clienteId, clienteNome, citaDiretamente) {
        this.id = id;
        this.clienteId = clienteId;
        this.clienteNome = clienteNome;
        this.citaDiretamente = citaDiretamente;
        this.canal = null;
        this.canalHabilitado = true;
        this.canais = [];        
    }

    adicionaCanal(canal) {
        this.canais.push(canal);
    }

    desabilitaCanal() {
        this.canalHabilitado = false;
    }

    get canaisExibidos() {
        if (this.canalHabilitado)
            return this.canais;

        const canais = [];

        for(const canal of this.canais) {
            if (canal.id == this.canal) {
                canais.push(canal);
                break;
            }
        }

        return canais;
    }

    get impar() {
        return this.id % 2 != 0;
    }
}