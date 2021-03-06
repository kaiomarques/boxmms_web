export default {

    getListByFilesIds : function (ids,  fn_resultado) {
        

            obj_api.call("clientes?filesIds=" + ids, "get", {}, function (result) {
                window.localStorage.setItem("ls_clientes", JSON.stringify(result));

                if (fn_resultado != null) {
                    fn_resultado(result);
                }

            });
    },
    getList: function (fn_resultado) {


        if (window.localStorage.getItem("ls_clientes") == null) {

            obj_api.call("clientes?todos=1", "get", {}, function (result) {
                window.localStorage.setItem("ls_clientes", JSON.stringify(result.data));

                if (fn_resultado != null) {
                    fn_resultado(result.data);
                }

            });
        } else {

            if (fn_resultado != null) {
                fn_resultado(JSON.parse(window.localStorage.getItem("ls_clientes")));
            }
        }

    }

}