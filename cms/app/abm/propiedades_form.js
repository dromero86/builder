app.define("app.abm.propiedades_form",function()
{  

    webix.ui ({ id : "_main_search", css : "acople input-text" , width : 440 }, $$("_main_search"));

    webix.ui
    ({
        id       : 'content',
        view     : "formview",
        dataview : "app.abm.propiedades_view",
        update   : "site_prop-update",
        source   : {"action": "site_prop-row","id": ( __.current["propiedad"] != undefined ? __.current["propiedad"].id : 0 ) },
        
        store    : "propiedad",
        title_set: __.current["propiedad"] != undefined ? "PROPIEDAD EN "+__.current["propiedad"].addr.toUpperCase() : "",
        title_add: "NUEVA PROPIEDAD", 
        validate : true,
        elements :
        {
            view      : "tabview",
            multiview : { keepViews:true },  
            tabbar    : 
            {
                optionWidth:150,
                on: 
                {
                    onAfterTabClick: function()
                    {
                        if( this.getValue() == "_surface_map_child" )
                        {
                            $$("maploc").resizeMap(); 
                        }
                    } 
                }
            },
            cells: 
            [
                {
                    header: "INFORMACIÓN",
                    body: 
                    {
                        padding:25,
                        rows:
                        [
                            {
                                height:40
                            },
                            {
                                cols:
                                [ 
                                    { view:"combo"     , name:"tipo_prop"  , label:"Tipo"       , labelPosition:"top", options: __.req({"action":"site_prop_tipo-combo"     })       , required: true }, 
                                    { width: 25 },
                                    { view:"combo"     , name:"tipo_op"    , label:"Operación"  , labelPosition:"top", options: __.req({"action":"site_prop_operacion-combo"})       , required: true },
                                    { width: 25 },  
                                    { view:"segmented" , name:"destacado"  , label:"Destacado"  , labelPosition:"top", options:[ {id:0, value:"No"}, {id:1, value:"Si"} ], width:150, required: true },
                                    { width: 25 },
                                    { view:"segmented" , name:"habilitado" , label:"Habilitado" , labelPosition:"top", options:[ {id:0, value:"No"}, {id:1, value:"Si"} ], width:150, required: true }
                                ]
                            },
                            {
                                height:40
                            },
                            {
                                cols:
                                [
                                    { view:"text"  , name:"addr"      , label:"Dirección" , labelPosition:"top" , required: true }, 
                                    { width: 25 },
                                    { view:"text"  , name:"barrio"    , label:"Barrio"    , labelPosition:"top" , required: true }
                                ]
                            },
                            
                            {
                                height:40
                            },
                            { view:"ckeditor" , name:"detalle" , label:"Detalle de la propiedad"  } 
                        ]
                    }
                },
                {
                    header: "CARACTERISTICAS",
                    body: 
                    { 
                        padding:25,
                        rows:
                        [

                            { height:40  },
                            {
                                cols:
                                [ 
                                    { view:"segmented" , name:"estado"      , label:"Estado"       , labelPosition:"top" , required: true , options:[{ id: "Bueno", value:"Bueno"}, { id: "Muy Bueno", value:"Muy Bueno"}, { id: "Excelente", value:"Excelente"}] },{ width: 25 },
                                    { view:"segmented" , name:"patio"       , label:"Patio"        , labelPosition:"top" , required: true , options:[{ id: "0", value:"No"}, { id: "1", value:"1"}, { id: "2", value:"2"},{ id: "3", value:"3"},{ id: "4", value:"4"}] },{ width: 25 },
                                    { view:"text"      , name:"precio"      , label:"Precio"       , labelPosition:"top" , required: true },{ width: 25 },
                                    { view:"text"      , name:"sup"         , label:"Superficie m<sup>2</sup>" , labelPosition:"top" }
                                ]
                            },
                            { height:40  },
                            {
                                cols:
                                [ 
                                    { view:"segmented"      , name:"hab"         , label:"Habitaciones" , labelPosition:"top" , required: true, options:[{ id: "1", value:"1"}, { id: "2", value:"2"},{ id: "3", value:"3"},{ id: "4", value:"4"},{ id: "5", value:"5"},{ id: "6", value:"6"},{ id: "7", value:"7"},{ id: "8", value:"8"}] },{ width: 25 },
                                    { view:"segmented"      , name:"cochera"     , label:"Cochera"    , labelPosition:"top" , required: true, options:[{ id: "0", value:"No"}, { id: "1", value:"1"}, { id: "2", value:"2"},{ id: "3", value:"3"},{ id: "4", value:"4"}] },{ width: 25 },
                                    { view:"segmented"      , name:"toalet"      , label:"Toalet"     , labelPosition:"top", required: true, options:[{ id: "0", value:"No"}, { id: "1", value:"1"}, { id: "2", value:"2"},{ id: "3", value:"3"},{ id: "4", value:"4"}] },{ width: 25 },
                                    { view:"segmented"      , name:"amb_cant"    , label:"Ambientes"  , labelPosition:"top" , required: true, options:[{ id: "1", value:"1"}, { id: "2", value:"2"},{ id: "3", value:"3"},{ id: "4", value:"4"},{ id: "5", value:"5"},{ id: "6", value:"6"},{ id: "7", value:"7"},{ id: "8", value:"8"}] }
                                ]
                            },
                            { height:40  },
                            {
                                cols: 
                                [ 
                                    { view:"segmented" , name:"expensas"    , label:"Expensas"   , labelPosition:"top" , required: true , options:[{ id: "Si", value:"Si"}, { id: "No", value:"No"}] },{ width: 25 },
                                    { view:"segmented" , name:"luz"         , label:"Luz"        , labelPosition:"top" , required: true , options:[{ id: "Si", value:"Si"}, { id: "No", value:"No"}] },{ width: 25 },
                                    { view:"segmented" , name:"gas"         , label:"Gas"        , labelPosition:"top" , required: true , options:[{ id: "Si", value:"Si"}, { id: "No", value:"No"}] },{ width: 25 },
                                    { view:"segmented" , name:"agua"        , label:"Agua"       , labelPosition:"top" , required: true , options:[{ id: "Si", value:"Si"}, { id: "No", value:"No"}] }
                                ]
                            },
                            { height:40  },
                            {
                                cols:
                                [ 
                                    { view:"segmented" , name:"cloacas"     , label:"Cloaca"     , labelPosition:"top" , required: true , options:[{ id: "Si", value:"Si"}, { id: "No", value:"No"}] },{ width: 25 },
                                    { view:"segmented" , name:"calefaccion" , label:"Calefaccion", labelPosition:"top" , required: true , options:[{ id: "Si", value:"Si"}, { id: "No", value:"No"}] },{ width: 25 },
                                    { view:"segmented" , name:"tel"         , label:"Telefono"   , labelPosition:"top" , required: true , options:[{ id: "Si", value:"Si"}, { id: "No", value:"No"}] },{ width: 25 },
                                    { view:"segmented" , name:"terraza"     , label:"Terraza"    , labelPosition:"top" , required: true , options:[{ id: "Si", value:"Si"}, { id: "No", value:"No"}]  }

                                ]
                            },
                            { height:40  },
                            { view:"textarea" , name:"amb_detalle" , label:"Detalle de los ambientes"   , labelPosition:"top" } 
                        ]
                    }
                },
                {
                    header: "FOTOS",
                    body: 
                    {
                        padding:25,
                        rows:
                        [
                            { height: 40 },

                            {
                                cols:
                                [
                                    {   
                                        view         : "image"              , 
                                        name         : "foto0"              ,  
                                        label        : "Foto Portada"       ,
                                        idImage      : "tmpimg0"            , 
                                        pathImage    : "ui/img/propiedades/",
                                        uploadLink   : __.req({"action":"upload-image", "move": "ui/img/propiedades"}),
                                        defaultImage : __.base_url()+"cms/ui/img/image.png",
                                        required     : true 

                                    },
                                    { width: 25 },
                                    {   
                                        view        : "image"              , 
                                        name        : "foto1"              , 
                                        label       : "Foto 1"             ,
                                        idImage     : "tmpimg1"            , 
                                        pathImage   : "ui/img/propiedades/",
                                        uploadLink  : __.req({"action":"upload-image", "move": "ui/img/propiedades"}),
                                        defaultImage: __.base_url()+"cms/ui/img/image.png" 
                                    }
                                ] 
                            },

                            { height: 40 },

                            {
                                cols:
                                [
                                    {   
                                        view        : "image"              , 
                                        name        : "foto2"              , 
                                        label       : "Foto 2"             ,
                                        idImage     : "tmpimg2"            , 
                                        pathImage   : "ui/img/propiedades/",
                                        uploadLink  : __.req({"action":"upload-image", "move": "ui/img/propiedades"}),
                                        defaultImage: __.base_url()+"cms/ui/img/image.png"
                                    },
                                    { width: 25 },
                                    {   
                                        view        : "image"              , 
                                        name        : "foto3"              , 
                                        label       : "Foto 3"             ,
                                        idImage     : "tmpimg3"            , 
                                        pathImage   : "ui/img/propiedades/",
                                        uploadLink  : __.req({"action":"upload-image", "move": "ui/img/propiedades"}),
                                        defaultImage: __.base_url()+"cms/ui/img/image.png"
                                    }
                                ]
                            },  

                            {}
                        ]        
                    } 
                }, 
                { 
                    header: "MAPA",
                    body: 
                    { 
                        id      : "_surface_map_child",
                        padding : 25,
                        rows    :
                        [
                            { height: 40 },
                            {
                                cols:
                                [
                                    { template: "<div style=' padding-top: 25px;'>Arrastre el marcador hasta la dirección de la propiedad</div>"}, 
                                    {},
                                    { view:"text", name:"lat", id:"latex", label:"Latitud", labelPosition:"top", required: true },
                                    { width: 25 },
                                    { view:"text", name:"lon", id:"lonex", label:"Logitud", labelPosition:"top", required: true }
                                ]
                            },
                            {
                                id     : "maploc",
                                view   : "leaflet-map",
                                zoom   : 16, 
                                on     :
                                {
                                    mapLoadFinish: function(view, map)
                                    {
                                        var that = this;


                                        __.PAYLOAD({"action":"databot"}, {select:{ from: "site_config", field:"nombre,valor"}, filter:[ {field: "nombre", is:"in('lat','lon')", to:"null"}] } , function(response){
            
                                            var result = JSON.parse(response);

                                            var row = {};
                                 
                                            for( var i in result.data )
                                            {
                                                var item = result.data[i];

                                                row[item.nombre] = parseFloat(item.valor);
                                            }

                                            var lat = __.current["propiedad"] != undefined ? ( __.current["propiedad"].lat ? parseFloat(__.current["propiedad"].lat) : row.lat ): row.lat;
                                            var lon = __.current["propiedad"] != undefined ? ( __.current["propiedad"].lon ? parseFloat(__.current["propiedad"].lon) : row.lon ): row.lon;
                                            var addr= __.current["propiedad"] != undefined ? __.current["propiedad"].addr : "Nueva propiedad";  
                        
                                            map.MKR = L.marker([lat, lon], { draggable: true }).bindPopup(addr).addTo(map);
                                            that.setCenter( lat, lon );
                                       
                                            map.MKR.on("dragend",  function(event) {

                                                var ll = this.getLatLng();
                                              
                                                $$("latex").setValue(ll.lat);
                                                $$("lonex").setValue(ll.lng);  
                                             
                                            });
                                        });
                                    }
                                }                       
                            }  
                        ]
                    }
                } 
            ]
        } 
    },
    $$('content'));

});