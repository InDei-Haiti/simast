// See post: http://asmaloney.com/2014/01/code/creating-an-interactive-map-with-leaflet-and-openstreetmap/
//console.log('width avant: '+$('.right').width());
proj4.defs("urn:ogc:def:crs:EPSG::26918", '+proj=utm +zone=18 +ellps=GRS80 +datum=NAD83 +units=m +no_defs');
var map = L.map( 'map', {
    center: [18.046476, -73.9999999],
    minZoom: 2,
    zoom: 8,
    scrollWheelZoom:false
});
//map.remove();
map.on("load",function() {
	$('#map').appendTo($('#tabs-6'));
});
//var myLayer = L.geoJson().addTo(map);
L.tileLayer( /*'http://{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright" title="OpenStreetMap" target="_blank">OpenStreetMap</a> contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" title="MapQuest" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png" width="16" height="16">',
    subdomains: ['otile1','otile2','otile3','otile4']
}*/
		'http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
		    maxZoom: 20,
		    subdomains:['mt0','mt1','mt2','mt3']
		}).addTo( map );





/*$.ajax({
    type: 'GET',
    url: "/modules/outputs/maps/departments.geojson",
    dataType: 'json',
    success: function(data){
    	alert("Ok");
        myLayer.addData(data);
    }
});*/

//console.log('width apres: '+$('.right').width());
// $('#tabs').tabs();

var myURL = jQuery( 'script[src$="leaf-demo.js"]' ).attr( 'src' ).replace( 'leaf-demo.js', '' );

//alert("Value: "+(parseInt("3990".toString()[0])*Math.pow(10, Math.trunc(Math.log10(2300)))));


function onEachFeature(feature, layer){

}

function legend(grades, sum) {
    var legend = L.control({position: 'topright'});

    legend.onAdd = function (map) {

        var div = L.DomUtil.create('div', 'info legend'),

            labels = [];

        // loop through our density intervals and generate a label with a colored square for each interval
        div.innerHTML +=
            '<i style="background:' + getColor(grades[0], sum) + '"></i>-'+grades[0]+' <br>';
        for (var i = 0; i < grades.length; i++) {
            div.innerHTML +=
                '<i style="background:' + getColor(grades[i] + 1, sum) + '"></i> '+
                grades[i] + (grades[i + 1] ? '&ndash;' + grades[i + 1] + '<br>' : '+');
        }

        return div;
    };
    legend.addTo(map);
}





var tabLegend = [];
function populateLegendVal(sum) {
    tab = [];
    calV = parseInt(Math.trunc(sum * 0.5).toString()[0]) * Math.pow(10, Math.trunc(Math.log10(Math.trunc(sum * 0.05))));
    tab.push(calV);
    calV = parseInt(Math.trunc(sum * 0.1).toString()[0]) * Math.pow(10, Math.trunc(Math.log10(Math.trunc(sum * 0.1))));
    tab.push(calV);
    calV = parseInt(Math.trunc(sum * 0.15).toString()[0]) * Math.pow(10, Math.trunc(Math.log10(Math.trunc(sum * 0.15))));
    tab.push(calV);
    calV = parseInt(Math.trunc(sum * 0.15).toString()[0]) * Math.pow(10, Math.trunc(Math.log10(Math.trunc(sum * 0.15))));
    tab.push(calV);
    calV = parseInt(Math.trunc(sum * 0.2).toString()[0]) * Math.pow(10, Math.trunc(Math.log10(Math.trunc(sum * 0.2))));
    tab.push(calV);
    calV = parseInt(Math.trunc(sum * 0.25).toString()[0]) * Math.pow(10, Math.trunc(Math.log10(Math.trunc(sum * 0.25))));
    tab.push(calV);
    calV = parseInt(Math.trunc(sum * 0.3).toString()[0]) * Math.pow(10, Math.trunc(Math.log10(Math.trunc(sum * 0.3))));
    tab.push(calV);
    tab = tab.filter(function(item, pos){
        return tab.indexOf(item)== pos;
    });

    return tab;
}
function getColor(d, sum) {
	tabLegend = populateLegendVal(sum);
    tabLegend.reverse();
	countTab = tabLegend.length;
	colorL = '';
	if(d > tabLegend[0]){
        colorL = '#800026';
	} else if(d > tabLegend[1]){
        colorL = '#BD0026';
	} else if(d > tabLegend[2]){
        colorL = '#E31A1C';
	} else if(d > tabLegend[3]){
        colorL = '#FC4E2A';
	} else if(d > tabLegend[4]){
        colorL = '#FD8D3C';
	} else if(d > tabLegend[5]){
        colorL = '#FEB24C';
	}else if(d > tabLegend[6]){
        colorL = '#FED976';
	} else {
        colorL = '#FFEDA0';
    }
    tabLegend.reverse();
    return colorL;
}
tempFeatures = {};

