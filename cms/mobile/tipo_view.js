app.define("mobile.tipo_view",function()
{
    webix.ui
    ({
        id       : 'content'  ,
        width    : "100%"     ,
        height   : "auto"     ,
        rows:
        [
            {
                view     : "mobilist"               ,
                title    : "TIPO DE INMUEBLE"            ,
                form     : "mobile.tipo_form",
                store    : "tipo"              ,
                data_id  : "_dt_tipo"          , 
                type     : 
                { 
                    templateStart: "<div webix_l_id='#id#' class='webix_list_item'>",
                    template     : "http->./cms/ui/tpl/mobile_operacion.html",
                    templateEnd  : "</div>"
                },
                query    : { select:"site_prop_tipo" }  
            }
        ]
    },
    $$('content'));    

}); 