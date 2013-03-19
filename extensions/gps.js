var gps_map;
var gps_layer_hill;

function gps_zoom_change(event) {
  options_set("gps_zoom", gps_map.zoom);
}

function gps_baselayer_change(event) {
  options_set("gps_baselayer", gps_map.baseLayer.id);
}

function gps_layer_change(event) {
  options_set("gps_layer_hill", gps_layer_hill.visibility?"on":"off");
}

function gps_init() {
  var map=document.getElementById("gps_map");

  gps_map = new OpenLayers.Map("gps_map",
          { maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
            numZoomLevels: 19,
            maxResolution: 156543.0399,
            units: 'm',
            projection: new OpenLayers.Projection("EPSG:900913"),
            displayProjection: new OpenLayers.Projection("EPSG:4326"),
            controls: [ new OpenLayers.Control.PanZoom(),
                        new OpenLayers.Control.LayerSwitcher(),
                        new OpenLayers.Control.Navigation() ]
          });

  var l = new OpenLayers.Layer.OSM.Mapnik("OpenStreetMap - Mapnik");
  gps_map.addLayer(l);

  var l = new OpenLayers.Layer.OSM("OpenStreetBrowser", "http://www.openstreetbrowser.org/tiles/base/${z}/${x}/${y}.png", {numZoomLevels: 19});
  gps_map.addLayer(l);

  var l= new OpenLayers.Layer.Google( "Google", { type: G_HYBRID_MAP,
     projection: new OpenLayers.Projection("EPSG:4326"), 'sphericalMercator': true } );
  gps_map.addLayer(l);

  var z=options_get("gps_layer_hill");
  gps_layer_hill = new OpenLayers.Layer.OSM(
                 "Hillshading (NASA SRTM3 v2)",
                 "http://toolserver.org/~cmarqu/hill/${z}/${x}/${y}.png",
                { type: 'png',
                  displayOutsideMaxExtent: true, isBaseLayer: false,
                  transparent: true, "visibility": z!="off"});
  gps_map.addLayer(gps_layer_hill);

  var z=options_get("gps_baselayer");
  for(var i=0; i<gps_map.layers.length; i++) {
    if(gps_map.layers[i].id==z)
      gps_map.setBaseLayer(gps_map.layers[i]);
  }

  var z=options_get("gps_zoom");
  if(!z)
    z=13;

  var gps_pos=new OpenLayers.LonLat(map.getAttribute("pos_lon"), map.getAttribute("pos_lat")).transform(new OpenLayers.Projection("EPSG:4326"), new OpenLayers.Projection("EPSG:900913"));
  gps_map.setCenter(gps_pos, z);

  if(gps_route) {
    var gps_route_layer=new OpenLayers.Layer.Vector("Route", {});
    gps_route_layer.setOpacity(0.7);
    gps_map.addLayer(gps_route_layer);

    for(var i=0; i<gps_route.length; i++) {
      var route_part=[];
      for(var j=0; j<gps_route[i].length; j++) {
        var p=gps_route[i][j];
        var ll=new OpenLayers.LonLat(p.lon, p.lat).transform(new OpenLayers.Projection("EPSG:4326"), gps_map.getProjectionObject());
        route_part.push(new OpenLayers.Geometry.Point(ll.lon, ll.lat));
      }

      var ls=new OpenLayers.Geometry.LineString(route_part);
      var vector=new OpenLayers.Feature.Vector(ls, 0, {
        strokeWidth: 2,
        strokeColor: "black"
      });

      gps_route_layer.addFeatures([vector]);
    }
  }

  var markers = new OpenLayers.Layer.Markers( "Markers" );
  gps_map.addLayer(markers);

  var size = new OpenLayers.Size(21,25);
  var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
  var icon = new OpenLayers.Icon('http://www.openstreetmap.org/openlayers/img/marker.png',size,offset);
  markers.addMarker(new OpenLayers.Marker(gps_pos,icon));

  gps_map.events.register("zoomend", gps_map, gps_zoom_change);
  gps_map.events.register("changebaselayer", gps_map, gps_baselayer_change);
  gps_map.events.register("changelayer", gps_map, gps_layer_change);
}

register_initfun(gps_init);
