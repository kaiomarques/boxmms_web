import RequestInitializerFactory from "./shared/requests/RequestInitializerFactory";

export default class CanaisService {
    constructor() {
        this.requestInitializerFactory = new RequestInitializerFactory();
    }
    async listaAsync(clienteId) {
        let url = "api/v1/canais";
        
        if (clienteId) {
            url = `api/v1/clientes/${clienteId}/canais`;
        }
        
        const response = await fetch(url, this.requestInitializerFactory.get());
        const json = await response.json();
        return json.data;
    }
}