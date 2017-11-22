var  obj, filter, fist = false, garr, fl = 0, pfl = 0, filar = [], fchange, fstat,calendarFeild,rezo,dcl,
today,fakes,btr,count,heads,lets,qsaved,selects,calif,calwined,areas={row:[],col:[],subj:[]},$gtabs,stater,st_do,fields=[],dstp,
chex, rrr,st_upd=false,$smalltip=$j("#stip"),dmarker=false,tgt,rsip=false,today,aname='name', dw,aopen,tabevent=false,be;
var server;
if ($j.browser.msie) {be = "click";}else {be = "change";}if($j.browser.msie){aname='submitName';}
function datesoff(){$j("input.datepicker").val("");return false;}

var pager = function(){
    var self = this,ofc= new RegExp("offwall"),oftd= new RegExp("offview");
    this.currentPage = 0;this.numPerPage = 50;this.clastpage=0;
    var $table, $tbody, $thead,spre = "headerSort", $fb, lind = 0,$slist,thar=[],thl=0,offed=false,$lbody;
    this.$div = $j(".pgbs");
	this.liveObjs = [];	this.wmodean
    this.buts = [];this.numRows = 0;this.numPages = 0;this.allRows = [];this.cRows = [];this.filteredRows = [];this.lects = [];
	this.lectsv = [];this.lectsHTML = [];this.heads = [];this.sortMethods = [];this.curMethod = '';this.curKey = 0;this.curWay = 'desc';this.cleanSet = new RegExp("[\\s-]","g");
	this.visible = [];this.hiddenRows = [];
	this.head_active;this.sl_active;this.fillects;this.cellOldText;
    this.plurst = '';
	this.init = function(tid,mode){
        $j('#rtable').attr('style',$j('rtable').attr('style')+";empty-cells:show;border-collapse:separate;");
		if(server){
			this.numPerPage = this.numRow;
		}
        timeDiff.setStartTime();
		$table = $j("#" + tid).detach();
        $tbody = $j("tbody", $table);
        $thead = $j("thead", $table);
		self.wmode=mode;
        $fb = $j("#filbox");
		$slist=$j("#fil_stats");
        self.numRows = $j('tr',$tbody).length;
        self.numPerPage = self.numRows;
        self.numPages = Math.ceil(self.numRows / self.numPerPage);
		var $span=$j("<span/>")
			.append(["<div class='page_count' title='Total'>Records:&nbsp;<span class='xcnt'>",
						self.numRows,"</span></div>"].join(""))
			.append("<div class='navs first_page navdrt' title='First' data-drct='first'></div>")
			.append("<div class='navs prev_page navdrt' title='Previous' data-drct='prev'></div>")
			.append(["<div class='pinfor'><span class='curp'>" , (self.currentPage + 1) ,
					"</span> of <span class='totp'>" , (self.numPages) , "</span></div>"].join(""))
			.append("<div class='navs next_page navdrt' title='Next' data-drct='next'></div>")
			.append("<div class='navs last_page navdrt' title='Last' data-drct='last'></div>");

		$j(".navdrt",$span).click(function(){
			self.navgt(this.getAttribute("data-drct") );
		});

		
		$span/*.find(".navs").show().end()*/.appendTo(self.$div);
        $table.bind('repaginate', function(){
			var st = 0, lowend = (self.currentPage * self.numPerPage), highend = ((self.currentPage + 1) * self.numPerPage - 1), hiter = false,$lbody;
			//timeDiff.setStartTime();
					
			///var stsem=timeDiff.getDiff();
			var trar=$j('tr', $tbody),trl=trar.length;
			var row2show =[],row2hide=[];
			for(var i= 0; i < trl; i++){
				var $tr=$j(trar[i]);
				if (hiter === true) {
					//timeDiff.setStartTime();
					$j(trar).filter(":gt(" + (i - 1) + ")").addClass("offview");
					//alert(timeDiff.getDiff());
					i = trl + 1;
				}
				else {
					var cc = $tr.attr("class"), tcl=ofc.test(cc),todo = oftd.test(cc),trid ="#"+$tr.attr("id");
					if (tcl === false) {
						if (st >= lowend && st <= highend) {
							if (todo === true) {
								$tr.removeClass("offview");
							}
						}
						else
							if (todo === false) {
								$tr.addClass("offview");
							}
						++st;
						if (st > highend) {
							hiter = true;
						}
					}
				}
			}

			$tr=null;
			trar=null;
			trl=null;
			i=null;
			lowend=null;
			highend=null;
			self.arrows();
			//$tbody.appendTo($table);
			//self.evHandler(true);
			$lbody=null;
			fCleaner();
			//alert("1 = "+stsem+"; 2 = " + timeDiff.getDiff());
		});

        if (self.numPages > 1) {
            self.arrows();
        }
		thar=$j("tr:first > th", $thead);
		thl=thar.length;
		var firstRowColspan = $tbody.find("tr:eq(0)").find("td:eq(0)").attr("colspan");
		if(!isNaN(firstRowColspan) && firstRowColspan == thl){
			$table.appendTo("#mholder").show();
			return false;
		}
        self.collector(mode);
		//self.lector();
		$table
			.trigger('repaginate')
			.addClass("rtable");
		if(mode != 'site'){
			$table.disableSelection();
		}
        $j(".pagebox").show();
		$j(".cleanbox").show();
		acols = [];
		var colz=$j("colgroup > col", $table);
		var cl=colz.length;		
		for(var i=0;i < cl; i++){
			$j(colz[i]).attr("data-thid", i);
			acols.push(colz[i]);
		}		
		colz=null;
		cl=null;		
		if (mode === 'mas') {
			self.liveObjs.push(['"td.rowfdel", $tbody','mouseenter mouseleave',"self.rowfdel"]);
			//$j(".rowfdel:visible", $tbody).live('mouseenter mouseleave', function(e){});			
		}
		$table.appendTo("#mholder").show();
		self.evHandler(true);
		//alert(timeDiff.getDiff());
    };


	this.rowfdel = function(e){
		var hover = (e.type === 'mouseover' || e.type === 'mouseenter'), $this = $j(this);
		if (hover) {
			$j("div:lt(2)", $this).show();
			/*$j(".qeditor", this).live("click", function(e){
				var params = $j(this).attr("data-tbl").split("||");
				if (editArr[params[0]] == null) {
					return false;
				}
				else {
					var link = editArr[params[0]]['href'];
					for (var l = 0, t = editArr[params[0]]['vals'].length; l < t; l++) {
						var evals = editArr[params[0]]['vals'][l], vind = l + 1;
						link = link.replace("#" + evals + "#", params[vind]);
					}
					window.location.assign(link);
				}
			});*/
		}
		else {
			$j("div:lt(2)", $this).hide();
		//$j(".qeditor",this).unbind("click");
		}
	};

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
    };

    this.updatePages = function(){
        self.numPages = Math.ceil(self.numRows / self.numPerPage);
        $j(".pinfor").find("span.totp").text(self.numPages);
		$j(".xcnt").text(self.numRows);
        self.navgt('first');
    };
	
	this.evHandler = function(mode){
		var action='live';
		if(mode !== true){
			action='die';
		}
		if(self.liveObjs.length > 0){
			for(var i=0,j = self.liveObjs.length; i < j; i++){
				eval(['$j(',self.liveObjs[i][0],').',action ,'("',self.liveObjs[i][1],'",',self.liveObjs[i][2],')'].join(""));
			}
		}
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

	this.patchCell = function(/*cell,y, x,*/cell, dval,val,vval/*,cw*/){
		var btxt=trimView(vval),$zcell=$j(cell),
			y=$zcell.data("rid"),x=$zcell.data("col"),cw=$zcell.data("wid"),vprmode='common';
		var $tr = $zcell.closest('tr');
		var $th = $zcell.closest('table').find('th').eq($zcell.index());
		var fid = $th.attr("data-form");
		var fld = $th.attr("data-field");
		var did = $tr.attr("data-id");
		
		if($zcell.data("emode") == 'plural'){
			vprmode='plural';			
		}
		
		$zcell.empty().text(function(i, x){
			if(btxt.n == true){				
				$j(this).addClass("moreview");
				return btxt.s;
			}else{
				$j(this).removeClass("moreview");
				return vval;
			}			
		}).attr("data-text",vval).width(cw).data("wed",0);
		$j("body").css('cursor','progress');
		$j.ajax({
			type: 'post',
			url:  '/?m=outputs&suppressHeaders=1',
			data: 'mode=patch&x='+x+"&y="+y+"&val="+dval+"&fid="+fid+"&fld="+fld+"&did="+did+"&vval="+vval+'&vmd='+vprmode,
			success: function(data){
				if(trim(data) == 'ok'){
					$j(cell).cellType().addClass("ueds");
					self.allRows[y][x]=val;
					$j(self.allRows[y]['item']).find("td:eq("+x+")").text(function(i, x){
						if (btxt.n == true) {
							$j(this).addClass("moreview");
							return btxt.s;
						}else {
							$j(this).removeClass("moreview");
							return vval;
						}
					}).cellType().addClass("ueds");
				}else{
					alert("Changes not saved!");
				}
				$j("body").css('cursor','default');
			}
		});
	};


	this.delbAction = function(){
		if (confirm("You want delete this entry?")) {
			var $row = $j(this).parent().parent(), rid = $row.attr('id').replace('row_', '');
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
							self.visible = arrayElement('del',rid, self.visible);
							self.hiddenRows = arrayElement('add',rid, self.hiddenRows);
							/*var vispos = $j.inArray(rid, self.visible);
							if (vispos >= 0) {
								self.visible.splice(vispos, 1);
								self.hiddenRows.push(rid);
								self.hiddenRows.sort();
							}*/
						});
					}
				}
			});
		}
	};
	
	this.pluralPost = function(cell){
		var fid=$j(cell).find("form").attr("id"),
		formData = form2object(fid),viname,
		vval=[],rval=[],rvval=[],dbrid=$j(cell).parent().find("div.qeditor").attr("data-tbl").split("||")[2],
		col=$j(cell).data("col"), vcols=plur[col].columns,inames=plur[col].inames,hpos,vis=plur[col].visibility;
		if(formData.hasOwnProperty('dset')){
			for(var ix in formData.dset){
				for(var zix in formData.dset[ix]){
					rval.push(formData.dset[ix][zix]);
					hpos=$j.inArray(zix,inames);
					viname=$j.inArray(zix,inames);
					if(hpos >= 0){
						if(typeof vcols[hpos] == 'object'){
							rvval.push(vcols[hpos][formData.dset[ix][zix]]);
						}else if(vis[viname] === true){
							rvval.push(formData.dset[ix][zix]);
						}
					}
				}
				plur[col].data[dbrid][ix]=rval;
				vval.push(rvval.join("|"));
				rval=[];rvval=[];
			}			
		}
		this.patchCell(cell, JSON.stringify(formData),'',vval.join("; "));
	};
	
	this.pluralCancel = function(cell){	};
	
	this.dblAction = function(ev){
		var $ctr=$j(this).closest("tr"), rid = $ctr.attr("id").replace("row_", ''), colset = $ctr.find("td"),col=false,cell;
		/*if(this.tagName.toLowerCase() === 'td'){
			col=$j(colset).index(this);			
		}else if(this.tagName.toLowerCase() === "div"){
			var tdp=$j(this).closest("td");
			col=$j(colset).index(tdp);			
		}*/
		col=$j(colset).cellType().index(this);
		var cell = this,selt = '', cst = $j(cell).data("wed"),
		dbrid=$j(cell).parent().find("div.qeditor").attr("data-tbl").split("||")[1];
		if ((cst && cst == 1) || selects[col] === 'read-only') {
			return false;
		}
		else {
			$j(cell).data("wed", 1);
		}
		var cw = $j(this).width();
		cw -= 20;
		var txt = $j(cell).text(), $ued = null, nval2, nval, dtxt;
		self.cellOldText = {
			txt: txt,
			obj: cell			
		};
		
		$j(cell).data({
			wid: cw,
			'rid': rid,
			'col': col,
			'emode': 'common'	
		});
	
		dtxt = $j(cell).attr("data-text");
		dvalue = $j(cell).attr("data-value");
		if (dtxt && dtxt.length > 0/* && !txt*/) {
			txt = dtxt;
		} 
		txt=trim(txt);
		self.plurst=false;
		if (self.colType(col) != 'date' || selects[col] === 'plural') {
			$j(this).empty();
            if(selects[col] === 'plural'){
				//self.plurst=cell;
				var vpd=plur[col],vdat=vpd.data[dbrid],vcols=vpd.columns,aht,zval,xfid="ffrm_" +rid+ "_"+col;
				if(!vdat){
					$j(cell).data("wed", 0);					
					return false;					
				}
				$j(cell).data('emode','plural');
				var tab_edit=['<form id="',xfid,'"><input type="hidden" name="rindex" value="',dbrid,'"><table border=0><thead><tr>'],
					ths=vpd.header,tihs=vpd.inames,viss=vpd.visibility;
				for(var ith=0,ithe=ths.length; ith < ithe; ith++){
					if(viss[ith] === true){
						tab_edit+='<th>'+ths[ith]+'</th>';
					}else{
						tab_edit+='<th>&nbsp;</th>';
					}
				}
				tab_edit.push('</tr></thead><tbody>');
				for(var tl=0,te=vdat.length; tl < te; tl++){
					tab_edit.push('<tr>');
					for(tc=0,tce=vcols.length; tc< tce; tc++){
						(vdat[tl][tc] && vdat[tl][tc].length > 0) ? zval=vdat[tl][tc] : zval='';
						if(viss[tc] === false){
							aht=['<input type="hidden" name="dset.',tl,'.',tihs[tc],'" value="' , zval , '">'].join("");
						}else{
							if(vcols[tc] === 'date'){
								aht=['<input type="text" class="text mutedate" readonly="readonly" name="dset.',tl,'.',tihs[tc],'" value="',zval,'">'].join("");
							}else if(vcols[tc] === 'plain'){
								aht=['<input type="text" class="text" size="20" name="dset.',tl,'.',tihs[tc],'" value="', zval ,'">'].join("");
							}else if(typeof vcols[tc] === 'object'){
								var $ued = $j(["<div><select class='text' name='dset." ,tl, ".", tihs[tc] ,"' ></select></div>"].join("")),$ued1=$ued.find("select");
								//for (var i = 0,ll=vcols[tc].length; i < ll; i++) {
								for (var ix in vcols[tc]){
									if (ix == vdat[tl][tc]) {
										selt = "selected";
									}
									else {
										selt = '';
									}
									$j(["<option value='" , ix , "' " , selt , ">" , vcols[tc][ix] , "</option>"].join("")).appendTo($ued1);
								}
								aht=$ued.html();
							}
							
						}
						// end cell
						tab_edit.push('<td>'+aht+'</td>');
						aht='';
					}
					tab_edit.push('</tr>');
				}
				tab_edit.push('</tbody></table><br><input type="button" class="button bposte" value="Apply" >&nbsp;&nbsp;&nbsp;	<input type="button" class="button equit" value="Cancel" ></form>');
				
				$j(tab_edit.join("") ).find(".bposte").bind("click",function(e){
					self.pluralPost( cell );
				}).end()
				.find("input.equit").bind("click",function(e){
					$j(cell).empty().data("wed", 0).text(self.cellOldText.txt);
				})
				.end().appendTo(cell);
			}
			else if (selects[col] == 'plain' || selects[col] == 'unique' || selects[col] == 'calculateText') {
				$j([" <input type='text' value=\"" , txt , "\" class='edbox' style='width:" , (cw) , "px;'>"].join("")).bind('keypress', {
					obj: this
				}, function(e){                    
					var code = (e.keyCode ? e.keyCode : e.which);
					if (code == 13) {
						nval = $j(this).val();
						nval2 = nval;
						//self.patchCell(cell, rid, col, nval, nval, nval2, cw);
						self.patchCell(cell, /* rid, col,*/ nval, nval, nval2/*, cw*/);
					}
					else 
						if (code == 27) {
							$j(cell).empty().data("wed", 0).text(self.cellOldText.txt);
						}
				}).appendTo(cell).focus();
			}else if (selects[col] == 'numeric' || selects[col] == 'calculateNumeric') {
				$j([" <input type='number' value=\"" , txt , "\" class='edbox' style='width:" , (cw) , "px;'>"].join("")).bind('keypress', {
					obj: this
				}, function(e){                    
					var code = (e.keyCode ? e.keyCode : e.which);
					if (code == 13) {
						nval = $j(this).val();
						nval2 = nval;
						//self.patchCell(cell, rid, col, nval, nval, nval2, cw);
						self.patchCell(cell, /* rid, col,*/ nval, nval, nval2/*, cw*/);
					}
					else 
						if (code == 27) {
							$j(cell).empty().data("wed", 0).text(self.cellOldText.txt);
						}
				}).appendTo(cell).focus();
			}
			else 
				if (isArray(selects[col])) {
					if (multies[col] === false || !multies[col]) {
						funcact = cell.getAttribute('func-action');
						funcid = cell.getAttribute('func-id');
						//funcidchild = cell.getAttribute('func-id-child');
						//funcchild = cell.getAttribute('func-child'); 
						//funcact = funcact.replace("??", "'");
						//if(funcact != '')
							//funcact = "onchange='"+funcact+"'";
						parentid = cell.getAttribute('parent-id');
						mode = cell.getAttribute('mode');
						var selectvalues = null;
						var $ued = $j("<select class='dred_sel text' id='"+funcid+"' onchange='"+funcact+"'></select>");//style='width:" + (cw) + "px;'
						loading = false;
						if(parentid !=null && parentid !='' && mode !=null && mode !=''){
							
							var $td = $("td[func-id='"+parentid+"']");
							if($td != null){
								//if()
								data_value = $td.attr('data-value');
								
								if(data_value != ''){
									
									$j.get("?m=wizard&ccode="+data_value+"&mode="+mode+"&suppressHeaders=1", function (msg) {
										loading = true;
										if (msg && msg !== 'fail') {
											selectvalues = $j.parseJSON(msg);
											$ued.empty();
											$ued.append("<option value='-1'>- Select Commun -</option>");
											//$j(["<option value='" , key , "' " , selt , ">" , value , "</option>"].join("")).appendTo($ued);
											$.each(selectvalues, function(key,value){
												if (dvalue == key) {
													selt = "selected";
												}else {
													selt = '';
												}
												$j(["<option value='" , key , "' " , selt , ">" , value , "</option>"].join("")).appendTo($ued);
											});		
										}
										loading = false;
									});
								}
							}
						}
						
						
						var ll = selects[col].length;
						if(selectvalues == null && loading == false){
							$ued.empty();
							for (var i = 0; i < ll; i++) {
								if (dvalue == selects[col][i].v) {
									selt = "selected";
								}else {
									selt = '';
								}
								$j(["<option value='" , selects[col][i].r , "' " , selt , ">" , selects[col][i].v , "</option>"].join("")).appendTo($ued);
							}
					    }
						$ued.bind('change', function(e){
							var nv = $j(this).val();
							nval2 = $j(this).find("option[value=" + nv + "]").text();
							nval = nval2.toLowerCase();
							//self.patchCell(cell, rid, col, nv, nval, nval2, cw);
							self.patchCell(cell ,/* rid, col,*/ nv, nval, nval2/*, cw*/);
						}).bind('keypress', function(e){
							var code = (e.keyCode ? e.keyCode : e.which);
							if (code == 13) {
								var nv = $j(this).val();
								nval2 = $j(this).find("option[value=" + nv + "]").text();
								nval = nval2.toLowerCase();
								//self.patchCell(cell, rid, col, nv, nval, nval2, cw);
								self.patchCell(cell, /*, rid, col,*/ nv, nval, nval2/*, cw*/);
							}
							else 
								if (code == 27) {
									$j(cell).data("wed", 0).empty().text(txt);
								}
						}).appendTo(cell);
					}else 
						if (multies[col] == 'multi') {
							var $ued = $j("<div class='iisel'></div>"), rstr = '';
							var ll = selects[col].length, pretxt = txt.split(",");
							pretxt = $j.map(pretxt, function(a){
								return trim(a);
							});
							for (var i = 0; i < ll; i++) {
								var carr = selects[col], prest = '', tobj;
								if (typeof carr[i].v == "object" && carr[i].v  !== null) {
									prest = carr[i].r + '-';
									$j(["<b>" , carr[i]['v']['title'] , "</b><br>"].join("")).appendTo($ued);
									$j(carr[i]['v']['kids']).each(function(i, x){
										for (var s in x) {
											tobj = {
												v: x[s],
												r: prest + s
											};
											rstr = self.checkAdd(tobj, pretxt);
											$j(rstr).appendTo($ued);
										}
									});
									$j("<hr>").appendTo($ued);
									rstr = '';
								}
								else {
									rstr = self.checkAdd(selects[col][i], pretxt);
									$j(rstr).appendTo($ued);
								}
							}
							
							var $fbox = $j("<div/>",{"class": "footbox"})
								.append("<span class='lbutt fbutton' title='Cancel' onclick='gpgr.editCancel();' style='float:left;'>Cancel</span>")
								.append("<span class='lbutt fbutton bpost' title='Apply' style='float:right;right:15px;'> Apply</span>");
							$j(".bpost", $fbox).bind("click", function(e){
								var list = [], txt = [];
								$j(this).closest("div.outbox").find("div.iisel").find("label").each(function(i, x){
									$j(this).find("input:checked").each(function(){
										list.push($j(this).val());
										txt.push($j(this).parent().text());
									});
								});
								nval2 = txt.join(', ');
								nval = list.join(",");
								nv = nval;
								//self.patchCell(cell, rid, col, nv, nval, nval2, cw);
								self.patchCell(cell,/* rid, col,*/ nv, nval, nval2/*, cw*/);
								$j("#bselbox").remove();
							});
							
							var boff = $j(cell).offset();
							boff.left += $j(cell).width();
							boff.top += $j(cell).height();
							var $obox = $j("<div class='outbox' id='bselbox'></div>");
							$ued.appendTo($obox);
							$fbox.appendTo($obox);
							$obox.css({
								left: boff.left,
								top: boff.top
							}).appendTo("body").hide().fixPosition();
							$fbox.css("top", $ued.height() + 3);
						}
				}
		}
		else 
			if (self.colType(col) == 'date') {
				var odate = self.treatVal('date', txt);
				if (!odate || odate.length == 0) {
					odate = today;
				}                
				
				calif = randomString();
				$j("<input type='hidden' class='date_edit_" + calif + "'>").bind('refresh', function(w){
					nval = $j(".date_edit_" + calif).val();
					nval2 = nval.split("/").reverse().join("-");
					//nval2 = self.treatVal('date', nval);
					//self.patchCell(cell, rid, col, nval2, nval, nval2, cw);
					self.patchCell(cell,/* rid, col,*/ nval2, nval, nval2/*, cw*/);
				}).appendTo(cell);
				$j(cell).data("wed", 0);
				popCalendarEd(calif, odate);
			}
	};

    this.collector = function(mode){
    	
        //var grows = $j("tr", $tbody);
		self.sortMethods=heads;
		self.allRows=btr;
		self.lects=lets;

		var trst=$j("tr",$tbody), itd=trst.length,prow;
		while(itd--){
			prow=self.allRows[itd];
			self.allRows[itd]=null;
			prow['item']=$j(trst[itd]).clone();
			prow['fake']= mode === 'site' ? [] : fakes[itd];
			prow['hidden']=false;
			prow['uid']=itd;
			self.allRows[itd]=prow;
			self.visible.push(itd);
		}
		prow=null;

		if (mode === 'mas') {
			self.liveObjs.push(['".delbutt:visible",$tbody','click','self.delbAction']);			
			self.liveObjs.push(['".vcell:visible", $tbody','dblclick','self.dblAction']);		
		}
		btr=null;fakes=null;lets=null;grows=null;heads=null;        
		$j(".forsize",$thead)
			.live("mouseenter",function(e){
						if (!rsip) {
							$j(this).bind("mousemove", monPosR);
							redv = this;
						}
					})
			.live("mouseleave",function(e){
				var utime,cbu = $j(this).attr("data-thid");
				if (!rsip)
					$j(this).unbind('mousemove', monPosR);
				redv = false;
			});
		for(var hid=0; hid < thl;hid++){
			var cthis=thar[hid],txt = $j(cthis).text(),cw=$j(cthis).width();
			filar[hid]={methods:{},mvals:[],state:false};
			if (st_do <= 1) {
				fields.push({
					id: hid,
					title: $j(cthis).text(),
					//type: self.colType(hid),
					type: $j(cthis).attr("type"),
					parent: $j(cthis).attr("data-part"),
					field: $j(cthis).attr("data-field"),
					datatype: $j(cthis).attr("data-type"),
                    datasys: $j(cthis).attr("data-sys"),
					datatitle: $j(cthis).attr("data-title"),
					dataform: $j(cthis).attr("data-form")
				});
			}else{
				$j("#shome").html("<span class='note'>Cannot build stat table from multiple forms</span>");
			}

            $j(cthis)
			.bind('mouseenter', function(){
				var mp=$j(this).offset(), mw=$j(this).outerWidth();
                /*$j(".head_menu",this).addClass("head_menu_on").removeClass("head_menu_sort").css({
					left: ((mp.left+mw)-18),
					top: mp.top+1
				});*/
				/*$j(".hstat_menu",this).addClass("hstat_menu_on").removeClass("head_menu_sort").css({
					left: (mp.left+1),
					top: mp.top
				});*/
				//$j(this).addClass("head_act");
            })
			.bind("mouseleave",{hdi:hid}, function(x){
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
                });/*.end()
				.find("div.hstat_menu").each(function(){
					$j(this).removeClass("hstat_menu_on");
					if(getout){
						$j(this).addClass("head_menu_sort");
					}
				})*/
            })
			.bind("click", {
                'head_id': hid
            }, function(xd){
            	if($j(this).closest("th").index()!=0){
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
            	}
            })
			.data("ow",$j(cthis).width());
        }//);

		$j("div.head_menu",thar).live("click",function(df){
				self.headMenuWork(df);
        });  //).appendTo(cthis);
    };

	this.editCancel = function(){
		$j(this.cellOldText.obj).text(this.cellOldText.txt).data("wed",0);;
		$j("#bselbox").remove();
	};

	this.checkAdd = function(elem, arr){
		var selt='',res='',val;
		if (typeof elem == "object" && elem.hasOwnProperty("v")) {
			if ($j.inArray(elem.v, arr) >= 0) {
				selt = "checked";
			}
			val={0: elem.r,1: elem.v};
		}else{
			if ($j.inArray(elem, arr) >= 0) {
				selt = "checked";
			}
			val={0:elem.toLowerCase(), 1:elem};
		}
		return ["<label><input name='xcol[]' type='checkbox' value='",val[0],"' ",selt,">", val[1] ,"</label><br>"].join("");
	};

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
			cbon = filar[heid].state;
			cben = countMethods(heid) > 0;
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
	};

	this.showslist = function(hid){
		var rowe = $j.inArray(hid, areas.row), cole = $j.inArray(hid, areas.col), mr = {
			c: false,
			d: false
		}, mc = mr, $po = $j("#head_" + hid), pp = $po.offset(), 
		np = {
			x: pp.left,
			y: (pp.top + 28)
		};
		self.sl_active=$j(".hstat_menu",$po);
		self.sl_active.addClass("menu_stay");
		
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
	};

	this.recrute = function(ev, obj){
		var hid=$slist.data("key"),state=$j(obj).is(":checked"),cl=obj.className;
		cl=cl.replace("_check",'');
		if(state){
			areas[cl].push(hid);
		}else{
			var pos=$j.inArray(hid,areas[cl]);
			areas[cl].splice(pos,1);
		}
		sl_upd=true;
		self.showslist(hid);
	};

	this.cleaner = function (e){
		var obj=$j(e.target).parent(),hid=$j(obj).data("hid"),nname= $j(obj).closest("div.dgetter").attr("id");
		nname=nname.replace("box",'');
		if(nname=='r'){
			nname='row';
		}else if(nname=='c'){
			nname='col';
		}
		var pos=$j.inArray(hid,areas[nname]);
		areas[nname].splice(pos,1);

		$j(obj).closest("li").remove();
	};

	this.justHideMenu = function(){
		if($thead){
			$j("th > div",$thead).removeClass("head_menu_on hstat_menu_on menu_stay");
		}
		if ($fb) {
			$fb.hide();
		}
		if ($slist) {
			$slist.hide();
		}
	};

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
	};

	this.menuKiller = function(ev){
		var et=ev.currentTarget;
		var p1=$j(ev.target).closest("div.filter_box");
		if (et != self.head_active && (!p1 || p1.length == 0)) {
			self.closeMenu();
		}
	};

    this.findlect = function(key, arr){
        if (arr.length == 0) {
            return false;
        }
        else {            
            for (var i = 0,ll = arr.length; i < ll; i++) {
                if (arr[i] && arr[i].r == key) {
                    return true;
                }
            }
        }
    };

    this.lectSort = function(a, b){
        var x = a.r, y = b.r;
        return x - y;
    };

	this.getLects = function(i){
		return this.lects[i];
	};

	this.lector = function (i) {
		var ul, li, cb, sp;
		if (!self.lectsHTML[i]) {
			ul = $j("<ul class='tobs' id='outf'></ul>");
		}
		else {
			return false;
		}
		var ll = self.lects.length, frag = document.createDocumentFragment();
		li = $j("<li class='ffbc fil_line'></li>");
		cb = $j("<input type='checkbox'>");
		sp = $j("<span class='sline'></span>");
		var tar = self.lects[i], x = 0;
		//for (var x = 0; x < tar.length; x++) {
		for (var tx in tar) {
			if (tar.hasOwnProperty(tx)) {
				var val = tar[tx].r, vval = tar[tx].v;
				if (val || val == false) {
					//var t = $j(cb).clone(true);
					var t = $j(cb).clone(true);
					$j(t).bind(be, {
						tid:val,
						col:i
					}, function (x) {
						var st = $j(this).is(":checked"), tobj = this, cx = x.data.col;
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
						self.fillects = true;
						setTimeout(function () {
							self.runFilters();
							//self.pickLects();
							memo.toggle();
						}, 20);
					});
					$j(t).attr({
						"cact":val,
						'cact_id':tx
					});
					var t1 = $j(li).clone(true), t2 = $j(sp).clone(true).text(vval);
					if (val == false) {
						$j(t2).addClass("palebor");
					}
					$j(t1).append(t).append(t2);
					frag.appendChild(t1[0]);
				}
				++x;
			}
		}
		$j(ul)[0].appendChild(frag);
		$j(ul).disableSelection();
		self.lectsHTML[i] = ul;
		ul = null;
		frag = null;
		//}
	};

	this.pickLects = function(i){};

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
    };

    this.colVals = function(key){
        return $j(self.lectsHTML[key]).clone(true);
    };

    this.oppoWay = function(way){
        var nway;
        if (way == 'desc') {
            nway = 'asc';
        }
        else {
            nway = 'desc';
        }
        return nway;
    };

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
                $j("#head_" + z).removeClass([spre , "asc" , ' ', spre , "desc"].join("")).next().removeClass("head_menu_sort");
            }
            else {
                self.heads[z] = nway;
            }
        }
    };

    this.ifsort = function(way){
        self.curKey = $j("#filbox").data("skey");
        self.msort(self.curKey, way);
        self.hideMenu();
        filmter.hideAll();
    };

    this.hideMenu = function(){
		if (!self.fillects) {
			$fb.hide();
			filmter.hideAll();
		}
		self.fillects=false;
        $j(".head_menu", $thead).each(function(inx){
            $j(this).removeClass("head_menu_on menu_stay").data("cact", false).prev().removeClass("head_sel_act");
        });
    };

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
            var $lbody=$tbody.detach().empty(),cv = 0;
			self.visible=[];
            for (var i = 0, tll = tar.length; i < tll; i++) {
				var obj = tar[i], $nr = $j(obj['item']).clone(true);
				//var nr=tar[i]['item'];
				if (cv >= self.numPerPage) {
					$nr.addClass('offview');
				}
				else {
					$nr.removeClass('offview');
					cv++;
				}
				if (obj.hidden) {
					$nr.addClass('offwall');
				}
				else {
					self.visible.push(obj.uid);
				}
				$nr.prependTo($lbody);
			}
			$table.append($lbody);
            $j("#head_" + key).removeClass(spre + '' + self.oppoWay(way)).addClass(spre + way).find("div.head_menu",this).addClass("head_menu_sort");
			self.visible.reverse();
            self.navgt({
                data: {
                    action: 'first'
                }
            });
			tar=null;
            memo.toggle();
        }, 20);
    };

    this.iterer = function(a, b){
        var x = a[self.curKey], y = b[self.curKey], r1, r2, r3;
        if (isNaN(x) && self.curMethod !== 'string') {
            x = 0;
        }
        if (isNaN(y) && self.curMethod !== 'string') {
            y = 0;
        }
		if(x === false && y === false){
			return 0;
		}else if(x === false){
			return -1;
		}else if(y === false){
			return 1;
		}
        if (self.curWay === "desc") {
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
    };

    this.treatVal = function(way, val){
        if (way === 'int' || way === 'date') {
            if (val && val.length > 0) {
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
            if (way === 'float') {
                val = parseFloat(val);
            }
            else
                if (way === 'string') {
                    if (!val) {
                        val = '';
                    }
                    else {
                        val = trim(val.toLowerCase());
                    }
                }
        return val;
    };

    this.arrows = function(){
		if (self.numPages > 0) {
			if (self.currentPage > 0) {
				if ((self.currentPage - 1) >= 0) {
					$j(".prev_page", self.$div).removeClass("hide_butt");
				}
				else {
					$j(".prev_page", self.$div).addClass("hide_butt");
				}
				if (self.currentPage > 0) {
					$j(".first_page", self.$div).removeClass("hide_butt");
				}
				else {
					$j(".first_page", self.$div).addClass("hide_butt");
				}
			}
			else {
				$j(".prev_page", self.$div).addClass("hide_butt");
				$j(".first_page", self.$div).addClass("hide_butt");
			}
			if ((self.currentPage + 1) < self.numPages) {
				$j(".next_page", self.$div).removeClass("hide_butt");
			}
			else {
				$j(".next_page", self.$div).addClass("hide_butt");
			}
			if (self.currentPage < self.numPages - 1) {
				$j(".last_page", self.$div).removeClass("hide_butt");
			}
			else {
				$j(".last_page", self.$div).addClass("hide_butt");
			}
		}
	};

    this.navgt = function(met){
		self.clastpage=self.currentPage;
        switch (met) {
            case 'first':
                self.currentPage = 0;
                break;
            case 'last':
                self.currentPage = self.numPages - 1;
                break;
            case 'next':
				if (self.currentPage < (self.numPages - 1)) {
					++self.currentPage;
				}
                break;
            case 'prev':
				if (self.currentPage > 0) {
					--self.currentPage;
				}
                break;
            default:
                break;

        }
        self.hideMenu();
        $j(".pinfor").find("span.curp").text(self.currentPage + 1);
        $table.trigger('repaginate');
    };

	this.getVisibles = function(){
		//return this.visible;
		var useRowList = this.hiddenRows,
			rowCase = 'hidden';
		if(this.visible.length < this.hiddenRows.length){
			useRowList = this.visible;
			rowCase = 'visible';
		}
		return [useRowList, rowCase];
	};

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
	};
	
	this.saveToFile = function(){

		/*var fname=prompt("Please enter name for table file");
		if(fname === null){
			return false;
		}
		while(!fname || fname.length == 0 || trim(fname) == ''){
			fname=prompt("Please enter valid name for table file!");
			if(fname === null){
				return false;
			}
		}*/
		/*$dbox = $j("<div id=\'dbaddf\' title=\'Save To File\'></div>");
		 $dbox.append("<table>" +
				 "<tr><td>Query Name</td><td><input type='text' id='fnamefile'/></td></tr>"+
				 "<tr><td>Organisation Name</td><td><input type='text' id='fonamefile'/></td></tr>"+
				 "<tr><td>Beneficieries</td><td><input type='number' value='"+countall+"' readonly='readonly'/></td></tr>"+
				 "<tr><td>Description</td><td><textarea id='fdesc'></textarea></td></tr>"+
		 		"</table>");*/
         $dbox = $j("#dbaddf");
		 $dbox.dialog({
			 modal: true,
             resize: "auto",
			 resizable: false,
			 autoOpen: true,
			 buttons: {
			        "Save": function(){
			        	fname = 'test';
			    		$j("#stabboxfile").val(JSON.stringify(self.getVisibles()));
			    		/*document.savemefile.fname.value=$j("#fnamefile").val();
			    		document.savemefile.foname.value=$j("#fonamefile").val();
			    		document.savemefile.fdesc.value=$j("#fdesc").val();
			    		document.savemefile.ftotal.value=countall;
			    		document.savemefile.flink.value = window.location.href;*/
			    		if($j("#fsaveben").is(':checked')){
                            document.savemefile.is_save.value="1";
						}else{
                            document.savemefile.is_save.value="0";
						}

                        document.savemefile.qname.value=$j("#fnamefile").val();
                        document.savemefile.qdesc.value=$j("#fdesc").val();
                        document.savemefile.activity_id.value=$j("#ftask").val();
                        document.savemefile.sector_id.value=$j("#fsector").val();
                        document.savemefile.st_area_id.value=$j("#fst_area").val();
                        document.savemefile.beneficieries.value=$j("#fcountall").val();
                        document.savemefile.amount.value=$j("#famount").val();

			    		document.savemefile.submit();
			        },
			        "Cancel": function() {
                        $dbox.dialog( "close" );
			        }
			      },
		      close: function() {
                  this.dialog( "close" );
		    	  //document.savemefile.reset();
		        //allFields.removeClass( "ui-state-error" );
		      }
		 }).prev(".ui-dialog-titlebar").css("background","#aed0ea").css("border","1px solid #aed0ea");
	};

    this.chooseSet = function(urlData, type, data_item, project){

        $dbox = $j("#dbset");
        $dbox.dialog({
            modal: true,
            resize: "auto",
            resizable: false,
            autoOpen: true,
            buttons: {
                "Save": function(){
                    		setid = $j('#chooseset').val();
                    		describe = $j('#desc_dash_item').val();
							$j.ajax({
								type: "post",
								data: urlData+'&mode=grapher_save&type='+type+'&data_item='+JSON.stringify(data_item)+'&setid='+setid+'&describe='+describe+'&project='+project,
								url: "/?m=outputs&&suppressHeaders=1",
								success: function(msg){
                                    alert(msg);
                                    $dbox.dialog( "close" );
									//alert(msg)/*.delay(2000).fadeOut(3000)*/;
								}
							});
                },
                Cancel: function() {
                    this.dialog( "close" );
                }
            },
            close: function() {
                this.dialog( "close" );
                //document.savemefile.reset();
                //allFields.removeClass( "ui-state-error" );
            }
        }).prev(".ui-dialog-titlebar").css("background","#aed0ea").css("border","1px solid #aed0ea");
    };

	this.startss = function(){
		if (rrr > 0) {
			gpgr.justHideMenu();
			if (!stater) {
				stater = new sFrames();
			}
			stater.init();
			grapher.init();
			reporter.reget();
			$j("#tabs> ul > li:eq(3)").removeClass("tabs-disabled");

			sl_upd = false;
			$j("#tabs").toTab(3);
		}
	};
	
	this.mapss = function(){
		tyr = "Emmanuel";
		frt = 'Emmanuel';
		dic = {};
		dic[frt] = 9;
		
		if (rrr > 0) {
			/*gpgr.justHideMenu();
			if (!stater) {
				stater = new sFrames();
			}
			stater.init();
			grapher.init();
			reporter.reget();*/
			$j("#tabs> ul > li:eq(5)").removeClass("tabs-disabled");

			$j("#tabs").toTab(5);
			$('#map').show();
			map.invalidateSize();
			$('#map').appendTo($('#tabs-6'));
			
			//var $hapt =$j("#box-home");
			$j('#popupfield_table').append('<tr><th align="left"><label><input type="checkbox" id="chkall">Check/Uncheck all</label></th></tr>');
			$('th input:checkbox').click(function(e) {
				var table = $(e.target).closest('#popupfield_table');
				$('td input:checkbox', table).attr('checked', e.target.checked);
            });
			
			for(var i=0,fl=fields.length;i < fl; i++){
				$j('#maplon_select').append('<option value="wform_'+fields[i].dataform+'_'+fields[i].field+'">'+fields[i].parent+' : '+fields[i].title+'</option>');
				$j('#maplat_select').append('<option value="wform_'+fields[i].dataform+'_'+fields[i].field+'">'+fields[i].parent+' : '+fields[i].title+'</option>');
				$j('#mapdep_select').append('<option value="wform_'+fields[i].dataform+'_'+fields[i].field+'">'+fields[i].parent+' : '+fields[i].title+'</option>');
				$j('#mapcom_select').append('<option value="wform_'+fields[i].dataform+'_'+fields[i].field+'">'+fields[i].parent+' : '+fields[i].title+'</option>');
				$j('#mapsec_select').append('<option value="wform_'+fields[i].dataform+'_'+fields[i].field+'">'+fields[i].parent+' : '+fields[i].title+'</option>');
				//if(fields[i].datasys && (fields[i].datasys!='SysCommunes' || fields[i].datasys!='SysCommunes'){
					//$j('#mappingfield_table').append('<tr><td align="left"><label><input type="radio" name="mapping_field" value="'+fields[i].field+'">'+fields[i].title+'</label></td></tr>');
				//}
				if(fields[i].field!="")
					$j('#popupfield_table').append('<tr><td align="left"><label><input type="checkbox" class="datapopup" data-val="'+fields[i].title+'" name="popup_field[]" value="wform_'+fields[i].dataform+'_'+fields[i].field+'">'+fields[i].title+'</label></td></tr>');
				if((fields[i].datatype=='select' || fields[i].datatype=='calculateChoice') && fields[i].datasys != 'SysCommunes' && fields[i].datasys != 'SysDepartment')
					$j('#mtype_table').append('<tr><td align="left"><label><input type="radio" class="datamtype" data-val="'+fields[i].title+'" name="mtype_field" value="wform_'+fields[i].dataform+'_'+fields[i].field+'">'+fields[i].title+'</label></td></tr>');
				if((fields[i].datatype=='select' || fields[i].datatype=='calculateChoice') && fields[i].datasys != 'SysCommunes' && fields[i].datasys != 'SysDepartment'){
					this.addToMapsFilter(fields[i].dataform,fields[i].field,fields[i].title);
			    }
			}	
		}
	};
	
	this.addToMapsFilter = function(frmid,fld,title){
		$j.get("/?m=wizard&mode=loadFldValDistinct&fid="+frmid+"&fld="+fld+"&suppressHeaders=1", function (msg) {
			if (msg && msg !== 'fail') {
				msg = $j.parseJSON(msg);
				optss = '';
				optss = '<option value="">'+title+'</option>';
				$.each(msg, function(iii,e){
					optss += '<option value="'+e+'">'+e+'</option>';
				});	
				$j('#mfilter_table').append('<tr><td align="left"><select class="datamfilter" data-tmp="wform_'+frmid+'_'+fld+'">'+optss+'</select></td></tr>');
			}		
		});
	}

    this.runFilters = function(event){
        var tfs = 0, utext, odm = false, tr_del = false, killed = 0, i = 0, met = 0, alive = 0, tstr, t, zcl, sVal, tcl, once = false,wildCardPatt = new RegExp(regexEscape("#"), 'g');
		timeDiff.setStartTime();
		$lbody=$tbody.detach();
		self.visible=[];
	    self.hiddenRows = [];
		var fillength=filar.length,tlength=fillength;
        //$j(filar).each(function(er){
		while(tlength--){
            if (filar[tlength] && filar[tlength].state == true) {
                tfs++;
            }
        }//);
		var tar=$j("tr",$lbody);
        if (tfs == 0)  {
			var ltt = self.allRows.length;			
			$j(tar).removeClass("offwall");
			self.numRows=ltt;
			while(ltt--){
				self.allRows[ltt]['hidden'] = false;
				self.visible.push(ltt);
				/*var hidrow = $j.inArray(ltt, self.hiddenRows);
				if(hidrow >= 0){
					self.hiddenRows.splice(hidrow,1);
				}*/
				self.hiddenRows = arrayElement('del', ltt, self.hiddenRows);
			}
			self.updatePages();
			$lbody.appendTo($table);
			return;
		}
        self.cRows = [];
				
		for(var y=0,tl=tar.length; y < tl; y++){
        	var self1=tar[y],ind = self1.id.replace(/[^\d]+/g, ''),hits = 0,Row=self.allRows[ind];
			while(!Row){
				Row=self.allRows[++ind];
			}
			var fakes = Row['fake'],upret;        
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
							var sFilterTxt = regexEscape("#" + utext, "#").replace(wildCardPatt, '.*?');
							sFilterTxt = sFilterTxt || '.*';
							sFilterTxt = '^' + sFilterTxt;
							var filterPatt = new RegExp(sFilterTxt, "i");
							tcase = "str";
						}
						else
							if (ztype == 'date') {
								for (var usl in zmtds) {
									if(zmtds.hasOwnProperty(usl)){
										if (zmtds[usl] && zmtds[usl].r.length > 0) {
											myequ.push(usl + " " + zmtds[usl].r);
										}
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
									must.push([' == ' , pret , zvals[cv] , pret].join(""));
								}
							}
						}
						sVal = Row[iCC];
						var bMatch = true, bOddRow = true, smar = [],notArr=isArray(sVal),usVal;
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
								//sVal = dval;
								//Row[iCC] = dval;
							}else if(!isNaN(sVal) && !notArr){
								tstr = sVal+'';
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
									wt += [sVal , " " , myequ[zs] , " && "].join("");
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
										xt += [sVal[1] , ' ' , umv , ' || '].join("");
									}else{
										var lar=sVal[0];
										for(var ic=0,il=lar.length; ic < il; ic++){
											xt += [pret , lar[ic] , pret , ' ' , umv , ' || '].join("");
										}
									}
								}
								else {
									if (sVal == 'false') {
										xt += [sVal , ' ' , umv , ' || '].join("");
									}
									else {
										xt += [pret , sVal , pret , ' ' , umv , ' || '].join("");
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
					self.hiddenRows  = arrayElement('del',ind, self.hiddenRows);
					//}
					i = 0;
					alive++;
				}
				else {
					//if (!self.allRows[ind]['hidden']) {
					$j(self1,$lbody).addClass("offwall");
					self.allRows[ind]['hidden'] = true;
					self.hiddenRows = arrayElement('add',ind, self.hiddenRows);
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
		//alert(timeDiff.getDiff());
    }
};

function fCleaner(){
	var r=0,fll=filar.length;	
	while(fll--){		
		if(filar[fll].state == true){
			++r;
		}
	}
    if (r > 0) {
        $j(".fclean").attr("disabled", false);
        $j(".fmbox").css("background-position", "0px -13px");
        //$j(".fmbox").val('Search On');
    }
    else {
        $j(".fclean").attr("disabled", true);
        $j(".fmbox").css("background-position", "0px 0px");
        //$j(".fmbox").val('Search Off');
    }
}

function cleanAllF(){
	var fll=filar.length;	
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
		gpgr.hiddenRows = [];
	},10);
}

