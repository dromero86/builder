app.define("mobile.dashcenter", function()
{ 
    webix.ui
    ({
        id        : "content", 
        width     : '100%' ,
        height    : 'auto' , 
        type      : "space", 
        borderless: true,
        rows:
        [  
            {
                borderless: true,
                view      : "scrollview",
                scroll    : "y"         , 
                body      :
                {
                    borderless: true,
                    type      : "space", 
                    rows:
                    [
                        { height: 15, css:"spacer" },
                        { template:"<div class='notify'>Bienvenido al panel de control</div>", css:"shadow", height:85 },
                        { height: 15, css:"spacer" },
                        {
                            rows:
                            [
                                { id:"_card_prop" , view:"dashcard",  data: { color:"red", icon:"home", value:"0", label:"Propiedades"} },
                                
                                { height: 15, css:"spacer" },

                                { id:"_card_ops"  , view:"dashcard",  data: { color:"orange", icon:"handshake-o", value:"0", label:"Operaciones"} },

                                { height: 15, css:"spacer" },

                                { id:"_card_tipo" , view:"dashcard",  data: { color:"green", icon:"gavel" , value:"0", label:"Tipo de inmuebles"} }
                            ]
                        } 
                    ]
                }
            } 
        ]
    }, $$("content")); 



    __.GET({"action":"dash-data"} , function(response)
    {
        $$("_card_prop").parse({ color:"red"   , icon:"home"       , value:response.propiedades  , label:"Propiedades"});
        $$("_card_ops" ).parse({ color:"orange", icon:"handshake-o", value:response.operaciones  , label:"Operaciones"});
        $$("_card_tipo").parse({ color:"green" , icon:"gavel"      , value:response.tipos        , label:"Tipo de inmuebles"});
    });
 
     
});