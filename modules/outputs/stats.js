var fstatp,redv =false,rezo,Rezo,lahand,dcl,flag,tw,acols,shadow;
var chart = null;
var map;
var sFrames = function (){
	this.$bh=$j("#shome");
	this.cols=[];this.rows=[];this.$rbox;this.$cbox;this.ctt=$j("#cbox");this.rtt=$j("#rbox");
	this.periods=[];this.leader;this.ntype;this.nfld;this.ltext;
	this.times = ['none','All','weekly','monthly','quarterly','annually'];this.$gbox=$j("#gbox");
	this.lf;this.launch=false;this.title;this.dobc= new RegExp("dob",'ig');	
	this.ddvi = $j("<div class='kill_area' title='Delete'></div>");
	this.ranges = [];	
	this.groupSum={visual:{},title:''};
	this.boxMode;
	this.plurchoise = [];	
}

function chartz (){
	this.a= '';
}

extend(sFrames,saveClass);

extend(chartz,saveClass);

sFrames.prototype.init = function(){
	
	this.extra='<label><input type="checkbox" id="brest">Build result table</label><br><br>';
	var self=this;
	if(this.lauch){
		return;
	}else{
		$j("#launchbut").attr("disabled",false);
	}
	var $hapt =$j("#box-home");

	for(var i=0,fl=fields.length;i < fl; i++){
        console.log(fields[i].title+": "+fields[i].type);
		$j(['<li class="refulli ui-corner-tr" ><div class="ulit fbox">',fields[i].parent,' : ',fields[i].title,'</div></li>'].join(""))
			.attr("data-hid",fields[i].id)
			.attr("type",fields[i].type)
			.attr("data-type",fields[i].datatype)
			.attr("data-sys",fields[i].datasys)
			.attr("data-field",fields[i].field)
            .attr("title",fields[i].datatitle)
			.attr("data-form",fields[i].dataform)
			.helpone()
			.appendTo($hapt);
	}
	$hapt = null;
	this.launch=true;	
	$j("#vbox").sortable().disableSelection();
    $j("#rbox").sortable({
        tolerance: 'pointer',
        stop: function(event, ui){
            self.launchQuery("rbox");
			//self.Leader();
        },
		 deactivate: function(event,ui){
		 	//self.Leader();
		 }
    }).disableSelection();
    $j("#cbox").sortable({
        tolerance: 'pointer',
        stop: function(event, ui){
            self.launchQuery("cbox");
        }
    })
	.selectable({
		noConflict: true,
		stop: function(){
			var result = $j("#selected-result").empty(),cnt=0;
			$j("li > .bfg",this).removeClass("headf-connect");
			$j(".ui-selected", this).filter(".fbox2").each(function(){				
				$j(this).parent().find(".bfg").addClass("headf-connect").end()
					.find(".kill_area").selectable("destroy");				
				++cnt;
			});	
			self.grouper();		
		}
	})
	.disableSelection();
    $j("#fsrcr").droppable({
        accept: '#box-home li,  li.head-field',
        activeClass: 'ui-state-hover',
        hoverClass: 'ui-state-active',
        addClasses: "head-field hfr",
        greedy: true,
        tolerance: 'intersect',
        drop: function(ev, $ui){
            self.rcvField($ui, "rbox", ev);			
            return true;
        }
    });
    $j("#fsrcc").droppable({
        accept: '#box-home li, li.head-field',
        activeClass: 'ui-state-hover',
        hoverClass: 'ui-state-active',
        addClasses: "head-field hfc",
        greedy: true,
        tolerance: 'intersect',
        drop: function(ev, $ui){
            self.rcvField($ui, "cbox", ev);
			$j("#cbox",this).selectable('refresh');
            return true;
        }
    });	
	
	$j(".fcheck").live("focusout",function(e){
		var $li=$j(this).closest("tr"),
			zid=$li.attr("id").replace("piod_",''),
			meclass=this.className;
		if(meclass.match(/nto/)){
			self.ranges[self.lf].val[zid].e=$j(this).val();
		}else if (meclass.match(/nfrom/)){
			self.ranges[self.lf].val[zid].s=$j(this).val();
		}else if (meclass.match(/nname/)){
			self.ranges[self.lf].val[zid].n=$j(this).val();
		}
	});
	
	$j("#fperiod").live("change",function(e){
		self.ranges[self.lf].val=$j(this).val();		
	});
	
	if(fstatp && typeof fstatp === "object"){
		this.ranges = fstatp.range;
		self.turnSwitch(fstatp);
		if((fstatp.rbox && fstatp.rbox.length > 0) || (fstatp.cbox && fstatp.cbox.length > 0 ) ){
			this.arrangeFields({
				rbox: fstatp.rbox,
				cbox: fstatp.cbox
			});
		}
		fstatp=null;
		$j(".stab_let").attr("disabled",false);
	}
	
	$j(".head-field",$j(".dgetter")).live("mouseenter mouseleave",function(e){
		var hover = (e.type === 'mouseenter' || e.type === 'mouseover'),$subj=$j(".kill_area",this);
		if (hover) {
			$subj.addClass("showx");
		}else{
			$subj.removeClass("showx");
		}
	});
};

sFrames.prototype.dataBox = function(way){
	if(way === undefined){
		return this.boxMode;
	}else{
		this.boxMode=way;		
	}
};

sFrames.prototype.destroy = function(){
	this.$bh.children().hide();
	this.$gbox.empty();
	$j("#tthome").empty();	
};

sFrames.prototype.findcopy = function (dfn){
    var tfn, fob,
		tar=$j("li",$j("#box-home")[0]),tl=tar.length;    
	while(tl--){
		var tli=tar[tl];
        tfn = $j(tli).attr('data-hid');
        if (tfn === dfn) {
            fob = tli;
            tl=0;
        }
    }
    return fob;
};

$j.fn.helpone = function () {
	$j(this).draggable({
		revert:'invalid',
		helper:'clone',
		greedy:true,
		cursor:'crosshair',
		cursorAt:{
			top:18,
			left:25
		},
		start:function (event, ui) {
			dstp = $j(this).parent();
			//alert("qwerfwergwerg");
			//return false;
		}
	});

	$j(this).draggable('enable').disableSelection();

	return this;
};

sFrames.prototype.checkCurr =  function (cdiv, $nval){
    var res = true, nxt = $j($nval).attr("data-hid");
    if (cdiv === "cbox" || cdiv === "rbox") {
        $j("#" + cdiv + " > li:visible").each(function(){
            var txt = $j(this).attr("data-hid");
            if (txt == nxt) {
                res = false;
            }
        });
    }
    else 
        if (cdiv === "vbox") {
            var dbv = $j("#" + cdiv + " > li").size();
            if (dbv > 0 || (dbv == 0 && $j.inArray(nxt, datapos) < 0)) {
                res = false;
            }
        }
    return res;
}

sFrames.prototype.recruiter = function(id,area){
	var tvl,tdl,pos;
	if (area === 'rbox') {
			tvl = 'row';
			tdl = 'col';
		}
		else 
			if (area === 'cbox') {
				tvl = 'col';
				tdl = 'row';
			}
	areas[tvl].push(id);			
	pos=$j.inArray(id,areas[tdl]);
	if (pos >= 0) {
		areas[tdl].splice(pos, 1);
	}	
};

sFrames.prototype.groupKiller = function(id){
	delete this.groupSum[id];
	delete this.groupSum.visual[id];
	$j("#gslist").find(".fld_"+id).remove();
};

sFrames.prototype.findGrouped = function(area,me){
	var self=this;
	$j("#" + area + " > li:visible").each(function(){
    	var txt = $j(this).attr("data-hid"),vtype=$j(this).attr("data-type");
		if(vtype === 'date' && self.ranges[txt] && amnt(self.ranges[txt]) > 0 && self.ranges[txt]['val'] != 'none' && txt != me && self.ranges[me]){
			self.ranges[me]['val']='none';
			if($j("#gbox").data("active-period") == me){
				$j("#gbox").find("select").val("none");
			}
		}
	});
};

