 var xMap;

function showRend(){
    if ($j('#rendp').is(":visible")) {
        $j('#rendp').hide();
    }
    else {
        $j('#rendp').show();
    }
}

      var Icon = new GIcon();
      Icon.image = "marker.png";
      Icon.shadow = "http://www.google.com/mapfiles/shadow50.png";
      Icon.iconSize = new GSize(20, 34);
      Icon.shadowSize = new GSize(37, 34);
      Icon.iconAnchor = new GPoint(9, 34);
      Icon.infoWindowAnchor = new GPoint(9, 2);
      Icon.infoShadowAnchor = new GPoint(18, 25);
      
      /*var clusterIcon = new GIcon();
      clusterIcon.image = 'http://econym.org.uk/gmap/blue_large.png';
      clusterIcon.shadow = 'http://econym.org.uk/gmap/shadow_large.png';
      clusterIcon.iconSize = new GSize( 30, 51 );
      clusterIcon.shadowSize = new GSize( 56, 51 );
      clusterIcon.iconAnchor = new GPoint( 13, 34 );
      clusterIcon.infoWindowAnchor = new GPoint( 13, 3 );
      clusterIcon.infoShadowAnchor = new GPoint( 27, 37 );*/

 function lMap(){
 	var gmap, allm = [], ci = 0, box,tasks=[],
 		africa = {lat:	17.978733,lon: 38.671875, zoom: 3},
 		$accord,self,pbar,box,cluester; 		
	
	this.home = function(){
		gmap.setCenter(new google.maps.LatLng(africa.lat,africa.lon), africa.zoom);
	}
 	
 	this.initz = function(){
 		self=this;
 		gmap = new google.maps.Map2(document.getElementById("map"));
 		gmap.setCenter(new google.maps.LatLng(africa.lat,africa.lon), africa.zoom);
 		gmap.addControl(new GLargeMapControl());
 		//gmap.addControl(new GMapTypeControl());
			gmap.addControl(new GScaleControl(50));
			var position = new GControlPosition(G_ANCHOR_TOP_RIGHT, new GSize(7, 30));
			gmap.addControl(new GMenuMapTypeControl(), position);
			
			if (!GNavLabelControl) {
				alert("GNavLabelControl is not supported \nby this version of api. \n2." + G_API_VERSION);
			}
			else {
				gmap.addControl(new GNavLabelControl());
			}			
			
			/*self.gmgr = new MarkerManager(gmap, mgrOptions);*/
			self.$sbid = $j("#sbar");
			self.myVP = new GLatLngBounds();
			
			pbar = new ProgressbarControl(gmap, {width:150,pcolor:'#98A7B9'}); 
			/*cluester = new Clusterer(gmap);
			cluester.icon = clusterIcon;      
      		cluester.maxVisibleMarkers = 100;
      		cluester.gridSize = 5;
      		cluester.minMarkersPerClusterer = 5;
      		cluester.maxLinesPerInfoBox = 6;*/
						
		}
		
		this.note = function(name, descr){
			var html="<span style='background-color:#eee;'>"+name+"</span><p>"+descr+"</p>";
			return html;
		}
		
		this.placer = function(tar){
			// Add 10 markers to the map at random locations
			//var self=this;			
			pbar.start(tar.length);					
			var bounds = gmap.getBounds();
			var southWest = bounds.getSouthWest();
			var northEast = bounds.getNorthEast();
			var lngSpan = northEast.lng() - southWest.lng();
			var latSpan = northEast.lat() - southWest.lat();			
			var i=0;
			(function(){				
				var point = new GLatLng(tar[i][3],tar[i][4]);
				var tm = new GMarker(point, {
					title: tar[i][1] 
				});
				tm = tm.hideId(tar[i][0]);				
				//cluester.AddMarker(tm,tar[i][1]);				
				
				tm.bindInfoWindowHtml(self.note(tar[i][1],tar[i][2]));
				
				pbar.updateLoader(1);
				self.collect(tm);				
				if(i == (tar.length -1 )){					
					self.buildBox();
					ci = i;
					return;					
				}else{
					i++;					
					setTimeout(arguments.callee, 0);
				}				
			})();		
		}
		
		this.buildBox = function(){
			pbar.remove();
			if (box){ 
				gmap.removeOverlay(box);
			}
			box = gmap.showBounds(self.myVP, {
				top: 30,
				right: 10,
				left: 50
			}).drawBox({
				liWidth: 1
			});
			gmap.addOverlay(box);
		}
		
		this.collect = function(im){
			allm.push(im);
			this.linker(im);
			this.myVP.extend(im.getLatLng());
		}
		
		this.blinder = function(){
			for (var i = 0; i < allm.length; i++) {
				var z = allm[i];
				if (!z.isHidden()) {
					z.closeInfoWindow();
					z.hide();
				}
				else {
					z.show();
				}
			}
		}
		
		this.mfresh = function(){
			this.gmgr.refresh();
		}
		this.JSstr = function (as){
			var str = "{";
			for (var t in as) {
				if (t && t.length > 0 && as.hasOwnProperty(t)) {
					str += '"'+ t + '":' + "[" + as[t].join(",") + "],";
				}
			}	
			str = str.replace(/\,$/, '') + "}";
			return str;
		}
		
		this.refresh = function(){
			var qur = [];
			showRend();
			setTimeout(function(){
			if(!$accord){
				$accord= $j("#accordion").find("ul.maplist");
			}
			$accord.each(function(){
				var zname=$j(this).attr("data-name");
				$j("input:checked",this).each(function(){
					if(! isArray(qur[zname])){
						qur[zname]= [];
					}
					var cval= $j(this).val();
					if(cval >= 0){
						qur[zname].push(cval);
					}					
				});
				if(qur[zname].length == 0){
					 delete qur[zname];
				}
			});			
			var sd = self.JSstr(qur);
						
			if(sd && sd.length > 0){
				$j.ajax({
					type: "POST",
					url: "?m=outputs&a=map&suppressHeaders=1",
					data: "getlist=tasks&needs="+sd,					
					success: function(msg){
						if(msg.length >0){
							tasks = eval('(' + msg + ')');
							self.placer(tasks);
							showRend();
						}
					}
				});
			}
			
		},10);
		}
		
		this.saveStatic = function (){
			$sb=$j("#save_but");$sb.attr("disabled","true").hide();
			$licon=$j("#load_text").show();						
			var maptypes = {m: 'roadmap',h: 'hybrid',k: 'satellite',t: 'terrain'};
			var forpic = 0,marx =[];
			var ctype1=gmap.getCurrentMapType(),czoom=gmap.getZoom();
			var alltypes=gmap.getMapTypes();
			var ctype=maptypes[ctype1.getUrlArg()];
			var picmark='http://maps.google.com/maps/api/staticmap?';
			var bounds = gmap.getBounds();
			merk=gmap.getCurrentMapType();
			prjn= merk.getProjection();
			var southWest = bounds.getSouthWest(),northEast = bounds.getNorthEast();
			/*var lngSpan = northEast.lng() - southWest.lng();
			var latSpan = northEast.lat() - southWest.lat();*/
			
			var myoffset= new GLatLng(northEast.lat(),southWest.lng());
			var markadd='',offset=prjn.fromLatLngToPixel(myoffset,czoom);
			//var rectbound= new GLatLngBounds();
			var markss=''
			$j(allm).each(function(){
				var cpos= this.getLatLng();
				if(bounds.containsLatLng(cpos)){
					var npos=prjn.fromLatLngToPixel(cpos, czoom); 
					//rectbound.extend(cpos);
					//cpos= cpos.toUrlValue();
					//markadd+=cpos+',red|';
					marx[forpic] = {
						x: Math.abs(offset.x - npos.x),
						y: Math.abs(offset.y - npos.y)
					};
					markss+=marx[forpic].x+','+marx[forpic].y+'|';
					forpic++;
				}				
			});
			markss=markss.replace(/\|$/,'');
			/*if(forpic == 0){*/
				var ccc=gmap.getCenter();
				ccc=ccc.toUrlValue();
				//markadd+='&center='+ccc+'&zoom='+gmap.getZoom();
			/*}else{*/
			/*if(forpic > 0){
				//markadd=markadd.replace(/\|$/ ,'');
				var southWest1 = rectbound.getSouthWest().toUrlValue();
				var northEast1 = rectbound.getNorthEast().toUrlValue();
				var SW= southWest1.split(',');
				var NE = northEast1.split(',');
				
				markadd+='path=weight:5|color:0x00ff0044|fillcolor:0x00ff0044|'
					+southWest1+'|'
					+NE[0]+','+SW[1]+'|'
					+northEast1+'|'
					+SW[0]+','+NE[1]+'|'+southWest1;
					
			}*/
			picmark+= markadd +'&key='+topKey+'&format=png32&size=500x500&sensor=false&maptype='+ctype+'&center='+ccc+'&zoom='+czoom;
			var simg= document.createElement("img");
			simg.width=500;
			simg.height=500;
			var slink;
			$j.ajax({
				type: "POST",
				data: "points="+markss+'&link='+picmark,
				url: "?m=outputs&suppressHeaders=1&a=mapimg",
				success: function(msg){
					if(msg.length > 5){
						eval(msg);
						simg.src=slink;
						simg.style.border="2px solid black";
						$j("<div id='imbox' title='Static map image'></div>").dialog({
        					width: 550,
        					height: 550,
        					resizable: false
    					}).append(simg);
						$sb.attr("disabled",false).show();
						$licon.hide();
					}
				}
			});
					
			//simg.src=picmark;
			
			//var $div=$j("div#imger").css('border','3px solid black').center();
			
			//$div.show().append(simg);
			
		}
		
		
		this.linker = function(pin){
			var label = document.createElement("a");
			label.href = '#';
			label.innerHTML = pin.getTitle();			
			label.id = 'link_' + pin.hideId();
			label.style.display = "block";
			label.onclick = function(){
				GEvent.trigger(pin, 'click');
				return false
			};//x-browser
			label.onfocus = function(){				
				GEvent.trigger(pin, 'click');
				return false
			};
			this.$sbid.append(label);
			GEvent.addListener(pin, 'click', function(){
				label.focus();
				return false
			});
			GEvent.addListener(pin, 'mouseover', function(){				
				var xf = this.hideId();
				$j("#link_" + xf, this.$sbid).css('background-color', 'yellow');
				
			});
			GEvent.addListener(pin, 'mouseout', function(){
				var xf = this.hideId();
				$j("#link_" + xf, this.$sbid).css('background-color', 'white');
			});
			
			
			return pin;
			
		}
		
		this.clean = function(){
			gmap.clearOverlays();
			allm = [];
			this.$sbid.empty();
			ci = 0;
		}
		
	}