function populateMap(str){

	console.log(geoDataDic);
    if(geoData=='SysDepartment') {
        urlGeo = '/modules/outputs/maps/departments.geojson';
        $.getJSON(urlGeo, function (data) {
            sumVal = 0;
            $j.each(geoDataDic, function (i, val) {

            	val.datamapping.forEach(function(item){
                    sumVal += parseInt(item.val);
                });

            });
            geoProj = L.Proj.geoJson(data,
                {
                    style: function (feature) {
                        fillColor = '#cccccc';
                        if (geoDataDic.hasOwnProperty(feature.properties.ID_Dep)) {
                        	val = null;
                        	geoDataDic[feature.properties.ID_Dep].datamapping.forEach(function(item){
                        		if(str==item.label)
                        			val = item.val;
							});
							if(val != null) {
								fillColor = getColor(parseInt(val), sumVal);

                            }
                        }

                        return {
                            fillColor: fillColor,
                            weight: 2,
                            opacity: 1,
                            color: 'white',
                            dashArray: '3',
                            fillOpacity: 0.7
                        };
                    }
                }
            ).addTo(map);
            legend(tabLegend, sumVal);
            geoProj.bindPopup(function (layer) {
                html = '';
                html += geoDataDic[layer.feature.properties.ID_Dep].coordinate + '<br/>';
                geoDataDic[layer.feature.properties.ID_Dep].datamapping.forEach(function (item) {
                	if(item.label==str)
						html += '<span style="color: darkblue">'+item.label+": "+item.val+"</span><br/>";
                	else
						html += item.label+": "+item.val+"<br/>";
                });
                return html;
            });
        });
    }

    if(geoData=='SysCommunes') {
        urlGeo = '/modules/outputs/maps/communes.geojson';
        $.getJSON(urlGeo, function (data) {
            sumVal = 0;
            $j.each(geoDataDic, function (i, val) {

                val.datamapping.forEach(function(item){
                    sumVal += parseInt(item.val);
                });

            });
            geoProj = L.Proj.geoJson(data,
                {
                    style: function (feature) {
                        fillColor = '#cccccc';
                        if (geoDataDic.hasOwnProperty(feature.properties.id_com)) {
                            val = null;
                            geoDataDic[feature.properties.id_com].datamapping.forEach(function(item){
                                if(str==item.label)
                                    val = item.val;
                            });
                            if(val != null) {
                                fillColor = getColor(parseInt(val), sumVal);

                            }
                        }

                        return {
                            fillColor: fillColor,
                            weight: 2,
                            opacity: 1,
                            color: 'white',
                            dashArray: '3',
                            fillOpacity: 0.7
                        };
                    }
                }
            ).addTo(map);
            legend(tabLegend, sumVal);
            geoProj.bindPopup(function (layer) {
                html = '';
                html += geoDataDic[layer.feature.properties.id_com].coordinate + '<br/>';
                geoDataDic[layer.feature.properties.id_com].datamapping.forEach(function (item) {
                    if(item.label==str)
                        html += '<span style="color: darkblue">'+item.label+": "+item.val+"</span><br/>";
                    else
                        html += item.label+": "+item.val+"<br/>";
                })
                //html += geoDataDic[layer.feature.properties.id_com].datamapping +': '+geoDataDic[layer.feature.properties.id_com].val + '<br/>';
                return html;
            });
        });
    }
}


$('#mapstab').click(function(){
	$('#map').show();
	 map.invalidateSize();
	$('#map').appendTo($('#tabs-6'));
});