sFrames.prototype.rcvField = function ($pitem, udiv, pev){
	var self=this,$item,fromer,df,aclass;
	
    if (pev != "emul") {
        $item = $pitem.draggable;		
    }
    else {
        $item = $pitem;		
    }
    fromer = $pitem.sender;
	df = $item.attr('data-hid');
	this.findGrouped(udiv,df);
    if (self.checkCurr(udiv, $item)) {
		var dimg = $j(self.ddvi).clone(true), opp, $nwid, cact = false;
		if (dstp) {
			dstp = false;
			$nwid = $j($item).clone(true);
			$j($item).draggable('disable');
			
		}
		else {
			
			var $icopy = self.findcopy(df);
			$nwid = $j($icopy).clone();
			$nwid.draggable('enable').removeClass('ui-state-disabled');
			$item.hide().remove();
			$j($icopy).draggable('disable').addClass('ui-state-disabled');
			cact = true;
		}
		self.recruiter($j($item).attr("data-hid"), udiv);
		$nwid.addClass("head-field").removeClass("ui-widget-content").find("div.fbox").toggleClass("fbox fbox2");//.addClass("fbox2");
		// if(pev == "emul")$nwid.draggable('enable').removeClass('ui-state-disabled');
		$nwid.find("div").after(dimg);
		if (udiv == "rbox") {
			$nwid.bind('click', function(e){
				//self.dataBox='row';
				self.Leader(this);
			});
			aclass = 'hfr';
		}
		else {
			$nwid.bind('click', function(e){
				if (e.shiftKey) {
					//$j("div",this).addClass("ui-selected");
					
					var bclass = "headf-connect", $obj = $j(".bfg", this), inClass = $obj.hasClass(bclass);
					if (inClass) {
						$obj.removeClass(bclass);
						var id = $j(this).find(".fbox2").removeClass("thisli").end().attr("data-hid");
						self.groupKiller(id);
					}
					else {
						$obj.addClass(bclass);
					}
					self.grouper();
				}else if ($j(".headf-connect", this).length == 1 && self.groupMS() == 'summ') {
						self.dataBox('group');
						$j(this).closest("ul").find(".fbox2").removeClass("thisli").end().end().find(".fbox2").addClass("thisli");
						
						var lid = $j(this).attr('data-hid'), list = gpgr.getLects(lid), ltext = $j(this).text(), defval = -100, 
						ml = $j("<select id='gpoptions' class='selgroup'></select>").bind('change', {
							oid: lid,
							otxt: ltext
						}, function(e){
							var $gpo = $j("#gpoptions"), xv = $gpo.val(), $gsl = $j("#gslist"), lx = e.data.oid;
							if (xv > -100) {
								self.groupSum[lx] = xv;
								var ntxt = e.data.otxt + ' = ' + $j("option[value='" + xv + "']", $gpo).text();
								self.groupSum.visual[lx] = ntxt;
								if ($j(".fld_" + lx, $gsl).length == 0) {
									$gsl.append(["<p class='fld_" , lx , "'> " , ntxt , "<p>"].join(""));
								}
								else {
									$j(".fld_" + lx, $gsl).text(ntxt);
								}
							}
							else {
								self.groupKiller(lx);
							}
						});
						
						$j(ml).append("<option value='-100'> none </option>");
						if (list.length > 0) {
							for (var i = 0, j = list.length; i < j; i++) {
								$j(ml).append(["<option value='" , i , "'>" , list[i].v , '</option>'].join(""));
								if (self.groupSum[lid] == i) {
									defval = i;
								}
							}
						}
						$j(ml).val(defval);
						var vlin = $j("<input type='text' size='20' name='group_name'>").focusout(function(e){
							var x = $j(this).val();
							if (x) {
								self.groupSum.title = x;
							}
						}).attr("value", function(i, v){
							if (self.groupSum.title.length > 0) {
								return self.groupSum.title;
							}
							else 
								if (v.length > 0) {
									self.groupSum.title = v;
									return v;
								}
								else {
									var x = $j("input", self.$gbox).val();
									if (x) {
										self.groupSum.title = x;
									}
									return x;
								}
						});
						var $dfl = $j("<div/>"), $gsl = $j("<span id='gslist'></span>");
						$dfl.append("<span>Name of the group</span>").append(vlin).append("<br>").append(ml);
						
						var vbs = self.groupSum.visual;
						for (var df in vbs) {
							$gsl.append(["<p class='fld_" , df , "'>" , vbs[df] , '</p>'].join(""));
						}
						$gsl.appendTo($dfl);
						self.$gbox.empty().append($dfl);
						
					} else {
						self.Leader(this);
					}
			});
			$nwid.prepend("<div class='bfg'></div>");//.unbind('click');
			aclass = 'hfc';
		}
		$nwid.addClass(aclass).appendTo("#" + udiv);
		$nwid.find(".kill_area").bind('click', {
			area: udiv
		}, function(x){
			self.setFree(this, 'click', x.data.area);
		});
	}
	else 
		if (dstp) {
			$j($item).draggable({
				revert: 'valid'
			});
		}
	$j("#launchbut").attr("disabled",false);   
};

sFrames.prototype.setFree = function(obj,mode,area){

	var $self1 = $j(obj),self=this,$spar,ffdel,tdl;
	if (mode == 'click') {
		$spar = $self1.parent();
		ffdel = $spar.attr("data-hid");
	}else if(mode == 'fake'){
		$spar = $self1;
		ffdel = $self1.attr("data-hid");
	}
	//pdiv = $self1.parents().get(1).attr("id");
	tdl='col';
	if (area == 'rbox') {
		tdl = 'row';
	}
	var pos=$j.inArray(ffdel,areas[tdl]);
	areas[tdl].splice(pos,1);
	$spar.remove();
	delete this.plurchoise[ffdel];
	var tar=$j("ul#box-home > li"),tl= tar.length;
	while(tl--){
	//$j("ul#box-home > li").each(function(){
		var $this=$j(tar[tl]);
		var tfn = $this.attr("data-hid");
		if (tfn == ffdel) {
			$this.helpone();
			if (mode == "click") {
				self.Leader();
			}
			self.ranges[tfn] = null;
			//return false;
			tl=0;
		}
	}//);
};

sFrames.prototype.grouper = function(){
	var cnt=$j("#cbox").find('.headf-connect').length;
	if (cnt > 0) {
		$j("#colgroupz").show();
	}
	else {
		$j("#colgroupz").hide();
		this.$gbox.empty();
		this.groupSum={visual:{},title:''};	
	}
	return cnt;
};

sFrames.prototype.turnSwitch = function(tobel){
	var list=['sunqs','stots_rows','stots_cols','sblanks','sperc-cols','sperc-rows','delta-count'];
	for(var i=0,j=list.length; i < j; i++){
		var ld=list[i];
		if (tobel.hasOwnProperty(ld) && tobel[ld] !== undefined) {
			if (tobel[ld] > 0) {
				$j("#"+ld).attr('checked', true);
			}
		}
	}	
};

sFrames.prototype.arrangeFields = function(t){
	var areas = ["rbox", "cbox"], self = this,al=areas.length;
	for (var i = 0; i < al; i++) {
		$j("#" + areas[i] + " > li").each(function(){
			var $tl = $j(this);
			var ffdel = $tl.attr("data-hid");
			$tl.remove();
			$j("ul#box-home > li[data-hid='" + ffdel + "']").each(function(){
				var tfn = $j(this).attr("data-hid");
				if (tfn == ffdel) {
					$j(this).helpone();
					return false;
				}
			});
		});
	}
	
	for (var x = 0; x < al; x++) {
		var i = areas[x];
		if (t.hasOwnProperty(i) && t[i].length > 0) {
			var fs = t[i];
			if (fs.length > 0) {
				for (var z in fs) {
					if (z && fs[z].id >= 0) {
						var fv = self.findcopy(fs[z].id);
						if (fv) {
							var $uu = $j(fv);
							dstp = $j(fv).parent();
							self.rcvField($uu, i, "emul");
						}
					}
				}
			}
		}
	}
	fromlist = true;	
};

sFrames.prototype.launchQuery = function (udiv){}

sFrames.prototype.pclean = function(){
	var self=this,ordr=[this.rtt,this.ctt];
	this.ranges=[];
	this.$gbox.empty();	
	$j(ordr).each(function(){
		$j("li",this).each(function(){
			self.setFree(this,'fake');
		});
	});	
	this.lf=null;
	$j("#tthome").empty();
	$j(".stab_let").attr("disabled",true);
	$j("#colgroupz").hide();
	$j("#bbbox").find("ul:eq(1)").find("input").attr("checked",false);
};

sFrames.prototype.fillRange = function(nl){
	var self= this;
	if(!self.ranges){
		self.ranges=[];
	}
	if(!self.ranges[nl]){
		self.ranges[nl] = {
			type: self.ntype,
			title: self.title,
			fld: self.nfld
		};		
	}	
	if(!self.ranges[nl].val){
		self.ranges[nl].val=[];
	}
};

sFrames.prototype.DCG = function(nl){
	var self = this,abort=false;
	if (!self.ranges || !self.ranges[nl] || (self.ranges[nl].val && self.ranges[nl].val.length == 2)) {
		var $padd = $j("#bef-but").find("select:eq(0)").clone(true).attr({
			"id": "fdelta_2",
			"data-stage": "2"
		});
		$j("#crem").before("<span>To</span>").before($padd);
		$j("#crem").val("Del");
		
		self.fillRange(nl);
		//self.ranges[nl].val=[null,null,null];
		
		if(self.ranges[nl].val.length === 2){
			self.ranges[nl].val[2]=null;
		}
		$padd = null;
	}else{
		$j("#bef-but").find("select:last").remove().end().find("span:last").remove();
		 self.ranges[nl].val.pop();
		$j("#crem").val("Add");
	}
};

