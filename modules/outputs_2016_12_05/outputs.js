var  obj, filter, fist = false, garr, fl = 0, pfl = 0, filar = [], fchange, fstat;
var today,fakes,btr,heads,lets,qsaved,selects,calif,calwined,areas={row:[],col:[],subj:[]},$gtabs,stater,st_do,fields=[],dstp;
var chex, rrr,st_upd=false,$smalltip=$j("#stip"),dmarker=false,tgt,rsip=false;
var aname='name', dw,aopen;
var be;if ($j.browser.msie) {be = "click";}else {be = "change";}
if($j.browser.msie){
	aname='submitName';
}
function datesoff(){
    $j("input.datepicker").val("");
    return false;
}  

var pager = function(){
    var self = this;
    this.currentPage = 0;
    this.numPerPage = 10;
    var $table, $tbody, $thead,spre = "headerSort", $fb, lind = 0,$slist,thar=[],thl=0;
    this.$div = $j("#pgbs");
    this.buts = [];this.numRows = 0;this.numPages = 0;this.allRows = [];this.cRows = [];this.filteredRows = [];this.lects = [];this.lectsv = [];this.lectsHTML = [];this.heads = [];this.sortMethods = [];this.curMethod = '';this.curKey = 0;this.curWay = 'desc';this.cleanSet = new RegExp("[\\s-]","g");	this.visible = [];this.head_active;this.sl_active;this.fillects;
	this.init = function(tid,mode){
        $table = $j("#" + tid).detach();
        $tbody = $j("tbody", $table);
        $thead = $j("thead", $table);
        $fb = $j("#filbox");
		$slist=$j("#fil_stats");
        self.numRows = $table.find('tbody tr').length;
        self.numPages = Math.ceil(self.numRows / self.numPerPage);
		var $span=$j("<span/>");
        $j("<div class='navs first_page' title='First'></div>").bind('click', {
            action: 'first'
        }, self.navgt).appendTo($span);
        $j("<div class='navs prev_page' title='Previous'></div>").bind('click', {
            action: 'prev'
        }, self.navgt).appendTo($span);
        $j("<div id='pinfor' style='float:left;'><span class='curp'>" + (self.currentPage + 1) + "</span> of <span class='totp'>" + (self.numPages) + "</span></div>")
		.appendTo($span);
        $j("<div class='navs next_page' title='Next'></div>").bind('click', {
            action: 'next'
        }, self.navgt).appendTo($span);
        $j("<div class='navs last_page' title='Last'></div>").bind('click', {
            action: 'last'
        }, self.navgt).appendTo($span);		
		$span.appendTo(self.$div);
        $table.bind('repaginate', function(){
			var st = 0, lowend = (self.currentPage * self.numPerPage), highend = ((self.currentPage + 1) * self.numPerPage - 1), hiter = false,
			$lbody=$tbody.detach();
			var trar=$j('tr', $lbody),trl=trar.length,ofc= new RegExp("offwall","gi"),oftd= new RegExp("offview","gi");
			//.each(function(i){			
			for(var i= 0; i < trl; i++){
				var tr=trar[i];
				if (hiter) {
					$j(tr).addClass("offview");
				}
				else {
					var cc = tr.className, tcl = cc.match(ofc), todo = cc.match(oftd);
					if (!tcl) {
						if (st >= lowend && st <= highend) {
							if (todo) {
								$j(tr).removeClass("offview");
							}
						}
						else 
							if (!todo) {
								$j(tr).addClass("offview");
							}
						st++;
						if (st > highend) {
							hiter = true;
						}
					}
				}				
			}
			tr=null;
			trar=null;
			trl=null;
			i=null;
			lowend=null;
			highend=null;
			self.arrows();
			$table.append($lbody);
			$lbody=null;
			fCleaner();
		});
        
        if (self.numPages > 1) {
            self.arrows();
        }
		thar=$j("tr:first > th", $thead);
		thl=thar.length;	
        self.collector(mode);
		//self.lector();
		$table
			.trigger('repaginate')        
			.attr("class", "rtable");		
        $j("#pagebox").show();
		$j("#cleanbox").show();		
		acols = [];	
		var colz=$j("colgroup > col", $table);
		var cl=colz.length;		
		for(var i=0;i < cl; i++){			
			$j(colz[i]).attr("data-thid", i);
			acols.push(colz[i]);		
		}
		colz=null;
		cl=null;
		$table.appendTo("#mholder");
		if (mode != 'out') {
			$j(".rowfdel", $tbody).live('mouseenter mouseleave', function(e){
				var hover = (e.type === 'mouseover'), $this = $j(this);
				if (hover) {
					$j(".delbutt", $this).show();
				}
				else {
					$j(".delbutt", $this).hide();
				}
			});
		}					
    }
	
    
    this.reorder = function(obj){
        var tt=$j(obj).val();
		if(tt == -1){
			self.numPerPage = self.numRows;
		}else{
			self.numPerPage = tt;
		}
        memo.toggle();
        this.updatePages();
        memo.toggle();
    }
    
    this.updatePages = function(){
        self.numPages = Math.ceil(self.numRows / self.numPerPage);
        $j("#pinfor").find("span.totp").text(self.numPages);
        self.navgt({
            data: {
                action: 'first'
            }
        });
    }
    
    this.getHeadDataStat = function(){
		var items=["col",'row'];
		var res={row:[],col:[]};
		for (var c = 0; c <  2; c++) {
			if (areas[items[c]].length > 0) {
				var ll = areas[items[c]].length;
				for (var i = 0; i < ll; i++) {
					res[items[c]].push ({
						type: self.colType(areas[items[c]][i]),
						title: $j("th:eq(" + areas[items[c]][i] + ")",$thead).text(),
						id: areas[items[c]][i]
					});
				}				
			}
		}
		return res;
	}
	
	this.patchCell = function(cell,y, x, dval,val,vval,cw){
		$j(cell).empty().text(vval).width(cw).data("wed",0);			
		$j("body").css('cursor','progress');	
		$j.ajax({
			type: 'get',
			url:  '/?m=outputs&suppressHeaders=1',
			data: 'mode=patch&x='+x+"&y="+y+"&val="+dval,
			success: function(data){
				if(data == 'ok'){
					$j(cell).addClass("ueds");
					self.allRows[y][x]=val;
					$j(self.allRows[y]['item']).find("td:eq("+x+")").text(vval).addClass("ueds");
				}else{
					alert("Changes not saved!");
				}
				$j("body").css('cursor','default');
			}
			
		});
		
	}
	
    this.collector = function(mode){
    	
        //var grows = $j("tr", $tbody);
		self.sortMethods=heads;
		self.allRows=btr;
		self.lects=lets;

		var trst=$j("tr",$tbody);
		var itd=trst.length,prow;
		while(itd--){
			prow=self.allRows[itd];self.allRows[itd]=null;
			prow['item']=$j(trst[itd]).clone();
			prow['fake']=fakes[itd];
			prow['hidden']=false;
			prow['uid']=itd;
			self.allRows[itd]=prow;
			self.visible.push(itd);
			prow=null;		
		}	

		if (mode != 'out') {
			$j(".delbutt",$tbody).live("click",function(){
				if (confirm("You want delete this entry?")) {
					var $row = $j(this).parent().parent();
					var rid = $row.attr('id').replace('row_', '');
					$j.ajax({
						type: "get",
						url: "?m=outputs&suppressHeaders=1&mode=rowkill",
						data: 'row=' + rid,
						success: function(data){
							if (data == 'ok') {
								$row.fadeOut('slow', function(){
									$row.remove();
									$row = null;
									self.updatePages();
									self.allRows[rid] = null;
									var vispos = $j.inArray(rid, self.visible);
									if (vispos >= 0) {
										self.visible.splice(vispos, 1);
									}
								});
							}
							
						}
					});
					
				}
			});
		
			$j(".vcell", $tbody).live("dblclick", function(ev){
				var rid = $j(this).parent().attr("id").replace("row_", ''),
				col = $j(this).prevAll().length,cell = this, selt = '', cst = $j(cell).data("wed");
				if (cst && cst == 1) {
					return false;
				}
				else {
					$j(cell).data("wed", 1);
				}
				var cw = $j(this).width();
				cw -= 20;
				var txt = $j(cell).text(), $ued = null, nval2, nval;
				if (self.colType(col) != 'date') {
					$j(this).empty();
					if (selects[col] == 'plain') {
						$j("<input type='text' value='" + txt + "' class='edbox' style='width:" + (cw) + "px;'>").bind('keypress', {
							obj: this
						}, function(e){
							var code = (e.keyCode ? e.keyCode : e.which);
							if (code == 13) {
								nval = $j(this).val();
								nval2 = nval;
								self.patchCell(cell, rid, col, nval, nval, nval2, cw);
							}
							else 
								if (code == 27) {
									$j(cell).empty().data("wed", 0).text(txt);
								}
						}).appendTo(cell);
					}
					else 
						if (isArray(selects[col])) {
							var $ued = $j("<select class='dred_sel' ></select>");//style='width:" + (cw) + "px;'
							var ll = selects[col].length;
							for (var i = 0; i < ll; i++) {
								if (txt == selects[col][i].v) {
									selt = "selected";
								}
								else {
									selt = '';
								}
								$j("<option value='" + selects[col][i].r + "' " + selt + ">" + selects[col][i].v + "</option>").appendTo($ued);
							}
							$ued.bind('change', function(e){
								var nv = $j(this).val();
								nval2 = $j(this).find("option[value=" + nv + "]").text();
								nval = nval2.toLowerCase();
								self.patchCell(cell, rid, col, nv, nval, nval2, cw);
							}).bind('keypress', function(e){
								var code = (e.keyCode ? e.keyCode : e.which);
								if (code == 13) {
									var nv = $j(this).val();
									nval2 = $j(this).find("option[value=" + nv + "]").text();
									nval = nval2.toLowerCase();
									self.patchCell(cell, rid, col, nv, nval, nval2, cw);
								}
								else 
									if (code == 27) {
										$j(cell).data("wed", 0).empty().text(txt);
									}
							}).appendTo(cell);
						}
				}
				else 
					if (self.colType(col) == 'date') {
						var odate = self.treatVal('date', txt);
						calif = randomString();
						$j("<input type='hidden' class='date_edit_" + calif + "'>").bind('refresh', function(w){
							nval = $j(".date_edit_" + calif).val();
							nval2 = nval.split("/").reverse().join("-");
							//nval2 = self.treatVal('date', nval);
							self.patchCell(cell, rid, col, nval2, nval, nval2, cw);
						}).appendTo(cell);
						$j(cell).data("wed", 0);
						popCalendarEd(calif, odate);
					}
				
			});
		}

		btr=null;
		fakes=null;	
		lets=null;
        grows=null;		
		heads=null;
        
        //$j("tr:first > th", $thead).each(function(hid){		
		$j(".forsize",$thead)
			.live("mouseenter",function(e){
						if (!rsip) {
							$j(this).bind("mousemove", monPosR);
							redv = this;
						}
					})
			.live("mouseleave",function(e){
				var utime;
				var cbu = $j(this).attr("data-thid");
				if (!rsip) 
					$j(this).unbind('mousemove', monPosR);
				redv = false;
			});
		for(var hid=0; hid < thl;hid++){
			var cthis=thar[hid];
            var txt = $j(cthis).text(),cw=$j(cthis).width();
			filar[hid]={methods:{},mvals:[],state:false};

			if (st_do <= 1) {
				fields.push({
					id: 1,//hid,
					title: "Text",//$j(cthis).text(),
					type: "string",self.colType(hid),
					parent: 'None'$j(cthis).attr("data-part")					
				});
			}else{
				$j("#shome").html("<span class='note'>Cannot build stat table from multiple forms</span>");
			}
			
            $j(cthis)
			.bind('mouseover', function(){
				var mp=$j(this).offset();
				var mw=$j(this).outerWidth();
                $j(".head_menu",this).addClass("head_menu_on").removeClass("head_menu_sort").css({
					left: ((mp.left+mw)-18),
					top: mp.top
				});
				$j(".hstat_menu",this).addClass("hstat_menu_on").removeClass("head_menu_sort").css({
					left: (mp.left+1),
					top: mp.top
				});
				$j(this).addClass("head_act");                
            })
			.bind("mouseout",{hdi:hid}, function(x){
				var me=this,uid=x.data.hdi,getout;	
				$j(this).removeClass("head_act")
				.find("div.head_menu").each(function(){
                    var cst = $j(this).data('cact');
                    if (!cst || cst == 0) {
                        $j(this).removeClass("head_menu_on");
						var pcl=self.heads[uid];
						if($j(this).hasClass(spre+pcl)){
							$j(this).addClass("head_menu_sort");
							getout=true;
						}
                    }
                    else {
                        $j(this).addClass('head_sel_act');
						getout=false;
                    }
                }).end()
				.find("div.hstat_menu").each(function(){
					$j(this).removeClass("hstat_menu_on");
					if(getout){
						$j(this).addClass("head_menu_sort");
					}
				})
            })
			.bind("click", {
                'head_id': hid
            }, function(xd){
				if(xd.target.className.match(/head_menu/g)){
					//return ;					
					//$j(xd.target).trigger("click");
					self.headMenuWork(xd);
					return;
				}
                var mp=$j(this).closest("th").data("resize"),heid=xd.data.head_id;
				if(mp === true){
					return false;
				}
				//add sorting in here
                if (!self.heads[heid]) {
                    self.heads[heid] = 'desc';
                }
                var oway = self.heads[heid], nway
                nway = self.oppoWay(oway);
                self.msort(heid, nway);
                $j(this).removeClass("head_act");
            })
			.data("ow",$j(cthis).width());
        }//);
		console.log(fields);
		$j("div.head_menu",thar).live("click",function(df){
				self.headMenuWork(df);
        });  //).appendTo(cthis);
    }
	
	this.headMenuWork = function(df){
		var $ard = $j(df.target), $hcell = $ard.parent(),
		heid = $hcell.attr("data-thid");//df.data.head_id;
		self.lector(heid);		                
		var meon = $ard.data("cact"), cbon, cben;
		if (!meon || meon == 0) {		
			for (var ix = 0; ix < thl; ix++) {
				var tdc = thar[ix];
				if (ix != heid) {
					$j(tdc).removeClass("head_sel_act").find("div.head_menu").removeClass("head_menu_on menu_stay").data("cact", false);
				}
			}
			filmter.hideAll();
			$slist.hide();
			self.head_active = $ard;
			$ard.data("cact", 1).addClass("menu_stay");
			var pp = $hcell.offset();
			$fb.show();
			var np = {
				x: (pp.left + $hcell.width() - $fb.width()),
				y: (pp.top + $ard.height() + 5)
			};
			if (filar[heid].state) {
				cbon = true;
			}
			else {
				cbon = false;
			}
			if (countMethods(heid) > 0) {
				cben = true;
			}
			else {
				cben = false;
			}
			$fb.css({
				left: np.x,
				top: np.y
			}).data("skey", heid).show();
			$j(document).bind("click", function(e){
				self.menuKiller(e);
			});
			$j("#fil_on", $fb).attr({
				"checked": cbon,
				"disabled": !cben
			});
		}
		else {
			self.closeMenu();
		}
		df.stopPropagation();
		return false;
	}
	
	this.showslist = function(hid){		
		var rowe=$j.inArray(hid,areas.row);
		var cole=$j.inArray(hid,areas.col);
		var mr={c:false,d:false},mc=mr;	
		var $po = $j("#head_"+hid);
		self.sl_active=$j(".hstat_menu",$po);
		self.sl_active.addClass("menu_stay");
		var pp=$po.offset();
        var np = {
                x: pp.left,
                y: (pp.top + 28)
        };	
		if(rowe >= 0){			
				mr = {
					c: true,
					d: false
				};
				mc = {
					c: false,
					d: true
				};			
		}else if(cole >= 0 ){			
				mr = {
					d: true,
					c: false
				};
				mc = {
					d: false,
					c: true
				};
		}
		$slist
			.find("input.col_check").attr({
				"disabled":mc.d,
				"checked":mc.c
			}).end()
			.find("input.row_check").attr({
				"disabled":mr.d,
				"checked":mr.c
			}).end()
			.css({left:np.x,top:np.y})
			.data("key",hid)
			.show();
		$j(document).bind("click",function(e){
			self.menuKiller(e);
		});
	}
		
	this.recrute = function(ev, obj){
		var hid=$slist.data("key");
		var state=$j(obj).is(":checked");
		var cl=obj.className;
		cl=cl.replace("_check",'');
		if(state){
			areas[cl].push(hid);			
		}else{
			var pos=$j.inArray(hid,areas[cl]);
			areas[cl].splice(pos,1);
		}
		sl_upd=true;
		self.showslist(hid);
	}
	
	this.cleaner = function (e){
		var obj=$j(e.target).parent();
		var hid=$j(obj).data("hid");
		var nname= $j(obj).closest("div.dgetter").attr("id");
		nname=nname.replace("box",'');
		if(nname=='r'){
			nname='row';
		}else if(nname=='c'){
			nname='col';
		}
		var pos=$j.inArray(hid,areas[nname]);
		areas[nname].splice(pos,1);
		
		$j(obj).closest("li").remove();
	}
	
	this.justHideMenu = function(){
		if($thead){
			$j("th > div",$thead).removeClass("head_menu_on hstat_menu_on menu_stay");
		}		
		$fb.hide();		
		$slist.hide();
	}
	
	this.closeMenu = function(){
		if (self.head_active) {
			self.head_active.data("cact", 0).removeClass("menu_stay head_menu_on").parent().data("skey", false).removeClass('head_sel_act');
		}
		if (self.sl_active) {
			self.sl_active.removeClass("menu_stay hstat_menu_on");
		}
		self.justHideMenu();
		filmter.hideAll();
		$j(document).unbind("click");
	}
	
	this.menuKiller = function(ev){
		var et=ev.currentTarget;
		var p1=$j(ev.target).closest("div.filter_box");		
		if (et != self.head_active && (!p1 || p1.length == 0)) {
			self.closeMenu();
		}
	}
    
    this.findlect = function(key, arr){
        if (arr.length == 0) {
            return false;
        }
        else {
            var ll = arr.length;
            for (var i = 0; i < ll; i++) {
                if (arr[i] && arr[i].r == key) {
                    return true;
                }
            }
        }
    }
    
    this.lectSort = function(a, b){
        var x = a.r, y = b.r;
        return x - y;
    }
    
	this.getLects = function(i){
		return this.lects[i];
	}
	
    this.lector = function(i){
        var ul, li, cb, sp;
		if (!self.lectsHTML[i]) {
			ul = $j("<ul class='tobs' id='outf'></ul>");
		}
		else {
		//ul = $j(self.lectsHTML[i]).clone(true);
			return false;
		}
        //self.lects[key].push(val);
        var ll = self.lects.length,frag=document.createDocumentFragment();
        li = $j("<li class='ffbc fil_line'></li>");
        cb = $j("<input type='checkbox'>");
        sp = $j("<span class='sline'></span>");
        //for (var i = 0; i < ll; i++) {
                       
            
            var tar = self.lects[i];
            //tar.sort(self.lectSort);
            var x=0;
            //for (var x = 0; x < tar.length; x++) {
			for(var tx in tar){
                var val = tar[tx].r;
                var vval = tar[tx].v;
                if (val || val==false) {
                    //var t = $j(cb).clone(true);
					var t=$j(cb).clone(true);
                    $j(t).bind(be, {
                        tid: val,
                        col: i
                    }, function(x){						
                        var st = $j(this).is(":checked"),tobj = this,cx=x.data.col;
                        if ($j("ul#outf").find("input:checked").length > 0) {                            
                            if (!st) {
                                fl++;
                            }
                            filmter.setColValues(cx, $j(tobj).attr("cact"), st);
                        }
                        else {                            
                            filar[cx].mvals = [];
                            filar[cx].state = false;                            
                        }
                        memo.toggle();
						self.fillects=true;
                        setTimeout(function(){
                            self.runFilters();
							//self.pickLects();
                            memo.toggle();                            
                        }, 20);
                    });
                    $j(t).attr({
						"cact": val,
						'cact_id': tx
						});
					var t1 = $j(li).clone(true);
                    var t2 = $j(sp).clone(true).text(vval);
					if(val == false){
						$j(t2).addClass("palebor");
					}
                    $j(t1).append(t).append(t2);
                    //$j(t2).appendTo(t1);
                    //$j(ul).append(t1);
					frag.appendChild(t1[0]);
                }
				x++;
            }
			$j(ul)[0].appendChild(frag);
            $j(ul).disableSelection();
            self.lectsHTML[i] = ul;//$j(ul).clone(true);            
			ul=null;        
			frag=null;
        //}
    }
    
	this.pickLects = function(i){
		
	}
	
    this.colType = function(key){
        var ct = self.sortMethods[key], rr;
        if (ct != 'string') {
            if (ct == 'date') {
                rr = 'date';
            }
            else {
                rr = 'number';
            }
        }
        else {
            rr = 'string';
        }
        return rr;
    }
    
    this.colVals = function(key){
        return $j(self.lectsHTML[key]).clone(true);
    }
    
    this.oppoWay = function(way){
        var nway;
        if (way == 'desc') {
            nway = 'asc';
        }
        else {
            nway = 'desc';
        }
        return nway;
    }
    
    this.cleanHeadSort = function(cur, nway){
        var ul = 0;
        if (self.heads.length == 0) {
            ul = cur + 1;
        }
        else {
            ul = self.heads.length;
        }
        for (var z = 0; z < ul; z++) {
            if (z != cur) {
                self.heads[z] = '';
                $j("#head_" + z).removeClass(spre + "asc" + ' '+ spre + "desc").next().removeClass("head_menu_sort");
            }
            else {
                self.heads[z] = nway;
            }
        }
    }
    
    this.ifsort = function(way){
        self.curKey = $j("#filbox").data("skey");
        self.msort(self.curKey, way);
        self.hideMenu();
        filmter.hideAll();
    }
    
    this.hideMenu = function(){
		if (!self.fillects) {
			$fb.hide();
			filmter.hideAll();
		}
		self.fillects=false;
        $j(".head_menu", $thead).each(function(inx){
            $j(this).removeClass("head_menu_on menu_stay").data("cact", false).prev().removeClass("head_sel_act");
        });
    }
    
    this.msort = function(key, way){
        memo.toggle();
        setTimeout(function(){
			var frag= document.createDocumentFragment();
            self.curMethod = self.sortMethods[key];
            self.curKey = key;
            self.curWay = way;
            self.cleanHeadSort(key, way);
            //self.allRows.sort(self.iterer);
            var tar = self.allRows.slice(0);
            tar.sort(self.iterer);
            var $lbody=$tbody.detach().empty();
            var tll = tar.length;
            var cv = 0;
			self.visible=[];
            for (var i = 0; i < tll; i++) {
                var $nr = $j(tar[i]['item']).clone(true);
				//var nr=tar[i]['item'];
                if (cv >= self.numPerPage) {
                    $nr.addClass('offview');
                }
                else {
                    $nr.removeClass('offview');
                    cv++;
                }
				if(tar[i].hidden){
					$nr.addClass('offwall');
				}else{
					self.visible.push(tar[i].uid);
				}
                $nr.prependTo($lbody);
				//$nr.prependTo($j(frag));
				/*if (nr && nr['context']) {
					frag.appendChild(nr['context']);
				}*/
                
            }
			$table.append($lbody);
            $j("#head_" + key).removeClass(spre + '' + self.oppoWay(way)).addClass(spre + way).find("div.head_menu",this).addClass("head_menu_sort");
			self.visible.reverse();
            self.navgt({
                data: {
                    action: 'first'
                }
            });
            memo.toggle();
        }, 50);
    }
    
    this.iterer = function(a, b){
        var x = a[self.curKey], y = b[self.curKey], r1, r2, r3;		
        if (isNaN(x) && self.curMethod != 'string') {
            x = 0;
        }
        if (isNaN(y) && self.curMethod != 'string') {
            y = 0;
        }
		if(x==false && y == false){
			return 0;
		}else if(x == false){
			return -1;
		}else if(y == false){
			return 1;
		}
        if (self.curWay == "desc") {
            r1 = 1;
            r2 = -1;
        }
        else {
            r1 = -1;
            r2 = 1;
        }
        r3 = ((x < y) ? r1 : ((x > y) ? r2 : 0));
        if (a['hidden'] && b['hidden']) {
            r3 = 0;
        }
        else 
            if (a['hidden']) {
                r3 = 1;
            }
            else 
                if (b['hidden']) {
                    r3 = -1;
                }
        return r3;
    }
    
    this.treatVal = function(way, val){
        if (way == 'int' || way == 'date') {
            if (val.length > 0) {
				val = parseInt(val.replace(self.cleanSet,''));
				
            }
            else {
                val = 0;
            }
            if (isNaN(val)) {
                val = 0;
            }
        }
        else 
            if (way == 'float') {
                val = parseFloat(val);
            }
            else 
                if (way == 'string') {
                    if (!val) {
                        val = '';
                    }
                    else {
                        val = trim(val.toLowerCase());
                    }
                }
        return val;
    }
    
    this.arrows = function(){
        if (self.numPages > 0) {
            if (self.currentPage > 0) {
                if ((self.currentPage - 1) >= 0) {
                    $j(".prev_page", self.$div).show();
                }
                else {
                    $j(".prev_page", self.$div).hide();
                }
                if (self.currentPage > 0) {
                    $j(".first_page", self.$div).show();
                }
                else {
                    $j(".first_page", self.$div).hide();
                }
            }
            else {
                $j(".prev_page", self.$div).hide();
                $j(".first_page", self.$div).hide();
            }
            if ((self.currentPage + 1) < self.numPages) {
                $j(".next_page", self.$div).show();
            }
            else {
                $j(".next_page", self.$div).hide();
            }
            if (self.currentPage < self.numPages - 1) {
                $j(".last_page", self.$div).show();
            }
            else {
                $j(".last_page", self.$div).hide();
            }
            
        }
    }
    
    this.navgt = function(met){
        switch (met.data.action) {
            case 'first':
                self.currentPage = 0;
                break;
            case 'last':
                self.currentPage = self.numPages - 1;
                break;
            case 'next':
                self.currentPage++;
                break;
            case 'prev':
                self.currentPage--;
                break;
            default:
                break;
                
        }
        self.hideMenu();
        $j("#pinfor").find("span.curp").text(self.currentPage + 1);
        $table.trigger('repaginate');
    }
	
	this.getVisibles = function(){
		return this.visible;
	}
    
	this.saveTable = function(){
		
		var fname=prompt("Please enter name for table file");
		if(fname === null){
			return false;
		}		
		while(!fname || fname.length == 0 || trim(fname) == ''){
			fname=prompt("Please enter valid name for table file!");
			if(fname === null){
				return false;
			}
		}		
		$j("#stabbox").val(JSON.stringify(self.getVisibles()));
		document.saveme.fname.value=fname;
		document.saveme.submit();
	}
	
	this.startss = function(){
		if (rrr > 0) {
			gpgr.justHideMenu();
			if (!stater) {
				stater = new sFrames();
				stater.init();
				
			}
			sl_upd = false;
			$j("#tabs").toTab(3);			
		}
	}
	
    this.runFilters = function(event){
        var tfs = 0, utext, odm = false, tr_del = false, killed = 0, i = 0, met = 0, alive = 0, tstr, t, zcl, sVal, tcl, once = false,wildCardPatt = new RegExp(regexEscape("#"), 'g');
		$lbody=$tbody.detach();
		self.visible=[];
		var fillength=filar.length,tlength=fillength;
        //$j(filar).each(function(er){
		while(tlength--){			
            if (filar[tlength] && filar[tlength].state == true) {
                tfs++;
            }
        }//);
        
        if (tfs == 0)  {
			var ltt = self.allRows.length;
			/*for (var i = 0; i < ltt; i++) {
				$j("tr#row_" + i, $tbody).removeClass("offwall");
				self.allRows[i]['hidden'] = false;
				self.visible.push(i);				
			}*/
			$j("tr",$lbody).removeClass("offwall");
			self.numRows=ltt;
			while(ltt--){
				self.allRows[ltt]['hidden'] = false;
				self.visible.push(ltt);
			}						
			self.updatePages();
			return;
		}
        self.cRows = [];
		var tar=$j("tr",$lbody);
		// $j('tr', $lbody).each(function(y){
		for(var y=0,tl=tar.length; y < tl; y++){
            //var self1 = this;
			var self1=tar[y],ind = self1.id.replace(/[^\d]+/g, ''),hits = 0,
			Row=self.allRows[ind];
			while(!Row){
				Row=self.allRows[++ind];
			}
			var fakes = Row['fake'],upret;
            //$j(filar).each(function(iCC){
			for(var iCC=0; iCC < fillength; iCC++){					                
                    var zcol = filar[iCC],myequ = [], tcase, must = [],fpos=$j.inArray(iCC, fakes),
						zmtds=zcol.methods,zvals=zcol.mvals;
                    if (zcol && zcol.state &&  fpos < 0) {
						once = true;
						var tds = iCC, tec = false, ztype = self.colType(iCC), pret, dval, tstr = '';
						if (ztype == 'string') {
							dval = '';
							pret = '"';
						}
						else {
							pret = '';
							dval = 0;
						}
						if (zmtds && zmtds['match']) {
							if (zmtds['match'].length > 0) {
								if (fchange === iCC && !zcol.state) {
									utext = "";
									tec = true;
								}
								else {
									utext = zmtds['match'];
								}
							}
							var sFilterTxt = regexEscape("#" + utext, "#").replace(wildCardPatt, '.*');
							sFilterTxt = sFilterTxt || '.*';
							sFilterTxt = '^' + sFilterTxt;
							var filterPatt = new RegExp(sFilterTxt, "i");
							tcase = "str";
						}
						else 
							if (ztype == 'date') {
								for (var usl in zmtds) {
									if (zmtds[usl] && zmtds[usl].r.length > 0) {
										myequ.push(usl + " " + zmtds[usl].r);
									}
								}
								tec = true;
								sFilterTxt = ' ';
								tcase = "digit";
							}
							else {
								if (/*fchange != iCC || zcol.state &&*/zmtds) {
									for (var usl in zmtds) {
										if (zmtds[usl].length > 0) {
											myequ.push(usl + " " + zmtds[usl]);
										}
									}
									sFilterTxt = ' ';
								}
								else {
									tec = true;
									sFilterTxt = '';
								}
								tcase = "digit";
							}
						if (zvals && zvals.length > 0) {
							for (var cv = 0; cv < zvals.length; cv++) {
								if (zvals[cv] == "false") {
									must.push(' == false');
								}
								else {
									must.push(' == ' + pret + zvals[cv] + pret);
								}
							}
						}
						sVal = Row[iCC];
						var bMatch = true, bOddRow = true, smar = new Array(),notArr=isArray(sVal),usVal;
						tr_del = false;
						
						
						if (ztype == 'string' && sVal.length > 0 && sVal != 'false' ) {
							if(notArr){
								usVal=sVal[1];
							}else{
								usVal=sVal;
							}
							if (usVal !== false) {
								tstr = usVal.replace(" /(\n)|(\r)/ig", '').replace("/\s\s/ig", ' ').replace("/^\s/ig", '');
							}
						}
						else {
							tstr = '';
							if (isNaN(sVal) && !notArr) {
								sVal = dval;
								Row[iCC] = dval;
							}
						}
						if (tcase == "str") {
							if (filterPatt.test(tstr) === bMatch) {
								hits++;
							}
							else {
								tr_del = true;
							}
						}
						else {
							if (!sVal || sVal.length == 0) {
								sVal = dval;
							}
							if (tstr.length == 0) 
								tstr = 0;
							var wt = "", resl;
							$j(myequ).each(function(zs){
								if (myequ[zs].length > 0) {
									wt += sVal + " " + myequ[zs] + " && ";
								}
							});
							wt = wt.replace(/&&\s$/, '');
							if (wt.length > 0) {
								eval("resl=" + wt);
								if (resl) {
									hits++;
								}
							}
							else {
								if (!fstat && tfs == 1 && fchange) {
									hits++;
								}
							}
						}
						if (must.length > 0) {
							var xt = '', resl,umv;
							for (var i = 0; i < must.length; i++) {
								umv=must[i];
								if (notArr && sVal.length == 2 ) {
									if(sVal[1] === false){
										xt += sVal[1] + ' ' + umv + ' || ';
									}else{
										var lar=sVal[0];
										for(var ic=0,il=lar.length; ic < il; ic++){
											xt += pret + lar[ic] + pret + ' ' + umv + ' || ';
										}
									}
								}
								else {
									if (sVal == 'false') {
										xt += sVal + ' ' + umv + ' || ';
									}
									else {
										xt += pret + sVal + pret + ' ' + umv + ' || ';
									}
								}
								
							}
							xt = xt.replace(/\|\|\s$/, '');
							if (xt.length > 0) {
								eval("resl=" + xt);
								if (!resl) {
									hits = 0;
								}
								else {
									//if(hits == 0){
									hits++;
								//}
								}
							}
						}
					//}
					}
					/*else {
						if (fpos >= 0 && zcol && zcol.state) {
							hits++;
						}
					}*/
            }//);
			//if (once) {
				if (tfs <= hits) {
					//if (Row['hidden']) {
					$j(self1, $lbody).removeClass('offwall');
					self.allRows[ind]['hidden'] = false;
					self.visible.push(ind);
					//}
					i = 0;
					alive++;
				}
				else {
					//if (!self.allRows[ind]['hidden']) {
					$j(self1,$lbody).addClass("offwall");
					self.allRows[ind]['hidden'] = true;
				//}
				}
			//}
        }//);
		$table.append($lbody);
		$lbody=null;
        if (once) {
			//if (alive > 0) {
			self.numRows = alive;
			self.updatePages();
		}
		else {
			$table.trigger("repaginate");
		}                 
    }
}

