app.define("mobile.tipo_form",function()
{ 
    webix.ui
    ({
        id       : 'content',
        view     : "formview",
        dataview : "mobile.tipo_view",
        update   : "site_prop_tipo-update",
        source   : {"action": "site_prop_tipo-row","id": ( __.current["tipo"] != undefined ? __.current["tipo"].id : 0 ) },
        store    : "tipo",
        title_set: __.current["tipo"] != undefined ? __.current["tipo"].nombre.toUpperCase() : "",
        title_add: "NUEVO TIPO DE INMUEBLE", 
        validate : true,
        elements :
        {
            view   : "scrollview",  
            css    : "focus-scroll",
            scroll : "y", 
            body   :
            {
                padding: 10,
                rows:
                [
                    { height: 40 },
                    { view:"text", name:"nombre", label:"Name", labelPosition:"top", labelWidth:100, required: true, validate: webix.rules.isNotEmpty },
                    {} 
                ]
            }
        } 
    },
    $$('content'));    

});

