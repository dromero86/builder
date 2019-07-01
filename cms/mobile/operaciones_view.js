app.define("mobile.operaciones_view",function()
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
                title    : "OPERACIONES"            ,
                form     : "mobile.operaciones_form",
                store    : "operacion"              ,
                data_id  : "_dt_operacion"          , 
                type     : 
                { 
                    templateStart: "<div webix_l_id='#id#' class='webix_list_item'>",
                    template     : "http->./cms/ui/tpl/mobile_operacion.html",
                    templateEnd  : "</div>"
                },
                query    : { select:"site_prop_operacion" }  
            }
        ]
    },
    $$('content'));
   
}); 