sFrames.prototype.CleanRange = function(nl){
	var self= this;
	this.$gbox.html("<span>this will be simple list</span>").show();
	delete self.ranges[nl];
};

sFrames.prototype.emuLeader = function (l){
	var thid=$j(l).attr("data-hid");
	this.plurchoise[thid] = $j(l).val();
	var areas=['rbox','cbox'],$founded=false;
	for(var i=0;i < areas.length; i++){
		$j("#"+areas[i]).find("li").each(function(){
			if($j(this).attr("data-hid") == thid){
				$founded=$j(this);
			}
		});
	}
	if($founded !== false){
		$founded.trigger("click");
	}
};

sFrames.prototype.Leader = function(obj){
	var self = this, abort = false,$nl = $j(obj), exx = false, fval = false, nlead = $nl.attr("data-hid"), deltas = $j("#delta-count").is(":checked"),pluse;
	
	$j("#bclean").show();
	//self.ntype = $nl.attr("data-type");
	self.ntype = $nl.attr("type");
    self.nfld= 'wform_'+$nl.attr("data-form")+'.'+$nl.attr("data-field");
	self.ltext = $nl.text();
	self.title = $nl.text();
	pluse =  (typeof plus[nlead] === "object");
	if(pluse === true){
		var bats=plus[nlead];
		if (isNaN(this.plurchoise[nlead])) {
			var $pickPart = $j("<select class='text' onchange='stater.emuLeader(this)' data-hid='" + nlead + "'><option value='-1'>-- Select Part --</option></select>");
			for (var i = 0, l = bats.header.length; i < l; i++) {
				if (bats.visibility[i] === true) {
					$pickPart.append("<option value='"+i+"'>" + bats.header[i] + "</option>");
				}
			}
			this.$gbox.empty().append($pickPart).show().data("active-period", nlead);
			self.lf = nlead;
			return;
		}
		else {
			self.ntype = (($j.isArray(bats.columns[this.plurchoise[nlead]]) || typeof bats.columns[this.plurchoise[nlead]] === 'object') ? 'string' : bats.columns[this.plurchoise[nlead]]);
		}
	}
	if (!(self.ranges[nlead] == null)) {
		exx = true;
	}
	if (self.lf !== nlead || self.dataBox() !== 'row') {
		if (self.title && self.title.length > 0) {
			var xm = self.title.match(self.dobc);
			if (xm && xm.length > 0) {
				self.ntype = "number";
			}
		}
		var $tadd, edue, epost, ncase = false;
		console.log(self.ntype);
		switch (self.ntype) {
			case "string":
				if (selects[nlead] === 'plain' || deltas === false) {
					$tadd = $j("<span>this will be simple list</span>");
					delete self.ranges[nlead];
					abort = true;
				}
				else 
					if (deltas === true && isArray(selects[nlead])) {
						var deltac = selects[nlead];
						$tadd = $j("<select id='fdelta_0' class='text' data-stage='0'></select>").bind("change", {
							dpid: nlead
						}, function(e){
							var par = e.data.dpid, nval = $j(this).val(), zpart = $j(this).attr("data-stage");
							if (!isArray(self.ranges[par]['val'])) {
								self.ranges[par]['val'] = [];
							}
							self.ranges[par]['val'][zpart] = nval;
						/*if (nval != 'none') {
					 
					 }*/
						});
						$tadd.append("<option value='-1'> ---- </option>");
						for (var i = 0, l = deltac.length; i < l; i++) {
							$tadd.append(["<option value='", deltac[i].v, "' ", (i >= 0 ? '' : "selected=\"selected\""), ">", deltac[i].v, "</option>"].join(""));
						}
						var $tadd2 = $tadd.clone(true).attr("data-stage", "1").attr("id", "fdelta_1"), $par = $j("<div id='bef-but'/>").append("<span>From</span>").append($tadd).append("<span>To</span>").append($tadd2);
						$tadd = $par;
						$tadd2 = null;
						$par = null;
						if (exx) {
							var exval = self.ranges[nlead].val;
							if (exval !== false && exval.length > 0) {
								var epar = [];
								if (exval.length === 3) {
									var $padd = $tadd.find("select:eq(0)").clone(true).attr({
										"id": "fdelta_2",
										"data-stage": "2"
									});
									$tadd.append("<span>To</span>").append($padd);
									$padd = null;
									
								}
								for (var ind in exval) {
									if (!isNaN(ind)) {
										epar.push(["$j('#fdelta_" , ind , "').val('" ,exval[ind] , "');"].join("") );
									}
								}
								epost = epar.join("");
								epar = null;
								fval = exval;
							}
							self.ranges[nlead]['val'] = fval;
						}
						else {
							self.fillRange(nlead);
							self.ranges[nlead]['val'] = [null, null];
						}
						var butext = 'Del';
						if (!exx || (fval && fval.length == 2)) {
							butext = 'Add';
						}
						$tadd.append(["&nbsp;<input type='button' class='text' value='" , butext , "' id='crem' onclick='stater.DCG(\"" , nlead , "\")'>",
								"&nbsp;<input type='button' class='text' value='Clear Range' id='crem' onclick='stater.CleanRange(\"" , nlead , "\")'>"].join("") );
					}
				break;
			case "date":
				$tadd = $j("<select id='fperiod' class='text'></select>").bind("change", {
					dpid: nlead
				}, function(e){
					var par = e.data.dpid, nval = $j(this).val();
					self.ranges[par]['val'] = nval;
					if (nval != 'none') {
					
					}
				});
				for (var i = 0, l = this.times.length; i < l; i++) {
					$tadd.append(["<option value='", self.times[i], "' ", (i > 0 ? '' : "selected=\"selected\""), ">", self.times[i], "</option>"].join(""));
				}
				edue = "$j('#fperiod',this.$gbox).before(\"<p>Select period type&nbsp;&nbsp;</p>\");";
				fval = 'none';
				if (exx) {
					var exval = self.ranges[nlead].val;
					if (exval !== false) {
						epost = "$j('#fperiod').val('" + exval + "');";
						fval = exval;
					}
					self.ranges[nlead]['val'] = fval;
				}
				break;
			case "number":
				//$tadd = $j("<ul id='oledr'></ul>");
				$tadd = $j("<table cellpadding=1 cellspacing=1 border=0 id='oledr'></table>");
				$tadd.append("<thead><tr><th>From</th><th>&nbsp;</th><th>To</th><th>Name</th><th>&nbsp;</th><th>&nbsp;</th></tr></thead><tbody></tbody>");
				edue = 'this.addRow(0,false);';
				if (exx) {
					epost = "self.refill(nlead);";
					edue = '';
				}
				ncase = true;
				break;
			default:
				break;
		}
		this.$gbox.empty().append($tadd).show().data("active-period", nlead);
		self.lf = nlead;
		if (!isArray(self.ranges[self.lf]) && !exx) {
			if (abort === false) {
				self.ranges[self.lf] = {
					type: self.ntype,
					title: self.title,
					fld: self.nfld
				};
				if (ncase) {
					self.ranges[self.lf]['val'] = [];
				}
				else {
					self.ranges[self.lf]['val'] = fval;
				}
			}
		}
		eval(edue);
		eval(epost);		
	}	
}

sFrames.prototype.refill = function(id){
	var self=this;
	if(this.ranges[id].val.length > 0){
		$j(this.ranges[id].val).each(function(i){
			if (!(this === null)) {
				self.addRow(i, true, this);
			}
		});
	}
}

sFrames.prototype.addRow = function(pid,cold,cvo){
	var self=this,cl=$j("tbody > tr",this.$gbox).length,lto=$j("#piod_"+pid,this.$gbox).find("input.nto").val();
	if(!lto || lto.length == 0){
		lto=0;
	}
	if(cold){
		cvo=self.ranges[self.lf].val[pid];
		lto=cvo.s;
	}else{
		cvo={s:'',e:'',n:''};
	}	
	var tst=["<tr id='piod_",cl,"'>",
			"<td><input class='nfrom fcheck text' type='text'  value='",lto,"' size='5'></td><td> - </td>",
			"<td><input class='nto fcheck text' type='text' size='5' value='",cvo.e,"'>",
			"<td><input class='nname fcheck binput text' type='text' size='5' value='",cvo.n,"'></td>",
			"<td>",				
				//"<span class='fbutton addbutt' onclick='stater.addRow(",cl,",false);' title='Add'></span>",
				"<span class='fa fa-plus' style='color: #354c8c' onclick='stater.addRow(",cl,",false);' title='Add'></span>",
				//"<span class='fbutton delbutt' onclick='stater.delRow(",cl,");' title='Delete'></span>",
				"<span class='fa fa-remove' style='color: red' onclick='stater.delRow(",cl,");' title='Delete'></span>",
			"</td></tr>"].join("");
	if (cl > 0 && !cold) {
		$j(tst).insertAfter($j("#piod_" + pid));
	}
	else if(cl == 0 || cold){
		$j("tbody", this.$gbox).append(tst);
	}	
	//self.ranges[self.lf].push(pid);
	if (!cold) {
		self.ranges[self.lf].val[cl] = {
			s: lto,
			e: 0,
			n:''
		};
	}
}

