export default class RequestInitializerFactory {
    get() {
        return this._criaInitializer("GET");
    }
    _criaInitializer(method) {
        const headers = new Headers();
        headers.append("Authorization", window.API_AUTHORIZATION);
        headers.append("apiauth", window.API_MYAUTH);

        headers.append("Content-Type", "application/x-www-form-urlencoded");
        
        return {
            method: method,
            headers: headers,
            mode: "cors",
            cache: "default"
        }
    }
}