function fCleaner(){
	var r=0;
	var fll=filar.length;
	//for(var i=0; i< ; i++){
	while(fll--){
		//r+=countMethods(i);
		if(filar[fll].state == true){
			r++;
		}
	}
    if (r > 0) {
        $j("#fclean").attr("disabled", false);
        $j("#fmbox").css("background-position", "0px -13px");
    }
    else {
        $j("#fclean").attr("disabled", true);
        $j("#fmbox").css("background-position", "0px 0px");
    }
}

function cleanAllF(){
	var fll=filar.length;
	//for(var i=0; i < filar.length;i++){
	while(fll--){
		filar[fll] = {
			methods: {},
			mvals: [],
			state: false
		};
	}
	memo.toggle();
	setTimeout(function(){
		gpgr.runFilters();
		memo.toggle();
	},10);
}

function progress(){
    this.msg = 'Loading...';
    this.mode = 0;
    this.$box;
}

progress.prototype.init = function(){
    this.$box = $j("#mbox").text(this.msg).center();
}

progress.prototype.toggle = function(){
    if (this.mode == 0) {
        this.$box.show();
        this.mode = 1;
    }
    else {
        this.$box.hide();
        this.mode = 0;
    }
}

progress.prototype.banner = function(ntxt){
    if (ntxt && ntxt.length > 0) {
        this.msg = ntxt;
    }
    else {
        this.msg = 'Rendering';
    }
    this.init();
}


