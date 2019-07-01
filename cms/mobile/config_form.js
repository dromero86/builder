app.define("mobile.config_form",function()
{ 
    webix.ui
    ({
        id       : 'content',
        view     : "formview",
        dataview : "mobile.config_view",
        update   : "site_config-update",
        source   : {"action": "site_config-row","id": ( __.current["config"] != undefined ? __.current["config"].id : 0 ) },
        store    : "config",
        title_set: __.current["config"] != undefined ? "VARIABLE "+__.current["config"].nombre.toUpperCase() : "",
        title_add: "NUEVA VARIABLE", 
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
                    {  view:"text", name:"nombre", label:"Name", labelPosition:"top", labelWidth:100, pattern:{ mask:"####################", allow:/[a-z_]/g }, required: true, invalidMessage: "Estan permitidos caracteres a-z con _ (guion bajo)" },
                    { height: 40 },
                    {  view:"text", name:"valor", label:"Value", labelPosition:"top", labelWidth:100 },
                    {}
                ]
            }
        },
        on:
        {
            formReady: function(view, isComplete)
            {
                console.log("formReady",view, isComplete); 
            }
        } 
    },
    $$('content'));    

});

