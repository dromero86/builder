app.define("app.abm.mail_view",function()
{
 
    var on_search_change = function()
    {  
        var value     = $$("_main_search").getValue().toLowerCase();
        var splitval  = value.split(" ");
        var svalue    = '';
        for(var i=0;i<splitval.length;i++)
        {
            svalue=splitval[i];

            $$("_dt_mail").filter(function(obj)
            {
                return obj.mensaje.toLowerCase().indexOf(svalue)!=-1;
            })
        } 
    };


    webix.ui
    ({
        id         : "_main_search", 
        css        : "acople input-text" ,
        view       : "text"              ,
        placeholder: "Buscar mensaje", 
        width      : 440  
    }, $$("_main_search"));

    document.querySelector("[view_id='_main_search'] div input").onkeypress = function(code)
    { 
        try{ clearTimeout(timer); }catch(e){}

        timer = setTimeout( on_search_change, 100);
    };
 

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
                        click     : function(){  app.require("app.dashcenter"); }             
                    }, 
                    { view  : "label" , label : "MENSAJES" },
                    {                    
                    }
                ]
            },
            {   
                id          : "_dt_mail" ,
                view        : "datatable"       ,   
                resizeColumn: true              ,
                navigation  : true              , 
                select      : "row"             ,  
                rowHeight   : 53                ,
                columns     : 
                [ 
                    {id:"id"        , header:{text:"#"      , height:35}   , sort: 'int'    , adjust      : true },
                    {id:"ayn"       , header:{text:"De"     , height:35}   , sort: 'string' , adjust      : true } ,
                    {id:"mensaje"   , header:{text:"Asunto" , height:35}   , sort: 'string' , fillspace   : true },
                    {id:"tipo_email", header:{text:"Tipo"   , height:35}   , sort: 'int'    , adjust      : true , options:[{id:"1", value:"Contacto"}, {id:"2", value:"Tasacion"}, {id:"3", value:"Propiedad"}]} ,
                    {id:"fecha"     , header:{text:"Fecha"  , height:35}   , sort: 'string' , adjust      : true , format : webix.Date.dateToStr("%d/%m/%Y")   }       
                ] ,
                flag : false ,
                on:
                {   
                    onAfterRender: function()
                    {
                        var table = this;

                        if(table.config.flag == false)
                        {
                            table.config.flag = true;

                            __.PAYLOAD({"action":"databot"}, { select: "site_contacto"} , function(response){
                                
                                var result = JSON.parse(response);

                                table.parse(result.data);
                            });
                        }
                    },
                    onItemClick: function(id)
                    {

                        var object = this.getItem(id); 

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

                    }
                }
            }, 
            {view:"resizer"},
            {
                id       : "_details",
                css      : "_mail_theme",
                template : "No hay mensajes seleccionados"
            }            
        ] 
    },
    $$('content'));

}); 