var filtersClass = function(){
    this.numberfil = [];
    this.numberfil[0] = {
        "title": 'more',
        "html": "gt",
        "func": ">"
    };
    this.numberfil[1] = {
        "title": 'less',
        "html": "lt",
        "func": "<"
    };
    this.numberfil[2] = {
        "title": 'equal',
        "html": "eq",
        "func": "=="
    };
    this.numberfil[3] = {
        "title": 'not equal',
        "html": "ne",
        "func": "!="
    };
    
    this.number_block;
    this.text_block;
    this.date_block;
    this.calendarField = '';
    this.$filters = $j("#fil_list");
    this.$uniques = $j("#filin_list");
	this.filterBox = document.createElement('input');
	this.dateBox = $j("<div class='dbox'></div>");
}

filtersClass.prototype.getFilters = function(){
    return filar;
}

filtersClass.prototype.setColValues = function(col, val, add){
    if (add) {
        if (!filar[col]) {
            filar[col] = {
                mvals: [],
                state: true
            };
        }
        filar[col].mvals.push(val);
        filar[col].state = true;
    }
    else {
        var tar = filar[col].mvals, ntar = [];
        if (tar.length > 1) {
            for (var i = 0; i < tar.length; i++) {
                if (tar[i] != val) {
                    ntar.push(tar[i]);
                }
            }
            filar[col].mvals = ntar;
        }
        else {
            filar[col].mvals = [];
            filar[col].state = false;
        }
    }
    
}