sFrames.prototype.delRow = function(id){
	$j("#piod_"+id).remove();
	this.ranges[this.lf].val[id]=null;
	if($j("tbody > tr",this.$gbox).length == 0){
		this.$gbox.empty();
	}
}

sFrames.prototype.groupMS = function(){
	var $vc = $j("#colgroupz"), res = false;
	if ($vc.is(":visible")) {
		res = $j("input[name='wayofg']:checked", $vc).val();
	}
	return res;
}

sFrames.prototype.chState = function(id){
	return $j("#"+id).is(":checked");
}

sFrames.prototype.collector = function(filtersPlain){
	var $rcr=$j("#rctrl"),	
	pres = {
		rows: [],
		cols: [],
		cgroup: [],
		range: this.ranges,
		id: this.lf,
		type: this.ntype,
		title: this.title,
		list: gpgr.getVisibles(),
		result_filter: pf,
		stots_rows: this.chState("stots-rows"),
		stots_cols: this.chState("stots-cols"),
		sperc_rows: this.chState("sperc-rows"),
		sperc_cols: this.chState("sperc-cols"),
		delta_count: this.chState("delta-count"),
		records:	this.chState("records"),
        datapercent:	this.chState("datapercent"),
		sunqs: this.chState("sunqs"),
		sblanks: this.chState("sblanks"),
		brest: $j("#brest",$j("#dbox")[0]).is(":checked"),
		gsums: this.groupSum,
		gmetd: (function(x){
				return x.groupMS();		
		})(this),
		pluralchoice: this.plurchoise,
		plurals : (function(){
			var res=false;
			if(plus && amnt(plus) > 0){
				res=[];				
				for(var z in plus){
					if(plus[z] !== null){
						res[z]=plus[z];
						res[z].data=null;
					}
				}				
			}
			return res;
		})(),
		lvds: [$j("#lvd_date").val(),$j("#more_opts").find("select").val()],
		date_crit: $j(":input[name='dfilter']",$rcr).val(),
		vis_crit: $j(":input[name='vis_sel']",$rcr).val(),
		cur_center: this.chState("curcen"),
		actives: this.chState("ashow"),
		filters: filtersPlain ? filar : JSON.stringify(filar),
		relft: rels,
		resultq: rqid				
	};
	$j("li", this.rtt).each(function(){
		var $th=$j(this);
		pres.rows.push({
			id: $th.attr("data-hid"),
			field:'wform_'+$th.attr("data-form")+'.'+$th.attr("data-field"),
			type: $th.attr("data-type"),
			title: $th.text()
		});
	});
	$j("li", this.ctt).each(function(){
		var $th=$j(this);
		pres.cols.push({
			id: $th.attr("data-hid"),
			field:'wform_'+$th.attr("data-form")+'.'+$th.attr("data-field"),
			type: $th.attr("data-type"),
			title: $th.text()
		});
		if($j("div.bfg",$th).hasClass("headf-connect")){
			pres.cgroup.push($th.attr("data-hid"));
		}
	});
	return pres;
};
urlData = null;
sFrames.prototype.run = function(){
	var self=this;
	
	if((areas.col.length + areas.row.length) == 0 ){
		$j(".stab_let").attr("disabled",true);			
		return false;		
	}
	if (this.groupMS() == 'summ') {
		var groupsm = this.grouper();
		if (groupsm > 0) {
			if (groupsm == 1) {
				alert("Number of fields for grouping should be more than 1");
				return false;
			}
			var cnts = 0;
			for (var zs in this.groupSum.visual) {
				cnts++;
			}
			if (cnts == 0 || cnts != groupsm) {
				alert("You have to select values for grouped fields");
				return false;
			}
		}
	}
	var wrong=false;
	
	
	if (!wrong) {
		$j("#load_progress").show();
		$j("#launchbut").attr("disabled",true);
		grapher.reload();
		/* update 1 juin $j.ajax({
			type: "post",
			url: "/?m=outputs&a=calc&suppressHeaders=1",
			data: 'mode=btable&calcs=' + JSON.stringify(self.collector()),
			success: function(msg){
				if (msg.length > 0) {
					$j("#tthome").html(msg);
					msg=null;
					if (!$j("#tthome").find("table").hasClass("empty")) {
						$j("#stat_tab_holder").clone(true).addClass("stabh_vis").prependTo("#tthome").show();
						reporter.reget();
						$j(".stab_let").attr("disabled", false);
						grapher.init();
					}
				}
				$j("#launchbut").attr("disabled",false);
				$j("#load_progress").hide();
			}
		});*/
		rows_fld = [];
		$j('#rbox li').each(function(){
			fld = $(this).attr('data-field');
			fld_form = $(this).attr('data-form');
			rows_fld.push('wform_'+fld_form+'.'+fld);
		});
		cols_fld = [];
		$j('#cbox li').each(function(){
			fld = $(this).attr('data-field');
			fld_form = $(this).attr('data-form');
			cols_fld.push('wform_'+fld_form+'.'+fld);
		});
		querysave = $j('#querysave').val();
		data = {row:rows_fld,col:cols_fld,querysave:querysave};
		urlData = 'mode=btable&calcs=' + JSON.stringify(data)+'&calcs2=' + JSON.stringify(self.collector());
		console.log(urlData);
		$j.ajax({
			type: "post",
			data: urlData,
			url: "/?m=outputs&&suppressHeaders=1",
			success: function(msg){
				if (msg.length > 0) {
				    $j("#tthome").html(msg);
				    msg=null;
					if (!$j("#tthome").find("table").hasClass("empty")) {
						//$j("#stat_tab_holder").clone(true).addClass("stabh_vis").prependTo("#tthome").show();
						$stat_tab = $j("#stat_tab_holder").clone(true);
                        /*$stat_tab.append(
							'<table>\n' +
                            '            <tr><td><span id="pick_table" class="fa fa-table" style="color: #354c8c" title="Pick whole Statistic table"></span></td><td><span class="fa fa-file-text" style="color: #354c8c" onclick="popupDescStats()" title="Description"></span></td></tr>\n' +
                            '        </table>'
						);*/
                        $stat_tab.find("#pick_table").addClass("stabh_vis");
                        $stat_tab.prependTo("#tthome").show();
                        sommation();
						reporter.reget();
						$j(".stab_let").attr("disabled", false);
						grapher.init();
					}
				}
				$j("#launchbut").attr("disabled",false);
				$j("#load_progress").hide();
			}
			
		});
	}	
}

sFrames.prototype.doSave = function(pdata){
	var self = this;
	$j.ajax({
		type: 'post',
		url: '/?m=outputs&suppressHeaders=1&a=calc',
		data: ["setsd=", JSON.stringify(self.collector(true)), "&qrid=", rqid, "&", pdata].join(""),
		success: function(data){
			if (data.length > 0) {
				if (data != "fail") {
					//we saved query					
					if (parseInt(data) > 0) {
						$j("#slogo").hide();
						self.mode = 'save';
						self.dialogNote("Query saved");
						self.cid = 0;
						
						setTimeout(function(){
							$j("#dbox").dialog('close').remove();
						}, 1500);
						var t = {
							'data': data,
							'type': 'Stats'
						};
						self.saveQuery(t);						
					}
				}
			}
			$j("#slogo").hide();
			return false;
		}
	});
}
 
chartz.prototype.doSave = function(pdata){
	var self = this,legt='';
	if(rrr === 0 && rqid > 0){
		legt='&legacy='+rqid;
	}
	$j.ajax({
		type: 'post',
		url: '/?m=outputs&suppressHeaders=1&a=calc',
		data: ["setsd=", JSON.stringify(stater.collector()), "&qrid=", rqid, "&graph_data=", JSON.stringify(grapher.emulSend()),legt, "&", pdata].join(""),
		success: function(data){
			if (data.length > 0) {
				if (data != "fail") {
					//we saved query					
					if (parseInt(data) > 0) {
						$j("#slogo").hide();
						self.mode = 'save';
						self.dialogNote("Query saved");
						self.cid = 0;						
						setTimeout(function(){
							$j("#dbox").dialog('close').remove();
						}, 1500);
						var t = {
							'data': data,
							'type': 'Chart'
						};
						self.saveQuery(t);
					}
				}
			}
			$j("#slogo").hide();
			return false;
		}
	});
}
var buttons = Highcharts.getOptions().exporting.buttons.contextButton.menuItems;