function progress(){
    this.msg = 'Loading...';
    this.mode = 0;
    this.$box = false;
}

progress.prototype.init = function(){
    this.$box = $j("#mbox").text(this.msg).center();
};

progress.prototype.toggle = function(){
    if (this.mode == 0) {
        this.$box.show();
        this.mode = 1;
    }
    else {
        this.$box.hide();
        this.mode = 0;
    }
};

progress.prototype.banner = function(ntxt){
    if (ntxt && ntxt.length > 0) {
        this.msg = ntxt;
    }
    else {
        this.msg = 'Rendering';
    }
    this.init();
};


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

    this.number_block = false;
    this.text_block = false;
    this.date_block =false;
    this.calendarField = '';
    this.$filters = $j("#fil_list");
    this.$uniques = $j("#filin_list");
	this.filterBox = document.createElement('input');
	this.dateBox = $j("<div class='dbox'></div>");
};

filtersClass.prototype.getFilters = function(){
    return filar;
};

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
		$j("#fil_on").attr({
			"checked":true,
			"disabled":false
			});
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
};

filtersClass.prototype.me = function(){
    return this;
};

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
};

filtersClass.prototype.init = function(){
    var self = this;
    $j(self.filterBox).attr('type', 'text').addClass('_filterText box').blur(function(){
        var ft = $j(this).val();
        if (ft.length > 0) {
            $j(this).removeClass("box").addClass("filter_work");
        }
        else {            
            var jmd = $j(this).attr("data-method");            
            filTool('', false, jmd, '');
            this.id = "";
            $j(this).removeClass("filter_work").addClass("box");
        }
    }).focus(function(){
        var $fpar = $j(this).parent().parent(),fid = $j("#filbox").data("skey"),xval = filar[fid].methods,self1 = this;
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
			["<input type='text' class='button ' style='width:100px;' disabled='disabled'>&nbsp;&nbsp;",
			"<div class='clfld'></div><a href='#' ><img src='/images/calendar.png' alt='Calendar' border='0'></a>",
			"<input type='hidden' name='' value=''>"].join(""));

	$j(this.dateBox).find("input[type=text]")
	.bind('refresh', function(){
        var self = filmter.me(),
        $dad = $j(this).parent(),
        $fpar = $dad.parent().parent(), ust,        
        fmtd = $j(this).removeClass("boxd").addClass("filter_work_date").attr("data-method"),
        mename = $j(this).attr(aname),
        hv = $dad.find("input["+aname+"='filter_" + mename + "']"),
        self1 = this,lval=this.value;        
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
        self.launchFilter(this);
    }).bind("cleanDate", {
        meobj: this
    }, function(x){
        var $me = $j(this),$tp = $me.parent(),cmtd = $me.attr("data-method");        
		$tp.eraser(false);                
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

    $t = $j("<li class='ffbb fil_line'></li>");
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
};

filtersClass.prototype.checkFilter = function(cbox){
    var self = this,area = $j("#filbox").data("skey");
	memo.toggle();
	setTimeout(function(){
		filar[area] = {
			methods: {},
			mvals: [],
			state: false
		};
		gpgr.runFilters();	
		memo.toggle();
	},50);
};

filtersClass.prototype.hideAll = function(){
    this.$uniques.hide();
    this.$filters.hide();
};

filtersClass.prototype.showfils = function(cdiv){
	this.hideAll();
    var self = this,tdsc = $j("#filbox").data("skey"),ftype, fdht = "", uval, ind, z = 0,
		ftype = gpgr.colType(tdsc),poss = $j(cdiv).offset(),posw = $j(cdiv).outerWidth(),lop = false, cval;
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
    self.$filters.css({
	    visibility  : 'collapse'
    }).html(fdht).show();

	var winHeight = $j(window).height(),winWidth = $j(window).width(),
		winTop = this.pageYOffset || $j.boxModel && document.documentElement.scrollTop || document.body.scrollTop,
		winLeft = this.pageXOffset || $j.boxModel && document.documentElement.scrollLeft || document.body.scrollLeft,
		winBottom = winHeight + winTop,
		winRight = winWidth + winLeft,
		docHeight = $j(document).height(),docWidth = $j(document).width(),deltay=0,deltax=0,
		newpos = {x: (poss.left + posw + 2), y: poss.top},
		elHeight = parseInt( self.$filters.height() ),
		elWidth =  parseInt( self.$filters.width() ),
		elBottom = newpos.y + elHeight,
		elMargin = (poss.left + posw + 2 ) + elWidth,
		percentage = 0, hiddenTop = 0, hiddenBottom = 0,hiddenLeft=0,hiddenRight = 0;
	if(newpos.x < winLeft ){
		hiddenLeft = winLeft - parseInt(newpos.x);
	}
	if(elMargin > winRight){
		hiddenRight = elMargin - winRight;
		if (hiddenRight > 5) {
			newpos.x = newpos.x - elWidth - posw - 5;
		}
	}
	if(hiddenLeft > 5){
		newpos.x = newpos.x + hiddenLeft + 20;
	}
	self.$filters.css({
        "left": newpos.x,
        "top": poss.top,
		"visibility": "visible"
    });
};

filtersClass.prototype.popCalendar = function(f){
    this.calendarField = f.data.fname;
    var idate = this.$filters.find("input["+aname+"='filter_" + this.calendarField + "']").val();
	if(!idate){
		idate=today;
	}
    window.open('index.php?m=public&a=calendar&dialog=1&callback=filmter.setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
};

filtersClass.prototype.setCalendar = function(idate, fdate){
    this.$filters
		.find(["input[",aname,"='filter_" , this.calendarField , "']"].join("")).val(idate).end()
		.find(["input[",aname,"='" , this.calendarField , "']"].join("")).val(fdate).trigger("refresh");
};

filtersClass.prototype.lects = function(cdiv){
    this.hideAll();
    var zkey = $j("#filbox").data("skey"),
    	$nht = gpgr.colVals(zkey),bkeys=filar[zkey].mvals;
    if (filar[zkey] && filar[zkey].mvals && filar[zkey].mvals.length > 0) {
		var tinar=$j("input", $nht), tl=tinar.length,cthis,cv;
        //.each(function(){
		while(tl--){
			cthis=tinar[tl];
            cv = $j(cthis).attr('cact');
            if ($j.inArray(cv, bkeys) >= 0) {
                $j(cthis).attr("checked", true);
            }
        }
    }
	bkeys=null;
	tinar=null;
	cthis=null;
    var poss = $j(cdiv).offset(), posw = $j(cdiv).outerWidth(),
        winHeight = $j(window).height(),winWidth = $j(window).width(),
		winTop = this.pageYOffset || $j.boxModel && document.documentElement.scrollTop || document.body.scrollTop,
		winLeft = this.pageXOffset || $j.boxModel && document.documentElement.scrollLeft || document.body.scrollLeft,
		winBottom = winHeight + winTop,
		winRight = winWidth + winLeft,
		docHeight = $j(document).height(),docWidth = $j(document).width(),deltay=0,deltax=0,
        $filinList = $j("div#filin_list");

	$filinList
		    .css({visibility: "collapse"})
		    .html($nht).show();

	var newpos = {x: (poss.left + posw + 2), y: poss.top},
		elHeight = parseInt( $filinList.height() ),
		elWidth =  parseInt( $filinList.width() ),
		elBottom = newpos.y + elHeight,
		elMargin = (poss.left + posw + 2 ) + elWidth,
		percentage = 0, hiddenTop = 0, hiddenBottom = 0,hiddenLeft=0,hiddenRight = 0;
	if(newpos.x < winLeft ){
		hiddenLeft = winLeft - parseInt(newpos.x);
	}

	if(elMargin > winRight){
		hiddenRight = elMargin - winRight;
		if (hiddenRight > 5) {
			newpos.x = newpos.x - elWidth - posw - 5;
		}
	}

	if(hiddenLeft > 5){
		newpos.x = newpos.x + hiddenLeft + 20;
	}


    $filinList		  
		    .css({
                "left": newpos.x,
                "top": poss.top,
	            "visibility": 'visible'
            });
};

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
		self.doSave(['mode=query&filters=' , fils , '&qname=' , qnm , '&qdesc=' , qdsc , "&imode=" , this.mode , "&sid=" , this.cid ,"&actvs=" ,  $j("#ashow").is(":checked")].join(""));
	
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
};

saveClass.prototype.saveDialog = function(){
	var t= "Save current query",name='',desc='',id=0,self=this;
	$j(["<div title='" , t , "' id='dbox' class='diabox'>Name:&nbsp;",
		"<input type='text' style='border: 1px solid black; width: 150px;' id='qname' class='qncl' value='" , name , "'>"+
		"<br>Description: <textarea cols='34' rows='2' id='qdesc' class='qdcl'>" , desc , "</textarea><br>" ,
		"<input type='hidden' id='quid' value='" , id , "'>" ,
		this.extra,
		"<input type='button' class='button' value='Save' >&nbsp;&nbsp;" ,
		"<input type='button' class='button' id='dbox-kill' value='Cancel' onclick='$j(\"#dbox\").dialog(\"close\").remove();'>",
		"<div id='slogo' class='saving'></div>",
		"</div>"].join(""))
	.dialog({
			resizable: false,
			width: 350
		})
	.find("input.button:eq(0)").click(function(e){
		self.saveQuery(false);
	}).end()
	.show();
};

saveClass.prototype.closeEdit = function  (){
	dmarker=0;
	$j("#debox").dialog("close").hide();
};

saveClass.prototype.dialogNote = function(txt){
	$j("#slogo").add(".saving").addClass("savewarn")
			.fadeOut(0).text(txt).show()
		.fadeIn(500,function(){
				$j(this)
					.fadeOut(2500,function(){$j(this).text("").fadeIn(0);})});
};

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
	var dst=["imode=edit&mode=query&qname=", vals.name ,'&qdesc=',vals.desc,
			'&sdate=',vals.srdate ,"&edate=",vals.erdate,"&sid=",vals.id,"&stype=",vals.stype,"&showr=",vals.showr].join("");
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
							tvr=trimView(vals.name);
							if(tvr.n === true){
								$tdo.addClass("moreview");
							}else{
								$tdo.removeClass("moreview");
							}
							$tdo.find("span").text(tvr.s);
							break;
						case 3:
							$tdo.attr("data-text",vals.desc);
							tvr=trimView(vals.desc);
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
};

saveClass.prototype.viewDate = function(date){
	var pz,res=0;
	if (typeof date  !== "undefined" && date != 0 && date.length == 10) {
		pz = date.split("/");
		//res=pz[2]+pz[1]+pz[0];
		res = pz.reverse().join("");
	}
	return res;
};

saveClass.prototype.add2Table = function(rdata){
	var self=this;
	var $qtable=$j("#qtable").detach();
	var nl=$j("tbody > tr",$qtable).length;
	var tvn=trimView(rdata.name);
	var qurerAction = 'run';
	if(!rdata.hasOwnProperty("desc")){
		rdata.desc ='';
	}
	var tvd=trimView(rdata.desc);
	var rtgt,ztype='qeditor',row_class="generic_row";
	if(rdata.type == 'Report'){
		ztype='qreditor';
	}else if(rdata.type == 'Report Item'){
		ztype='qieditor';
		row_class="rep-item_row";
		qurerAction = 'rview';
		//nl=rdata.id;
	}

	var $newRow = $j("<tr/>",{
		"id"        : "qsr_" + nl,
		"class"     : row_class,
		"data-showr": rdata.brest
	})
		//.append(["<!--<td title='Edit' align='center'><div data-id='",rdata.id,"' class='",( ztype ),"'></div></td>-->"].join(""))
		.append(["<td ",(tvn.n ===true? 'class="moreview"' : "") ," data-text='",rdata.name,"' title = '"+rdata.name+"'  data-id='"+rdata.id+"' ><span class='fhref flink' onclick='qurer.run(\"",nl,"\",\"", qurerAction ,"\");' >", tvn.s ,"</span></td>"].join(""))
		.append("<td align='center'>"+(rdata.hasOwnProperty("type") ? rdata.type : "&nbsp;")+"</td>")
		.append("<td align='center'>"+(rdata.hasOwnProperty("itemType") ? rdata.itemType : "&nbsp;")+"</td>") //added for report items extra column
		.append(["<td ",(tvd.n ===true? 'class="moreview"' : "")  ," data-text='",rdata.desc,"'>",'<input type="hidden" id="start_',nl,'" value="',self.viewDate(rdata.sdate),'">', '<input type="hidden" id="end_', nl, '" value="', self.viewDate(rdata.edate)+ '">', tvd.s ,"</td>"].join(""))
		/*.append(['<td ><div class="tdw">',
				(rdata.hasOwnProperty("sdate") ?
					['<div class="stdw">',
						( rdata.sdate.length > 0 ? rdata.sdate : 'N/D&nbsp;' ),
						'</div><img width="16" height="16" border="0" alt="Calendar" src="/images/calendar.png" class="calpic" onclick="popTCalendar(\'start_', nl , '\')"></a>',
						'<input type="hidden" id="start_',nl,'" value="',self.viewDate(rdata.sdate),'"></div>'
					].join("")
					: ""),
				'</td>'
			].join(""))//onclick="popTCalendar(\'start_',nl ,  '\')"
		.append(['<td ><div class="tdw">',
			(rdata.hasOwnProperty("edate") ?
					['<div class="stdw">',
						( rdata.edate.length > 0 ? rdata.edate : 'N/D&nbsp;' ),
					'</div><img width="16" height="16" border="0" alt="Calendar" src="/images/calendar.png" class="calpic" onclick="popTCalendar(\'end_', nl , '\')">',
					'<input type="hidden" id="end_', nl, '" value="', self.viewDate(rdata.edate)+ '"></div>'].join("") : "")
			,'</td>'].join(""))//onclick="popTCalendar(\'end_',nl ,  '\')"
		*/
		//.append('<td ><span title="Run" class="fhref" <img src="/images/run1.png" weight=22 height=22 border=0 alt="Run"></span></td>')
		.append(['<td align="center"><span title="Delete" style="color: blue;font-size: large" class="fhref fa fa-trash-o" onclick="qurer.delq(\'',nl,'\');" ><!--<img src="/images/delete1.png" width="16" height="16" border=0 alt="Delete">--></span> <span title="Export" style="color: blue;font-size: large" class="exportq fa fa-download" onclick="qurer.run(\'',nl,'\',\'export\');" ></span></td>'].join(""));
		//.append(['<td align="center"><div title="Export" style="color: blue;font-size: large" class="exportq fa fa-download" onclick="qurer.run(\'',nl,'\',\'export\');" ></div></td>'].join(""));
		
	if(rdata.eaction && rdata.eaction == 'update'){
		$j("tbody tr",$qtable).each(function(){
			if($j(this).find("td:eq(0) > div").attr("data-id") == rdata.id){
				rtgt = this;
			}
		});
		if(rtgt){
			$j(rtgt).replaceWith($newRow);
		}
	}else{
		$newRow.appendTo($qtable);
	}
	
	$qtable.insertAfter("#importbox");
	if(!tabevent){
		addQTlook();
	}

		$j("#qtable").trigger("update");

};

function trimView(str,xlength){
	var res={};
	if(!xlength){
		xlength = 45;
	}
	if(str && str.length > xlength){
		res={
			n:true,
			s: ''
		};
		var words=str.split(" "),clen=0,ind=0;
		while(clen < xlength){
			var nast=words[ind]+' ';
			res.s+=nast;
			clen+=nast.length;
			ind++;
		}
		if(res.s.length > xlength){
			res.s=res.s.slice(0,xlength);
		}
		if(res.s.length < str.length){
			res.s+='...';
		}
	}else{
		res={n: false,s: str}
	}
	return res;
}

function extend(Child, Parent) {
	var F = function() { }
	F.prototype = Parent.prototype;
	Child.prototype = new F();
	Child.prototype.constructor = Child;
	Child.superclass = Parent.prototype;
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
};

qlHandler.prototype.update = function(){
	var cv=this.$sl.val();
	if(cv > 0 ){
		this.$buts.show();
	}else{
		this.$buts.hide();
	}
};

qlHandler.prototype.reporter = function(start,end,id,kadze){
	var nw=window.open(['/?m=outputs&a=reports&mode=compile&itid=' ,id , "&ds=",start,"&de=",end,"&kadze=",(kadze ? "kami" : "")].join(""), 'reportwin');
	nw.focus();
};

qlHandler.prototype.run = function(cv,todo){
	//var cv=this.$sl.val();
	// alert(cv);
	if(!isNaN(cv) && cv >= 0){
		rowdbID = 0;
		var $uv = $j("#qsr_" + cv, $j("#qtable")[0]);//,

				// rowdbID = $uv.find("td:eq(0) > div").attr("data-id");
		var rowdbID = $uv.find("td:eq(0)").attr("data-id");//Correction puisqu'il n'y avait pas de div
		// console.log(rowdbID);
		// console.log(todo);
		if (todo === 'rview' ) {
			window.open("/?m=outputs&a=reports&mode=item_view&iid=" + rowdbID, "_blank");
			return;
		}

		var qbegin=null;//$j("#start_" + cv).val().length > 0 ? $j("#start_" + cv).val() : null,
			qend=null;//$j("#end_" + cv).val().length > 0 ? $j("#end_" + cv).val() : null,
			//console.log($j("td:eq(2)", $uv).html());
			majorType = $j("td:eq(1)", $uv).text().toLowerCase();
		// alert(majorType);
		if(parseInt(qbegin) === 0){
			qbegin = null;
		}
		if(parseInt(qend) === 0){
			qend = null;
		}
		if(qbegin !== null && qend !== null && qbegin > qend){
			alert("You have configured query with wrong date parameters.\n End date can't be earlier than Start date.");
			return false;
		}

		if (majorType == 'report' && todo !== 'export') {
			//this.reporter(qbegin,qend,$j(".qreditor", $uv).attr("data-id"));
			this.reporter(qbegin,qend,rowdbID);
		}
		else {
			/*
			 * document.xform.filter_beginner.value=$j("#start_"+cv).val();
	 		 *	document.xform.filter_finisher.value=$j("#end_"+cv).val();
	 		*/
			//document.xform.beginner.value = qbegin;//"lala";
			//document.xform.finisher.value = qend;//"lala";
			document.xform.stype.value = $uv.find("td:eq(2)").text();
			document.xform.qsid.value = rowdbID;
			document.xform.faction.value = todo;
			document.xform.action = "/?m=outputs";
			if (todo == 'export') {
				// alert("In export");
				$j("#sendAll").attr("action", function(i, v){
					return v + "&suppressHeaders=1";
				})
			}
			
			document.xform.submit();
		}
	}
};

qlHandler.prototype.delq = function(cv){
	if(confirm("You want delete this query ?")){		
		var $qr=$j("#qsr_"+cv,$j("#qtable")[0]),
			data=['mode=query&imode=del&stype=',$qr.find("td:eq(1)").text(),
				'&sid=',parseInt($qr.find("td:eq(0)").attr("data-id"))].join("");
		$j.ajax({
			url: "/?m=outputs&suppressHeaders=1",
			type: 'post',
			data: data,
			success: function(data){
				console.log(data);
				if(trim(data) == 'ok'){
					info("Query deleted ",1);
					$qr.fadeOut('fast',function(){
						$qr.remove();
					});
				}else{
					info("Failed to delete query, try again later",0);
				}
			},
			error: function (error) {
                console.log(error);
            }
		});
	}else{
		return false;
	}
};

qlHandler.prototype.findEntry = function(id){
	var ql = qsaved.length;
	for (var i = 0; i < ql; i++) {
		if (qsaved[i].id == id) {
			return i;

		}
	}
};

qlHandler.prototype.edit = function(){
	var nid=this.$sl.val();
	if (nid > 0) {
		this.cid=nid;
		this.mode='edit';
		var ri=this.findEntry(nid);
		$j("#qname").val(qsaved[ri].name);
		$j("#qdesc").val(qsaved[ri].qdesc);
	}
};

qlHandler.prototype.del = function(){
	var nid=$j(obj).val();
	if (nid > 0) {
		this.cid=nid;
		this.mode='edit';
		this.saveQuery();
	}
};

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
					if (parseInt(data) > 0) {						
						self.mode = 'save';
						self.cid = 0;
						self.$boxer.dialog('close');
						var t={'data':data,'type':'Table'};
						//self.saveQuery(t);
					}
				}
			}
			$j("#slogo").hide();
			return false;
		}
	});
};

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
};

