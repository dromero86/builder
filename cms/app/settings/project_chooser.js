app.define("app.settings.project_chooser",function()
{   

    //$$("_project_chooser").show();
    //on_close

    webix.ui
    ({
        view          : "chooser", 
        id            : "_project_chooser",
        place_data    : [],
        folder_url    : __.req({"action":"halcon-folder-list"}) ,
        on_left       : function(){},
        on_right      : function(){},
        on_place_item : function(){},
        on_folder_item: function(id)
        {
            var item = this.getItem(id);

            $$("_project_chooser-search").setValue(item.name);   
        },
        on_close      : function()
        { 
            $$("_project_chooser").close(); 
        },
        on_finish     : function()
        {
            var item = $$("_project_chooser-folder").getSelectedItem();
  
            webix.storage.local.put("builder",
            { 
                project: item.name, 
                preview: item.web, 
                page   : "", 
                block  : "" 
            });
 
            $$("_project_chooser").close(); 

            app.require("app.dashcenter"); 
        }
    });

    $$("_project_chooser").show();
});