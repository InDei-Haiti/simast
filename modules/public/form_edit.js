var cofoll = function(){
	var curRow,rowID,self = this;
	var subjNums = {'child':1,'parent': 2,'caregiver':3};
	this.marker=false;
	this.active_but = false;
	this.rissue = [];
	this.rissue_other = [];
	this.rserv = [];
	this.rserv_other = [];
	this.rcare = [];
	this.rstore = {};
	this.rstore_other = {};
	this.dsel = new RegExp("\\d-\\d?","g");
	this.partCase=false;
	this.use_list=[];
	this.use_other = [];
	this.use_etalon=[];
	this.use_string='';
	this.last;
	this.lastID;
	this.tbl = $j("#opf > tbody");
	this.now=false;
	this.trs;
	this.ed_open =false;


	this.postName = function(obj,alone){
		var $par= $j(obj).closest("td");
		var nadm=$j("input",$par).val();
		if (nadm && trim(nadm).length > 0) {
			self.getName(obj, nadm, alone);
		}
	}


	this.counter = function (obj){
		var clcase= obj.className.replace("_row",""),awork='',bempty=1,ctype,dmatch= /^\d{1,2}(\-|\/|\.)\d{1,2}\1\d{4}$/;
		self.catchRow(obj);
		if(clcase === "add"){
			var $nrow=$j(self.curRow).clone(true);
			$nrow.find("td").removeClass("celloff").find(":input").attr("disabled",false);
			if(intlist['care'] || dsplit){
				$nrow.html(function(i,x){
					var xr = new RegExp('_'+self.rowID,"g");
					return x.replace(xr,'_'+self.trs);
					})
					.find(".xtras").hide().end()
					.find("img").remove().end()
					
  					.find("#date_"+self.trs)
					 	.attr("class","text")
						.val($j("#date_"+self.rowID).val());
  				 awork='attachPicker($j("#date_'+self.trs+'"),\'\')';
				 bempty = 2;
			}
			$nrow
				.attr("data-row",self.trs)
				.find(".client_name").html("").end()
				.find("select.xtras").find("option:first").attr("selected",true).end().end()
				.find("input[type!='button'],select,textarea")
					.each(function(){
						ctype=$j(this).attr("type");
						$j(this).attr("checked",false)
							.val(function(i,x){
								if(! dmatch.test(x) && ctype != 'radio'){
									return "";
								}else{
									return x;
								}
							})
							.attr("name",function(i,x){
								var nname=x.replace(/\[\d{0,}\]/,'['+self.trs+"]");
								return nname;
							});
					}).end()
				.appendTo(self.tbl);
			eval(awork);
		}else if(clcase == "del" && self.trs > 1){
			$j(self.curRow).remove();
			self.rissue[self.rowID]=null;
			self.rissue_other[self.rowID]=null;
			self.rserv[self.rowID]=null;
			self.rserv_other[self.rowID]=null;
		}
	}

	this.simpleSend = function(){
		if (!manField("clinic_id")) {
			alert("Please select Clinic!");
			return false;
		}
		if($j(".mandat").val() == "" || $j("select.mandat").val() == 0){
		    alert("Please Enter name!");
		    $j(".mandat").focus();
		    return false;
		}

		$j("#jfk").val(JSON.stringify([this.rstore,this.rstore_other]));
		$j("#filid").submit();

	}

	this.xtras = function(obj){
		var $but=$j(obj),bval=$but.val(),adds='';
		if(bval == "1"){
			adds='block';
		}else{
			adds='none';
		}
		$but.parent().find(':input:last').css("display",adds).find("option:first").attr("selected",true);
	}

	this.forSend = function(){
		var nameClear=0,names=0,focus_on=false;
		if(!manField("clinic_id")){
		    alert("Please select Clinic!");
		    return false;
		}
		if (!intlist['care']) {
			$j(".subj_selects", self.tbl).each(function(){
				if ($j(this).val() == "xsel") {
					$j(this).focus().parent().addClass("warn");
				}
				else {
					nameClear++;
				}
				names++;
			});
			
			if(!manField("officer_id")){
				alert("Please select Staff!");
				return false;
			}
			if (nameClear != names) {
				alert("Please select client name");
				return false;
			}
			if (names == 0 || names < $j("tr", self.tbl).length) {
				alert("You have to enter admission number and then select client name");
				return false;
			}
		}
		if($j(".mandat").val() == "" || $j("select.mandat").val() == 0){
		    alert("Please Enter name!");
		    $j(".mandat").focus();
		    return false;
		}
		var sett = {
			store: 		 this.rstore,
			store_other :this.rstore_other			
		}
		$j("#jfk").val(JSON.stringify(sett));
		$j("#filid").submit();
	}

	this.autoCheck = function(obj){
		var $bblock=$j(obj).closest("div"),tmode=$bblock.data("xmode"),
		 	nv=trim($j(obj).val()),flag=false;
		if(nv.length > 0){
			flag=true;
		}
		if (tmode == "issue") {
			$j(obj).prev().attr("checked", flag);
		}else{
			$bblock.find("input[type='checkbox']:last").attr("checked",flag);
		}
	}

	this.reboot = function(){
		if(!intlist){
			intlist=[];
		}
		if (this.partCase.length > 0) {
			this.use_list = this.migr8(this.rstore[this.partCase]);
			this.use_other = this.migr8(this.rstore_other[this.partCase]);
			this.use_etalon = intlist[this.partCase];
			this.use_string = vlist[this.partCase];
		/*}
	 else
	 if (this.partCase == "service") {
	 this.use_list = this.migr8(this.rserv);
	 this.use_other = this.migr8(this.rserv_other);
	 this.use_etalon = sint;
	 this.use_string = servList;
	 }*/
		}
	}

	this.catchRow = function(obj){
		if (self.rowID > 0 && self.ed_open == true) {
			self.patch();
		}
		var cls;
		//alert(obj.tagName);
		if (obj) {
			var ctag=obj.tagName.toLowerCase();
			if (ctag == "td") {
				cls = obj.className;
			}
			else {
				cls = $j(obj).closest("td").attr("class");
			}
			if (ctag != "div" /*&& ctag != "input"*/ ) {
				var mt = cls.match(/(.*)_/);
				this.partCase = mt[1];
				self.reboot();
				if(this.partCase == 'care') {
					/*this.use_list = this.migr8(this.rcare);*/
					this.use_other = [];
					/*this.use_etalon = cint;
					this.use_string = careList;*/
				}/*else{
					
				}*/
			}
			this.last = this.partCase;
			this.curRow = $j(obj).closest("tr");
			this.rowID = $j(this.curRow).attr('data-row');
			this.lastID = this.rowID;
			this.trs = $j("tr",self.tbl).length;
		}
		else {
			this.partCase = this.last;
			this.rowID = this.lastID;
		}


	}

	this.store = function(){
		var ccase=$j("#ilist").data("xmode");
		if(ccase != this.partCase){
			this.partCase= ccase;
		}
		if(this.partCase.length > 0/* == "issue"*/){
			this.rstore[this.partCase]=this.migr8( this.use_list);
			this.rstore_other[this.partCase]=this.migr8 (this.use_other);
		}/*else if (this.partCase == "service"){
			this.rserv=this.migr8(this.use_list);
			this.rserv_other=this.migr8(this.use_other);
		}else{
			this.rcare=this.migr8(this.use_list);
		}*/
		/*this.use_list=[];
		this.use_other=[];*/
		this.use_etalon=[];
		this.use_string='';
	}


	this.migr8 = function(oar){
		if(!oar || oar.length == 0){
			return [];
		}
		var nar=[],i;
		//$j(oar).each(function(x,i){
		for (var x in oar) {
			i = oar[x];
			if (i) {
				nar[x] = i;
				i=null;
			}
		}
		return nar;
	}

	this.setClientType = function(row,name){
		self.catchRow(row);
		if(name && subjNums.hasOwnProperty(name)){
			$j(self.curRow).find("td:eq(2) > select").val(function(i,x){
				return subjNums[name];
			});
		}
	}

	this.setName = function(name,alone){
		var btab=1,nlist=[],mode;
		if(alone){
			btab=2;
		}
		if(name === true){
			mode='icon';
		}else if(name === false || name === 'failfail'){
			alert("Please enter valid Adm #");
			mode='fail';
		}else{
			mode='data';
			nlist = $j.parseJSON(name);
			if (!nlist || !nlist.child) {
				nlist.child = ['', '', ''];
			}
			if (typeof iNJs !== 'undefined') {
				//case of work in discharge form
				//1. Assign client names
				
				var kidinfo = nlist.child, cnames = kidinfo[3], sost = '',tca=true,tcad='disable';
				if(kidinfo[10] == 1 || kidinfo[10] == 11){
					$j("#ftab").find(":input:not(.adm_field)").attr("disabled",true);
					$j(".adm_field").val("");
					alert(["Selected client ",cnames.fname,' ',cnames.lname+" current status is "+ statuses[kidinfo[10]] +" .\n Please change status with social visit before filling this form!"].join(""));					
					return false;
				}else{
					$j("#ftab").find(":input").attr("disabled",false);
					if(kidinfo[10] == 7){
						$j("#tca_fld").attr("disabled",false).datepick('enable');
						tca=false;
						tcad='enable';
					}
					$j(".hasDatepick:eq(0)").datepick("enable");
					$j("#dt_client_status_vis").val(statuses[kidinfo[10]]);
					$j("#dsc_date").val(kidinfo[11]);
					$j(".hasDatepick").attr("disabled",false).datepick("enable");
					$j("#dt_client_status").val(kidinfo[10]);
					//$j("#tca_fld").attr("disabled",tca).datepick(tcad);
				}
				
				
				for (var idn in cnames) {
					$j("#" + idn).val(cnames[idn]);
				}
				$j("#doa_cell").text(kidinfo[4]);
				$j("#dob_cell").text(kidinfo[5]);
				$j("#sex_cell").text(kidinfo[7]);
				if (kidinfo[6]) {
					var agt = kidinfo[6].split("|");
					if (agt[1].length > 0) {
						$j("#exact_cell").find(":radio[value='" + agt[1] + "']").attr("checked", true);
					}
				}
				$j("#yrz_cell").find("input").val(kidinfo[1]);
				$j("#mnth_cell").find("input").val(kidinfo[8]);
				$j("#timein").val(kidinfo[9]);
				if (nlist.caregiver && nlist.caregiver.length > 0) {
					var sopts = ['<select id="csel" name="dis_caregiver" class="text"><option disabled value="-1" selected="selected">Select Caregiver</option>'];
					for (var ci in nlist.caregiver) {
						if(nlist.caregiver.hasOwnProperty(ci)){
							sopts.push(['<option value="', nlist.careids[ci], '" data-rship="', nlist.relship[ci], '">', nlist.caregiver[ci], '</option>'].join(""));
						}
						
					}
					sopts.push("</select>")
					sost = sopts.join("");					
				}
				$j("#clid").val(nlist.client_id);
				$j("#care_names").html(sost);
				$j("#care_rels").val("");
				
				return;
			}
			$j(".genderp",this.curRow)
				.find("input[value='"+nlist.child[2]+"']").attr("checked",true).end()
				.find("input").attr("readonly",function(i,x){
					var dc=true;
					if(nlist.child[2] === null || nlist.child[2].length === 0){
						dc= false;
					}
					return dc;
				}).end()
				.next().find("input").val(function(i, x){
					var dc = false;
					if (nlist.child[1].length > 0) {
						dc = true;
					}
					$j(this).attr("readonly", dc);
					return nlist.child[1];
				});
		}
		
		if(mode === 'fail'){
			name = false;
		}

		$j(this.curRow)
		.find(".clid").val(function (i,x){
			if(mode=='data'){
				return nlist['client_id'];
			}else{
				return '';
			}
		}).end()
		.find(".client_name").html(function(i,x){
			if(name === true){
				return '<img src="/images/tiny_load.gif">';
			}else if(name === false){
				return '';
			}else{
				if(alone){
					return '<span>'+nlist['child'][0]+'</span>';
				}
				var code='<select name="fentry['+self.rowID+'][client_object]" class="subj_selects">',vitem,it=0;
				code += '<option value="xsel" disabled="disabled" selected>-- Select --</option>';
				for (var subj in nlist) {
					if (subj != 'client_id') {
						code += '<optgroup label="' + subj + '">';
						var item = nlist[subj];
						if (isArray(item) && it > 0) {
							for (var j = 0, l = item.length; j < l; j++) {
								if(typeof item[j] !== "string"){
									vitem=item[j][0];
								}else{
									vitem=item[j];
								}
								code += '<option value="' + vitem + '">' + vitem + '</option>';
							}
						}
						else {
							if(it === 0 && isArray(item)){
								vitem=item[0];
							}else{
								vitem=item;
							}
							code += '<option value="' + vitem + '">' + vitem + '</option>';
						}
						code += '</optgroup>';
					}
					++it;
				}
				code+='</select>';
				return code;
			}
		});
	}

	this.getName = function(row,admno,alone){
		self.catchRow(row);
		self.setName(true,alone);
		$j.ajax({
			type: 'get',
			url: '?m=followup&a=namer&suppressHeaders=1',
			data: 'nadm='+admno+ ( alone ? '&alone='+alone : '' ),
			success: function(data){
				if(data &&  ! (/fail/g.test(data))){
					self.setName(data,alone);
					data=null;
				}else if(data === 'existfail'){
					alert("Client with entered ADM # already has discharge entry. Try another ADM #");
					return false;
				}
				else 
					if(data === 'fail' || data === 'failfail'){
					self.setName(false);
				}
			}
		});
	}

	this.show = function (obj){
		self.catchRow(obj);
		if (self.marker !== false && self.marker === self.rowID && self.ed_open) {
			var lstr = '',onshow=$j("#ilist").data("xmode");
			if (onshow != self.partCase) {
				//self.partCase = onshow;
				self.rowID = $j("#ilist").data("row");
				lstr = "self.show(obj);";
			}
			self.closeMenu();
			self.marker = false;
			eval(lstr);
			return false;
		}
		//else {
			$j("#iinfo").html("").hide();
			var $block = $j("#ilist").detach(), boff = $j(obj).offset();
			self.now = self.partCase;
			self.marker = self.rowID;
			self.active_but = obj;
			boff.left += 40;
			boff.top += 30;
			$block.html(self.use_string)
				.css({
					left: boff.left,
					top: boff.top
				})
				.appendTo("body");
				
			if (!isArray(self.use_list[self.rowID])) {
				self.use_list[self.rowID] = [];
				self.use_other[self.rowID] = false;
			}
			else {
				for (var i = 0, j = self.use_list[self.rowID].length; i < j; i++) {
					$j("#ilist").find("input[value='" + self.use_list[self.rowID][i] + "']").attr("checked", true);
				}
				if (self.use_other[self.rowID] !== false) {
					//$j("#ilist").find("input[name='" + self.partCase + "s_note']").val(self.use_other[self.rowID]);
					$j("#ilist").find("input.live_edit").val(self.use_other[self.rowID]);
				}
			}


			$j(document).bind("click", function(e){
				if (self.ed_open === true) {
					editor.close(e);
					return;
				}
				self.ed_open = true;
			});
			$block.data({
				xmode	: self.partCase,
				row		: self.rowID
			}).fixPosition();
			//self.store();

			return false;
		//}
	}

	this.close = function(ev){
		self.catchRow();
		var et=ev.currentTarget.activeElement,
		    p1=$j(ev.target).closest("#ilist");
		if (self.active_but != et && (!p1 || p1.length == 0)) {
			self.patch();
			self.closeMenu();
		}
		return ;
	}

	this.closeMenu = function(){
		self.patch();
		$j("#ilist").html("").hide();
		$j(document).unbind("click");
		self.ed_open=false;
		//self.rebindplm();
	}

	this.patch = function (){
		if (self.rowID >= 0) {
			self.use_list[self.rowID] = [];
			self.use_other[self.rowID] = '';
		}
		$j("#ilist").find("input:checked").each(function(){
			self.use_list[self.rowID].push($j(this).val());
		});
		var otherv=$j("#ilist").find("input[type='text']").val();
		if(otherv && otherv.length > 0){
			self.use_other[self.rowID]=otherv;
		}
		self.store();
		self.now = false;
	}
	
	this.collectJets = function(){
		var l={refs:[],care:[],issue:[],adhs:[],service:[]};
		if(typeof inj_refs !== 'undefined' && inj_refs.length > 0){
			l.refs=[inj_refs];
		}else{
			delete l.refs;
		}
		if(typeof inj_refer_note !== undefined){
			this.rstore_other ={refs:[inj_refer_note]};
		}
		if(typeof inj_care !== 'undefined' && inj_care.length > 0){
			l.care =[inj_care];
		}else{
			delete l.care;
		}
		if(typeof inj_issue !== 'undefined' && inj_issue.length > 0){
			l.issue =[inj_issue];
		}else{
			delete l.issue;
		}
		if(typeof inj_service !== 'undefined' && inj_service.length > 0){
			l.service =[inj_service];
		}else{
			delete l.service;
		}
		if(typeof inj_adhs !== 'undefined' && inj_adhs.length > 0){
			l.adhs =[inj_adhs];
		}else{
			delete l.adhs;
		}
		
		if(amnt(l) > 0){
			this.rstore = l;
		}
	}

	this.showSelected = function(obj){		
		if(this.ed_open == true){
			return false;
		}		
		this.catchRow(obj);
		if(self.use_list[self.rowID] && self.use_list[self.rowID].length > 0 && self.now != self.partCase){
			var $block=$j("#iinfo"),lv,code=[],boff = $j(obj).offset();
			boff.left += 50;
			boff.top += 35;
			if(self.partCase == 'care'){
				boff.left+= 60;
			}
			code.push("<ul>");
			for(var i=0,j = self.use_list[self.rowID].length; i < j; i++){
				lv=self.use_list[self.rowID][i];
				if(!isNaN(lv)){
					lv=lv +'';
				}
				if (lv) {
					code.push("<li>");
					if (lv.match(self.dsel)) {
						var zz = lv.split("-");
						code.push(self.use_etalon[zz[0]]['title'] + "&nbsp;-");
						code.push(self.use_etalon[zz[0]]['kids'][zz[1]]);
					}
					else {
						code.push(self.use_etalon[lv]);
					}
					if (((self.partCase === 'issue' && lv === "14") || 
						(self.partCase === 'refs' && lv === "9") || 
						(self.partCase === 'service' && lv === "6")) &&
					(self.use_other && self.use_other[self.rowID] && self.use_other[self.rowID].length > 0)) {
						if (self.partCase == "issue") {
							code.push("Other");
						}
						code.push(" - ");
						code.push(self.use_other[self.rowID]);
					}
					code.push('</li>');
				}
			}
			$block.html(code.join(""))
				.appendTo("body")
				.css({
					left: boff.left,
					top: boff.top
				})				
				.fixPosition();
		}else{
			return false;
		}
	}
}
var editor = new cofoll;

