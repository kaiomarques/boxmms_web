import RequestInitializerFactory from "./shared/requests/RequestInitializerFactory";

export default class ClientesService {
    constructor() {
        this.requestInitializerFactory = new RequestInitializerFactory();
    }
    async listaAsync() {
        const response = await fetch("api/v1/clientes", this.requestInitializerFactory.get());
        const json = await response.json();
        return json.data;
    }
}