var gps_map;

function gps_init() {
  var map=document.getElementById("gps_map");

  gps_map=new OpenLayers.Map(map);

   var google = new OpenLayers.Layer.Google( "Google", { type: G_HYBRID_MAP } );
   gps_map.addLayer(google);
   google.displayInLayerSwitcher=true;
 
  var wms = new OpenLayers.Layer.WMS( "OpenLayers WMS", 
              "http://labs.metacarta.com/wms/vmap0", {layers: 'basic'} );
  gps_map.addLayer(wms);

  var gps_pos=new OpenLayers.LonLat(map.getAttribute("pos_lon"), map.getAttribute("pos_lat"));
  gps_map.setCenter(gps_pos, 13);

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

  var size = new OpenLayers.Size(10,17);
  var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
  var icon = new OpenLayers.Icon('http://boston.openguides.org/markers/AQUA.png',size,offset);
  markers.addMarker(new OpenLayers.Marker(gps_pos,icon));
}

register_initfun(gps_init);
