app.define("app.abm.config_form",function()
{
    webix.ui ({ id : "_main_search", css : "acople input-text" , width : 440 }, $$("_main_search"));
    webix.ui
    ({
        id       : 'content',
        view     : "formview",
        dataview : "app.abm.config_view",
        update   : "site_config-update",
        source   : {"action": "site_config-row","id": ( __.current["config"] != undefined ? __.current["config"].id : 0 ) },
        store    : "config",
        title_set: __.current["config"] != undefined ? "VARIABLE "+__.current["config"].nombre.toUpperCase() : "",
        title_add: "NUEVA VARIABLE", 
        validate : true,
        elements :
        {
            padding : 25,
            rows    :
            [
                { height: 40 },
                { view:"text", name:"nombre", label:"Name", labelPosition:"top", labelWidth:100, pattern:{ mask:"####################", allow:/[a-z_]/g }, required: true, invalidMessage: "Estan permitidos caracteres a-z con _ (guion bajo)" },
                { height: 40 },
                { view:"text", name:"valor", label:"Value", labelPosition:"top", labelWidth:100 }, 
                {} 
            ]
        } 
    },
    $$('content'));

});