qlHandler.prototype.extractRowSet = function(txt){
    var msg,msg_class;
    if (txt != 'fail') {
        txt = $j.parseJSON(txt);
        //qurer.add2Table(txt);
        $j('#setbox').toggle();
        msg="Set saved";
        msg_class="msg_ok";
        console.log(txt);
        //$j("#settable").
		var $newRow = $j("<tr/>");
        $newRow.append('<td>'+txt.set+'</td>');
        $newRow.append('<td><span id="set_d_'+txt.id+'" onclick="qurer.delSetRow('+txt.id+',\''+txt.set+'\')"  class="fhref fa fa-trash-o" style="color: blue;font-size: large"></span><span id="set_e_'+txt.id+'"  class="fhref fa fa-edit" style="color: blue;font-size: large"></span>');
        $newRow.append('</td>');
        $newRow.appendTo($j("#settable"));
        /*<span id="set_e_'.$set['id'].'"  class="fhref fa fa-edit" style="color: blue;font-size: large"></span>
            </td>');
*/
            /*<tr>

        <td>
        <span id="set_d_'.$set['id'].'" onclick="qurer.delSetRow('.$set['id'].',\''.$set['setname'].'\')"  class="fhref fa fa-trash-o" style="color: blue;font-size: large"></span>
            <span id="set_e_'.$set['id'].'"  class="fhref fa fa-edit" style="color: blue;font-size: large"></span>
            </td>
            </tr>*/
    }else{
        msg="set not saved";
        msg_class="msg_bad";
    }
    $j("#msg_place").addClass(msg_class).html(msg).show().delay(3000).fadeOut(2000,function(){
        $j(this).removeClass(msg_class).hide();
    });
    $j("#setbox").find("input:eq(0)").val("");
};