function watchsels(){
	$j(".maplist > li > input").each(function(){
		$j(this).bind("change click",function(){
			var ons=0,mone=false;
			var nval=$j(this).val();
			var cked = $j(this).is(":checked");
			var $pul=$j(this).parent().parent();
			$pul.find("input").each(function(){
				if (nval == "-1" && cked) {
					$j(this).attr("checked", false);
				}
				else {
					if ($j(this).is(":checked")) {
						ons++;
						if ($j(this).val() == "-1") {
							mone = true;
						}
					}
				}			
			});
			if(ons > 1 || (ons == 1 && !mone)){
				$j("input:first",$pul).attr("checked",false);
			}else{
				$j("input:first",$pul).attr("checked",true);
			}
		});
	})
}					
					
	GMarker.prototype.hideId = function(mid){
		if (isNaN(mid) && !mid) {
			if (this.xid && (this.xid > 0 || this.xid.length > 0)) {
				return this.xid;
			}
		}
		else {
			if (!isNaN(mid) && mid >= 0) {
				this.xid = mid;
				return this;
			}
		}
	}
                    
    /**
     * GMap2.showBounds() method
     * @ author Esa 2008
     * @ param bounds_ GLatLngBounds()
     * @ param opt_options Optional options object {top, right, bottom, left, instant, save}
     */
            
 GMap2.prototype.showBounds = function(bounds_, opt_options){
 	var opts = opt_options || {};
 	opts.top = opt_options.top * 1 || 0;
 	opts.left = opt_options.left * 1 || 0;
 	opts.bottom = opt_options.bottom * 1 || 0;
 	opts.right = opt_options.right * 1 || 0;
 	opts.save = opt_options.save || true;
 	opts.disableSetCenter = opt_options.disableSetCenter || false;
 	var ty = this.getCurrentMapType();
 	var port = this.getSize();
 	if (!opts.disableSetCenter) {
 		var virtualPort = new GSize(port.width - opts.left - opts.right, port.height - opts.top - opts.bottom);
 		this.setZoom(ty.getBoundsZoomLevel(bounds_, virtualPort));
 		var xOffs = (opts.left - opts.right) / 2;
 		var yOffs = (opts.top - opts.bottom) / 2;
 		var bPxCenter = this.fromLatLngToDivPixel(bounds_.getCenter());
 		var newCenter = this.fromDivPixelToLatLng(new GPoint(bPxCenter.x - xOffs, bPxCenter.y - yOffs));
 		this.setCenter(newCenter);
 		if (opts.save) 
 			this.savePosition();
 	}
 	var portBounds = new GLatLngBounds();
 	portBounds.extend(this.fromContainerPixelToLatLng(new GPoint(opts.left, port.height - opts.bottom)));
 	portBounds.extend(this.fromContainerPixelToLatLng(new GPoint(port.width - opts.right, opts.top)));
 	return portBounds;
 }
                	
