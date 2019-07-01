app.define("app.abm.slider_form",function()
{
    webix.ui ({ id : "_main_search", css : "acople input-text" , width : 440 }, $$("_main_search"));
    webix.ui
    ({
        id       : 'content',
        view     : "formview",
        dataview : "app.abm.slider_view",
        update   : "site_carrusel-update",
        source   : {"action": "site_carrusel-row","id": ( __.current["slider"] != undefined ? __.current["slider"].id : 0 ) },
        store    : "slider",
        title_set: __.current["slider"] != undefined ? __.current["slider"].detalle.toUpperCase() : "",
        title_add: "NUEVO DESTACADO", 
        validate : true,
        elements :
        {
            padding: 25,
            rows:
            [
                { height: 40 },
                {
                    cols:
                    [
                        { view:"text", name:"nombre" , label:"Name"   , labelPosition:"top", labelWidth:100, required: true, validate: webix.rules.isNotEmpty },
                        { width: 25 },
                        { view:"segmented", name:"activo" , label:"Activo" , labelPosition:"top", width:150, required: true, options:[ {id:0, value:"No"}, {id:1, value:"Si"} ] }
                    ]

                },
                { height: 25 },
                {
                    cols:
                    [
                        { view:"text", name:"detalle", label:"Detalle", labelPosition:"top", labelWidth:100, required: true, validate: webix.rules.isNotEmpty },
                        { width: 25 },
                        { view:"text", name:"link"   , label:"Link"   , labelPosition:"top", labelWidth:100 },
                        { width: 25 },
                        {   
                            view         : "image"          , 
                            name         : "imagen"         ,  
                            label        : "Imagen"         ,
                            idImage      : "tmpimga"        , 
                            pathImage    : "ui/img/slider/" ,
                            uploadLink   : __.req({"action":"upload-image", "move": "ui/img/slider"}),
                            defaultImage : __.base_url()+"cms/ui/img/image.png",
                            required     : true  
                        }
                    ]
                },  
                {} 
            ]
        } 
    },
    $$('content'));    

});

