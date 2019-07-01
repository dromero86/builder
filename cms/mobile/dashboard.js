app.define("mobile.dashboard",function()
{   
    webix.ui
    ({
        id  : "app.page",  
        rows:
        [
            { id : "app.header", cols: [ { view : "mobibar", id:"app.mainbar" }  ] },
            { 
                id : "content",
                width    : "100%"     ,
                height   : "auto"     
            }
        ]
    }); 

    webix.ui({
        id      : "_sidebar",
        view    : "sidemenu", 
        position: "left"    ,
        width   : 200       ,
        state   : function(state)
        {
          var toolbarHeight = $$("app.header").$height;
          state.top         = toolbarHeight;
          state.height     -= toolbarHeight;
        },
        body    :
        {
            view        : "list"     , 
            borderless  : true       ,  
            scroll      : false      ,
            template    : "<span class='webix_icon fa-#icon#'></span> &nbsp; #value#",
            data        : usr.mobile , 
            type        : 
            { 
                height: 60
            },
            on          :
            {
                onItemClick: function(id)
                { 
                    if( __.isNumber(id)) return;

                    $$("content").disable();

                    $$("_sidebar").hide();

                    app.require( this.getItem(id).id );
                } 
            }
        }
    }).show(); 

    webix.ui
    ({
        view      : "popup"        ,
        id        : "my_pop"       ,
        css       : "toolbar-popup",
        head      : "Submenu"      ,
        width     : 170            ,
        borderless: true           ,
        margin    : 0              ,
        padding   : 0              ,
        body      :
        {
            view      : "list", 
            borderless: true  ,
            margin    : 0     ,
            padding   : 0     ,
            type      : { height:48 },
            template  : "<span class='webix_icon fa-#icon#'></span> #name#", 
            select    : true  ,
            autoheight: true  ,
            data      : usr.mobile_options,
            on        :
            {
                onItemClick: function(id)
                { 
                    if( __.isNumber(id)) return; 

                    app.require( this.getItem(id).id  );

                    $$("my_pop").hide();
                } 
            }            
        }
    });

    setTimeout(function(){

        document.body.removeAttribute("style");
        document.querySelector("[view_id='app.page']").removeAttribute("style");

    },1000);


    waves.clear();

    app.require("mobile.dashcenter", function(){

        waves.add({ css: "webix_tree_item" , effect: "waves-dark"});
        waves.add({ css: "webix_el_button" }); 
        waves.add({ css: "logospace" , effect: "waves-blue" });  
    });
});
