app.define("mobile.slider_view",function()
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
                title    : "DESTACADOS"            ,
                form     : "mobile.slider_form",
                store    : "slider"              ,
                data_id  : "_dt_slider"          , 
                type     : 
                { 
                    templateStart: "<div webix_l_id='#id#' class='webix_list_item'>",
                    template     : "http->./cms/ui/tpl/mobile_slider.html",
                    templateEnd  : "</div>"
                },
                query    : { select:"site_carrusel" }  
            }
        ]
    },
    $$('content'));    

}); 