function binds (){
    	$j(".adm_field").live('keypress', function(e){
		var code = (e.keyCode ? e.keyCode : e.which);
		var nv = $j(this).val();
		if (code == 13 && nv.length > 1) {
			editor.getName(this, nv);
		}
	});
	$j(".ltrigger").live("mouseenter", function(e){
		$j(this).addClass("hiback");
		editor.showSelected(this);
	}).live("mouseleave", function(e){
		$j(this).removeClass("hiback");
		$j("#iinfo").html("").hide();
	});	
	$j(".live_edit").live("keyup", function(e){
		editor.autoCheck(this);
	});
}

binds();

function loadLocation(item){
	var nsel=$j(item).val();
	if(parseInt( nsel ) > 0){
		$j("#locsel").empty().hide();
		$j("#loader").show();		
		$j.ajax({
			url: '/?m=public&suppressHeaders=1&a=center_location',
			type: 'get',
			data: "cid="+nsel,
			success: function(data){
				if(data && data.length > 0){
					if(data != 'fail'){
						var locs=$j.parseJSON(data),selopts=[];
						data=null;						
						for(var il in locs){
							selopts.push(['<option value="',locs[il].id,'">',locs[il].name,'</option>'].join('') );
						}						
						$j("#locsel").html(selopts.join('')).show();
					}										
				}
			    $j("#loader").hide();
			}
		});
	}
}

function rowMode(item){
	var $row=$j(item).closest("tr"),$row_cells=$j("td",$row);
	var mode = $j(".definer > :radio:checked ",$row).val(), untouch=['definer','calendar','rowplim'],find=false;
	
	if (mode == 1) { //old - means client case
		$row_cells.each(function(){
			$j(this).removeClass("celloff").find(":input").attr("disabled",false);
		});
		$j(".comobi ",$row).addClass("celloff").find(":input").attr("disabled",true);
	}else if(mode == 2){ // new - this is case of comunity mobilization
		$row_cells.each(function(x){
			find = false;
			for (var i = 0, j = untouch.length; i < j; i++) {
				if ($j(this).hasClass(untouch[i])) {
					find = true; 
				}
			}
			if (find === false) {
				$j(this).addClass("celloff").find(":input").attr("disabled", true);
			}
		});
		$j(".comobi",$row).removeClass("celloff").find(":input").attr("disabled",false);
	}
}
$j.fn.getExtras = function(){
	$j(this).attr("checked",true);
	editor.xtras(this);
	return this;
}

$j.fn.rowCHW = function(){
	$j(this).attr("checked",true);
	rowMode(this);
	return this;
}

