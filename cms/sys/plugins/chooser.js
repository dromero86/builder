webix.protoUI
({ 
    name    : 'chooser', 
    $init: function(config) 
    {  

        config.css         = "chooser"   ; 
        config.position    = "center"    ;
        config.modal       = true        ;
        config.borderless  = true        ;
        config.margin      = 0           ;
        config.padding     = 0           ; 
        config.resize      = true        ;
        config.width       = 675         ;
        config.height      = 470         ;
        config.head        = {

            borderless  : true ,
            rows        :
            [
                {
                    borderless  : true ,
                    cols        :
                    [
                        { 
                            borderless  : true ,
                            css         : "chooser-header-label",
                            view        : "label", 
                            label       : "Seleccionar carpeta" 
                        },
                        { 
                            borderless  : true ,
                            css         : "chooser-header-icon",
                            view        : "button"           ,
                            type        : "icon"             ,
                            icon        : "close"            , 
                            width       : 56,
                            click       : config.on_close                                       
                        }
                    ]
                },
                {
                    borderless  : true ,
                    view        : "toolbar", 
                    elements    :
                    [
                        { 
                            id         : config.id+"-left",
                            borderless : true ,
                            view       : "button"           ,
                            type       : "icon"             ,
                            icon       : "chevron-left"     , 
                            width      : 56 ,
                            click      : config.on_left                                        
                        },
                        { 
                            id          : config.id+"-right",
                            borderless  : true ,
                            view        : "button"           ,
                            type        : "icon"             ,
                            icon        : "chevron-right"    , 
                            width       : 56    ,
                            click       : config.on_right                                       
                        },
                        {
                            id          : config.id+"-search",
                            borderless  : true ,
                            view        : "text" 
                        } 
                    ]
                }
            ]
        };

        config.body        = {
            borderless  : true ,
            rows        :
            [ 
                {
                    borderless  : true ,
                    cols        :
                    [
                        { 
                            id          : config.id+"-place",
                            borderless  : true  ,
                            view        : "tree",
                            select      : true  ,  
                            width       : 170   ,
                            url         : config.place_url == undefined ? null : config.place_url,
                            data        : config.place_data == undefined ? [] : config.place_data,
                            on:
                            {
                                onItemClick: config.on_place_item
                            }

                        },
                        {  
                            id          : config.id+"-folder",
                            view        : "datatable"       ,
                            select      : "row"             ,    
                            resizeColumn: true              ,
                            navigation  : true              ,   
                            columnHeight: 30,
                            columns     :
                            [
                                {id:"name", header: "Nombre" , sort: 'string', fillspace:true, template: "<i class='fa fa-folder'></i> #name#" }, 
                                {id:"date", header: "Fecha"  , sort: 'string', adjust   :true },  
                                {id:"type", header: "Tipo"   , sort: 'string', adjust   :true },  
                                {id:"size", header: "Tamaño" , sort: 'string', adjust   :true }  
                            ],
                            url : config.folder_url  == undefined ? null : config.folder_url,
                            data: config.folder_data == undefined ? null : config.folder_data,
                            on  :
                            {
                                onItemClick: config.on_folder_item
                            }
                        }
                    ]
                }, 
                {
                    css         : "chooser-footer",
                    borderless  : true ,
                    height      : 45,
                    cols:
                    [
                        {borderless  : true },
                        { 
                            id          : config.id+"-finish",
                            borderless : true ,
                            view       : "button", 
                            css        : "btn" , 
                            width      : 100,  
                            value      : "Seleccionar", 
                            click      : config.on_finish
                        },
                        { width:10},
                        { 
                            id          : config.id+"-cancel",
                            borderless : true ,
                            view       : "button",  
                            css        : "cancel" , 
                            width      : 100,  
                            value      : "Cancelar", 
                            click      : config.on_close
                        }
                    ]
                }
            ]
        }; 
    }
}, webix.ui.window, webix.EventSystem);