app.define("app.abm.config_view",function()
{
    webix.ui ({ id : "_main_search", css : "acople input-text" , width : 440 }, $$("_main_search"));
    webix.ui
    ({
        id     : 'content'  ,
        view   : "datalist" ,
        title  : "CONFIGURACIÓN",
        form   : "app.abm.config_form",
        store  : "config" ,
        columns: [ 
            {id:"id"       , header:{text:"#"     , height:35}   , sort: 'int'    , adjust    : true },
            {id:"nombre"   , header:{text:"Name"  , height:35}   , sort: 'string' , adjust    : true }, 
            {id:"valor"    , header:{text:"Value" , height:35}   , sort: 'string' , fillspace : true }       
        ],
        query : { select:"site_config" } 
    },
    $$('content'));

}); 