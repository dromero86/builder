app.define("app.source_editor",function()
{  

    webix.ui
    ({ 
        id         : "content"   , 
        width      : '100%'      ,
        height     : 'auto'      , 
        rows:
        [
            {
                id    :"_sources",
                css   :"form-view",
                view  : "tabview",
                tabbar: {  optionWidth:200  },
                cells :
                [
                    {  
                        header: "welcome",  
                        body  : 
                        {  
                            id  : "_preview_tab",
                            view: "iframe",
                            src : __.current.site.preview
                        } 
                    }
                ]
            } 
        ]
    }, $$("content"));  


});