filtersClass.prototype.me = function(){
    return this;
}

filtersClass.prototype.launchFilter = function(ffoc){
    memo.toggle();
    setTimeout(function(){
        if ($j.browser.msie) {
            $j(ffoc).parent().focus().end().focus();
        }
        gpgr.runFilters();
        ffoc = false;
        memo.toggle();
    }, 100);
}

filtersClass.prototype.init = function(){
    var self = this;
    $j(self.filterBox).attr('type', 'text').addClass('_filterText box').blur(function(){
        var ft = $j(this).val();
        if (ft.length > 0) {
            $j(this).removeClass("box").addClass("filter_work");
        }
        else {
            //var fid = $j("#filbox").data("skey");
            var jmd = $j(this).attr("data-method");
            //filar[fid].methods[jmd] = "";
            filTool('', false, jmd, '');
            this.id = "";
            $j(this).removeClass("filter_work").addClass("box");
        }
    }).focus(function(){
        var $fpar = $j(this).parent().parent();
        var fid = $j("#filbox").data("skey");
        var xval = filar[fid].methods;
        var self1 = this;
        $fpar.find("input").each(function(){
            var cmd = $j(this).attr("data-method");
            if (self1 != this && (!xval[cmd] || xval[cmd] == "") ) {
                $j(this).removeClass("filter_work").addClass("box");
            }
            else {
                $j(this).removeClass("box").addClass("filter_work");
            }
        });
        this.id = '_filterText' + fid;
        
    }).keypress(function(e){
	var code = (e.keyCode ? e.keyCode : e.which);
		if (code == 13) {		
			//keyup(function(){
			// clearTimeout(filter);
			var $fpar = $j(this).parent().parent(), ust, fid = $j("#filbox").data("skey"), fmtd = $j(this).attr("data-method"), lval = this.value;
			
			if (lval.length > 0) {
				ust = true;
				if (fmtd) {
					filar[fid].methods[fmtd] = lval;
					if (fmtd == "==" || fmtd == "<>") {
						$j("input:lt(2)", $fpar).val("").removeClass("filter_work").addClass("box");
						/*filar[fid].methods[">"] = "";
				 filar[fid].methods["<"] = "";*/
						filTool('', false, ">", '');
						filTool('', false, "<", '');
					}
					else {
						$j("input:eq(2)", $fpar).val("").removeClass("filter_work").addClass("box");
						//filar[fid].methods["=="] = "";
						filTool('', false, "==", '');
						filTool('', false, "!=", '');
					}
				}
				else {
					filTool(lval, true, "match", '');
					
				//filar[fid].methods['match'] = this.value;
				}
			}
			else {
				ust = false;
				filTool('', false, fmtd, '');
			}
			$j("input#fil_on").attr({
				'disabled': !ust,
				'checked': ust
			});
			//filar[fid].state = ust;
			self.launchFilter(this);
		}
    });
    
    //if (!$j.browser.msie) {
		$j(self.dateBox).html(
			"<input type='text' class='button ' style='width:100px;' disabled='disabled'>&nbsp;&nbsp;"+
			"<div class='clfld'></div><a href='#' ><img src='/images/calendar.png' alt='Calendar' border='0'></a>"+
			"<input type='hidden' name='' value=''>");

	$j(this.dateBox).find("input[type=text]")
	.bind('refresh', function(){
        var self = filmter.me(),
        $dad = $j(this).parent(),
        $fpar = $dad.parent().parent(), ust,
        //var fid = $j("#filbox").data("skey");
        fmtd = $j(this).removeClass("boxd").addClass("filter_work_date").attr("data-method"),
        mename = $j(this).attr(aname),
        hv = $dad.find("input["+aname+"='filter_" + mename + "']"),
        self1 = this,lval=this.value;
        /*$dad.find("div.clfld").addClass('clflda').attr("title",'Clear').bind("click",{$pobj:$dad},function(ev){
         $j(self1).trigger("cleanDate");
         });*/
        $j(this).eraser(true);
        
        if (fmtd) {
            filTool(hv.val(), true, fmtd, lval);
            if (fmtd == "==" || fmtd == "!=") {
                $j("input[type=text]:lt(2)", $fpar).val("").removeClass("filter_work_date").addClass("boxd");
                filTool(0, false, ">", '');
                filTool(0, false, "<", '');
            }
            else {
                $j("input[type=text]:gt(1)", $fpar).val("").removeClass("filter_work_date").addClass("boxd");
                filTool(0, false, "==", '');
                filTool(0, false, "!=", '');
            }
        }
        if (lval.length > 0) {
            ust = true;
        }
        else {
            ust = false;
        }
        /*$j("input#fil_on").attr({
         'disabled': !ust,
         'checked': ust
         });*/
        //filar[fid].state = ust;
        self.launchFilter(this);
    }).bind("cleanDate", {
        meobj: this
    }, function(x){
        var $me = $j(this);
        var $tp = $me.parent();
        /*$tp.find("div.clfld").removeClass('clflda').attr("title",'').unbind("click");*/
        //eraser($tp, false);
		$tp.eraser(false);
        //var fid = $j("#filbox").data("skey");
        var cmtd = $me.attr("data-method");
        $me.removeClass("filter_work_date").addClass("boxd");
        filTool('', false, cmtd, '');
        self.launchFilter($me);        
    });
    	
    this.number_block = $j("<ul class='tobs'></ul>");
    this.text_block = $j(this.number_block).clone(true);
    this.date_block = $j(this.number_block).clone(true);
    var $t = $j("<li class='ffbb fil_line'><span class='comsign '></span></li>");
    for (var iz in this.numberfil) {
        var lv = this.numberfil[iz];
        if (lv.func) {
            var $tc = $j(this.filterBox).clone(true);
            $tc.attr("data-method", lv.func);
            $tc.addClass("numeric").numeric();
            var $t1 = $t.clone(true).find("span").addClass(lv.html).end();
            var $t2 = $t1.clone();
            $j(this.number_block).append($t2.append($tc));
            $tc = null;
            var $tc1 = $j(this.dateBox).clone(true);
            $j("input[type='text']",$tc1).attr("data-method", lv.func).each(function(){
				$j(this).attr(aname,lv.html);
			});
			$tc1.find("a").bind('click', {
                fname: lv.html,
				loc: $tc1
            }, function(ev){
                self.popCalendar(ev);
            });
			$j("input[type='hidden']",$tc1).attr(aname, "filter_" + lv.html);
			$t1.append($tc1);
            $j(this.date_block).append($t1);
			$tc1=null;
			$t1=null;
        }
    }
    
    var $t = $j("<li class='ffbb fil_line'></li>");
    var s = $j("<span class='comsign ts'></span>");
    $t.append(s).append(this.filterBox);
    $j(this.text_block).append($t);
    
    if ($j.browser.msie) {
        $j("input#fil_on").click(function(){
            filmter.checkFilter(this);
        });
        
        if ($j.browser.version == 7) {
            $j("input#fil_on").css("top", "-26px");			
        }
        if ($j.browser.version > 7) {
            $j("#lbl").css("top", "-3px");
        }
    }
}