qlHandler.prototype.delSetRow = function(id,setname){
    fname=prompt("Please enter the set name");
    if(trim(setname)==trim(fname)) {
        var $ritem = $j("#set_d_"+id).closest("tr");
        $j.get("/?m=outputs&mode=delSet&suppressHeaders=1&id=" + id, function (res) {
            if (res && res != 'fail') {
                $ritem.fadeOut('slow', function () {
                    $ritem.remove();
                });
            }
        });
    }else {
    	alert("can't delete");
	}
};

qlHandler.prototype.edit = function(id_edit,nomSet){
	$("#setModal .modal-header h2").text('Modification du Set : '+nomSet);
	$j.ajax({
		type: 'get',
		url:  '/?m=outputs&a=reports&mode=getSetElements&suppressHeaders=1&set_id='+id_edit,
		success: function(data){
			var elms = JSON.parse(data);
			if(elms.status == 'success' && elms.value.length>0){
				$("#setModal table tbody").empty();
				for(var i=0;i <elms.value.length ; i++){
					var titre = (elms.value[i].title == null) ? '[NaN]' : elms.value[i].title;
					$("#setModal table tbody").append(
						'<tr>' +
							'<td id="fromGraph_'+elms.value[i].id+'">'+titre+'</td>' +
							'<td>'+elms.value[i].type+'</td>' +
							'<td><a href="#" onclick="qurer.delElms('+elms.value[i].id+')">Delete</a></td>' +
						'</tr>');
				}

			}else{
				console.log("Pas de donnees");
			}
		}});
	var getModal = $("#setModal");
	getModal.css("display","block");
	$("#closeSetModal").click(function(){
		getModal.css("display","none");
	});
};

