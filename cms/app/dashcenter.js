app.define("app.dashcenter", function()
{ 
    webix.ui
    ({
        id  : "app.page",  
        rows:
        [ 
            { 
                id         : "app.body",
                view       : "accordion",
                margin     : 0,
                multi      : true, 
                borderless : true,
                cols       :
                [
                    {                         
                        id           : "app.sidenav",
                        width        : 250,  
                        borderless   : true,
                        header       : "BUILDER",
                        headerHeight : 22, 
                        body         : 
                        {   
                            view       : "accordion", 
                            margin     : 0,
                            multi      : true, 
                            borderless : true,
                            rows       :
                            [   
                                {
                                    id         : "app.sidenav.logo",
                                    borderless : true,
                                    css        : "builder-logo",
                                    height     : 188,
                                    template   : "<img src='#path#' style='width:100%' />",
                                    data       : {path:"./cms/ui/img/builder.jpg"}
                                },
                                { id : "app.sidenav.pages", header : "VIEWS", body : { id : "page.list" } }
                            ]
                        } 
                    },
                    { id : "content", width : '100%' , height : 'auto' }
                ]
            }
        ]
    }); 



    app.require("app.source_editor" , function(){

        setTimeout(function()
        {
            app.require("app.settings.view_tree" , function()
            {
                app.require("app.settings.view_menu_context");
            });

        },0);

    });
     
});