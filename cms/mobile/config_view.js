app.define("mobile.config_view",function()
{ 
    webix.ui
    ({
        id       : 'content'  ,
        width    : "100%"     ,
        height   : "auto"     ,
        rows:
        [
            {
                view     : "mobilist"          ,
                title    : "CONFIGURACIÓN"     ,
                form     : "mobile.config_form",
                store    : "config"            ,
                data_id  : "_dt_config"        , 
                type     : 
                { 
                    templateStart: "<div webix_l_id='#id#' class='webix_list_item'>",
                    template     : "http->./cms/ui/tpl/mobile_config.html",
                    templateEnd  : "</div>"
                },
                query    : { select:"site_config" }  
            }
        ]
    },
    $$('content'));
   
}); 