qlHandler.prototype.delElms = function(idElms){
	$j.ajax({
		type: 'get',
		url:  '/?m=outputs&a=reports&mode=getSetElements&suppressHeaders=1&toDel='+idElms,
		success: function(data){
			if(data == 'ok'){
				alert("L'element a ete enleve du SET avec success");

				$("#fromGraph_"+idElms).closest("tr").remove();
			}
		}});
};
qlHandler.prototype.addElms = function(){
		alert("En developpement");
};

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
};
//$(document).ready(function($) {
	//map.invalidateSize();
	/*$('#tabs').on('tabs-6', function(event, ui) {
		map.invalidateSize();
	});*/
//});
function tabPrepare(stg){
	if(stg){
		tgt=stg;
	}
	$j("#tabs").tabs().show().toTab(tgt);
	//setTimeout(function() {
	    //map.invalidateSize();
	    //alert("invalidateSize");
	 //}, 1000);
	
	
	
	//map.invalidateSize();
	//var container = $j("#tabs"); // this = 'tabs' div
    //L.DomEvent.on(container, 'mousedown', L.DomEvent.stopPropagation).on(container, 'doubleclick', L.DomEvent.stopPropagation).on(container, 'click', L.DomEvent.stopPropagation);

}

function prePage(mode){
	shadow = $j("#shadow");
	$j(shadow).fadeTo(1, 0.5).hide();
	dw=($j(document).width()+'');
	dw=dw.replace(/\d\d$/,"");
	$j(".mtab").width(dw+'00');

	$j(".moretable").delegate(".moreview", 'mouseenter mouseleave', function (e) {
		var hover = (e.type === 'mouseenter');
		var mpar = $j(this).closest("tr").attr('id');
		if (hover) {
			var xp = $j(this).offset(), npos = {x:e.pageX, y:e.pageY}, npos0 = cloneThis(npos),
					winHeight = $j(window).height(), winWidth = $j(window).width(),
					winTop = this.pageYOffset || $j.boxModel && document.documentElement.scrollTop || document.body.scrollTop,
					winLeft = this.pageXOffset || $j.boxModel && document.documentElement.scrollLeft || document.body.scrollLeft,
					winBottom = winHeight + winTop,
					winRight = winWidth + winLeft,
					docHeight = $j(document).height(), docWidth = $j(document).width(), deltay = 0, deltax = 0;
			if (docHeight > winHeight) {
				deltay = -20;
			}
			if (docWidth > winWidth) {
				deltax = -20;
			}

			winBottom += deltay;
			winRight += deltax;

			// Get element top offset and height
			$smalltip
					.html($j(this).attr("data-text"))
					.css({visibility:"collapse"})
					.data("current", $j(this).parent().attr('id'))
					.show();
			var elTop = npos.y,
					elHeight = parseInt($smalltip.height()),
					elWidth = parseInt($smalltip.width()),
					elBottom = elTop + elHeight,
					elMargin = npos.x + $smalltip.width(),
					percentage = 0, hiddenTop = 0, hiddenBottom = 0, hiddenLeft = 0, hiddenRight = 0;

			// Get percentage of unviewable area
			if (xp.top < winTop) // Area above the viewport
				hiddenTop = winTop - xp.top;
			if (elBottom > winBottom) // Area below the viewport
				hiddenBottom = elBottom - winBottom;


			if (hiddenBottom > 5) {
				npos.y = npos.y - (hiddenBottom * 1.2);
			} else if (hiddenTop > 5) {
				npos.y = npos.y + (hiddenTop * 1.2);
			}

			if ((npos.x < winLeft )) {
				hiddenLeft = winLeft - parseInt(npos.x);
			}

			if (elMargin > winRight) {
				hiddenRight = elMargin - winRight;
				if (hiddenRight > 5) {
					npos.x = npos.x - elWidth - 20;
				}
			}

			if (hiddenLeft > 5) {
				npos.x = npos.x + hiddenLeft + 20;
			}

			var xpdelta = {x:(npos0.x - npos.x) + 5, y:(npos0.y - npos.y) + 5};
			$smalltip
					.css({
						left:npos.x,
						top:npos.y,
						visibility:"visible"
					});

			$j(this).add("*", this).bind("mousemove", {pdelta:xpdelta}, function (e) {
				$smalltip.css({
					left:e.pageX - (e.data.pdelta.x - 15),
					top:e.pageY - (e.data.pdelta.y + 5) + 15
				});
			});
		} else {
			$smalltip.hide();
		}
	});


	if (mode === 'site') {
		memo.init();
		memo.toggle();
		filmter.init();/**/
		gpgr.init('rtable', mode);
		$j("#rtable").tableStripe();
		return;
	}

	if(mode === undefined){
		mode='mas';
	}
	/*************TEST BLOCK ***************************/
	stater = new sFrames();
	/*if(rrr > 0){
		stater.init();
		grapher.init();
	}*/
	if(mode == 'out')
		reporter.reget();



	/************* END OF TEST BLOCK ******************/
	if (rrr > 0 || multistart) {
        $fcol = $j("#folder");
		if (mode == 'mas') {
			$j("<div style='float:left;'>Forms<div class='colic'></div></div>").data("mode", 'off').click(function(){
				toggleForms(this);
			}).insertBefore($fcol);
			$j(".mutedate").live("click",function(e){
				popPCalendar(this);
			});
		}
        memo.init();
        memo.toggle();
        setTimeout(function(){
            filmter.init();
            gpgr.init('rtable',mode);
			if (tgt == 3) {
				$j("#tabs > ul > li:eq(3)").removeClass("tabs-disabled");
				stater = stater ? stater : new sFrames;
				stater.init();
				grapher.init();								
				if(multistart !== false &&  multistart > 0){
					tabPrepare(tgt);
					stater.run();
					if(multistart === 2 && chartMode){					
						//launch graph build with saved parameters
						grapher.inject(chartMode);
					}
				}
				reporter.init();
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


	//$j(".qeditor",$j("#qtable")[0]).live('click',function(e){
	// 	$j($j("#qtable")[0]).live('click',function(e){
	// 		var $tr = $j(this).closest("tr"),
	// 			qid = $j(this).attr("data-id"),
	// 			qname, qdesc, qstart = {}, qend = {},zmode,
	// 			tds=$j("td", $tr),str;
	// 		for(var i=0,j=tds.length; i < j; i++){
	// 		//$j("td", $tr).each(function(i){
	// 			var $tdo=$j(tds[i]);
	// 			switch (i) {
	// 				case 1:
	// 					qname = $tdo.attr("data-text");
	// 					break;
	// 				case 2:
	// 					zmode= $tdo.text().toLowerCase();
	// 					break;
	// 				case 3:
	// 					qdesc = $tdo.attr("data-text");
	// 					break;
	// 				case 4:
	// 					qstart.r = $j("input", $tdo).val();
	// 					qstart.v = $j(".stdw", $tdo).text();
	// 					if(trim(qstart.v) == "N/D"){
	// 						qstart={
	// 							r:0,
	// 							v:''
	// 						}
	// 					}
	// 					break;
	// 				case 5:
	// 					qend.r = $j("input", $tdo).val();
	// 					qend.v = $j(".stdw", $tdo).text();
	// 					if(trim(qend.v) == "N/D"){
	// 						qend={
	// 							r:0,
	// 							v:''
	// 						}
	// 					}
	// 					break;
	// 				default:
	// 					break;
	// 			}
	// 		}//);
	// 		var $zd=$j("#debox").dialog({
	// 			width: 350,
	// 			height: 270,
	// 			resizable: false
	// 		}).find("#qname").val(qname).end()
	// 			.find("#qdesc").val(qdesc).end()
	// 			.find("#qstart_date").val(qstart.v).end()
	// 			.find("#qend_date").val(qend.v).end()
	// 			.find("#quid").val(qid).end()
	// 			.find(".datepicker[name^='filter_']").each(function(){
	// 				if ($j(this).attr("name") == "filter_qstart") {
	// 					$j(this).val(qstart.r);
	// 				}else {
	// 					$j(this).val(qend.r);
	// 				}
	// 			}).end()
	// 			.find('#brest').each(function(){
	// 				if(zmode == 'stats'){
	// 					eval("str = "+$tr.attr("data-showr")+";");
	// 					$j(this).attr("checked",str).show().parent().show();
	// 				}else{
	// 					$j(this).hide().parent().hide();
	// 				}
	// 			}).end()
	// 			.data("row",$tr.attr("id"))
	// 			.data("stype",zmode)
	// 			.show();
	// 			/*if(zmode == "stats"){
	// 				$j("table",$zd).hide();
	// 			}else{*/
	// 				$j("table",$zd).show();
	// 			//}
	// 	});

	$j(".jcheck",$j("#sendAll")[0]).live(be, function(){
		var st=$j(this).is(":checked"),
		$bbb=$j("#fcleaner"),
		bst=$bbb.attr("disabled");
		if(st){
			bst=false;
		}else{
			bst = $j(".jcheck:checked").length <= 0;
		}
		$bbb.attr('disabled',bst);
	});

	$j("div.exborder").css("display","inline");	
	makeView('hands');

	$j(".alltag").bind(be,function(e){
		var cstate=$j(this).is(":checked"),fval,fst;
		$j(this).closest(".cblox")
			.find("li > label > input").attr("checked",cstate);
	});

	$j(".myimporter").delegate("input:eq(0)","change",formFileExt).next().attr("disabled", true);

	var $qtb=$j("#qtable");
	if ($j("tbody > tr", $qtb).length > 0) {
		addQTlook();
	}
	
	$j("#more_flip").click(function(e){
		$j("#more_opts").toggle();
		$j(this).toggleClass("result_opts-more result_opts-less");
	});

	$j(".vdemo").live("click",function(){
		window.open("/?m=outputs&a=reports&mode=item_view&iid="+$j(this).attr("data-pid"),"_blank");
	});

	$j("#ittable").delegate(".deletq","click",function(){
		var $ritem = $j(this).closest("tr"),
			idi = $ritem.find("td:first > div").attr("data-pid");
		if(confirm("Do you want delete this saved item?")){
			$j.get("/?m=outputs&a=reports&mode=item_kill&suppressHeaders=1&iid="+idi,function(res){
				if(res && res != 'fail'){
					$ritem.fadeOut('slow',function(){
						$ritem.remove();
					});
				}
			});
		}
	});



	/*$j(".calpic").live("click",function(){
		if(!$j(this).data("con")){
			$j(this).data("con",true);
		}else{
			$j(this).parent().find("img").trigger("click");
			return false;
		}
		var ttc = cloneThis(calObj),self=this;
		//ttc.showTrigger = false;
		if($j(this).parent().find(".fdt").length == 0){
			$j("<input>",{ "class": "fdt", "type" : "text"}).appendTo( $j(this).parent());
		}else{
			$j(this).parent().find(".fdt").trigger("click");
		}

		ttc.defaultDate = $j(this).parent().find(".stdw").text();
		if(trim(ttc.defaultDate) == 'N/D'){
			ttc.defaultDate = '';
		}
		ttc.onClose = function (dates) {
			var vald= $j(this).parent().find(".fdt").val(), pdval = date2Val(vald);
			$j(this).closest("td")
					.find(".stdw").attr("fsort",pdval).text(vald).end()
					.find(":input:hidden:not(.fdt)").val(pdval);
		};
		$j(this).parent().find(".fdt").datepick(ttc);
		$j(this).closest("td").find(".trigger").trigger("click");
	});*/

	if(mode !== 'mas')
		refillSavedItems();
}

function addQTlook(){
	tabPrepare(0);
	$j("#qtable").tablesorter({
			headers: {
				0: {
					sorter: false
				},
				5: {
					sorter: "size"
				},
				6: {
					sorter: "size"
				},
				7: {
					sorter: false
				},
				8: {
					sorter: false
				}
			},
			widgets: ["fixHead"]
		}).tableStripe();
		tabevent=true;	
		return true;
}

function xtraSubmit(){
	$j('<iframe name="uploadQ" src="about:blank" width="0" height="0" style="display:none;" id="queryloader" ></iframe>').append(document.body);
	document.upq.submit();
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
		var tar = $j("." + cln);
		for (var i = 0, j = tar.length; i < j; i++) {
			//$j("." + cln).each(function(){
			var $sobj = $j(tar[i]), bid = $sobj.attr("data-col"), tid = $sobj.attr('id'), $but1 = $but.clone();
			$but1.data("tgt", bid).data("vstt", 0).click(function(ev){
				onf(this);
				ev.stopPropagation();
			});
			$sobj.click(function(ev){
				onf($j("div", this));
				ev.stopPropagation();
			});
			$but1.prependTo($sobj);
			var dshow;
			if (isArray(aopen) && aopen.length > 0) {
				dshow = $j.inArray(tid, aopen);
			}else{
				dshow=-1;
			}
			if (dshow >= 0) {
				onf($j(".switch", $sobj));
			}
			else {
				$j("#block_" + bid).hide();
			}
			$but1 = undefined;
			//$sobj.next().addClass("exborderv");
		}//);
	}	
}

function refillSavedItems() { //remove $nbody
	var selfUpdate=false;
	/*if (!$nbody) {
		$nbody = $j("#ittable  tbody");
		selfUpdate = true;
	}*/
	//$nbody.empty();
	//remove all existing report items from general query list
	$j("#qtable").find(".qieditor").each(function(){
		$j(this).closest("tr").remove();
	});
	var sitems = reporter.getItemsList();
	for (var it in sitems) {
		if (sitems.hasOwnProperty(it) && sitems[it] && it > 0) {
			qurer.add2Table({
			    name        : sitems[it].n,
				itemType    : sitems[it].c == 'stat' ? 'tabular' : 'graph',
				type        : 'Report Item',
				id          : it
			});
			/*$nbody.append(["<tr><td ><div class='fbutton vdemo' data-pid='", it, "'/></td>",
				"<td>", sitems[it].n, "</td>",
				"<td>", sitems[it].c, "</td>",
				"<td><div class='deletq fhref unfloat'>&nbsp;</div> </td>",
				"</tr>"
			].join(""));*/
		}
	}
	/*if(selfUpdate === true){
		$j("#ittable").trigger("update");
	}*/
}

function rebootQTable(so) {
	if ($j(so).val() == 'items') {
		var iaction = $j(so).attr("data-items");
		if (iaction == '' || iaction == 2) {
			var $ntab = $j("#ittable"), $nbody = $j("tbody", $ntab);

			refillSavedItems($nbody);

			$j(so).attr("data-items", "1");
			if (iaction == '') {
				$ntab.tableStripe().tablesorter({
					headers:{
						0:{
							sorter:false
						},
						3:{
							sorter:false
						}
					},
					widgets:["fixHead"]
				}).hide();
			} else {
				$ntab.trigger("update");
			}
		}
	}
	$j("#ittable, #qtable").toggle();
}

function onf(self){
    var bid = $j(self).data("tgt");
    var $blk = $j("#block_" + bid);
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

function shCl(fid,id){
	//document.location.href="/?m=wizard&a=form_use&fid="+fid+"&idIns="+id+"&todo=view&teaser=1&rtable=1&tab=0";
	//document.location.go();
	window.open("/?m=wizard&a=form_use&fid="+fid+"&idIns="+id+"&todo=view&teaser=1&rtable=1&tab=0&dialog=1","calwin","top=250,left=250,width=auto,height=220,scollbars=false,resizable");
}

function shEd(fid,id,itemid){
	///?m=wizard&a=form_use&fid=47&todo=addedit&itemid=1&idIns=1
	//http://localhost/?m=wizard&a=form_use&fid=47&todo=addedit&itemid=1&idIns=1
	//document.location.href="/?m=wizard&a=form_useed&fid="+fid+"&idIns="+id+"&itemid="+itemid+"&todo=addedit";
	window.open("/?m=wizard&a=form_useed&fid="+fid+"&idIns="+id+"&itemid="+itemid+"&todo=addedit",'_blank');
}

/*function shSh(fid,id,itemid){
	///?m=wizard&a=form_use&fid=47&todo=addedit&itemid=1&idIns=1
	//http://localhost/?m=wizard&a=form_use&fid=47&todo=addedit&itemid=1&idIns=1
	//document.location.href="/?m=wizard&a=form_useed&fid="+fid+"&idIns="+id+"&itemid="+itemid+"&todo=addedit";
	window.open("/?m=wizard&a=form_use&fid="+fid+"&idIns="+id+"&itemid="+itemid+"&todo=view");
}*/

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


function popPCalendar(field){
    idate = $j(field).val();
    window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendarP&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendarP(idate, fdate){
    $j(calendarField).val(fdate);
	
    /*fld_date = eval('document.xform.filter_' + calendarField);
    
    fld_date.value = idate;
	$j("input[name="+calendarField+"]").val(fdate);*/
    
}

function popCalendar(field){
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
		ins=5;
	}else{
		ins=6;
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
    //var acl = $j(".jcheck:checked").length;
	//return;
	var cnt = $j("#cboxes").find("input:checked").length;
	cnt += $j(".jcheck:checked").length;
    if (cnt > 0 ) {
        $j("#sendAll")
		.find("input.hasDatepick").attr("disabled",false).end()
		.attr("onsubmit","").submit();
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
	return self;

}

$j.fn.cellType = function(){
	var cellp=this;
	if($j(this)[0].tagName.toLowerCase() === 'div'){
		cellp=$j(this).closest("td");
	}
	return cellp;
};

$j.fn.toTab = function (tid){
	$j("ul.topnav > li:eq("+tid+")",this).find("a").trigger("click");
	return this;
};

function markAll(obj){
	var on=$j(obj).is(":checked");
	$j(obj).parent().parent().find("input:gt(0)").attr("checked",on);
}

function randomString(){
    var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz", string_length = 5, randomstring = [];
    for (var i = 0; i < string_length; i++) {
        var rnum = Math.floor(Math.random() * chars.length);
        randomstring.push(chars.substring(rnum, rnum + 1));
    }
    return randomstring.join("");
}

function listaMatic(){
	$j(".cblox").each(function(){
		var bpos = $j(this).offset().top,bhgt=$j(this).height(),inep = 0,$mblock=$j(this),move=false;
		$mblock.add("li",$mblock).css("visibility","hidden").show();
		$j("li",$mblock).each(function(){
			var diff = (($j(this).offset().top + $j(this).height()) - bpos), scpart = 0, clist = {},mdelta=0;
			if (diff > 300 || move === true) {
				scpart = (parseInt(diff / 300) + inep);
				if (scpart > 0 || move === true) {
					if (scpart > inep) {
						clist['margin-top'] = (-285 ) + "px";
						if (!move) {
							inep = scpart;
							move = true;
						}else{							
							scpart=++inep;
						}
					}else{
						delete clist.margin-top;
					}
					if(move === true && scpart === 0){
						scpart=inep;
					}
					clist['margin-left'] = (240 * scpart) + "px";
					
					$j(this).css(clist);
					
				}
			}
		});
		$mblock.add("li",$mblock).css("visibility","inherit");
	});
	$j(".exborder").css("visibility","inherit");
}

function monPosR(e) {
	if (rsip)return false;
	var cpx = e.pageX, cpy = e.pageY, $xt = $j(this), bpos = $xt.offset(), res;
	if (cpx >= bpos.left && cpx <= (bpos.left + 5) && !rsip) {
		$xt.css("cursor", "col-resize");
		rezo = new rFrm($xt);
		rezo.bindy();
		res = true;
	}
	else {
		$j(this).css("cursor", "default");
		res = false;
		if (rezo) {
			rezo.free();
		}
	}
	return res;
}

function rFrm(obj) {
	this.obj = obj;
	this.poss = [];
	slet = 0;
	var tself = this, $cell, tmj;
	this.ie7 = function () {
		if ($j.browser.msie && $j.browser.version < 8) {
			return true;
		} else {
			return false;
		}
	}
	this.meter = function (p, xp) {
		this.poss[p] = xp;
		if (p > 0) {
			return (this.poss[1] - this.poss[0]);
		}
	}
	this.killEvent = function (pE) {
		if (!pE)
			if (window.event)
				pE = window.event;
			else
				return;
		if (pE.cancelBubble != null)
			pE.cancelBubble = true;
		if (pE.stopPropagation)
			pE.stopPropagation();
		if (pE.preventDefault)
			pE.preventDefault();
		if (window.event)
			pE.returnValue = false;
		if (pE.cancel != null)
			pE.cancel = true;
	}  // StopEvent

	this.mdown = function () {
		$j(this).unbind('mousemove');
		var self = this, thh = self.id, wat = 1, slet = 0, ctm = null, monw, cwc, ow, oh;
		if (!dcl) {
			gpgr.justHideMenu();
			ctm = setTimeout(function (e) {
				if (wat == 1 && !dcl) {
					$cell = $j(self).prev();
					cellp = $cell.offset();
					cthid = $cell.attr("data-thid");
					ow = $cell.outerWidth();
					oh = $cell.outerHeight();
					trackm("on");
					tw = $j("#rtable").width();
					flag = 1;
					minw = 80;
					//cwc = findCol(acols, cthid);
					cwc = cthid;
					tself.meter(0, cellp.left);
					shadow.css({
						"left":cellp.left,
						"top":cellp.top,
						"width":ow,
						"height":oh
					}).show();
					wat = 0;
					rsip = true;
					tself.$cell = $cell;
					$j(document).css("cursor", 'col-resize').bind('mouseup', tself.doResize);
				}
			}, 300);
		}
	};
	this.mdclick = function (dth) {
		if (!lahand || (lahand != dth.timeStamp && lahand)) {
			lahand = dth.timeStamp;
		} else {
			return false;
		}
		//dth.stopPropagation();
		tself.killEvent(dth);
		typeof ctm !== 'undefined' ? clearTimeout(ctm) : false;
		ctm = null;
		dcl = true;
		memo.toggle();
		var $table = $j("#rtable"),
				$thfr = $j("thead > tr:first", $table),
				dcolz = $j("th", $thfr).length;
		setTimeout(function () {
			clearInterval(tself.tmj);
			//var did = $j(obj).data('dind');
			var $thi = $j(obj).prev(), //$j("th:eq("+did+")",$thfr);
					gfx = [], chc, cw1, cith = $thi.attr('data-thid');
			if ($j(acols[cith]).attr("data-turn") == 1) {
				var ow = $thi.data("ow"),
						cw = $thi.mywidth();
				cw1 = $j(acols[cith]).width();
				var delta = ow - cw1,
						ctw = $table.mywidth(),
				//$th.mywidth(ow);
						tp = $thi.offset(),
						ntw = ctw + delta;
				$table.mywidth(ntw);
				if ($j.browser.msie && $j.browser.version >= 8) {
					$j(acols[cith]).css("width", (ow + 10));//.next().css("width", 2);
				}

				else {
					$j(acols[cith]).mywidth(ow);//.css("left", tp.left);
				}
				if (tself.ie7()) {
					//$thi.mywidth(ow);
					$thi.find("div.exob").width(ow - 20);
				}
				$j(acols[cith]).attr("data-turn", 0);


				$j("#cwid").html(["old=" , ow , " current=" , cw1 , " cw=" , cw , " delta=" , delta].join(""));
			}
			else if (ofix)/*if($j(acols[cith]).data("turn") == 0)*/ {
				var bdelta = 0;
				//for (var dz = 0; dz < dragColumns.length; dz++) {
				for (var dz = 0; dz < dcolz; dz++) {
					//alert(dz);
					var $dc = $j("th:eq(" + dz + ")");//"dragColumns[dz]);
					if ($thi != $dc) {
						var ow = $dc.data("ow"), cid = $dc.attr("data-thid"), cw = $dc.mywidth(), tp = $dc.offset();
						if ($j.browser.msie && $j.browser.version >= 8) {
							cw1 = cw;
						}
						else {
							var cw1 = $j(acols[cid]).mywidth();
						}
						var delta = ow - cw1;
						if (Math.abs(delta) > 1) {
							bdelta += delta;
							gfx.push({
								ind:dz,
								nw:ow
							});
						}
					}
				}
				if (Math.abs(bdelta) > 0) {
					var ctw = $table.mywidth();
					ctw = (ctw + bdelta);
					$table.mywidth(ctw);
					for (var c = 0; c < gfx.length; c++) {
						$dc = $j("th:eq(" + gfx[c].ind + ")", $thfr); //(dragColumns[gfx[c].ind]);
						var cid = $dc.attr("data-thid"), tp = $dc.offset();
						$j(acols[cid]).mywidth(gfx[c].nw);
						$j(acols[cid]).attr("data-turn", 0);
						if (tself.ie7()) {
							//$dc.width(gfx[c].nw);
							$dc.find("div.exob").width((gfx[c].nw - 20));
						}

					}
				}
				ofix = false;
			}
			ctm = 0;
			wat = 0;
			slet = 0;
			//}
			dcl = false;
			myclearSelection();
			memo.toggle();
			$thi.unbind('dblclick', tself.mdclick);
			return false;
		}, 100);
	};

	this.bindy = function () {
		var self = this.obj;
		$j(self)
				.bind('mousedown', this.mdown)
				.bind('dblclick', this.mdclick)
				.data('resize', true);
	}

	this.free = function () {
		var self = this.obj;
		$j(self)
				.unbind('mousedown dblclick')
				.data('resize', false);

		slet = 1;
	}

	this.doResize = function (e) {
		myclearSelection();
		if (flag == 1) {
			slet = 0;
			trackm("off");
			sw = shadow.mywidth();
			var fin = tself.meter(1, e.pageX);
			tw = $j("#rtable").mywidth();
			//delta=e.pageX-startx;
			var ow = tself.$cell.mywidth(),
				cwc = tself.$cell.index();
			delta = sw - ow;
			if (Math.abs(delta) > 1) {
				memo.toggle();
				//$j("div#cwid").html("delta " + delta);
				$j("#rtable").mywidth(tw + delta);
				/*if (crows == 0) {
				 cwc = parseInt(cwc) + 1;
				 }*/
				tself.$cell.mywidth(sw);
				if (tself.ie7()) {
					//tself.$cell.find("div.exob").width(sw);
					tself.$cell.width(sw);
				}
				else {
					$j(acols[cwc]).css("width", (sw + 10));//.next().css("width", 2);
				}
				$j(acols[cwc]).attr("data-turn", 1);

				ofix = true;
				memo.toggle();
			}
			flag = 0;
			shadow.hide();
			rsip = false;
			$j(document).css('cursor', 'auto').unbind('mouseup', this.doResize);
		}
	}
}

function trackm(turner) {
	var zw = 0;
	if (turner == 'on') {
		$j(document).mousemove(function (e) {
			//$j('#mpos').html(e.pageX + ', ' + e.pageY);
			if (flag == 1) {
				zw = e.pageX - cellp.left;
				if (zw > minw) {
					shadow.width(zw);
				}
			}
		});
	}
	else if (turner == "off") {
		$j(document).unbind('mousemove');
	}
}

function myclearSelection() {
	var sel;
	if (document.selection && document.selection.empty && !$j.browser.msie) {
		document.selection.empty();
	}
	else if (window.getSelection) {
		sel = window.getSelection();
		if (sel && sel.removeAllRanges)
			sel.removeAllRanges();
	}
}

$j.fn.mywidth = function (nw) {
	var w = $j(this).width();
	if ((w == 0 || isNaN(w)) && $j.browser.msie) {
		w = this[0].style.pixelWidth;
		if (nw) {
			this[0].style.pixelWidth = nw;
		}
	}
	else if (nw) {
		$j(this).width(nw);
	}
	if (nw) {
		return this;
	}
	else {
		return w;
	}
}
function hasCheckedOnTable(table){
	checked = $('#'+table).find('input[type="checkbox"]:checked');
	if(checked.length>0){
		return true
	}else{
		return false;
	}
}
function loadCommune(dept_code,$commune){
	v = null;
	if($commune != null){
		$commune.empty();
		imgid = 'img_'+$commune.attr('id');
		$commune.after('<img src="/modules/outputs/images/loader.gif" id="'+imgid+'"/>');
		$commune.append("<option value='-1'>- Select Commun -</option>");
		$j.get("?m=wizard&ccode="+dept_code+"&mode=loadcommunDept&suppressHeaders=1", function (msg) {
			if (msg && msg !== 'fail') {
				msg = $j.parseJSON(msg);
				
				
				$.each(msg, function(key,value){
					
					$commune.append("<option value='"+key+"'>"+value+"</option>");
				});
				$('#'+imgid).hide();
				//updateSelect("#administration_sect");
			} else {
				$('#'+imgid).hide();
				//$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);
			}
		});
	}
}
function loadSection(commune_code,$section){
	v = null;
	$section.empty();
	imgid = 'img_'+$section.attr('id');
	$section.after('<img src="/modules/outputs/images/loader.gif" id="'+imgid+'"/>');
	$section.append("<option value='-1'>- Select Communal Section -</option>");
	$j.get("?m=wizard&ccode="+commune_code+"&mode=loadsection&suppressHeaders=1", function (msg) {
		if (msg && msg !== 'fail') {
			msg = $j.parseJSON(msg);
			
			
			$.each(msg, function(key,value){
				
				$section.append("<option value='"+key+"' >"+value+"</option>");
			});
			$('#'+imgid).hide();
			//updateSelect("#administration_sect");
		} else {
			$('#'+imgid).hide();
			//$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);
		}
	});
}
function populateCommune(department,commune){
	var $td = $("td[func-id='"+commune+"']");
	if($td != null){
		func = $td.attr('func-action');
		$td.html("<select class='dred_sel text' id='"+commune+"' onchange='"+func+"'></select>");
		$('#'+commune).change(function(){
			var nv = $j(this).val();
			nval2 = $j(this).find("option[value=" + nv + "]").text();
			nval = nval2.toLowerCase();
			var cw = $j(this).width();
			cw -= 20;
			var $ctr=$j(this).closest("tr"), rid = $ctr.attr("id").replace("row_", ''), colset = $ctr.find("td"),col=false;
			col=$j(colset).cellType().index($td);
			$td.data({
				wid: cw,
				'rid': rid,
				'col': col,
				'emode': 'common'	
			});
			gpgr.patchCell($td.get(0) ,/* rid, col,*/ nv, nval, nval2/*, cw*/);
		});
		
	}
	var $commune = $('#'+commune);
	var $department = $('#'+department);
	
	
	if($department != null && $commune != null){
		/*if($department.val() != '-1'){

			loadCommune($department.val());
		}*/
		//$department.change(function () {
			//if($department.val() != '-1'){
		loadCommune($department.val(),$commune);
			//}
		//});
		
	}
}

function populateSection(commune,section){
	var $td = $("td[func-id='"+section+"']");
	if($td != null){
		func = $td.attr('func-action');
		$td.html("<select class='dred_sel text' id='"+section+"' onchange='"+func+"'></select>");
		$('#'+section).change(function(){
			var nv = $j(this).val();
			nval2 = $j(this).find("option[value=" + nv + "]").text();
			nval = nval2.toLowerCase();
			var cw = $j(this).width();
			cw -= 20;
			var $ctr=$j(this).closest("tr"), rid = $ctr.attr("id").replace("row_", ''), colset = $ctr.find("td"),col=false;
			col=$j(colset).cellType().index($td);
			$td.data({
				wid: cw,
				'rid': rid,
				'col': col,
				'emode': 'common'	
			});
			
			gpgr.patchCell($td.get(0) ,/* rid, col,*/ nv, nval, nval2/*, cw*/);
		});
	}
	var $commune = $('#'+commune);
	var $section = $('#'+section);
	if($commune != null && $section != null){
		/*if($department.val() != '-1'){

			loadCommune($department.val());
		}*/
		//$department.change(function () {
			//if($department.val() != '-1'){
		loadSection($commune.val(),$section);
			//}
		//});
		
	}
}
