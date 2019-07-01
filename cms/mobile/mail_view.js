app.define("mobile.mail_view",function()
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
                title    : "MENSAJES"  ,
                form     : "mobile.mail_form"  ,
                store    : "mail"              ,
                data_id  : "_dt_mail"          , 
                type     : 
                { 
                    templateStart: "<div webix_l_id='#id#' class='webix_list_item'>",
                    template     : "http->./cms/ui/tpl/mobile_mail.html",
                    templateEnd  : "</div>"
                },
                query    : { select:"site_contacto" },
                on:
                {
                    onAfterDataParse: function(view)
                    {
                        $$("_ml_btn_add").hide();

                        var stack = document.querySelectorAll("[data='fecha']");
                        var format      = webix.Date.dateToStr("%d/%m/%Y");
  
                        for( var i in stack)
                        { 
                            if(typeof stack[i] == "object" )
                            {  
                                var fecha = stack[i].innerHTML;

                                stack[i].innerHTML = format(fecha);
                            }
                        }
                    }
                } 
            }
        ]
    },
    $$('content'));    

}); 