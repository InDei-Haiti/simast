 var xMap,bpzn=[],rbox,rect;

function showRend(){
    if ($j('#rendp').is(":visible")) {
        $j('#rendp').hide();
    }
    else {
        $j('#rendp').show();
    }
}

function lMap(){
 	var gmap, allm = [], ci = 0, box,tasks=[], 
 		haiti = {lat:	18.938000000000000,lon: -73.419999999, zoom: 8},
 		$accord,self,pbar,box,cluester,iwindow,legend,poly,initPoint=false,olay,infow,markers=[];
	
	this.home = function(){
		gmap.setCenter(new google.maps.LatLng(haiti.lat,haiti.lon));
		gmap.setZoom(haiti.zoom);
	}
 	
 	this.inpoly = function(){
 		return poly;
 	}

	this.clearFBorder = function(){
		bpzn = [[],[]];
	}

 	this.initz = function(){
 		self=this;
 		var latlng = new google.maps.LatLng(haiti.lat,haiti.lon); 		
	    var myOptions = {
	      zoom: haiti.zoom,
	      center: latlng,
	      mapTypeId: google.maps.MapTypeId.ROADMAP
	    };
	    gmap = new google.maps.Map(document.getElementById("map"), myOptions);
  		
  		
  		infowindow = new google.maps.InfoWindow({
    		'size': new google.maps.Size(352, 150)
  		});

 		self.$sbid = $j("#sbar");
		self.myVP = new google.maps.LatLngBounds();
			
		pbar = new progressBar();
		gmap.controls[google.maps.ControlPosition.RIGHT].push(pbar.getDiv());
		
		poly = new google.maps.Polyline({});
		poly.setMap(gmap);

		infow = new google.maps.InfoWindow();

		olay = new google.maps.OverlayView();
		olay.draw = function(){};
		olay.setMap(gmap);

		rbox = new google.maps.LatLngBounds();
		rect = new google.maps.Rectangle({
          map: gmap
        });
	}
	this.loadCoordinatesZone = function(zones){
		var coordinates = [];
		for(var i=0;i<zones.length;i++){
			geocoder = new google.maps.Geocoder();
			console.log(zones[i].zone);
			var wait = true;
			geocoder.geocode( { 'address': zones[i].zone}, function(results, status) {
			      if (status == google.maps.GeocoderStatus.OK) {
			    	console.log();
			        var marker = new google.maps.Marker({
			            map: gmap,
			            position: results[0].geometry.location
			        });
			      } else {
			        //alert("Geocode was not successful for the following reason: " + status);
			      }
			      wait = false;
			});
			/*while(wait){
				
			}*/
		}
		console.log(coordinates);
	}
	this.note = function(name, descr){
		return  ["<span style='background-color:#eee;'>",name,"</span><p>",descr,"</p>"].join("");			
	}

	 /**
* @param {google.maps.Map} map
* @param {google.maps.LatLng} latlng
* @param {int} z
* @return {google.maps.Point}
*/
	this.latlngToPoint = function( latlng, z){
		var normalizedPoint = gmap.getProjection().fromLatLngToPoint(latlng); // returns x,y normalized to 0~255
		var scale = Math.pow(2, z);
		var pixelCoordinate = new google.maps.Point(normalizedPoint.x * scale, normalizedPoint.y * scale);
		return pixelCoordinate;
	}
/**
* @param {google.maps.Map} map
* @param {google.maps.Point} point
* @param {int} z
* @return {google.maps.LatLng}
*/
	this.pointToLatlng = function(point, z){
		var scale = Math.pow(2, z);
		var normalizedPoint = new google.maps.Point(point.x / scale, point.y / scale);
		var latlng = gmap.getProjection().fromPointToLatLng(normalizedPoint);
		return latlng;
	}

		
		this.placer = function(tar){
			allm=[];
			rbox = null;
			rbox = new google.maps.LatLngBounds();
			this.$sbid.empty();	
			pbar.start(tar.length);					
			var bounds = gmap.getBounds();
			var southWest = bounds.getSouthWest();
			var northEast = bounds.getNorthEast();
			var lngSpan = northEast.lng() - southWest.lng();
			var latSpan = northEast.lat() - southWest.lat();		
			var i=0;
			(function(){				
				var myLatLng = new google.maps.LatLng(tar[i][3], tar[i][4]);
				var tm = new google.maps.Marker({
      				position: myLatLng,
      				map: gmap,
      				title: tar[i][1]      			
  				});
				tm = tm.hideId(tar[i][0]);
				markers.push(tm);
				if(initPoint == false){
					initPoint = myLatLng;
					rbox.extend(myLatLng);
				}else{
					if(!rbox.contains(myLatLng)){
						rbox.extend(myLatLng);
					}
				}
				(function(its,tm){
					google.maps.event.addListener(tm, 'click', function(e) {
						infow.setContent(self.note(its[1]/*+its[3]+"<>"+its[4]+" ID = "+its[6]*/ ,its[2]));
						infow.open(gmap, tm);
  					}); 
  				})(tar[i],tm);

				pbar.updateBar (1);
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
			pbar.hide();
			if (box){ 
				gmap.removeOverlay(box);
			}
			box = showBounds(gmap,self.myVP, {
				top: 30,
				right: 10,
				left: 50
			});
		}
		
		this.collect = function(im){
			allm.push(im);
			this.linker(im);
			this.myVP.extend(im.getPosition());
		}
		
		this.blinder = function(){
			for (var i = 0; i < allm.length; i++) {
				var z = allm[i];
				if (!z.isHidden()) {
					z.closeInfoWindow();
					z.hide();
				}else {
					z.show();
				}
			}
		}
		
		this.mfresh = function(){
			this.gmgr.refresh();
		}
	
		this.JSstr = function (as){
			var str = [];
			for (var t in as) {
				if (t && t.length > 0 && as.hasOwnProperty(t)) {
					str.push(['"', t , '":' , "[" , as[t].join(",") , "]"].join(""));
				}
			}			
			return ['{' , str.join(",") , '}'].join("");
		}
		
		
		
		this.refresh = function(){
			var qur = [];
			bpzn = [];
			initPoint=false;
			showRend();
			this.purity();
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
							try{
								tasks = $j.parseJSON( msg );
								if(tasks && tasks.length > 0){
									self.placer(tasks);
									showRend();
								}else{
									showRend();
									alert("Result is empty!");
								}
							}catch(e){
								alert("Empty result!");
								return false;
							}

						}
					}
				});
			}
			
		},10);
		}
		
		this.saveStatic = function (){
			$sb=$j("#save_but");
			$sb.attr("disabled","true").hide();
			$licon=$j("#load_text").show();						
			var maptypes = {m: 'roadmap',h: 'hybrid',k: 'satellite',t: 'terrain'},
				forpic = 0,marx =[],ctype1=gmap.mapTypeId,czoom=gmap.getZoom(),
				//ctype=maptypes[ctype1.getUrlArg()],
				ctype = ctype1,
				picmark=['http://maps.google.com/maps/api/staticmap?'],
				bounds = gmap.getBounds(),merk=gmap.mapTypeId,prjn= olay.getProjection(),
				southWest = bounds.getSouthWest(),northEast = bounds.getNorthEast(),
				myoffset= new google.maps.LatLng(northEast.lat(),southWest.lng()),
				markadd='',offset=prjn.fromLatLngToDivPixel(myoffset,czoom),
				markss=[];
			$j(allm).each(function(){
				var cpos= this.getPosition();
				if(bounds.contains(cpos)){
					var npos=prjn.fromLatLngToDivPixel(cpos, czoom);
					marx[forpic] = {
						x: Math.abs(offset.x - npos.x),
						y: Math.abs(offset.y - npos.y)
					};
					markss.push([marx[forpic].x,',',marx[forpic].y].join(""));
					forpic++;
				}				
			});
			var ccc=gmap.getCenter();
			ccc=ccc.toUrlValue();
			picmark.push([markadd ,'&format=png32&size=500x500&sensor=false&maptype=',ctype,'&center=',ccc,'&zoom=',czoom].join(""));
			var simg= document.createElement("img");
			simg.width=500;
			simg.height=500;
			var slink;
			$j.ajax({
				type: "POST",
				data: ["points=",markss.join("|"),'&link=',picmark.join("")].join(""),
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
		}
		
		this.purity = function(){
			for(var i=0,l=markers.length; i < l; i++){
				markers[i].kamikaze();
			}
			markers = [];
		}

		this.linker = function(pin){
			var label = document.createElement("a");
			label.href = '#';
			label.innerHTML = pin.getTitle();			
			label.id = 'link_' + pin.hideId();
			label.style.display = "block";
			label.onclick = function(){
				google.maps.event.trigger(pin, 'click');
				return false
			};//x-browser
			label.onfocus = function(){				
				google.maps.event.trigger(pin, 'click');
				return false
			};
			this.$sbid.append(label);
			google.maps.event.addListener(pin, 'click', function(){
				label.focus();
				return false;
			});
			google.maps.event.addListener(pin, 'mouseover', function(){				
				var xf = this.hideId();
				self.$sbid.find("a").css("background-color","inherit")
							.filter("#link_" + xf).css('background-color', 'yellow')
						.end()
					.end()
					.scrollTo('#link_'+xf,800);
			});
			google.maps.event.addListener(pin, 'mouseout', function(){
				var xf = this.hideId();
				$j("#link_" + xf, self.$sbid).css('background-color', 'inherit');
			});
			
			
			return pin;
			
		}
		
		this.clean = function(){
			self.purity();
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
					
	google.maps.Marker.prototype.hideId = function(mid){
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

    google.maps.Marker.prototype.kamikaze = function(mid){
		this.setMap(null);
	}

 	function showBounds (ith,bounds_,opt_options){
	    var cnt = rbox.getCenter();
		ith.fitBounds(rbox);
		ith.setCenter(cnt);
 	}
           	
xMap = new lMap;

                