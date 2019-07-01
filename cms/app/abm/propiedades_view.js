app.define("app.abm.propiedades_view",function()
{

    var on_search_change = function()
    {  
        var value     = $$("_main_search").getValue().toLowerCase();
        var splitval  = value.split(" ");
        var svalue    = '';
        for(var i=0;i<splitval.length;i++)
        {
            svalue=splitval[i];

            $$("_dt_propiedades").filter(function(obj)
            {
                return obj.addr.toLowerCase().indexOf(svalue)!=-1;
            })
        } 
    };


    webix.ui
    ({
        id         : "_main_search", 
        css        : "acople input-text" ,
        view       : "text"              ,
        placeholder: "Buscar propiedades", 
        width      : 440  
    }, $$("_main_search"));

    document.querySelector("[view_id='_main_search'] div input").onkeypress = function(code)
    { 
        try{ clearTimeout(timer); }catch(e){}

        timer = setTimeout( on_search_change, 100);
    };

    webix.ui
    ({
        id     : 'content'  ,
        view   : "datalist" ,
        title  : "PROPIEDADES",
        form   : "app.abm.propiedades_form",
        store  : "propiedad" ,
        data_id: "_dt_propiedades",
        columns: [ 
            {id:"id"        , header:{text:"#"        , height:35}   , sort: 'int'    , adjust      : true },
            {id:"addr"      , header:{text:"Dirección"  , height:35}   , sort: 'string' , fillspace   : true }, 
            {id:"barrio"    , header:{text:"Barrio"   , height:35}   , sort: 'string' , adjust      : true }, 
            {id:"tipo_prop" , header:{text:"Tipo"     , height:35}   , sort: 'string' , adjust      : true, options: __.req({"action":"site_prop_tipo-combo"}) },     
            {id:"tipo_op"   , header:{text:"Operacion", height:35}   , sort: 'string' , adjust      : true, options: __.req({"action":"site_prop_operacion-combo"})  },      
        ],
        query : { select:"site_prop" } 
    },
    $$('content'));

}); 