app.define("mobile.slider_form",function()
{ 
    webix.ui
    ({
        id       : 'content',
        view     : "formview",
        dataview : "mobile.slider_view",
        update   : "site_carrusel-update",
        source   : {"action": "site_carrusel-row","id": ( __.current["slider"] != undefined ? __.current["slider"].id : 0 ) },
        store    : "slider",
        title_set: __.current["slider"] != undefined ? __.current["slider"].nombre.toUpperCase() : "",
        title_add: "NUEVO DESTACADO", 
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
                    { height: 40 },
                    { view:"segmented", name:"activo" , label:"Activo" , labelPosition:"top", width:150, required: true, options:[ {id:0, value:"No"}, {id:1, value:"Si"} ] },
                    { height: 40 },
                    { view:"text", name:"detalle", label:"Detalle", labelPosition:"top", labelWidth:100, required: true, validate: webix.rules.isNotEmpty },
                    { height: 40 },
                    { view:"text", name:"link"   , label:"Link"   , labelPosition:"top", labelWidth:100 },
                    { height: 40 },
                    {  
                        asRow     : true,    
                        view         : "image"          , 
                        name         : "imagen"         ,  
                        label        : "Imagen"         ,
                        idImage      : "tmpimga"        , 
                        pathImage    : "ui/img/slider/" ,
                        uploadLink   : __.req({"action":"upload-image", "move": "ui/img/slider"}),
                        defaultImage : __.base_url()+"cms/ui/img/image.png",
                        required     : true  
                    },
                    {} 
                ]
            }
        } 
    },
    $$('content'));    

});

