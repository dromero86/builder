app.define("mobile.propiedades_view",function()
{ 
    webix.ui
    ({
        id       : 'content'  ,
        width    : "100%"     ,
        height   : "auto"     ,
        rows:
        [
            {
                view     : "mobilist" ,
                title    : "PROPIEDADES",
                form     : "mobile.propiedades_form",
                store    : "propiedad" ,
                data_id  : "_dt_propiedades", 
                type     : 
                { 
                    templateStart:"<div webix_l_id='#id#' class='webix_list_item'>",
                    template:"http->./cms/ui/tpl/mobile_list.html",
                    templateEnd:"</div>"
                },
                query    : { select:"site_prop" } ,
                on:
                {
                    onAfterDataParse: function(view)
                    {
                        console.log("onAfterDataParse", view); 

                        var onerrorimg = new Image();
                        onerrorimg.src = "./cms/ui/img/warning.jpg";
                        
                        var stack = document.querySelectorAll(".image_check");
  
                        for( var i in stack)
                        { 
                            if(typeof stack[i] == "object" )
                            { 
                                var node = stack[i];
                                var path = node.getAttributeNode("src").value;
  
                                var downloadingImage     = new Image();
                                downloadingImage.src     = path;
                                downloadingImage.ref     = node;
                                downloadingImage.onload  = function(){ };
                                downloadingImage.onerror = function()
                                {    
                                    this.ref.setAttribute("src" , onerrorimg.src );
                                    this.ref.setAttribute("class", this.ref.getAttributeNode("class").value + " offline" );
                                };
                                
                            } 
                        }
                    }
                }
            }
        ]
    },
    $$('content'));

}); 