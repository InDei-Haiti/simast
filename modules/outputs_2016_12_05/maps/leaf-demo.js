// See post: http://asmaloney.com/2014/01/code/creating-an-interactive-map-with-leaflet-and-openstreetmap/
console.log('width avant: '+$('.right').width());


var map = L.map( 'map', {
    center: [18.546476, -72.546464],
    minZoom: 2,
    zoom: 9,
    scrollWheelZoom:false
});

map.on("load",function() {
	$('#map').appendTo($('#tabs-6'));
});

L.tileLayer( /*'http://{s}.mqcdn.com/tiles/1.0.0/map/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright" title="OpenStreetMap" target="_blank">OpenStreetMap</a> contributors | Tiles Courtesy of <a href="http://www.mapquest.com/" title="MapQuest" target="_blank">MapQuest</a> <img src="http://developer.mapquest.com/content/osm/mq_logo.png" width="16" height="16">',
    subdomains: ['otile1','otile2','otile3','otile4']
}*/
		'http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',{
		    maxZoom: 20,
		    subdomains:['mt0','mt1','mt2','mt3']
		}).addTo( map );

console.log('width apres: '+$('.right').width());
// $('#tabs').tabs();


var myURL = jQuery( 'script[src$="leaf-demo.js"]' ).attr( 'src' ).replace( 'leaf-demo.js', '' );