var markercmp = 0;
$('#btngomap').click(function(){
	/*$j.get("/?m=outputs&suppressHeaders=1&"+$j('#mapform').serialize()+"&querysave="+$j('#querysave').val(), {mode: "geojsonMap"}, function (data) {
		console.log(data);
	});
	
	return;*/
	adm_location_lat_field = '';
	adm_location_lng_field = '';
	adm_location_field = '';
	coordinateTmp = {};
	nbrLoc = {};
	var error = false;
	var msgerror = '';
	if(!$('#maplon_select').val() && !$('#maplat_select').val() && !$('#mapdep_select').val() && !$('#mapcom_select').val()){
		error = true;
		msgerror = 'The geographical location is not defined';
	}
	
	if($('#maplon_select').val() && $('#maplat_select').val() && $('#mapdep_select').val() && $('#mapcom_select').val()){
		error = true;
		msgerror = 'Multiple geographical location';
	}
	
	if($('#maplon_select').val() && $('#maplat_select').val() && $('#mapdep_select').val()){
		error = true;
		msgerror = 'Multiple geographical location';
	}
	
	if($('#maplon_select').val() && $('#maplat_select').val() && $('#mapcom_select').val()){
		error = true;
		msgerror = 'Multiple geographical location';
	}
	
	if($('#mapdep_select').val() && $('#mapcom_select').val()){
		error = true;
		msgerror = 'Multiple geographical location';
	}
	
	if(error){
		alert(msgerror);
		return;
	}
	//$('#loader').show();

	var location_lat_field,location_lng_field,adm_location_lat_field,adm_location_lng_field,adm_location_field;
	
	if($('#maplon_select').val() && $('#maplat_select').val()){
		location_lat_field = $('#maplat_select').val();
		location_lng_field = $('#maplon_select').val();
	}
	
	if($('#mapdep_select').val()){
		adm_location_lat_field = $('#mapdep_select').val()+"_lat";
		adm_location_lng_field = $('#mapdep_select').val()+"_lng";
		adm_location_field = $('#mapdep_select').val();
	}
	if($('#mapcom_select').val()){
		adm_location_lat_field = $('#mapcom_select').val()+"_lat";
		adm_location_lng_field = $('#mapcom_select').val()+"_lng";
		adm_location_field = $('#mapcom_select').val();
	}
	
	var myIcon = [];
	myIcon[0] = L.icon({ iconUrl: myURL + 'images/1.png', iconRetinaUrl:
		myURL + 'images/1.png',
		popupAnchor: [0, -14] });
	myIcon[1] = L.icon({ iconUrl: myURL + 'images/2.png', iconRetinaUrl:
		myURL + 'images/2.png',
		popupAnchor: [0, -14] });
	myIcon[2] = L.icon({ iconUrl: myURL + 'images/3.png', iconRetinaUrl:
		myURL + 'images/3.png',
		popupAnchor: [0, -14] });
	myIcon[3] = L.icon({ iconUrl: myURL + 'images/4.png', iconRetinaUrl:
		myURL + 'images/4.png',
		popupAnchor: [0, -14] });
	myIcon[4] = L.icon({ iconUrl: myURL + 'images/5.png', iconRetinaUrl:
		myURL + 'images/5.png',
		popupAnchor: [0, -14] });
	myIcon[5] = L.icon({ iconUrl: myURL + 'images/6.png', iconRetinaUrl:
		myURL + 'images/6.png',
		popupAnchor: [0, -14] });
	console.log("/?m=outputs&suppressHeaders=1&"+$j('#mapform').serialize()+"&mode=jsonMap&querysave="+trim($j('#querysave').val()));
    /*$j.get("/?m=outputs&suppressHeaders=1&"+$j('#mapform').serialize(), {mode: "jsonMap",querysave:trim($j('#querysave').val())}, function (data) {

    }).fail(function(){
    	alert('can\'t load data');
	});*/
    $('#loader').show();
    /*$.ajax({
        xhr: function() {
            var xhr = new window.XMLHttpRequest();
            //Upload progress
            xhr.upload.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with upload progress
                    console.log(percentComplete);
                }
            }, false);
            //Download progress
            xhr.addEventListener("progress", function(evt){
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    //Do something with download progress
                    //console.log(percentComplete.toFixed(2)*100);
                    $('#loader').html('Please wait...<br/>&emsp;&emsp;'+Math.round(percentComplete.toFixed(2)*100)+'%');
                }
            }, false);
            return xhr;
        },
        type: 'GET',
        url: "/?m=outputs&suppressHeaders=1&"+$j('#mapform').serialize()+"&mode=jsonMap&querysave="+trim($j('#querysave').val()),
        dataType: 'json',
        success: function(data){
            //Do something success-ish
            var markerClusters = L.markerClusterGroup();
            console.log(data);
            $j.each(data.features,function(i,val){
                m = L.marker( [val.geometry.coordinates[1],val.geometry.coordinates[0]]);
                htmltext = '';
                $('.datapopup').each(function() {
                    if ($(this).is(":checked")) {
                        var fieldval = $(this).val();
                        //textval = val[fieldval];
                        //var objectConstructor = {}.constructor;
                        //if(textval.constructor === objectConstructor)
                        textval = val.properties[fieldval];
                        htmltext += '<tr><td><b>'+$(this).attr('data-val')+'<b>: </td><td>'+textval+'</td></tr>';
                        m.bindPopup(htmltext);
                    }
                });
                markerClusters.addLayer(m);

            });
            map.addLayer( markerClusters );
            $('#loader').hide();
        },
		error: function(){ $('#loader').hide();alert('can\'t load data'); },
    });*/


});
//reporter.initMaps();
// $('#map').appendTo($('#tabs-6'));

