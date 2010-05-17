var gps_map;

function gps_zoom_change(event) {
  options_set("gps_zoom", gps_map.zoom);
}

function gps_baselayer_change(event) {
  options_set("gps_baselayer", gps_map.baseLayer.id);
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

  var l = new OpenLayers.Layer.OSM("OpenStreetBrowser", "http://www.openstreetbrowser.org/tiles/base/${z}/${x}/${y}.png", {numZoomLevels: 19});
  gps_map.addLayer(l);

  var l = new OpenLayers.Layer.OSM.Mapnik("OpenStreetMap - Mapnik");
  gps_map.addLayer(l);

  var l= new OpenLayers.Layer.Google( "Google", { type: G_HYBRID_MAP,
     projection: new OpenLayers.Projection("EPSG:4326"), 'sphericalMercator': true } );
  gps_map.addLayer(l);

  var z=options_get("gps_baselayer");
  for(var i=0; i<gps_map.layers.length; i++) {
    if(gps_map.layers[i].id==z)
      gps_map.setBaseLayer(gps_map.layers[i]);
  }

  var z=options_get("gps_zoom");
  if(!z)
    z=13;

  var gps_pos=new OpenLayers.LonLat(map.getAttribute("pos_lon"), map.getAttribute("pos_lat")).transform(new OpenLayers.Projection("EPSG:4326"), gps_map.getProjectionObject());
  gps_map.setCenter(gps_pos, z);

  var markers = new OpenLayers.Layer.Markers( "Markers" );
  gps_map.addLayer(markers);

//  //for(var route_part in route) {
//    var geo=new Array();
//    // for(var poi in route_part) {
//      geo.push(new OpenLayers.Geometry.Point(0, 0));
//      geo.push(new OpenLayers.Geometry.Point(5, 5));
//    //}
//
//    var route_el=new OpenLayers.Geometry.MultiPoint();
//    markers.addMarker(route_el);
//  //}

  var size = new OpenLayers.Size(21,25);
  var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
  var icon = new OpenLayers.Icon('http://www.openstreetmap.org/openlayers/img/marker.png',size,offset);
  markers.addMarker(new OpenLayers.Marker(gps_pos,icon));

  gps_map.events.register("zoomend", gps_map, gps_zoom_change);
  gps_map.events.register("changebaselayer", gps_map, gps_baselayer_change);

}

register_initfun(gps_init);
