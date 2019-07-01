app.define("app.settings.view_menu_context",function()
{  

    var contextEvent = {

        option_view_new : function(choice, item)
        {
            webix.ui
            ({
                id          : "_option_view_new"   ,
                css         : "builder-modal"      ,
                view        : "window" , 
                position    : "center" , 
                headHeight  : 70       ,
                modal       : true     ,
                borderless  : true     ,
                resize      : true     ,
                margin      : 0        ,
                padding     : 0        ,
                head        : 
                {
                    view     : "toolbar", 
                    height   : 70,
                    elements :
                    [
                        { 
                            view  : "label",
                            label : "NUEVA VISTA" 
                        },
                        { 
                            view  : "icon", 
                            icon  : "close", 
                            width : 65,
                            click : function()
                            {
                                $$("_option_view_new").hide();
                            } 
                        }
                    ]
                },
                body :
                { 
                    rows:
                    [
                        { height:35},
                        { 
                            id          : "_view_name", 
                            view        : "text", 
                            placeholder : "NOMBRE DE LA VISTA"
                        },
                        { height:35},
                        {
                            height : 45,
                            cols   :
                            [   {},
                                { 
                                    view : "button"  , 
                                    value: "Cancelar", 
                                    css  : "btn-cancel",
                                    type : "danger"  , 
                                    click: function()
                                    { 
                                        $$("_option_view_new").hide(); 
                                    } 
                                },
                                { 
                                    view : "button"  , 
                                    css  : "btn-ok",
                                    value: "Crear" , 
                                    click: function()
                                    {
                                        var view_name = $$("_view_name").getValue();

                                        contextEvent._option_view_new(view_name);

                                        $$("_option_view_new").hide(); 
                                    } 
                                },
                                { width:10}
                            ]
                        },
                        { height:10}
                    ]
                }
            }).show();
        },

        _option_view_new: function(name)
        {
            $$("page.list").add
            ({
                id   : webix.uid(),
                open : true       ,
                value: name       ,
                icon : "sitemap"  ,
                type : "view"     ,
                data : [ ]
            }, 0);

            $$("page.list").callEvent("onDataUpdate", [true, true]);
        }, 

        option_view_clone : function(choice, item)
        { 
            webix.ui
            ({
                id          : "_option_view_clone"   ,
                css         : "builder-modal"      ,
                view        : "window" , 
                position    : "center" , 
                headHeight  : 70       ,
                modal       : true     ,
                borderless  : true     ,
                resize      : true     ,
                margin      : 0        ,
                padding     : 0        ,
                head        : 
                {
                    view     : "toolbar", 
                    height   : 70,
                    elements :
                    [
                        { 
                            view:"label",
                            label:"CLONAR VISTA" 
                        },
                        { 
                            view  : "icon", 
                            icon  : "close", 
                            width :65,
                            click : function()
                            {
                                $$("_option_view_clone").hide();
                            } 
                        }
                    ]
                },
                body :
                { 
                    rows:
                    [
                        { height:35},
                        { id:"_view_name", view:"text", placeholder:"NOMBRE DE LA VISTA"},
                        { height:35},
                        {
                            height:45,
                            cols:
                            [   {},
                                { 
                                    view : "button"  , 
                                    value: "Cancelar", 
                                    css:"btn-cancel",
                                    type : "danger"  , 
                                    click: function(){ $$("_option_view_clone").hide(); } 
                                },
                                { 
                                    view : "button"  , 
                                    css:"btn-ok",
                                    value: "Crear" , 
                                    click: function()
                                    {
                                        var view_name = $$("_view_name").getValue();

                                        contextEvent._option_view_clone(view_name, item);

                                        $$("_option_view_clone").hide(); 
                                    } 
                                },
                                { width:10}
                            ]
                        },
                        { height:10}
                    ]
                }
            }).show();
        },

        _option_view_clone: function(name, item){

            var data = $$("page.list").data.serialize(); 

            var clone = false;

            for(var i in data)
            {
                if(data[i].id == item.id)
                {
                    clone = data[i];
                }
            }
 
            if(clone!=false)
            {
                clone.id    = webix.uid();
                clone.value = name;
                clone.open  = true;

                delete clone["\$count"];
                delete clone["\$level"];
                delete clone["\$parent"];
                delete clone["indeterminate"];

                var pos = 1;
                for(var i in clone.data)
                {
                    clone.data[i].id = clone.id+"_"+pos;
                    pos++;
                }
 

                $$("page.list").add(clone, 0);

                $$("page.list").callEvent("onDataUpdate", [true, true]);
            }
        },

        option_view_rename : function(choice, item)
        {  
            webix.ui
            ({
                id          : "_option_view_rename"   ,
                css         : "builder-modal"      ,
                view        : "window" , 
                position    : "center" , 
                headHeight  : 70       ,
                modal       : true     ,
                borderless  : true     ,
                resize      : true     ,
                margin      : 0        ,
                padding     : 0        ,
                head        : 
                {
                    view     : "toolbar", 
                    height   : 70,
                    elements :
                    [
                        { 
                            view:"label",
                            label:"RENOMBRAR VISTA" 
                        },
                        { 
                            view  : "icon", 
                            icon  : "close", 
                            width :65,
                            click : function()
                            {
                                $$("_option_view_rename").hide();
                            } 
                        }
                    ]
                },
                body :
                { 
                    rows:
                    [
                        { height:35},
                        { id:"_view_name", view:"text", placeholder:"NOMBRE DE LA VISTA", value: item.value},
                        { height:35},
                        {
                            height:45,
                            cols:
                            [   {},
                                { 
                                    view : "button"  , 
                                    value: "Cancelar", 
                                    css:"btn-cancel",
                                    type : "danger"  , 
                                    click: function(){ $$("_option_view_rename").hide(); } 
                                },
                                { 
                                    view : "button"  , 
                                    css:"btn-ok",
                                    value: "Crear" , 
                                    click: function()
                                    {
                                        var view_name = $$("_view_name").getValue();

                                        contextEvent._option_view_rename(view_name, item);

                                        $$("_option_view_rename").hide(); 
                                    } 
                                },
                                { width:10}
                            ]
                        },
                        { height:10}
                    ]
                }
            }).show();
        },

        _option_view_rename : function(name, item){

            item.value = name;

            $$("page.list").updateItem(item.id, item);  
        },

        option_view_remove : function(choice, item)
        { 
            webix.confirm
            ({
                title    : "Builder",
                text     : "¿Desea eliminar la vista?",
                type     : "confirm-error",
                callback : function(result)
                {
                    if(result==true)
                    { 
                        var nodeId =  $$("page.list").getSelectedId();

                        if(nodeId)
                        {
                            $$("page.list").remove(nodeId); 

                            $$("page.list").callEvent("onDataUpdate", [true, true]);
                        }
                    }
                }
            });
        },

        option_block_new : function(choice, item)
        {  
            webix.ui
            ({
                id          : "_option_block_new"   ,
                css         : "builder-modal"      ,
                view        : "window" , 
                position    : "center" , 
                headHeight  : 70       ,
                modal       : true     ,
                borderless  : true     ,
                resize      : true     ,
                margin      : 0        ,
                padding     : 0        ,
                head        : 
                {
                    view     : "toolbar", 
                    height   : 70,
                    elements :
                    [
                        { 
                            view:"label",
                            label:"NUEVO BLOQUE" 
                        },
                        { 
                            view  : "icon", 
                            icon  : "close", 
                            width :65,
                            click : function()
                            {
                                $$("_option_block_new").hide();
                            } 
                        }
                    ]
                },
                body :
                { 
                    rows:
                    [
                        { height:35},
                        { id:"_view_name", view:"text", placeholder:"NOMBRE DEL BLOQUE"},
                        { height:35},
                        {
                            height:45,
                            cols:
                            [   {},
                                { 
                                    view : "button"  , 
                                    value: "Cancelar", 
                                    css:"btn-cancel",
                                    type : "danger"  , 
                                    click: function(){ $$("_option_block_new").hide(); } 
                                },
                                { 
                                    view : "button"  , 
                                    css:"btn-ok",
                                    value: "Crear" , 
                                    click: function()
                                    {
                                        var view_name = $$("_view_name").getValue();

                                        console.log("name", view_name);

                                        $$("_option_block_new").hide(); 
                                    } 
                                },
                                { width:10}
                            ]
                        },
                        { height:10}
                    ]
                }
            }).show();
        },

        option_block_change : function(choice, item)
        { 
            webix.modalbox
            ({
                css      : "builder-modal",
                title    : choice.value,
                text     : "Cambiar tipo de vista a",
                buttons  : ["Layout", "Header", "Footer", "Content"],
                width    : 500,
                callback : function(result)
                {

                    switch( parseInt(result) )
                    {
                        case 0: contextEvent.__option_block_change(item, "layout" ); break;
                        case 1: contextEvent.__option_block_change(item, "header" ); break;
                        case 2: contextEvent.__option_block_change(item, "footer" ); break;
                        case 3: contextEvent.__option_block_change(item, "content"); break;
                    }
                }
            });
        },
 
        __option_block_change : function(item, type)
        { 
            item.type = type;

            switch(type)
            {
                case "layout" : item.icon = "microchip"        ;  break;
                case "header" : item.icon = "arrow-circle-up"  ;  break;
                case "footer" : item.icon = "arrow-circle-down";  break;
                case "content": item.icon = "puzzle-piece"     ;  break;
            }

            $$("page.list").updateItem(item.id, item); 
        },

        option_block_add_file : function(choice, item)
        {  
            webix.ui
            ({
                view          : "chooser", 
                id            : "_file_explore",
                place_data    : [],
                folder_url    : __.req({"action":"halcon-file-list", "config": item.config}) ,
                on_left       : function(){},
                on_right      : function(){},
                on_place_item : function(){},
                on_folder_item: function(id)
                {
                    var _item = this.getItem(id);

                    $$("_file_explore-search").setValue(_item.name);   
                },
                on_close      : function()
                { 
                    $$("_file_explore").close(); 
                },
                on_finish     : function()
                {
                    var select_file = $$("_file_explore-folder").getSelectedItem(); 

                    contextEvent._option_block_add_file(select_file, choice, item);
           
                    $$("_file_explore").close();  
                }
            });

            $$("_file_explore").show(); 
        },

        _option_block_add_file :  function(file, choice, item){

            console.log("_option_block_add_file", file,  item);

            var index = $$("page.list").getBranchIndex(item.id);


            item.pos  = webix.uid();
            item.id   = item.$parent+"_"+item.pos;
            item.file = file.name  ;
            item.value= file.value ;  

            $$("page.list").add
            ({ 
                id    : item.id, 
                value : file.value, 
                type  : item.type , 
                icon  : item.icon , 
                file  : file.name ,
                view  : item.view ,
                config: item.config,
                pos   : item.pos  
            }, -1, item.$parent);  
       
            //$$("page.list").parse({ parent: item.$parent, pos: index+1, data: item });

            $$("page.list").callEvent("onDataUpdate", [true, true]);  
        },

        option_block_remove : function(choice, item)
        { 

            webix.confirm
            ({
                title    : "Builder",
                text     : "¿Desea eliminar el bloque?",
                type     : "confirm-error",
                callback : function(result)
                {
                    if(result==true)
                    { 
                        var nodeId = $$("page.list").getSelectedId();

                        if(nodeId)
                        {
                            $$("page.list").remove(nodeId); 

                            $$("page.list").callEvent("onDataUpdate", [true, true]);
                        }
                    }
                }
            });
        },

        option_block_rename : function(choice, item)
        { 

            webix.ui
            ({
                id          : "_option_block_rename"   ,
                css         : "builder-modal"      ,
                view        : "window" , 
                position    : "center" , 
                headHeight  : 70       ,
                modal       : true     ,
                borderless  : true     ,
                resize      : true     ,
                margin      : 0        ,
                padding     : 0        ,
                head        : 
                {
                    view     : "toolbar", 
                    height   : 70,
                    elements :
                    [
                        { 
                            view:"label",
                            label:"NUEVO BLOQUE" 
                        },
                        { 
                            view  : "icon", 
                            icon  : "close", 
                            width :65,
                            click : function()
                            {
                                $$("_option_block_rename").hide();
                            } 
                        }
                    ]
                },
                body :
                { 
                    rows:
                    [
                        { height:35},
                        { id:"_view_name", view:"text", placeholder:"NOMBRE DEL BLOQUE"},
                        { height:35},
                        {
                            height:45,
                            cols:
                            [   {},
                                { 
                                    view : "button"  , 
                                    value: "Cancelar", 
                                    css  : "btn-cancel",
                                    type : "danger"  , 
                                    click: function(){ $$("_option_block_rename").hide(); } 
                                },
                                { 
                                    view : "button"  , 
                                    css  : "btn-ok",
                                    value: "Crear" , 
                                    click: function()
                                    {
                                        var view_name = $$("_view_name").getValue();

                                        console.log("name", view_name);

                                        $$("_option_block_rename").hide(); 
                                    } 
                                },
                                { width:10}
                            ]
                        },
                        { height:10}
                    ]
                }
            }).show();

        }
    }; 

    webix.ui
    ({
        id      : "_builder_view_ctx",
        view    : "contextmenu",
        css     : "builder-context",
        width   : 250,
        type    : { height:48 },
        template: "<i class='fa fa-#icon#'></i>&nbsp; <span>#value#</span>",
        data    : 
        [
            {"id":"option_view_new"      , "value": "Nueva vista"                  , icon: "plus"            },
            {"id":"option_view_clone"    , "value": "Clonar vista"                 , icon: "clone"           },
            {"id":"option_view_rename"   , "value": "Renombrar vista"              , icon: "pencil-square-o" },
            {"id":"option_view_remove"   , "value": "Quitar vista"                 , icon: "eraser"          },
            { $template:"Separator"  },  
            {"id":"option_block_add_file", "value": "Insertar bloque de archivo"   , icon: "file"       },
            {"id":"option_block_change"  , "value": "Cambiar tipo de bloque"       , icon: "share"      },
            {"id":"option_block_remove"  , "value": "Borrar bloque"                , icon: "cut"        } 
        ],
        on  : 
        {
            onItemClick: function(id)
            {
                var item   = $$("page.list").getSelectedItem();

                var choice = this.getItem(id); 

                try{
                    contextEvent[choice.id](choice, item);
                }
                catch(e){
                    console.log(choice, item, e);
                } 
            }
        }
    }).attachTo( $$("page.list") );

});