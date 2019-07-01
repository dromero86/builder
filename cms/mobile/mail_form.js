app.define("mobile.mail_form",function()
{
    webix.ui
    ({
        id         : 'content'  ,
        width      : '100%'      ,
        height     : 'auto'      , 
        type       : "space"     , 
        css        : "data-view" ,
        borderless : true        ,
        rows       :
        [
            { 
                view  : "toolbar",
                css   : "toolbar-interior",       
                cols  : 
                [
                    {
                        view      : "button"    , 
                        type      : "icon"      , 
                        icon      : "chevron-left",
                        width     : 45          , 
                        align     : "center"    , 
                        css       : "app_button", 
                        borderless: true        ,
                        click     : function(){  app.require("mobile.mail_view"); }             
                    }, 
                    { view  : "label" , label :  ( parseInt(__.current["mail"].tipo_email) == 1 ? "MENSAJE DE CONTACTO" : ( parseInt(__.current["mail"].tipo_email) == 2 ? "PEDIDO DE TASACION" : "CONSULTA DE PROPIEDAD" ) )  }
                ]
            },
            {
                id       : "_details",
                css      : "_mail_theme",
                template : "No hay mensajes seleccionados"
            }
        ]
    },
    $$('content'));    


    var object = __.current["mail"]; 

    var tipo_email = parseInt(object.tipo_email);

    var format      = webix.Date.dateToStr("%d/%m/%Y");

    var html = "";

    html = html+"<div class='mail-table'><div class='mail-row'><div class='mail_title'><b>De:</b> "+object.ayn+" ("+object.email+")</div> <div class='mail_timezone'> <b>Enviado:</b> "+format(object.fecha)+"</div> </div></div>";
    html = html+"<div class='mail-table'><div class='mail-row'><center class='mail-body'  colspan='2'><blockquote>"+object.mensaje+"</blockquote></center></div></div>";

    switch(tipo_email)
    {
        case 1: 
            html = html+"<div class='mail-table'><div class='mail-row'><center class='mail-bottom'>Mensaje enviado desde el <b>formulario de contacto</b> </center></div></div>";
        break; //tasacion

        case 2:  
            html = html+"<div class='mail-table'><div class='mail-row'><center class='mail-data'><span data='tipo'></span> en <span data='operacion'></span> en: <span data='domicilio'>"+object.domicilio+"</span>, <span data='barrio'>"+object.barrio+"</span>, <span data='localidad'>"+object.localidad+"</span>, <span data='provincia'>"+object.provincia+"</span> </center></div></div>"; 

            html = html+"<div class='mail-table'><div class='mail-row'><center class='mail-bottom'>Mensaje enviado desde el <b>formulario de tasacion</b> </center></div></div>";
        break; //tasacion 

        case 3: 

            html = html+"<div class='mail-table'><div class='mail-row'><center><span data='prop'></span></center></div></div>";
            html = html+"<div class='mail-table'><div class='mail-row'><center class='mail-bottom'>Mensaje enviado desde el <b>formulario de propiedad</b> </center></div></div>";
        break; //propiedad

    }

    $$("_details").define("template",html);
    $$("_details").render();

    if(tipo_email == 2)
    {
        __.PAYLOAD({"action":"databot"}, { select:"site_prop_tipo", filter:[ {field: "id", is:"=", to: object.tipo }] } , function(response){
            
            var result = JSON.parse(response);

            for( var i in result.data )
            {
                var item = result.data[i];

                document.querySelector("[data='tipo']").innerHTML = item.nombre;
            }
        });

        __.PAYLOAD({"action":"databot"}, { select:"site_prop_operacion", filter:[ {field: "id", is:"=", to: object.operacion }] } , function(response){
            
            var result = JSON.parse(response);

            for( var i in result.data )
            {
                var item = result.data[i];

                document.querySelector("[data='operacion']").innerHTML = item.nombre;
            }
        });
    }

    if(tipo_email == 3)
    {
        __.PAYLOAD({"action":"databot"}, { select:"site_prop", join:"", filter:[ {field: "id", is:"=", to: object.prop }] } , function(response){
            
            var result = JSON.parse(response);

            for( var i in result.data )
            {
                var item = result.data[i];

                document.querySelector("[data='prop']").innerHTML = item.site_prop_tipo_nombre+" en "+item.site_prop_operacion_nombre+": "+item.addr+", "+item.barrio;
            }
        });
    }

}); 