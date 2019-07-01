app.define("app.settings.view_tree",function()
{  
    webix.ui
    ({ 
        id         : "page.list",
        view       : "tree",
        css        : "builder-tree-viewjson",
        activeTitle: true  ,
        threeState : true  ,
        select     : true  ,
        borderless : true  ,
        drag       : "move",
        template   : "{common.icon()}  <table><tr> <td style='width:6px'> <i class='fa fa-#icon#'></i> </td> <td>#value#</td> </tr></table>", 
        url        : __.req({"action":"halcon-pages-blocks", "project": __.current.site.project  }) ,
        on         :
        {
            onItemDblClick: function(id)
            {
                __.current.view = this.getItem(id);

                if(__.current.view.type != "view")
                {   
                    var html = null;
                    try
                    {
                        html = $$(__.current.view.value).getValue();
                    }
                    catch(e)
                    {
                        console.log(__.current.view.value, e);
                    }
                    
                    if(html==null)
                    {

                        $$("_sources").addView
                        ({
                            view       : "scrollview"          , 
                            scroll     : "y"                   , 
                            close      : true                  , 
                            borderless : true                  ,
                            header     : __.current.view.value , 
                            body       : 
                            { 
                                id         : __.current.view.value ,
                                ref        : __.current.view       , 
                                view       : 'ace-editor'          ,
                                theme      : 'monokai'             ,  
                                mode       : 'html'                ,
                                borderless : true                  ,
                                on         :
                                {
                                    onReady: function(editor)
                                    {
                                        console.log("editor", editor, this);

                                        editor.reflexTo = this.config.ref;

                                        editor.commands.addCommand
                                        ({
                                            name: 'save',
                                            bindKey: {win: "Ctrl-S", "mac": "Cmd-S"},
                                            exec: function(editor) 
                                            { 
                                                __.POST({"action":"halcon-save-source"}, {source: editor.reflexTo, code: B64.encode(editor.session.getValue()) }, function(o){
                                              
                                                    webix.message({type:"error", text: editor.reflexTo.value+" update succesfully!"});

                                                    var iframe = $$("_preview_tab").getIframe();
                                                    iframe.contentWindow.location.reload(true);
                                                });
                                            }
                                        });
                                    }
                                }
                            } 
                        });
                        
                        __.POST({"action":"halcon-view-source"}, {source: __.current.view}, function(o){
                     
                            $$(__.current.view.value).setValue(o.html);
                        });
                    }
                    else
                    {
                        console.log("not load, ready tab", __.current.view.value);
                    }
                }
            },

            onAfterDrop: function(context, native_event)
            {
                var data = this.data.serialize();

                __.POST({"action":"halcon-update-tree"}, {source: __.current.site, order: data }, function(o){
             
                    console.log("halcon-update-tree", o);

                    var iframe = $$("_preview_tab").getIframe();
                    iframe.contentWindow.location.reload(true);
                });

            },

            onDataUpdate: function(_id, _data)
            {
                var data = this.data.serialize();

                __.POST({"action":"halcon-update-tree"}, {source: __.current.site, order: data }, function(o){
             
                    console.log("halcon-update-tree", o);

                    var iframe = $$("_preview_tab").getIframe();
                    iframe.contentWindow.location.reload(true);
                }); 

            } 
        } 
    }, $$("page.list"));  
});