filtersClass.prototype.checkFilter = function(cbox){
    var self = this;
    var area = $j("#filbox").data("skey"), wo = 0, zcol = filar[area];
    var ctype = gpgr.colType(area);
	memo.toggle();
	setTimeout(function(){
		wo = countMethods(area);
		if (wo > 0) {
			var st = $j(cbox).is(":checked");
			filar[area].state = st;
			fchange = parseInt(area);
			fstat = st;
			gpgr.runFilters();
			fchange = false;
		}
		else {
			$j(cbox).attr("checked", false);
		}
		memo.toggle();
	},50);
}

filtersClass.prototype.hideAll = function(){
    this.$uniques.hide();
    this.$filters.hide();
}

filtersClass.prototype.showfils = function(cdiv){
	this.hideAll();
    var self = this,tdsc = $j("#filbox").data("skey"),ftype, fdht = "", uval, ind, z = 0,ftype = gpgr.colType(tdsc),poss = $j(cdiv).offset(),posw = $j(cdiv).outerWidth(),lop = false, cval;
    if (filar[tdsc]) {
        cval = filar[tdsc].methods;
    }
    else {
        cval = false;
    }
    if (ftype == "string") {
        fdht = $j(this.text_block).clone(true);
        if (cval && cval['match'] && cval['match'].length > 0) {
            uval = cval['match'];
        }
        else {
            uval = "";
        }
        $j(fdht).find("input").each(function(){
            this.value = uval;
            if (uval.length > 0) {
                $j(this).removeClass("box").addClass("filter_work");
            }
            else {
                $j(this).removeClass("filter_work").addClass("box");
            }
        });
    }
    else {
        if (ftype != 'date') {
            $j("input", this.number_block).each(function(){
                var cn = $j(this).attr("data-method");
                if (cn) {
                    if (cval && cval[cn] && cval[cn].length > 0) {
                        uval = cval[cn];
                    }
                    else {
                        uval = "";
                    }
                    $j(this).val(uval);
                    if (uval.length > 0) {
                        $j(this).removeClass("box").addClass("filter_work");
                        lop = true;
                    }
                    else {
                        $j(this).removeClass("filter_work").addClass("box");
                    }
                }
            });
            if (lop) 
                $j("input#fil_on").attr("disabled", false);
            fdht = $j(this.number_block).clone(true);
        }
        else {
            $j("input[type!='hidden']", this.date_block).each(function(){
                var cn = $j(this).attr("data-method");
                var mnm = $j(this).attr("name");
                if (cn ) {
                    if (cval && cval[cn] && cval[cn].r.length > 0) {
                        uval = cval[cn].v;
                        $j("input[name='filter_" + mnm + "']", self.date_block).val(cval[cn].r);
                    }
                    else {
                        uval = "";
                        if (!filar[tdsc]) {
                            filar[tdsc] = {
                                methods: {},
                                state: false
                            };
                        }
                        
                        filar[tdsc].methods[cn] = {
                            r: '',
                            v: ''
                        };
                    }
                    $j(this).val(uval);
                    if (uval.length > 0) {
                        $j(this).removeClass("boxd").addClass("filter_work_date");
                        $j(this).eraser(true);
                        lop = true;
                    }
                    else {
                        $j(this).removeClass("filter_work_date").addClass("boxd");
                        $j(this).eraser(false);
                    }
                }
            });
            if (lop) {
                $j("input#fil_on").attr("disabled", false);
            }
            fdht = $j(this.date_block).clone(true);
        }
    }
    $j(fdht).data("zid", tdsc);
    //clearTimeout(fbshowt);
    fbshowt = 0;
    self.$filters.empty().append(fdht).css({
        "left": poss.left + posw,
        "top": poss.top
    }).slideDown(5);
}