/**
 * GLatLngBounds.drawBox() method
                * Returns a GPolyline or GPolygon rectangle representing the bounds
                * optional parameter is an object with style properties {liColor, liWidth, liOpa, fillColor, fillOpa, polygon}
                * {polygon:true} switches the return object from GPolyline to GPolygon
                * @author Esa 2007
                 */
GLatLngBounds.prototype.drawBox = function(opt_options){
	var opts = opt_options || {};
	var northEast = this.getNorthEast();
	var southWest = this.getSouthWest();
	var topLat = northEast.lat();
	var rightLng = northEast.lng();
	var botLat = southWest.lat();
	var leftLng = southWest.lng();
	var pnts = [];
	pnts.push(southWest);
	pnts.push(new GLatLng(topLat, leftLng));
	pnts.push(northEast);
	pnts.push(new GLatLng(botLat, rightLng));
	pnts.push(southWest);
	var fillColor = opts.fillColor || opts.liColor || "#0055ff";
	var liWidth = opts.liWidth || 2;
	if (opts.polygon) {
		var boxPoly = new GPolygon(pnts, opts.liColor, liWidth, opts.liOpa, fillColor, opts.fillOpa);
	}
	else {
		var boxPoly = new GPolyline(pnts, opts.liColor, liWidth, opts.liOpa);
	}
	return boxPoly;
}