/*
 * var myIcon = L.icon({ iconUrl: myURL + 'images/pin24.png', iconRetinaUrl:
 * myURL + 'images/pin48.png', iconSize: [29, 24], iconAnchor: [9, 21],
 * popupAnchor: [0, -14] });
 * 
 * for ( var i=0; i < markers.length; ++i ) { L.marker( [markers[i].lat,
 * markers[i].lng], {icon: myIcon} ) .bindPopup( '<a href="' + markers[i].url + '"
 * target="_blank">' + markers[i].name + '</a>' ) .addTo( map ); }
 */

/*
 * $("body").on('shown','#tabs-6', function() { map.invalidateSize();
 * //L.Util.requestAnimFrame(map.invalidateSize,map,!1,map._container); });
 */



/*
 * function resizeMap() { var mapid = document.getElementById('map'); if(
 * (mapid.parentNode.offsetWidth >0) && (mapid.parentNode.offsetWidth < 640) ) {
 * mapid.style.width = '100%'; map.invalidateSize(); } } resizeMap();
 * 
 * $(document).ready(function($) { map.invalidateSize();
 * console.log($('.ui-tabs')); $('.ui-tabs').on('tabsactivate', function(event,
 * ui) { map.invalidateSize(); });
 * 
 * $('#mapstab').click(function(){ $('#tabs-maps').show(); map.invalidateSize();
 * });
 * 
 * 
 * }); $(document).bind('pageinit', function( event, data ){
 * map.invalidateSize(); }); height = $(document).height();
 * $(document).height(100);
 */

/*invalidateSize:function(t){
	if(!this._loaded)
		return this;
	t=o.extend({animate:!1,pan:!0},
	t===!0?{animate:!0}:t);
	var e=this.getSize();
	this._sizeChanged=!0,
	this._initialCenter=null;
	var i=this.getSize(),n=e.divideBy(2).round(),s=i.divideBy(2).round(),a=n.subtract(s);
	return a.x||a.y?(t.animate&&t.pan?this.panBy(a):(t.pan&&this._rawPanBy(a),this.fire("move"),t.debounceMoveend?(clearTimeout(this._sizeTimer),this._sizeTimer=setTimeout(o.bind(this.fire,this,"moveend"),200)):this.fire("moveend")),this.fire("resize",{oldSize:e,newSize:i})):this}
*/


//Popup as layer

/*
var popupLocation1 = new L.LatLng(51.5, -0.09);
var popupLocation2 = new L.LatLng(51.51, -0.08);

var popupContent1 = '<p>Hello world!<br />This is a nice popup.</p>',
popup1 = new L.Popup();

popup1.setLatLng(popupLocation1);
popup1.setContent(popupContent1);

var popupContent2 = '<p>Hello world!<br />This is a nice popup.</p>',
popup2 = new L.Popup();

popup2.setLatLng(popupLocation2);
popup2.setContent(popupContent2);

map.addLayer(popup1).addLayer(popup2);
 */