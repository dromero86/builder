app.define("app.abm.tipo_view",function()
{
    var on_search_change = function()
    {  
        var value     = $$("_main_search").getValue().toLowerCase();
        var splitval  = value.split(" ");
        var svalue    = '';
        for(var i=0;i<splitval.length;i++)
        {
            svalue=splitval[i];

            $$("_dt_tipo").filter(function(obj)
            {
                return obj.nombre.toLowerCase().indexOf(svalue)!=-1;
            })
        } 
    };


    webix.ui
    ({
        id         : "_main_search", 
        css        : "acople input-text" ,
        view       : "text"              ,
        placeholder: "Buscar tipo de inmueble", 
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
        title  : "TIPO DE INMUEBLE",
        form   : "app.abm.tipo_form",
        store  : "tipo" ,
        data_id: "_dt_tipo",
        columns: [ 
            {id:"id"        , header:{text:"#"        , height:35}   , sort: 'int'    , adjust      : true },
            {id:"nombre"    , header:{text:"Nombre"   , height:35}   , sort: 'string' , fillspace   : true }       
        ],
        query : { select:"site_prop_tipo" } 
    },
    $$('content'));

}); 