xMap = new lMap;

                
                    /*
                     * 
                     *    GEvent.addListener(map, 'click', function(ov, latlng, ovll) {
                     *    OR  GEvent.addListener(marker, 'click', function(latlng){
                          if (!ov && latlng) {
                            var min = 'regular map info:' + latlng;
                            var sum = '<p>summary map info:<br/><br/>' + latlng+ Math.random()+'</p>';
                            var tabs = [
                             new MaxContentTab('map tab0', 'map content0'),
                             new MaxContentTab('map tab1', 'map content1'),
                             new MaxContentTab('map tab2', 'map content2'), 
                             new MaxContentTab('map tab3', 'map content3')];
                            map.openMaxContentTabsHtml(latlng, min, sum, tabs, {
                              maxTitle: "More Map Info"
                            });
                          }
                        });
                        
                        function randomMarkers(integer){
          map.clearOverlays();
          for (var i=0; i<10; i++){
            var point = new GLatLng(southWest.lat() + span.lat() * Math.random(),
        						    southWest.lng() + span.lng() * Math.random());
            var spriteMarker = (new GMarker(point,{draggable:true, icon:sprite(random(integer))}));
            map.addOverlay(spriteMarker);
            markerBounds.extend(point);
          }
        }
        /**
         * sprites function
         * Esa 2008
         */
        /*function sprite(id){
          var icon = new GIcon(G_DEFAULT_ICON);
              icon.iconSize = new GSize(32,32);
        	  icon.shadow = "";
              icon.sprite = {};
        	  icon.sprite.image = "testhugesprite.png";
              icon.sprite.top = 2 + id * 34;
          return icon;
        }
        ///////////PROGREES bar 
		
        http://gmaps-utility-library.googlecode.com/svn/trunk/progressbarcontrol/1.0/src/progressbarcontrol_packed.js
         progressBar = new ProgressbarControl(map, {width:150}); 
          <button onclick="progressBar.start(500);">start</button> 
 			<button onclick="progressBar.updateLoader(7);">updateLoader</button> 
 			<button onclick="progressBar.remove();">remove</button>
 			
 			 geocoder
 			 geocoder = new GClientGeocoder();
 			  geocoder.getLocations(latlng, showAddress);
 			     function showAddress(response) {
      if (!response || response.Status.code != 200) {
        alert("Status Code:" + response.Status.code);
      } else {
        place = response.Placemark[0];
        point = new GLatLng(place.Point.coordinates[1],place.Point.coordinates[0]); 
        data.addRows(1);
		data.setCell(row_no,0,row_no + 1);
		data.setCell(row_no,1,place.address);
        data.setCell(row_no,2, cLat);
        data.setCell(row_no,3, cLng);
        ++row_no;
       
        visualization = new google.visualization.Table(document.getElementById('table_canvas'));
        visualization.draw(data, null);
        google.visualization.events.addListener(visualization, 'select', selectHandler);
       
         var marker = markers[row_no -1];
         marker.openInfoWindowHtml(
          '<b>orig latlng:</b>' + response.name + '<br/>' + 
          '<b>Reverse Geocoded latlng:</b>' + place.Point.coordinates[1] + "," + place.Point.coordinates[0] + '<br>' +
          '<b>Status Code:</b>' + response.Status.code + '<br>' +
          '<b>Status Request:</b>' + response.Status.request + '<br>' +
          '<b>Address:</b>' + place.address + '<br>' +
          '<b>Accuracy:</b>' + place.AddressDetails.Accuracy + '<br>' +
          '<b>Country code:</b> ' + place.AddressDetails.Country.CountryNameCode);
        }
        
    }
        */
		
		