filtersClass.prototype.popCalendar = function(f){
    this.calendarField = f.data.fname;	
    var idate = this.$filters.find("input["+aname+"='filter_" + this.calendarField + "']").val();	
	if(!idate){
		idate=today;
	}
    window.open('index.php?m=public&a=calendar&dialog=1&callback=filmter.setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

filtersClass.prototype.setCalendar = function(idate, fdate){
    this.$filters
		.find("input["+aname+"='filter_" + this.calendarField + "']").val(idate).end()	
		.find("input["+aname+"='" + this.calendarField + "']").val(fdate).trigger("refresh");
}

filtersClass.prototype.lects = function(cdiv){
    this.hideAll();
    var zkey = $j("#filbox").data("skey");
    var $nht = gpgr.colVals(zkey),bkeys=filar[zkey].mvals; 
    if (filar[zkey] && filar[zkey].mvals && filar[zkey].mvals.length > 0) {
		var tinar=$j("input", $nht), tl=tinar.length;
        //.each(function(){
		while(tl--){
			var cthis=tinar[tl];
            var cv = $j(cthis).attr('cact');
            if ($j.inArray(cv, bkeys) >= 0) {
                $j(cthis).attr("checked", true);
            }
        }
    }
	bkeys=null;
	tinar=null;
	cthis=null;
    var poss = $j(cdiv).offset(), posw = $j(cdiv).outerWidth();
    $j("div#filin_list").empty().append($nht).css({
        "left": poss.left + posw,
        "top": poss.top
    }).show();
    /*if ($j("div#filin_list").height() > 150) {
        $j("ul#outf").simplyScroll({
            className: 'vert',
            horizontal: false,
            frameRate: 20,
            speed: 5
        });
        if ($j.browser.version == 7) {
            $j("div.simply-scroll-btn").css("left", "0px");			
        }
    }*/
    
}

function saveClass(){
	this.clname="Saver";
	this.extra='';
}

saveClass.prototype.saveQuery = function(t){
	var self = this,res,
	fils = JSON.stringify(filar);
	this.$boxer=$j("#dbox");
	var qnm = trim($j(".qncl",this.$boxer).val()),
	qdsc = trim($j(".qdcl",this.$boxer).val());
	if (!t) {
		if (qnm.length == 0 || qdsc.length == 0) {
			if (qnm == '') {
				$j(".qncl", this.$boxer).focus();
			}
			else 
				if (qdsc == '') {
					$j(".qdcl", this.$boxer).focus();
				}
			alert("Please enter name and description of query!");
			return false;
		}
		$j("#slogo", this.$boxer).show();
		self.doSave('mode=query&filters=' + fils + '&qname=' + qnm + '&qdesc=' + qdsc + "&imode=" + this.mode + "&sid=" + this.cid);
	}
	else if(t && t.data > 0){
		self.add2Table({
			id: t.data,
			name: qnm,
			desc: qdsc,
			type: t.type,
			sdate: $j("#start_date").val(),
			edate: $j("#end_date").val(),
			brest: $j("#brest",$j("#dbox")[0]).is(":checked")
		})
	}
}

saveClass.prototype.saveDialog = function(){
	var t= "Save current query",name='',desc='',id=0,self=this;
	$j("<div title='" + t + "' id='dbox' class='diabox'>Name:&nbsp;"+
		"<input type='text' style='border: 1px solid black; width: 150px;' id='qname' class='qncl' value='" + name + "'>"+
		"<br>Description: <textarea cols='34' rows='2' id='qdesc' class='qdcl'>" + desc + "</textarea><br>" + 
		"<input type='hidden' id='quid' value='" + id + "'>" +
		this.extra+ 
		"<input type='button' class='button' value='Save' >&nbsp;&nbsp;" + 
		"<input type='button' class='button' id='dbox-kill' value='Cancel' onclick='$j(\"#dbox\").dialog(\"close\").remove();'>"+
		"<div id='slogo' class='saving'></div>"+		
		"</div>")
	.dialog({
			resizable: false,
			width: 350
		})
	.find("input.button:eq(0)").click(function(e){
		self.saveQuery(false);
	}).end()	
	.show();
	
}

saveClass.prototype.closeEdit = function  (){
	dmarker=0;
	$j("#debox").dialog("close").hide();
}

saveClass.prototype.dialogNote = function(txt){
	/*var $db=$j("#dbox"),$deb=$j("#debox"),$uc;
	if($db.is(":visible")){
		$uc=$db.clone(true);
	}else{
		$uc=$deb.clone(true);
	}*/
	$j("#slogo").add(".saving").addClass("savewarn").fadeOut(0).text(txt).show().fadeIn(500,function(){$j(this).fadeOut(2500,function(){$j(this).text("").fadeIn(0);})});
}

saveClass.prototype.trimView = function (str){
	var res={};
	if(str.length > 45){
		res.n=true;
		var words=str.split(" "),clen=0,ind=0;
		while(clen < 45){
			var nast=words[ind]+' ';
			res.s+=nast;
			clen+=nast.length;
			ind++;
		}
		if(res.s.length > 45){
			res.s=res.s.slice(0,44);
		}
		if(res.s.length < str.length){
			res.s+='...';
		}
	}else{
		res={n: false,s: str}
	}
	return res;
}

saveClass.prototype.editQuery = function (){
	var self= this;
	$j("#slogo").show();
	var zmode= $j("#debox").data("stype");
	var vals={
		name:$j("#qname").val(),
		desc: $j("#qdesc").val(),
		srdate: $j(".datepicker[name='filter_qstart']").val(),
		erdate: $j(".datepicker[name='filter_qend']").val(),
		svdate: $j("#qstart_date").val(),
		evdate: $j("#qend_date").val(),
		id: $j("#quid").val(),
		stype: zmode,
		showr: $j("#brest").is(":checked")
	};
	var dst="imode=edit&mode=query&qname="+ vals.name +'&qdesc='+vals.desc+
			'&sdate='+vals.srdate +"&edate="+vals.erdate+"&sid="+vals.id+"&stype="+vals.stype+"&showr="+vals.showr;
	$j.ajax({
		url: '/?m=outputs&suppressHeaders=1',
		type: 'post',
		data: dst,
		success: function(data){
			if (data == "ok") {
				self.dialogNote("Query saved");
				setTimeout(function(){
					$j("#debox").dialog('close');
				}, 3500);
				var $tr = $j("#"+$j("#debox").data("row")),tvr,tds=$j("td", $tr);				
				//$j("td", $tr).each(function(i){
				if(zmode == "stats"){
					$tr.attr("data-showr",vals.showr);
				}
				for(var i=0, j = tds.length; i < j ; i++){
					var $tdo=$j(tds[i]);
					switch (i) {
						case 1:
							$tdo.attr("data-text",vals.name);
							tvr=self.trimView(vals.name);
							if(tvr.n === true){
								$tdo.addClass("moreview");
							}else{
								$tdo.removeClass("moreview");
							}
							$tdo.text(tvr.s);
							break;						
						case 3:
							$tdo.attr("data-text",vals.desc);
							tvr=self.trimView(vals.desc);
							if(tvr.n === true){
								$tdo.addClass("moreview");
							}else{
								$tdo.removeClass("moreview");
							}
							$tdo.text(tvr.s);
							break;
						case 4:
							$j("input", $tdo).val(vals.srdate);
							$j(".stdw", $tdo).html((vals.svdate.length > 0 ? vals.svdate : 'N/D&nbsp;'));
							break;
						case 5:
							$j("input", $tdo).val(vals.erdate);
							$j(".stdw", $tdo).html((vals.evdate.length > 0 ? vals.evdate : 'N/D&nbsp;'));
							break;
						default:
							break;
					}
				}//);
			}
		}
	});
	
}

saveClass.prototype.viewDate = function(date){
	var pz,res=0;
	if (date != 0 && date.length == 10) {
		pz = date.split("/");
		res=pz[2]+pz[1]+pz[0];
	}
	return res;
}

saveClass.prototype.add2Table = function(rdata){
	var self=this;
	$qtable=$j("#qtable");
	var nl=$j("tr",$qtable).length;
	tvn=this.trimView(rdata.name);
	tvd=this.trimView(rdata.desc);
	$j("<tr/>")
		.attr("id","qsr_"+nl)
		.attr("data-showr",rdata.brest)
		.append("<td><div data-id='"+rdata.id+"' class='qeditor'></div></td>")
		.append("<td "+(tvn.n ===true? 'class="moreview"' : "")  +" data-text='"+rdata.name+"'>"+ tvn.s +"</td>")
		.append("<td>"+rdata.type+"</td>")
		.append("<td "+(tvd.n ===true? 'class="moreview"' : "")  +" data-text='"+rdata.desc+"'>"+ tvd.s +"</td>")
		.append('<td ><div class="tdw"><div class="stdw">'+(rdata.sdate.length > 0 ? rdata.sdate : 'N/D&nbsp;' )+'</div><a href="#" class="calpic" onclick="popTCalendar(\'start_'+nl +  '\')"><img width="16" height="16" border="0" alt="Calendar" src="/images/calendar.png"></a><input type="hidden" id="start_'+nl+'" value="'+self.viewDate(rdata.sdate)+'"></div></td>')
		.append('<td ><div class="tdw"><div class="stdw">'+(rdata.edate.length > 0 ? rdata.edate : 'N/D&nbsp;' )+'</div><a href="#" class="calpic" onclick="popTCalendar(\'end_'+nl +  '\')"><img width="16" height="16" border="0" alt="Calendar" src="/images/calendar.png"></a><input type="hidden" id="end_'+nl+'" value="'+self.viewDate(rdata.edate)+'"></div></td>')
		.append('<td ><span title="Run" class="fhref" onclick="qurer.run(\''+nl+'\');" ><img src="/images/run1.png" weight=22 height=22 border=0 alt="Run"></span></td>')
		.append('<td ><span title="Delete" class="fhref" onclick="qurer.delq(\''+nl+'\');" ><img src="/images/delete1.png" weight=16 height=16 border=0 alt="Delete"></a></td>')
		.append('<td ><div title="Export" class="exportq" onclick="qurer.run(\''+nl+'\',\'export\');" ></div></td>')		
		.appendTo($qtable);	
}

function extend(Child, Parent) {
	var F = function() { }
	F.prototype = Parent.prototype
	Child.prototype = new F()
	Child.prototype.constructor = Child
	Child.superclass = Parent.prototype
}

function qlHandler  (){
	this.current=false;
	qsaved= eval(''+qsaved+'');
	this.$sl=$j("#qseller");;
	var self=this;
	this.$buts=$j("<div style='float:left'>&nbsp;&nbsp;&nbsp;<input type='button' class='button' value='Run' onclick='qurer.run()'>&nbsp;&nbsp;&nbsp;<input type='button' class='button' value='Edit' onclick='qurer.edit()'>&nbsp;&nbsp;&nbsp;<input type='button' class='button' value='Delete' onclick='qurer.del()'></div>").css("display",'none');
	this.cid=0;
	this.mode='save';
	this.extra='';	
}

extend(qlHandler,saveClass);

qlHandler.prototype.listUpdate= function(){	
	this.$sl.empty();
	if(qsaved.length  > 0){
		var $oc=$j("<option value=''></option>");
		var ql=qsaved.length;
		this.$sl.append($oc.clone().text('--select query--').val("-1"));
		for(var i=0; i < ql; i++){
			var $t=$oc.clone(true).val(qsaved[i].id).text(qsaved[i].name);
			this.$sl.append($t);
			$t= null;
		}
						
		this.$sl.show();
		this.$buts.insertAfter(this.$sl);
		if(this.$sl.val() < 0){
			this.$buts.hide();
		}
	}else{
		this.$sl.hide();
	}
	$oc=null;
}

qlHandler.prototype.update = function(){
	var cv=this.$sl.val();
	if(cv > 0 ){
		this.$buts.show();
	}else{
		this.$buts.hide();
	}
}

qlHandler.prototype.run = function(cv,todo){
	//var cv=this.$sl.val();
	if(!isNaN(cv) && cv >= 0){
		var $uv=$j("#qsr_"+cv,$j("#qtable")[0]);		
		document.xform.filter_beginner.value=$j("#start_"+cv).val();
		document.xform.filter_finisher.value=$j("#end_"+cv).val();
		document.xform.beginner.value="lala";
		document.xform.finisher.value="lala";		
		document.xform.stype.value=$uv.find("td:eq(2)").text();
		document.xform.qsid.value=$uv.find(".qeditor").attr("data-id");
		document.xform.faction.value=todo;
		document.xform.action="/?m=outputs";
		if(todo == 'export'){
			$j("#sendAll").attr("action", function(i,v){
				return v+"&suppressHeaders=1";
			})
		}
		document.xform.submit();
	}
}

qlHandler.prototype.delq = function(cv){
	if(confirm("You want delete this query ?")){
		var $qr=$j("#qsr_"+cv);
		var data='mode=query&imode=del&stype='+$qr.find("td:eq(2)").text()+
		'&sid='+$qr.find(".qeditor").attr("data-id");
		$j.ajax({
			url: "/?m=outputs&suppressHeaders=1",
			type: 'post',
			data: data,
			success: function(data){
				if(data == 'ok'){
					$qr.fadeOut('slow',function(){
						$qr.remove();
					});
				}
			}
		});		
	}else{
		return false;
	}
}

qlHandler.prototype.findEntry = function(id){
	var ql = qsaved.length;
	for (var i = 0; i < ql; i++) {
		if (qsaved[i].id == id) {
			return i;
			
		}
	}
}

qlHandler.prototype.edit = function(){
	var nid=this.$sl.val();
	if (nid > 0) {
		this.cid=nid;
		this.mode='edit';
		var ri=this.findEntry(nid);
		$j("#qname").val(qsaved[ri].name);
		$j("#qdesc").val(qsaved[ri].qdesc);		
	}
}

qlHandler.prototype.del = function(){
	var nid=$j(obj).val();
	if (nid > 0) {
		this.cid=nid;
		this.mode='edit';
		this.saveQuery();
	}
}

qlHandler.prototype.doSave = function(pdata){	
	var self=this;
	$j.ajax({
		type: 'post',
		url: '/?m=outputs&suppressHeaders=1',
		data: pdata,
		success: function(data){
			if (data.length > 0) {
				if (data != "fail") {
					//we saved query
					//qsaved = eval('' + data + '');
					if (parseInt(data) > 0) {
						//self.listUpdate();
						//$j("#save_icon").blink(1, false);
						$j("#slogo").hide();
						self.mode = 'save';
						self.cid = 0;
						self.$boxer.dialog('close');
						var t={'data':data,'type':'Table'};
						self.saveQuery(t);
					}
				}
			}
			return false;
		}
	});	
}

qlHandler.prototype.extractRow = function(txt){
		var msg,msg_class;
		if (txt != 'fail') {
			txt = $j.parseJSON(txt);
			qurer.add2Table(txt);			
			$j('#importbox').toggle();
			msg="Query imported";
			msg_class="msg_ok";
		}else{
			msg="Query file is not valid, import failed";
			msg_class="msg_bad";
		}
		$j("#msg_place").addClass(msg_class).html(msg).show().delay(3000).fadeOut(2000,function(){
			$j(this).removeClass(msg_class).hide();
		});
		$j("#importbox").find("input:eq(0)").val("");
}

var gpgr = new pager;
var memo = new progress();
var filmter = new filtersClass();
var onelist = true;
var qurer = new qlHandler();

function startCallback(){
	// make something useful before submit (onStart)
	return true;
}


function filTool(val, add, mtd, vval){
    var key = $j("#filbox").data("skey");
    var mode = gpgr.colType(key), cnt = 0, res;
	
    if (add) {
        if (!filar[key]) {
            filar[key] = {};
        }
        if (!filar[key].methods) {
            filar[key].methods = {};
        }		
        if (mode == 'date') {
            filar[key].methods[mtd] = {
                r: val,
                v: vval
            };
        }
        else {
            filar[key].methods[mtd] = val;
        }
        res = true;
    }
    else {
		var fkey=filar[key],fmtd=fkey.methods;
        if (fkey.methods) {
            if (mode != 'string') {
                for (var umt in fmtd) {
                    if (umt != mtd) {
                        if (mode == 'date') {
                            if (fmtd[umt] && fmtd[umt].r.length > 0) {
                                cnt++;
                            }
                        }
                        else 
                            if (mode == 'number') {
                                if (fmtd[umt] && fmtd[umt] >= 0) {
                                    cnt++;
                                }
                            }
                    }
                    else {
                        if (mode == "date") {
                            filar[key].methods[mtd] = {
                                r: '',
                                v: ''
                            };
                        }
                        else {
                             delete filar[key].methods[mtd];
                        }
                    }
                }
            }
            else {
                if (fmtd.match && fmtd.match.length > 0) {
                    cnt++;
                }
            }
        }
        if (fkey.mvals && fkey.mvals.length > 0) {
            cnt++;
        }
        if (cnt == 0) {
            res = false;
        }
        else {
            res = true;
        }
    }
    filar[key].state = res;
    $j("input#fil_on").attr({
        'disabled': !res,
        'checked': res
    });
}

$j.fn.eraser = function  (state){
	var self=this,
		$par=$j(self).parent();
    if (state) {
		$par.find("div.clfld").addClass("clflda").attr("title", "Clear").bind("click", function(ev){
			$j(this).parent().find("input[type!='hidden']").each(function(){
				$j(this).trigger("cleanDate");
			});
		});
	}
	else {
		$j("div.clfld",$par).removeClass("clflda").attr("title", '').unbind("click");
		$j("input[type!='hidden']",$par).each(function(){
			$j(this).val("");
			var mnm = $j(this).attr("name");
			$j(self).find("input[name='filter_" + mnm + "']").val("");
		});
	}
	return self;
}

function tabPrepare(){
	/*$gtabs=$j("div#tabs").tabs({

   		select: function(event, ui){
			flipSel(ui);			
			var seltab = ui.index;
			if (seltab == 3) {//case for check whether we have set for stat builds
				if (rrr > 0) {
					gpgr.justHideMenu();					
					if (fields.length > 0) {
						if (!stater) {
							stater = new sFrames();
							stater.init();
						}												
						sl_upd=false;
					}
					else {
						if (stater) {
							stater.destroy();
						}
					}
				}
			}
		}
	}).tabs('select', 0);
	$j("div#tabs").paletab().data('disabled.tabs', []).show();*/
	var ft=0;
	/*if(rrr > 0){
		ft=2;
	}*/
	//$j("#tabs").tabs({select: function(event,ui){flipSel(ui);}}).tabs('select',tgt).show();
	$j("#tabs").tabs().show().toTab(tgt);	
	
}

function prePage(mode){	
	$j("#shadow").fadeTo(1, 0.5).hide();
	dw=($j(document).width()+'');dw=dw.replace(/\d\d$/,"");
	$j(".mtab").width(dw+'00');
	if(mode === undefined){
		mode='mas';		
	}
    if (rrr > 0) {
        $fcol = $j("#folder");
		if (mode == 'mas') {
			$j("<div style='float:left;'>Forms<div class='colic'></div></div>").data("mode", 'off').click(function(){
				toggleForms(this);
			}).insertBefore($fcol);
		}
        memo.init();
        memo.toggle();
        setTimeout(function(){
            filmter.init();
            gpgr.init('rtable',mode);
			if (tgt == 3) {
				stater = new sFrames;
				stater.init();
			}			
            memo.toggle();
            memo.banner();				
        }, 5);
    }else{
		if(tgt == 3){
			$j(".purestat").attr("disabled",false);
		}
	}    
	//qurer.listUpdate();
	
	$j(".moreview").live('mouseenter mouseleave',function(e){		
		var hover = (e.type === 'mouseover');
		var mpar=$j(this).closest("tr").attr('id');
		if(hover ){
			var xp=$j(this).offset();
			$smalltip
				.text($j(this).attr("data-text"))
				.css({
					left: xp.left+15,
					top: xp.top+10
				})
				.data("current",$j(this).parent().attr('id'))
				.show();
			$j(this).bind("mousemove",function(e){
				$smalltip.css({
					left: e.pageX+15,
					top: e.pageY+10
				});
			});
		}else{
			$smalltip.hide();			
		}		
	});
	
	$j(".qeditor",$j("#qtable")[0]).live('click',function(e){		
		var $tr = $j(this).closest("tr"),
			qid = $j(this).attr("data-id"),
			qname, qdesc, qstart = {}, qend = {},zmode,
			tds=$j("td", $tr),str;
		for(var i=0,j=tds.length; i < j; i++){
		//$j("td", $tr).each(function(i){
			var $tdo=$j(tds[i]);
			switch (i) {
				case 1:
					qname = $tdo.attr("data-text");
					break;
				case 2:
					zmode= $tdo.text().toLowerCase();
				case 3:
					qdesc = $tdo.attr("data-text");
					break;
				case 4:
					qstart.r = $j("input", $tdo).val();
					qstart.v = $j(".stdw", $tdo).text();
					if(trim(qstart.v) == "N/D"){
						qstart={
							r:0,
							v:''
						}
					}
					break;
				case 5:
					qend.r = $j("input", $tdo).val();
					qend.v = $j(".stdw", $tdo).text();
					if(trim(qend.v) == "N/D"){
						qend={
							r:0,
							v:''
						}
					}
					break;
				default:
					break;
			}
		}//);
		var $zd=$j("#debox").dialog({
			width: 350,
			height: 270,
			resizable: false			
		}).find("#qname").val(qname).end()
			.find("#qdesc").val(qdesc).end()
			.find("#qstart_date").val(qstart.v).end()
			.find("#qend_date").val(qend.v).end()
			.find("#quid").val(qid).end()
			.find(".datepicker[name^='filter_']").each(function(){
				if ($j(this).attr("name") == "filter_qstart") {
					$j(this).val(qstart.r);
				}
				else {
					$j(this).val(qend.r);
				}
			}).end()
			.find('#brest').each(function(){
				if(zmode == 'stats'){
					eval("str = "+$tr.attr("data-showr")+";");
					$j(this).attr("checked",str).show().parent().show();
				}else{
					$j(this).hide().parent().hide();
				}
			}).end()
			.data("row",$tr.attr("id"))
			.data("stype",zmode)
			.show();
			/*if(zmode == "stats"){
				$j("table",$zd).hide();
			}else{*/
				$j("table",$zd).show();
			//}
	});

	$j(".jcheck",$j("#sendAll")[0]).live(be, function(){
		var st=$j(this).is(":checked"),
		$bbb=$j("#fcleaner"),
		bst=$bbb.attr("disabled");		
		if(st){
			bst=false;
		}else{
			if($j(".jcheck:checked").length > 0){
				bst=false;
			}else{
				bst=true;
			}
		}
		$bbb.attr('disabled',bst);
	});
	
	$j("div.exborder").css("display","inline");
	makeView('hands');
	
	
	$j("#importbox")	
	.find("input").eq(0).bind("change",function(e){
		$j(this).next().attr("disabled",false);
	}).end().eq(1).attr("disabled",true);	
	
}

function xtraSubmit(){
	$j('<iframe name="uploadQ" src="about:blank" width="0" height="0" style="display:none;" id="queryloader" ></iframe>').append(document.body);
	document.upq.submit();
	/*$j("#queryloader").load(function(){
			qurer.extractRow();
			$j(this).unbind("load");
	});*/	
}

function toggleForms(obj){
    var $pt = $j(obj),cst = $pt.data("mode");
    if (cst == 'off') {
        $pt.find(".colic").css("background-position", "-51px -141px").end().data("mode", "on");
        $j("#folder").show();
    }
    else {
        $pt.find(".colic").css("background-position", "-38px -141px").end().data("mode", "off");
        $j("#folder").hide();
    }
    filmter.hideAll();
    gpgr.hideMenu();
}

function makeView(cln){
    if (cln != '') {
        var $but = $j("<div class='switch'></div>");
		var tar=$j("." + cln);
		for(var i=0,j = tar.length;i < j; i++){
        //$j("." + cln).each(function(){
			var $sobj=$j(tar[i]),
            	bid = $sobj.attr("data-col"),
				tid = $sobj.attr('id'),
            	$but1 = $but.clone();
            $but1.data("tgt", bid).data("vstt", 0).click(function(ev){
                onf(this);
                ev.stopPropagation();
            });
            $sobj.click(function(ev){
                onf($j("div", this));
                ev.stopPropagation();
            });
            $but1.prependTo($sobj);
			var dshow=$j.inArray(tid, aopen);
			if (dshow >= 0) {
				onf($j(".switch",$sobj));				
			}else{
				$j("#block_" + bid).hide();
			}
            delete $but1;
        }//);
        
    }
}

function onf(self){
    var bid = $j(self).data("tgt");
    $blk = $j("#block_" + bid);
    var stt = $j(self).data("vstt"), nps = "", nval = 0;
    if (stt == 1) {
        $blk.hide();
        nps = " 0 -144px";
        nval = 0;
    }
    else {
        $blk.show();
        nps = "-12px -144px";
        nval = 1;
    }
    $j(self).css("background-position", nps).data("vstt", nval);
}

function popCalendarEd(field,value){
	if (calwined) {
		calwined.close();
	}
    calif = field;    
    window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendarEd&date=' + value, 'calwined', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendarEd(idate, fdate){
	$j(".date_edit_"+calif).val(fdate).trigger("refresh");    
}


function popCalendar(field){
    calendarField = field;
    idate = eval('document.xform.filter_' + field + '.value');
    window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendar(idate, fdate){
    fld_date = eval('document.xform.filter_' + calendarField);
    /*fld_fdate = eval('document.xform.' + calendarField);*/
    fld_date.value = idate;
	$j("input[name="+calendarField+"]").val(fdate);
    //fld_fdate.value = fdate;
}

function popRCalendar(field){
    calendarField = field;
    idate = $j(".datepicker[name='filter_"+field+"']").val();
	if(idate == 0){
		idate=today;
	}
    window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendarR&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendarR(idate, fdate){
 	$j("#"+calendarField+"_date").val(fdate);
	$j(".datepicker[name='filter_"+calendarField+"']").val(idate); 
}

function popTCalendar(field){
    calendarField = field;	
    idate = $j("#"+field).val();
	if(idate == 0){
		idate=today;
	}
    window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendarT&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendarT(idate, fdate){
	var pts=calendarField.split("_");
	var $pr=$j("#qsr_"+pts[1]),ins=0;
	if(pts[0]=='start'){
		ins=4;
	}else{
		ins=5;
	}
	$j("td:eq("+ins+")",$pr)
		.find("div.stdw").text(fdate).end()
		.find("input").val(idate);	
 	 
}


function checkDate(){
    /*if (document.xform.beginner.value == "" || document.xformFilter.finisher.value== ""){
     alert("You must fill fields");
     return false;
     } */
    if (document.xform.filter_finisher.value != "" && document.xform.filter_beginner.value != "" &&
    document.xform.filter_finisher.value < document.xform.filter_beginner.value) {
        return false;
    }
    else {
        return true;
    }
}

function clearData(){
    $j(".jcheck").attr("checked", false);
	datesoff();
    
}

function getData(){
    var acl = $j(".jcheck:checked").length;
    if (acl > 0) {
        $j("#sendAll")
		.find("input.datepicker").attr("disabled",false).end()
		.submit();
    }
    else {
        alert("Please select at least one field for result table");
        return false;
    }
}

function countMethods(key){
	var r=0;
	var t=gpgr.colType(key);
	for(var c in filar[key].methods){
		if(t == 'date'){
			if(filar[key].methods[c].r > 0) {
				r++;
			}
		}else{
			if(filar[key].methods[c].length > 0){
				r++;
			}
		}		
	}
	if(filar[key].mvals && filar[key].mvals.length > 0){
		r++;
	}
	return r;
}

function regexEscape(txt, omit){
    var specials = ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'];
    if (omit) {
        for (var i = 0; i < specials.length; i++) {
            if (specials[i] === omit) {
                specials.splice(i, 1);
            }
        }
    }
    var escapePatt = new RegExp('(\\' + specials.join('|\\') + ')', 'g');
    return txt.replace(escapePatt, '\\$1');
}

function flipSel(path){	
    if (!path) 
        return false;
    var $zt = $j(path.panel).parent().find("ul");
    $zt.find("li").each(function(x){
        var self = this;
        $j(self).find("img").each(function(){
            $j(this).attr("src", function(){
                var tp = $j(this).attr("src");
                if (x === path.index && !tp.match("Selected")) {
                    return tp.replace("/tab", "/tabSelected");
                }
                else 
                    if (x != path.index) {
                        return tp.replace("Selected", "");
                    }
            });
        });
    });
}

$j.fn.blink = function(times, finalview){
	var self = $j(this);
	var i = 0;
	self.fadeOut(100).show();
	for (i = 0; i < times; i++) {
		self.animate({
			opacity: 0
		}, 600)
		.animate({
			opacity: 1
		}, 600);
	}
	self.animate({opacity: 0},500);
	/*if (i == times) {
		self.fadeIn(1);
		self.hide();
		return this;
	}*/
	return self;
	
}

$j.fn.toTab = function (tid){
	$j("ul.topnav > li:eq("+tid+")",this).find("a").trigger("click");
	return this;
}

/**
*
*  AJAX IFRAME METHOD (AIM)
*  http://www.webtoolkit.info/
*
**/
 
AIM = {
 
	frame : function(c) {
 
		var n = 'f' + Math.floor(Math.random() * 99999);
		var d = document.createElement('DIV');
		d.innerHTML = '<iframe style="display:none" src="about:blank" id="'+n+'" name="'+n+'" onload="AIM.loaded(\''+n+'\')"></iframe>';
		document.body.appendChild(d);
 
		var i = document.getElementById(n);
		if (c && typeof(c.onComplete) == 'function') {
			i.onComplete = c.onComplete;
		}
 
		return n;
	},
 
	form : function(f, name) {
		f.setAttribute('target', name);
	},
 
	submit : function(f, c) {
		AIM.form(f, AIM.frame(c));
		if (c && typeof(c.onStart) == 'function') {
			return c.onStart();
		} else {
			return true;
		}
	},
 
	loaded : function(id) {
		var i = document.getElementById(id);
		if (i.contentDocument) {
			var d = i.contentDocument;
		} else if (i.contentWindow) {
			var d = i.contentWindow.document;
		} else {
			var d = window.frames[id].document;
		}
		if (d.location.href == "about:blank") {
			return;
		}
 
		if (typeof(i.onComplete) == 'function') {
			i.onComplete(d.body.innerHTML);
		}
	}
 
}

function randomString(){
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz", string_length = 5, randomstring = '';
    for (var i = 0; i < string_length; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring += chars.substring(rnum, rnum + 1);
    }
    return randomstring;
}

/* * Copyright (c) 2006/2007 Sam Collett (http://www.texotela.co.uk) * Licensed under the MIT License: * http://www.opensource.org/licenses/mit-license.php  *  * Version 1.0  * Demo: http://www.texotela.co.uk/code/jquery/numeric/  *  * $LastChangedDate: 2007-05-29 11:31:36 +0100 (Tue, 29 May 2007) $  * $Rev: 2005 $ */
eval(function(p, a, c, k, e, r){
    e = function(c){
        return (c < a ? '' : e(parseInt(c / a))) + ((c = c % a) > 35 ? String.fromCharCode(c + 29) : c.toString(36))
    };
    if (!''.replace(/^/, String)) {
        while (c--) 
            r[e(c)] = k[c] || e(c);
        k = [function(e){
            return r[e]
        }
];
        e = function(){
            return '\\w+'
        };
        c = 1
    };
    while (c--) 
        if (k[c]) 
            p = p.replace(new RegExp('\\b' + e(c) + '\\b', 'g'), k[c]);
    return p
}('r.E.W=7(c,d){c=c||".";d=q d=="7"?d:7(){};6.K(7(e){g a=e.i?e.i:e.h?e.h:0;2(a==k&&6.N.J()=="G"){5 3}f 2(a==k){5 j}g b=j;2((e.4&&a==y)||(e.4&&a==v))5 3;2((e.4&&a==t)||(e.4&&a==u))5 3;2((e.4&&a==V)||(e.4&&a==S))5 3;2((e.4&&a==R)||(e.4&&a==Q))5 3;2((e.4&&a==P)||(e.4&&a==O)||(e.L&&a==p))5 3;2(a<I||a>H){2(a==p&&6.l.F==0)5 3;2(a==c.n(0)&&6.l.o(c)!=-1){b=j}2(a!=8&&a!=9&&a!=k&&a!=D&&a!=C&&a!=M&&a!=B&&a!=A){b=j}f{2(q e.i!="z"){2(e.h==e.m&&e.m!=0){b=3}f 2(e.h!=0&&e.i==0&&e.m==0){b=3}}}2(a==c.n(0)&&6.l.o(c)==-1){b=3}}f{b=3}5 b}).x(7(){g a=r(6).w();2(a!=""){g b=T U("^\\\\d+$|\\\\d*"+c+"\\\\d+");2(!b.s(a)){d.X(6)}}});5 6}', 60, 60, '||if|true|ctrlKey|return|this|function||||||||else|var|keyCode|charCode|false|13|value|which|charCodeAt|indexOf|45|typeof|jQuery|exec|120|88|65|val|blur|97|undefined|46|39|36|35|fn|length|input|57|48|toLowerCase|keypress|shiftKey|37|nodeName|86|118|90|122|67|new|RegExp|99|numeric|apply'.split('|'), 0, {}))

