var obj_datatable = {
    dataTable : function (selector, parameters) {
        var table = null;
      
        if ($.fn.dataTable.isDataTable(selector)) {
            table = $(selector).DataTable();
        } else {                
            table = $(selector).DataTable(parameters);
        }

        return table;
    },
    getPageLength : function() {
        return 50;
    },
    getLanguage: function (){
        
               var language = {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": "_MENU_ resultados por página",
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": "Nenhum registro encontrado",
                    "sSearch": "Pesquisar",
                    "oPaginate": {
                        "sNext": "Próximo",
                        "sPrevious": "Anterior",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    }
                };
                
                return language;
        
    }
    
    
}

window.obj_datatable = obj_datatable;