/*buttons.push({
    text: "Add Graph To Dashboard",
    onclick: function(){
        //alert(urlData);
        //urlData = 'mode=btable&calcs=' + JSON.stringify(data)+'&calcs2=' + JSON.stringify(self.collector());
        //var titlegd = '';prompt("Graph title : ", "");
        gpgr.chooseSet(urlData, 'GRAPH', graph_data, project);
        $j.get("/?m=outputs&mode=getSet&suppressHeaders=1", function (msg) {
            if (msg && msg !== 'fail') {
                msg = $j.parseJSON(msg);
                $j('#chooseset').empty();
                $j('#chooseset').append('<option value=""></option>');
                $.each(msg, function(iii,e){
                    $j('#chooseset').append('<option value="'+e.id+'">'+e.setname+'</option>');
                });
            }
        });

    }
});*/
/*buttons.push({
    text: "Add Table To Dashboard",
    onclick: function(){
        //alert(urlData);
        //urlData = 'mode=btable&calcs=' + JSON.stringify(data)+'&calcs2=' + JSON.stringify(self.collector());
        //var titlegd = '';prompt("Graph title : ", "");
        var $statTable = $j("#tthome").find("table");
        $statTable = JSON.stringify('<table cellpadding="2" cellspacing="1" border="0" class="tbl sttable">'+$statTable.html()+'</table>');

        gpgr.chooseSet(urlData, 'TABLE', null, project);
        $j.get("/?m=outputs&mode=getSet&suppressHeaders=1", function (msg) {
            if (msg && msg !== 'fail') {
                msg = $j.parseJSON(msg);
                $j('#chooseset').empty();
                $j('#chooseset').append('<option value=""></option>');
                $.each(msg, function(iii,e){
                    $j('#chooseset').append('<option value="'+e.id+'">'+e.setname+'</option>');
                });
            }
        });

    }
});*/
function addTableToDashboard(){
    var $statTable = $j("#tthome").find("table");
    $statTable = JSON.stringify('<table cellpadding="2" cellspacing="1" border="0" class="tbl sttable">'+$statTable.html()+'</table>');

    gpgr.chooseSet(urlData, 'TABLE', null, project);
    $j.get("/?m=outputs&mode=getSet&suppressHeaders=1", function (msg) {
        if (msg && msg !== 'fail') {
            msg = $j.parseJSON(msg);
            $j('#chooseset').empty();
            $j('#chooseset').append('<option value=""></option>');
            $.each(msg, function(iii,e){
                $j('#chooseset').append('<option value="'+e.id+'">'+e.setname+'</option>');
            });
        }
    });

}
function addGraphToDashboard(){
    gpgr.chooseSet(urlData, 'GRAPH', graph_data, project);
    $j.get("/?m=outputs&mode=getSet&suppressHeaders=1", function (msg) {
        if (msg && msg !== 'fail') {
            msg = $j.parseJSON(msg);
            $j('#chooseset').empty();
            $j('#chooseset').append('<option value=""></option>');
            $.each(msg, function(iii,e){
                $j('#chooseset').append('<option value="'+e.id+'">'+e.setname+'</option>');
            });
        }
    });

}
var grapher = (function(my){
	var dataset = [], cols = [], rows = [], $table, boxes, rowb = [], colb = [], vstate = false, palettes=[], currentPalette=0,pgData,colorLock=false;
	var dataSend = function(){
		var pboxes = outData(), colsInPart, ndataset = [], xepos = 0, ncols = [[]], ncolb, nrows = [], nrowb;
		if (colb.length > 0 && $j("#col_big").val() === 'xcall') {
			//perform aggregation of columns into parent
			for (var pi = 0, pl = colb.length; pi < pl; pi++) {
				colsInPart = cols[0][colb[pi][0]][0];
				ncols[0].push([1, cols[0][colb[pi][0]][1]])
				if (colsInPart > 0) {
					for (var xe = 0; xe < colsInPart; xe++) {
						for (var y = 0, yl = dataset.length; y < yl; y++) {
							if (!ndataset[y] && !isArray(ndataset[y])) {
								ndataset[y] = [];
							}
							if (!ndataset[y][pi]) {
								ndataset[y][pi] = 0;
							}
							ndataset[y][pi] += parseInt(dataset[y][(xepos + xe)]);
						}
					}
					xepos += xe;
				}
			}
			ncolb = [];
		}
		else {
			ndataset = dataset.slice(0);
			ncols = cols.slice(0);
			ncolb = colb.slice(0);
		}
		if (rowb.length > 0 && $j("#row_big").val() === 'ycall') {
            console.log("here");
			//perform aggregation of rowss into parent
			var rdataset=[],rowOffset=0;
			for (var pi = 0, pl = rowb.length; pi < pl; pi++) {
				rowsInPart = rows[rowb[pi][0]][0];
				nrows.push([1, rows[rowb[pi][0]][1]]);
				rdataset[pi]=[];
				if (rowsInPart > 0) {
					for (var xe = (0 + rowOffset); xe < (rowsInPart + rowOffset); xe++) {
						for (var y = 0, yl = ndataset[xe].length; y < yl; y++) {
							/*if (!ndataset[y] && !isArray(ndataset[y])) {
		 							ndataset[y] = [];
		 					}*/
							if (rdataset[pi][y] === undefined) {
								rdataset[pi][y] = 0;
							}
							if (isArray(ndataset[xe])) {
								rdataset[pi][y] += parseInt(ndataset[xe][y]);
							}
							else {
								if (!rdataset[pi]) {
									rdataset[pi] = 0;
								}
								rdataset[pi] += parseInt(ndataset[xe][y]);
							}
						}
					}
					rowOffset=rowsInPart;
					xepos += xe;
				}
			}
			nrowb = [];
			ndataset=rdataset.slice(0);
			rdataset = undefined;
		}
		else {
			//ndataset = ndataset.slice(0);
			nrows = rows.slice(0);
			nrowb = rowb.slice(0);
		}
		
		return JSON.stringify({
			"data": ndataset,
			'cols': ncols,
			'rows': nrows,
			'boxes': pboxes,
			'rowb': rowb,
			'colb': colb,
			'row_use': $j("#row_big").val(),
			'col_use': $j("#col_big").val()
		});
	};
	
	var outData = function(){
		boxes = stater.collector(); 
		return {
			'rows': boxes.rows,
			'cols': boxes.cols
		};
	}
	
	var collect = function(){
		cols = [];
		rows = [];
		rowb = [];
		colb = [];
		dataset = [];
		var $table = $j("#tthome").find("table"), $thead = $table.find("thead"), clp = 0, clpx = 0, ocols = 0, tcols = 0, colall = $j("#colall").is(":checked");
		if (colall === false) {
			$j("tr", $thead).each(function(i){
				cols[i] = [];
				$j("th[data-ptitle]", this).filter(":not(.missgr)").each(function(){
					clp = $j(this).attr("data-ptitle");
					tcols = $j(this).attr("colspan");
					ocols = $j(this).attr("data-ocols");
					if (tcols > 1) {
						tcols = ocols;
					}
					clpx = cols[i].push([tcols, clp]);
					if (i == 0) {
						colb.push([(clpx - 1), clp]);
					}
				});
			});
		} else {
			cols.push([1, 'All']);
		}
		console.log("Cols:");
		console.log(cols);
		var td, rsp = 0, $nobj, tdtxt, rpos, use_next = true, needcells = boxes.rows.length, rspleft = 0, noclass, vdc = new RegExp("vdata"), sudc = new RegExp("summr"), tct = new RegExp("tcol"), pcell = new RegExp("perc"), migro = new RegExp("missgr"), tcl;
		$j("tbody > tr ", $table).filter(":not(.jkdata)").each(function(y){
			if (!$j(this).hasClass("itog")) {
				$j("td", this).each(function(yd){
					if (use_next === true) {
						tcl = $j(this).attr("class");
						var vcs = vdc.test(tcl), scs = sudc.test(tcl), tcs = tct.test(tcl), pcs = pcell.test(tcl), mit = migro.test(tcl);
						rsp = $j(this).attr("rowspan");
						if (!vcs && !scs && !tcs && !pcs && !mit) {
							tdtxt = $j(this).attr("data-rtitle");
							if (!tdtxt || tdtxt.length == 0) {
								tdtxt = $j(this).text();
								//crsp--;
							}
							/*else{
							 //crsp=rsp;
							 }*/
							rpos = rows.push([rsp, tdtxt]);
							if (yd == 0 /*&& crsp == rsp*/) {
							
								//crsp--;
								$nobj = $j(this).next();
								noclass = $nobj.attr("class");
								if (!vdc.test(noclass) && !sudc.test(noclass) && !pcell.test(noclass)) {
									rowb.push([(rpos - 1), tdtxt]);
									rows.push([1, $nobj.text()]);
									use_next = false;
								}
							}
						}
						else 
							if (((!colall && !scs) || (colall === true && scs === true && !vcs)) && !tcs && !pcs && use_next === true) {
								if (!isArray(dataset[y])) {
									dataset[y] = [];
								}
								td = $j(this).text();
								if (trim(td).length === 0) {
									td = 0;
								}
								dataset[y].push(td);
							}
						
					}
					else {
						use_next = true;
					}
				});
			}
		});
		if (dataset.length == 0 && $j("tbody > tr", $table).length == 1) {
			$j("tbody > tr", $table).find("td:not(.summr):not(.rowhead)").each(function(zx){
				if (!isArray(dataset[0])) {
					dataset[0] = [];
				}
				dataset[0].push($j(this).text());
			});
		}
		
		if (rowb.length > 0) {
			$j(".rsbox").remove();
			var $rcase = $j("<div class='rsbox'><div class='btext'>Rows</div></div>");
			var $rsel = $j("<select name='row_big' id='row_big' class='text bselld'></select>");
			$j("<option value='ycall'>All</option>").appendTo($rsel);
			for (var i = 0, j = rowb.length; i < j; i++) {
				$j(["<option value='", rowb[i][0], "'>", rowb[i][1], "</option>"].join("")).appendTo($rsel);
			}
			$rsel.find("option:first").attr("selected", true).end().appendTo($rcase);
			$rcase.insertBefore("#chart_pref > span");
		}
		
		if (colb.length > 0) {
			$j(".csbox").remove();
			var $ccase = $j("<div class='csbox'><div class='btext'>Columns</div></div>");
			var $csel = $j("<select name='col_big' id='col_big' class='text bselld'></select>");
			$j("<option value='xcall'>All</option>").appendTo($csel);
			for (var i = 0, j = colb.length; i < j; i++) {
				$j(["<option value='", colb[i][0], "'>", colb[i][1], "</option>"].join("")).appendTo($csel);
			}
			$csel.find("option:first").attr("selected", true).end().appendTo($ccase);
			$ccase.insertBefore("#chart_pref > span");
		}
		$j(".bselld").change(function(){
			grapher.pieOpts();
		});
	};
	
	var extractRows = function(){
		if (rowb.length > 0) {
			var brow = $j("#row_big").val(), zrows = [], dinclude = 0;
			$j(rows).each(function(z){
				if (dinclude > 0) {
					zrows.push(this);
					dinclude--;
				}
				if (z == brow) {
					dinclude = this[0];
				}
			});
		}
		else {
			zrows = rows;
		}
		return zrows;
	};
	
	var zinit = function(){
		dataset = [];
		outData();
		$j(".csbox").remove();
		$j(".rsbox").remove();
		collect();
		if ((boxes.rows.length <= 2 && boxes.cols.length <= 2) && (boxes.rows.length > 0 || boxes.cols.length > 0)) {
			$j("#gr_but").show();
		}
		else {
			$j("#gr_but").hide();
			$j("#chart_pref").hide("slow");
			vstate = false;
		}
	};

	var bars = function(categoriesv,seriesv,title){

		chartOption = {
			chart: {
				type: 'bar'
			},
			title: {
				text: title
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories: categoriesv,
				title: {
					text: null
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Population',
					align: 'high'
				},
				labels: {
					overflow: 'justify'
				}
			},
			plotOptions: {
				bar: {
					dataLabels: {
						enabled: true,
						format: '{y} %',
						valueDecimals: 2
					}
				}
			},
			legend: {
				/*layout: 'vertical',
				 align: 'right',
				 verticalAlign: 'top',
				 x: -40,
				 y: 80,
				 floating: true,
				 borderWidth: 1,
				 backgroundColor: ((Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'),
				 shadow: true*/
				/*reversed: true*/
			},
			credits: {
				enabled: false
			},
			series: seriesv
		};
		if($j("#sperc-rows").is(":checked") || $j("#sperc-cols").is(":checked")) {
			chartOption['tooltip'] = {
				pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y:.2f}%</b><br/>',
					shared: true
			}
		}
		return /*$j('#graph_home').*/Highcharts.chart('graph_home', chartOption);
	};

    var sbars = function(categoriesv,seriesv,title){
		chartOption = {
			chart: {
				type: 'bar'
			},
			title: {
				text: title
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories: categoriesv,
				title: {
					text: null
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Population (millions)',
					align: 'high'
				},
				labels: {
					overflow: 'justify'
				}
			},
			tooltip: {
				valueSuffix: ' millions'
			},
			plotOptions: {
				series: {
					stacking: 'normal'
				}
			},
			legend: {
				reversed: true
			},
			credits: {
				enabled: false
			},
			series: seriesv
		};
        return /*$j('#graph_home').*/Highcharts.chart('graph_home', chartOption);
    };

    var pbars = function(categoriesv,seriesv,title){
		chartOption = {
			chart: {
				type: 'bar'
			},
			title: {
				text: title
			},
			subtitle: {
				text: ''
			},
			xAxis: {
				categories: categoriesv,
				title: {
					text: null
				}
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Population (millions)',
					align: 'high'
				},
				labels: {
					overflow: 'justify'
				}
			},
			tooltip: {
				pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> ({point.percentage:.2f}%)<br/>',
				shared: true
			},
			plotOptions: {
				series: {
					stacking: 'percent'
				}
			},
			legend: {
				reversed: true
			},
			credits: {
				enabled: false
			},
			series: seriesv
		};

		if($j("#sperc-rows").is(":checked") || $j("#sperc-cols").is(":checked")) {
			chartOption['tooltip'] = {
				pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y:.2f}%</b><br/>',
				shared: true
			}
		}
        return /*$j('#graph_home').*/Highcharts.chart('graph_home', chartOption);
    };

    var columns = function(seriesv,title) {
    	chartOption = {
			chart: {
				type: 'column',
				options3d: {
					enabled: true,
					alpha: 15,
					beta: 15,
					depth: 50,
					viewDistance: 25
				}
			},
			title: {
				text: title
			},
			xAxis: {
				type: 'category'
			},
			yAxis: {
				title: {
					text: ''
				}

			},
			legend: {
				enabled: false
			},
			plotOptions: {
				column: {
					depth: 25
				},
				series: {
					borderWidth: 0,
					dataLabels: {
						enabled: true,
						format: '{point.y}'
					}
				}
			},
			tooltip: {
				/*headerFormat: '<span style="font-size:11px">{series.name}</span><br>',*/
				pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y}</b><br/>'
			},
			series: [{
				name: 'Brands',
				colorByPoint: true,
				data: seriesv
			}]
		};
        return /*$j('#graph_home').*/Highcharts.chart('graph_home', chartOption);
    };

    var lines = function(categoriesv,seriesv,title){
        return /*$j('#graph_home').*/Highcharts.chart('graph_home', {
            title: {
                text: title
            },
            subtitle: {
                text: ''
            },
            xAxis: {
                categories: categoriesv,
                title: {
                    text: null
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Population (millions)',
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                },
				plotLines: [{
                	value: 0,
					width: 1
				}]
            },
			plotoptions: {
            	line: {
            		dataLabels: {
            			enabled: true
					},
					enableMousetracking: false
				}
			},
            legend: {
                layout: 'vertical',
				align: 'right',
				verticalAlign: 'middle',
				borderWidth: 0
            },
            credits: {
                enabled: false
            },
            series: seriesv
        });
    };

    var line = function(categoriesv,seriesv,title){
		chartOption = {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {
				text: title
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false
					},
					showInLegend: true
				}
			},
			series: seriesv
		};
        return /*$j('#graph_home').*/Highcharts.chart('graph_home', chartOption);
    };

    var pie = function(seriesv,title){
		chartOption = {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {
				text: title
			},
			tooltip: {
				pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: 'pointer',
					dataLabels: {
						enabled: false
					},
					showInLegend: true
				}
			},
			series: seriesv
		}
        return /*$j('#graph_home').*/Highcharts.chart('graph_home', chartOption);
    };

	function prepareForSend(plain){
		var adds = '';
		if ($j("#pieChoke").val()) {
			var adds = {
				urow: +$j("#pieChoke").val(),
				uvrow: $j("#pieChoke").find("option:selected").text()
			}
		}
		
		var tbpost = dataSend(), ctype = $j("#chart_type").val(),
			dataStr = {
				cmode: ctype,
				dset: plain ? $j.parseJSON(tbpost) : tbpost,
				urow: adds,
				palette: plain ? $j.parseJSON(palettes[currentPalette]) :  palettes[currentPalette]
			}; //"&urow=",$j("#pieChoke").val()
		return [tbpost, dataStr];
	}
	
	return {
		init: function(){
			zinit();
		},
		start: function(){
			if (!vstate) {
				$j("#chart_pref").show(300);
			}
			else {
				$j("#chart_pref").hide(300);
			}
			vstate = !vstate;
		},
		reload: function(){
			$j("#pieChoke").remove();
			$j("#chart_pref").find("select:first > option:first").attr("selected", true).end().hide();
			vstate = false;
			zinit();
		},
		emulSend: function(inplain){
			var r = prepareForSend(inplain);
			return r[1];
		},
		build: function(same){
			/*collect();*/
            var title = prompt("Graph title : ", "");
			var tbpostd;
			if (!same) {
				 tbpostd = prepareForSend();
				 pgData=tbpostd;
				 palettes=[];
				 currentPalette=0;
			}
			if( palettes[currentPalette]){
				pgData[1].palette=palettes[currentPalette];
			}else{
				delete pgData[1].palette;
			}
			
			if (pgData[0].length > 0) {
				$j("#chart_pref").find(":button").attr("disabled", true).end().find(".chrt_load").show();
				//$j("#graph_home").html(pgData[1]);
				var rstr = [],$ghome=$j("#graph_home");
				masterdata = $j.parseJSON(pgData[1]['dset']);
				console.log(masterdata);
				var options = null;
				var data = null;
				type = pgData[1]['cmode'];
				graph_data = {};
				if(pgData[1]['cmode']=='bars' || pgData[1]['cmode']=='pbars' || pgData[1]['cmode']=='sbars' ){
					categories = [];
                    if($j('#col_big:visible').length > 0){
                    	console.log("col_big:visible");
                        if($j('#col_big').val()=='xcall'){
                            for (idx = 0; idx < masterdata.rows.length; idx++) {
                                categories.push(masterdata.rows[idx][1]);
                            }
                        }
                    }else {
                    	console.log("No...");
                        for (idx = 0; idx < masterdata.rows.length; idx++) {
                            categories.push(masterdata.rows[idx][1]);
                        }
                    }
                    console.log("Categories");
                    console.log(categories);
					categories.reverse();
					series = [];
					console.log(masterdata.cols[1].length);
					if(masterdata.cols[1].length>0){
                        for (idx = 0; idx < masterdata.cols[1].length; idx++) {
                            itemseries = [];
                            for(idy=0;idy<masterdata.data.length;idy++){
                                if($j("#sperc-rows").is(":checked") || $j("#sperc-cols").is(":checked")) {
                                    itemseries.push(parseFloat(masterdata.data[idy][idx]));
                                }else{
                                    itemseries.push(parseInt(masterdata.data[idy][idx]));
                                }
                            }
                            itemseries.reverse();
                            series.push({name:masterdata.cols[1][idx][1],data:itemseries});
                        }
					}else{
                        itemseries = [];
                        for(idy=0;idy<categories.length;idy++){
                            if($j("#sperc-rows").is(":checked") || $j("#sperc-cols").is(":checked")) {
                                itemseries.push(parseFloat(masterdata.data[idy][idx]));
                            }else{
                                series.push({name:categories[idy],y:parseInt(masterdata.data[idy][0])});
                            }
                        }
                        categories=null;
					}


					console.log("Series");
					console.log(series)
                    series.reverse();
                    //chart = bars(categories,series,title);
                    if(pgData[1]['cmode']=='bars') {
                    	if(categories!=null)
                        	chart = bars(categories, series, title);
                    	else chart = columns(series, title);
                    }

                    if(pgData[1]['cmode']=='sbars')
                        chart = sbars(categories,series,title);

                    if(pgData[1]['cmode']=='pbars') {
                        chart = pbars(categories, series, title);
                    }
					graph_data['categories'] = categories;
					graph_data['series'] = series;
					graph_data['title'] = title;
                    graph_data['type'] = pgData[1]['cmode'];
				}else if(pgData[1]['cmode']=='lines'){
					var pieChoke = false;
					if($('#pieChoke').val() != "-1")
						pieChoke = true;



                    categories = [];
					series = [];
                    if(pieChoke){
						for(idx=0;idx<masterdata.rows[1].length;idx++){
							categories.push(masterdata.cols[1][idx][1]);
						}
                        categories.reverse();
                        tempseries = {name: 'Brands',data:[]};
                        for(idx=0;idx<masterdata.data.length;idx++) {
                            if (parseInt($('#pieChoke').val()) == idx) {
                                for(idy=0;idy<masterdata.data[idx].length;idy++) {
                                    tempseries['data'].push({name: masterdata.cols[1][idx][1],y: parseFloat(masterdata.data[idx][idy])});
                                }
                                tempseries['data'].reverse();
                            }
                        }
                        series.push(tempseries);
                    }else{
                        for (idx = 0; idx < masterdata.rows.length; idx++) {
                            categories.push(masterdata.rows[idx][1]);
                        }
                        categories.reverse();
                        for (idx = 0; idx < masterdata.cols[1].length; idx++) {
                            itemseries = [];
                            for(idy=0;idy<masterdata.data.length;idy++){
                                if($j("#sperc-rows").is(":checked") || $j("#sperc-cols").is(":checked")) {
                                    itemseries.push(parseFloat(masterdata.data[idy][idx]));
                                }else{
                                    itemseries.push(parseInt(masterdata.data[idy][idx]));
                                }
                            }
                            itemseries.reverse();
                            series.push({name:masterdata.cols[1][idx][1],data:itemseries});
                        }
                    }
					console.log(series);
					/*console.log(categories);*/
                    if(pieChoke){
                        chart = lines(categories,series,title);//line(series);
					}else{
                        chart = lines(categories,series,title);
					}
                    graph_data['categories'] = categories;
                    graph_data['series'] = series;
                    graph_data['title'] = title;
                    graph_data['type'] = pgData[1]['cmode'];
				}else if(pgData[1]['cmode']=='pie'){
					pieChoke = true;
					/*categories = [];
					for(idx=0;idx<masterdata.rows[1].length;idx++){
						categories.push(masterdata.cols[1][idx][1]);
					}*/
					series = [];
                    tempseries = {name: 'Brands',data:[]};
				    for(idx=0;idx<masterdata.data.length;idx++) {
						if (parseInt($('#pieChoke').val()) == idx) {
							for(idy=0;idy<masterdata.data[idx].length;idy++) {
								tempseries['data'].push({name: masterdata.cols[1][idy][1],y: parseFloat(masterdata.data[idx][idy])});
							}
                            tempseries['data'].reverse();
						}
				    }
                   // tempseries.reverse();
                    series.push(tempseries);

                    chart = pie(series,title);
                    graph_data['categories'] = categories;
                    graph_data['series'] = null;
                    graph_data['title'] = title;
                    /*series.reverse();

                    if(pieChoke){
                        lines(categories,series);
                    }else{
                        line(series);
                    }*/

                    graph_data['categories'] = null;
                    graph_data['series'] = series;
                    graph_data['title'] = title;
                    graph_data['type'] = pgData[1]['cmode'];

				}else if(pgData[1]['cmode']=='geoChart'){
					pieChoke = true;
					categories = [];
					for(idx=0;idx<masterdata.cols[1].length;idx++){
						categories.push(masterdata.cols[1][idx][1]);
					}
					series = [];
					for(idx=0;idx<masterdata.data.length;idx++){
						itemseries = [];
						for(idy=0;idy<categories.length;idy++){
							itemseries.push(parseInt(masterdata.data[idx][idy]));
						}
						series.push({name:masterdata.rows[idx][1],data:itemseries});
					}

					listall = [];
					listall.push(['City','Values']);
					for(idx=0;idx<categories.length;idx++) {
						part = [];
						part.push(categories[idx]);
						for(idy=0;idy<series.length;idy++){
							if(pieChoke) {
								if (parseInt($('#pieChoke').val()) == idy) {
									part.push(series[idy].data[idx]);
								}
							}
						}
						listall.push(part);
					}
					var options = {
						region: 'HT',
						dataMode: 'markers'
					};
					//options['region'] = 'HT';
					//options['colors'] = [0xFF8747, 0xFFB581, 0xc06000]; //orange colors
					//options['dataMode'] = 'markers';
					//options['showLegend'] = true;

					data = google.visualization.arrayToDataTable(listall);
					chart = new google.visualization.GeoMap(document.getElementById('graph_home'));
				}


				//chart.draw(data, options);
				parts = null;
                $j("#graph_tab_holder").clone(true).addClass("stabh_vis").prependTo("#graph_home").show();
				reporter.initGraph();
				$ghome.find(".bottom-buts")
					.hover(function(){
						$j(this).find("div").fadeTo("fast", 1);
					}, function(){
						$j(this).find("div").fadeTo("fast", 0.1);
					});

				if (currentPalette === 0) {
					$j("#prev_color").css("visibility",'collapse');
				}else{
					$j("#prev_color").css("visibility",'visible');
				}
				$j("#chart_pref").find(":button").attr("disabled", false).end().find(".chrt_load").hide();
				colorLock = false;



				/*$j.ajax({
					url: '?m=outputs&a=graph&suppressHeaders=1',
					type: 'post',
					data: pgData[1],
					success: function(msg){
						if (msg != 'fail' && trim(msg) != '' && msg.length > 10) {
							var parts = msg.split("\\c"), rstr = [],$ghome=$j("#graph_home");
							msg=null;
							if (!palettes[currentPalette]) {
								palettes[currentPalette] = parts[0];
							}							
							$j("#graph_home").html(["<img src='data:image/png;base64," , parts[1] , "' class='grer' data-rep_item='graph'><br><div class='bottom-buts'><div id='prev_color' class='buthi' title='Back' onclick='grapher.color(\"prev\");'></div><div id='fwd_color' class='buthi' title='Forward' onclick='grapher.color(\"next\");'></div></div><input type='button' value='Save Chart Query' onclick='chface.saveDialog()' style='float:left;' class='text'>"].join(""));
							parts = null;							
							reporter.initGraph();							
							$ghome.find(".bottom-buts")							
							.hover(function(){
								$j(this).find("div").fadeTo("fast", 1);
							}, function(){
								$j(this).find("div").fadeTo("fast", 0.1);
							});
							
							if (currentPalette === 0) {								
								$j("#prev_color").css("visibility",'collapse');
							}else{
								$j("#prev_color").css("visibility",'visible');
							}
							//$j(".bottom-buts",$ghome).width($j(".grer").width());
						}
						else {
							$j("#graph_home").empty();
							alert("Chart drawing failed");
						}
						$j("#chart_pref").find(":button").attr("disabled", false).end().find(".chrt_load").hide();					
						colorLock = false;
					}
				});*/
			}
		},
		
		color: function(mode){
			if (colorLock === true) {
				return false;
			}
			if (mode === 'prev' && currentPalette > 0) {
				if (currentPalette === 1) {
					$j("#prev_color").css("visibility",'collapse');
				}
				--currentPalette;
			}
			else 
				if (mode === 'next') {
					++currentPalette;
				}
			colorLock = true;
			if(currentPalette > 0){
				$j("#prev_color").css("visibility",'visible');
			}
			grapher.build(true);
		},
		pieOpts: function(){
			var cval = $j("#chart_type").val();
			if (cval === "pie" || cval === 'lines' || cval === 'geoChart') {
				var useRows = extractRows();
				$j("#pieChoke").remove();
				//collect();
				if (useRows.length > 0) {
					var $selb1 = $j("<select></select>");
					$selb1.attr({
						"name": 'pieRow',
						"id": "pieChoke"
					}).addClass("text");
					if (cval === 'lines') {
						$j("<option value='-1'>All</option>").appendTo($selb1);
					}
					for (var i = 0, j = useRows.length; i < j; i++) {
						$j(["<option value='", i, "'>", useRows[i][1], "</option>"].join("")).appendTo($selb1);
					}					
					$selb1.insertBefore($j("#chart_pref > span "));
				}
			}
			else {
				$j("#pieChoke").remove();
			}
		},
		hideOpts: function(){
			vstate = false;
			$j("#chart_pref").hide("fast");
		},
		inject: function(arr){
			var found = false;
			grapher.start();
			$j("#chart_type").find("option").each(function(){
				if ($j(this).attr("value") === arr.mode) {
					$j(this).attr("selected", true);
				}
			});
			if (arr.col_use !== false) {
				$j("#col_big").find("option").each(function(){
					if ($j(this).text() === arr.col_use) {
						$j(this).attr("selected", true);
						found = true;
					}
				});
				if (found === false) {
					$j("#col_big").find("option:eq(0)").attr("selected", true);
				}
			}
			found = false;
			if (arr.row_use !== false) {
				$j("#row_big").find("option").each(function(){
					if ($j(this).text() === arr.row_use) {
						$j(this).attr("selected", true);
						found = true;
					}
				});
				if (found === false) {
					$j("#row_big").find("option:eq(0)").attr("selected", true);
				}
			}
			if(arr.palette){
				palettes[0]=arr.palette;
			}
			found = false;
			grapher.pieOpts();
			/*
	 		* place for various types of charts
	 		*/
			if (arr.mode === 'lines' || arr.mode === 'pie') {
				if (arr.pie_row !== false) {
					$j("#pieChoke").find("option").each(function(){
						if ($j(this).text() == arr.pie_row) {
							$j(this).attr("selected");
							found = true;
						}
					});
					if (found === false) {
						$j("#pieChoke").find("option:eq(0)").attr("selected", true);
					}
				}
			}
			grapher.build();
		}
		
	}
}(grapher));

