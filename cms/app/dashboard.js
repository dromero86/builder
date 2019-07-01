app.define("app.dashboard",function()
{   
    var builder = webix.storage.local.get("builder");
    


    if( builder == null)
    {
        //modal project chooser
        app.require("app.settings.project_chooser"); 
    }
    else
    {
        __.current.site = builder;
        //open project
        app.require("app.dashcenter"); 
    }

});