teb = ['frem','fren','tet'];
console.log(teb.indexOf('fre'));
console.log(teb.indexOf('frem'));
$('#mapstab').click(function(){
	$('#map').show();
	 map.invalidateSize();
	$('#map').appendTo($('#tabs-6'));
});
function displayAll(data){
	console.log(data);
}
var markercmp = 0;
$('#btngomap').click(function(){
	$j.get("/?m=outputs&suppressHeaders=1&"+$j('#mapform').serialize()+"&querysave="+$j('#querysave').val(), {mode: "geojsonMap"}, function (data) {
		console.log(data);
	});
	
	return;
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
	$('#loader').show();
	
	/*if(($('#maplon_select').val() && $('#maplat_select').val()) && ($('#mapdep_select').val() || !$('#mapcom_select').val())){
		alert('Choose geographical coordinates fields or administrative location');
	}*/
	
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
	
	$j.get("/?m=outputs&suppressHeaders=1&"+$j('#mapform').serialize()+"&querysave="+$j('#querysave').val(), {mode: "jsonMap"}, function (data) {
		$('#loader').hide();
		data = $j.parseJSON(data);
		var markerClusters = L.markerClusterGroup();
		var adm = {};
		var m;
		if(location_lat_field != null && location_lng_field != null){
			datamtype = $('.datamtype:checked').val();
			iconIndex = 0;
			datamtypemap = {};
			i = 0;
			var process = function() {
				
				$.each(data, function(i,val ) {
					if(val[location_lat_field] != '' && val[location_lat_field] != null && val[location_lng_field] != null && val[location_lng_field] != ''){
						if(datamtype != null){
							tempbol = false;
							if($.isEmptyObject(datamtypemap)){
								datamtypemap[iconIndex] = val[datamtype];
							}else{
								keys = [];
								$.each(datamtypemap, function(id,d ) {
									if(d==val[datamtype]){
										tempbol = true;
									}
									keys.push(id);
								});
								if(!tempbol){
									iconIndex = Math.max.apply( Math, keys)+1;
									datamtypemap[iconIndex] = val[datamtype];
								}
							}	  
						}
						htmltext = '';
						
						$('.datapopup').each(function() {
							 if ($(this).is(":checked")) {
								var fieldval = $(this).val();
								textval = val[fieldval];
								var objectConstructor = {}.constructor;
								if(textval.constructor === objectConstructor)
									textval = val[fieldval]['name'];
								htmltext += '<tr><td><b>'+$(this).attr('data-val')+'<b>: </td><td>'+textval+'</td></tr>';
							 }
						});	
						
						if(htmltext){
							htmltext = '<table>'+htmltext+'</table>';
					    }
						if($("#markergroup").is(':checked')){
							isvisible = false;
							allvisible = true;
							biblio = {};
							$('.datamfilter').each(function() {
								if($(this).val()!="")
									biblio[$(this).val()] = 99;
							});
							$('.datamfilter').each(function() {
								if(biblio.hasOwnProperty(val[$(this).attr('data-tmp')])){
									isvisible = true;
								}
								if($(this).val()!=""){
									allvisible = false;
								}
							});
							if(isvisible){
								m = L.marker( [val[location_lat_field],val[location_lng_field]]/*,{icon: myIcon[iconIndex]}*/).bindPopup(htmltext).openPopup()
								markerClusters.addLayer(m);
							}else if(allvisible){
								m = L.marker( [val[location_lat_field],val[location_lng_field]]/*,{icon: myIcon[iconIndex]}*/).bindPopup(htmltext).openPopup()
								markerClusters.addLayer(m);
							}
							
						}else{
							L.marker( [val[location_lat_field],val[location_lng_field]]/*,{icon: myIcon[iconIndex]}*/).bindPopup(htmltext).openPopup().addTo(map);
						}
						
						iconIndex = 0;
					}
					if (i + 1 < length && i % 1000 == 0) {
				        setTimeout(process, 5000);
				    }
				});
			};
			process();
			console.log(datamtypemap);
			if($("#markergroup").is(':checked')){
				map.addLayer( markerClusters );
			}
		}else if(adm_location_field != null){
			
		}
		
		/*$.each(data, function( key, val ) {
			var htmltext = '';
			$('.datapopup').each(function() {
				 if ($(this).is(":checked")) {
					var fieldval = $(this).val();
					htmltext += '<tr><td><b>'+$(this).attr('data-val')+'<b>: </td><td>'+val[fieldval]+fieldval+'</td></tr>';
				 }
			});
			if(htmltext){
				htmltext = '<table>'+htmltext+'</table>';
		    }
			if($("#markergroup").is(':checked')){
				if(val.hasOwnProperty(adm_location_lat_field) && val.hasOwnProperty(adm_location_lng_field) && val[adm_location_lat_field] && val[adm_location_lng_field]){
					var m;
					if(val[location_lat_field] && val[location_lng_field]){
						m = L.marker( [val[location_lat_field],
						               val[location_lng_field]]/--, {icon: myIcon[1]}--/ ).bindPopup( htmltext );
							markerClusters.addLayer( m );
					}else{
						
						m = L.marker( [val[adm_location_lat_field], val[adm_location_lng_field]]/--, {icon: myIcon[1]}--/ ).bindPopup(val[adm_location_field]);;
						
						
						markerClusters.addLayer( m );
				    }
				}
			}else{
				
				if(val.hasOwnProperty(adm_location_lat_field) && val.hasOwnProperty(adm_location_lng_field) && val[adm_location_lat_field] && val[adm_location_lng_field]){
					
					if(val[location_lat_field] && val[location_lng_field]){
						
						m = L.marker( [val[location_lat_field],
						               val[location_lng_field]]/--, {icon: myIcon[1]}--/ ).bindPopup( htmltext );
						map.addLayer(m);
					}else{
						val_adm = val[adm_location_lat_field]+'_'+val[adm_location_lng_field];
						if(!coordinateTmp.hasOwnProperty(val_adm)){
							
							m = L.marker( [val[adm_location_lat_field],
								           val[adm_location_lng_field]]/--, {icon: myIcon[1]}--/ ) .bindPopup("<b>"+val[adm_location_field]+" ("+1+")</b>").openPopup();;
							map.addLayer(m);
							coordinateTmp[val_adm] = [m,1];
							
						}else{
							coordinateTmp[val_adm] = [coordinateTmp[val_adm][0],coordinateTmp[val_adm][1]+1];
							coordinateTmp[val_adm][0]._popup.setContent("<b>"+val[adm_location_field]+" ("+(coordinateTmp[val_adm][1])+")</b>")
						}
						
						
					}
				}
			}
			$('#loader').hide();
		});*/
		if($("#markergroup").is(':checked')){
			//map.addLayer( markerClusters );
		}
		
	});
});
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