var chface = new chartz;

if($j.browser.webkit === true){
	document.onselectstart = function () { return false; };
}




// Modification Sommation Table

function isInArray(value, array) {
    return array.indexOf(value) > -1;
}

Array.prototype.unique = function() {
    var a = this.concat();
    for(var i=0; i<a.length; ++i) {
        for(var j=i+1; j<a.length; ++j) {
            if(a[i] === a[j])
                a.splice(j--, 1);
        }
    }

    return a;
};

function sommation(){
    var premier_occ = $("#tthome table thead tr:eq(1) th:eq(1)").text();
    var tab_tete = [], slected = [];
    var equality = [];
    var in_ndex = -1;
    $("#tthome table thead tr:eq(1) th").each(function(){
        // alert($(this).text());
        // var atrib = $(this).attr("rowspan");
        if($(this).index()>=1){
            if($(this).text()==='Grand Total'){
                if($(this).index()){
                    in_ndex = 0;
                }
            }
            if(in_ndex != 0){
                tab_tete.push({nom: $(this).text(), data_oid: $(this).attr('data-oid'), data_ocols: $(this).attr('data-ocols')});
            }

        }
    });
    /*for(var i =0; i < tab_tete.length;i++){

        console.log(tab_tete[i]);
    }*/


    // Finding Duplicates
    for(var i = 0;i<tab_tete.length;i++){
        var lePush = [];
        for(var j = i+1; j < tab_tete.length;j++){
            if(tab_tete[i].nom == tab_tete[j].nom){
                lePush.push(j);
                slected.push(j);
            }
        }
        if(!isInArray(i,slected)){
            lePush.push(i);
            slected.push(i);
            equality.push({
                "nom":tab_tete[i].nom,
                "id_commun":lePush,
                "data_oid":tab_tete[i].data_oid,
                "data_ocols":tab_tete[i].data_ocols,
            });
        }else{
            continue;
        }
    }

    $("#tthome table thead tr:eq(1)").each(function(){
        var part1_bkp = $(this).clone();
        var part2_bkp = $(this).clone();

        part1_bkp.children().each(function(){
            if($(this).index()>0){
                $(this).remove();
            }
        });

        var numm = 1+ tab_tete.length;
        part2_bkp.find("th:lt("+numm+")").remove();

        $(this).empty();
        $(this).append(part1_bkp.find("th"));

        for(var z = 0;z < equality.length;z++){
            $(this).append("<th data-ptitle='"+equality[z].nom+"' data-oid='"+equality[z].data_oid+"' data-ocols='"+equality[z].data_ocols+"'>"+equality[z].nom+"</th>");
        }

        $(this).append(part2_bkp.find("th"));
    });

    $("#tthome table tbody tr").each(function(){
        var part1_bkp = $(this).clone();
        var part2_bkp = $(this).clone();

        // Sauvegarde de la premiere TD dans chaque TR
        part1_bkp.children().each(function(){
            if($(this).index()>0){
                $(this).remove();
            }
        });
        // Sauvegarde de la premiere TD dans chaque TR

        // Sauvegarde des derniers TD dans chaque TR
        var numm = 1+ tab_tete.length;
        part2_bkp.find("td:lt("+numm+")").remove();
        // Sauvegarde des derniers TD dans chaque TR
        // console.log( equality.length);
        var somme = [] ;
        for(var z = 0;z < equality.length;z++){
            var somme_1 = 0;
            for(var m = 0;m < equality[z].id_commun.length; m++){
                var adel = equality[z].id_commun[m]+1;
                somme_1 += parseInt($(this).find("td:eq("+adel+")").text());
            }
            somme.push(somme_1);
        }

        $(this).empty();
        $(this).append(part1_bkp.find("td"));
        for(var add = 0;add < somme.length;add++){
            $(this).append("<td class='vdata'>"+somme[add]+"</td>");
        }
        $(this).append(part2_bkp.find("td"));
        // console.log($(this).html());
        // $(this).children().each(function(){
        //     if($(this).index() == 0){
        //         console.log($(this));
        //     }
        // });
    });
}