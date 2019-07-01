app.define("mobile.operaciones_form",function()
{ 
    webix.ui
    ({
        id       : 'content',
        view     : "formview",
        dataview : "mobile.operaciones_view",
        update   : "site_prop_operacion-update",
        source   : {"action": "site_prop_operacion-row","id": ( __.current["operacion"] != undefined ? __.current["operacion"].id : 0 ) },
        store    : "operacion",
        title_set: __.current["operacion"] != undefined ? __.current["operacion"].nombre.toUpperCase() : "",
        title_add: "NUEVA OPERACION", 
        validate : true,
        elements :
        {
            view   : "scrollview",  
            css    : "focus-scroll",
            scroll : "y", 
            body   :
            {
                padding: 10,
                rows   :
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