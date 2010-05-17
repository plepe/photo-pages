var gps_map;

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

  var l= new OpenLayers.Layer.Google( "Google", { type: G_HYBRID_MAP,
     projection: new OpenLayers.Projection("EPSG:4326"), 'sphericalMercator': true } );
  gps_map.addLayer(l);

  var l = new OpenLayers.Layer.OSM.Mapnik("OpenStreetMap - Mapnik");
  gps_map.addLayer(l);

  var l = new OpenLayers.Layer.OSM("OpenStreetBrowser", "http://www.openstreetbrowser.org/tiles/base/${z}/${x}/${y}.png", {numZoomLevels: 19});
  gps_map.addLayer(l);

 
  var gps_pos=new OpenLayers.LonLat(map.getAttribute("pos_lon"), map.getAttribute("pos_lat")).transform(new OpenLayers.Projection("EPSG:4326"), gps_map.getProjectionObject());
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

  var size = new OpenLayers.Size(21,25);
  var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
  var icon = new OpenLayers.Icon('http://www.openstreetmap.org/openlayers/img/marker.png',size,offset);
  markers.addMarker(new OpenLayers.Marker(gps_pos,icon));
}

register_initfun(gps_init);
