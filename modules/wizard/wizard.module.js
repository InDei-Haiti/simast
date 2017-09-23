/**
 * Created by JetBrains PhpStorm.
 * User: stig
 * Date: 08.04.11
 * Time: 15:10
 */
var export_all;
var add_new_value_set;
var import_;
var search_;
var listformProject = {};
var wzrd = (function (my) {
	this.biglist = [];
	this.emsg = [];
	section = 'section';
	var self, rowID = 0, $store = $j("#stock"), $mainList = $j("#mainList"), $global = $j("#hugeStore"), $sectionCtrl = $j(".fbutton"),$checkinscriptionCtrl = $j("#checkinscription"),
		fieldTypes = {
			'empty': '--Select type--',
			//'entry_date': 'Visit date',
			'date': 'Date',
			'bigText': 'Memo',
			'select': 'List (single)',
			'select_multi': 'List (multi)',
			'radio': 'Radio',
			'checkbox': 'Multi-choice',
			'note': "Title",
			'plain': 'Text',
			'numeric': 'Numeric',
			'unique': 'Unique Value',
			'calculateText':'Calculate Text',
			'calculateNumeric':'Calculate Numeric',
			'calculateChoice':'Calculate Choice (single)',
			'calculateChoiceMult':'Calculate Choice (multi)',
			'dbtype': 'BigDataType'
		},
		textRowTypes = [
			'plain',
			'numeric',
			'positive',
			'range'
		],
		multiOption = ['clients', 'centers', 'staff', 'select'],
		parents = ['select', 'radio'],
		sysvalDepend = ['select', 'radio', 'checkbox', 'select_multi','calculateChoice','calculateChoiceMult'],
		sortableOptions = {
			placeholder: "ui-state-highlight",
			handle: false,
			//handle: "span.mholder",
			forcePlaceholderSize: true,
			connectWith: ".subStore, #mainList",
			tolerance: 'pointer',
			forceHelperSize: true,
			axis: 'y',
			opacity: 0.5,
			//revert: true,
			start: function (event, ui) {
				document.body.style.cursor = 'move';
			},
			stop: function (event, ui) {
				document.body.style.cursor = 'auto';
				var uitem = ui.item[0];
				if (
					( uitem.className.match(/subPart/) && uitem.parentNode.className.match(/qlist/) ) ||
						(uitem.className.match(/prow/) && !uitem.parentNode.className.match(/qlist/) )
					) {
					$j(this).sortable('cancel');
					return event;
				}
				enumItems();
			}
			/*containment: 'parent'*/
		},
		dropOptions = {
			accept: 'li.prow',
			activeClass: 'ui-state-hover',
			hoverClass: 'ui-state-active',
			greedy: true,
			tolerance: 'touch',
			drop: function (ev, $ui) {
				return ($ui.draggable.hasClass("prow") && $ui.draggable.closest(".qlist").length === 0);
			}

		},
		sysvals = false,
		digestUsed = 0,
		sortableSub = sortableOptions,
		abc = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
		abcind = 0,
		sectionCount = 0,
		vdate = false,
		formtouch = '';

	sortableSub.tolerance = "intersect";
	sortableSub.containment = ":not(.qlist)";
	delete sortableSub.containment;
	this.registryUsed = false;

	function collectParentsObs($exclude) {
		var parent = [], apix, $pblock;
		if (self.registryUsed === true) {
			$pblock = $j(".subPart:eq(0)", $global);
		} else {
			$pblock = $global;
		}
		$pblock.find("li:not(:.subpart)").each(function () {
			var tt = $j(this).data("dtype"), sv = $j(".sysval_use", this).val();
			if ((((tt === 'radio' || tt === 'select') && sv) || $j.inArray(tt, multiOption) >= 0 )
				&& $j(this).find(".rdataName").val() != '' && $j(this) != $exclude) {
				parent[$j(this).attr("data-rid")] = $j(this);
			}
		});
		return parent.length > 0 ? parent : false;
	}

	function collectParents($exclude) {
		var parent = [], apix, $pblock,
			acceptlist = ['radio', 'select', 'plain', 'numeric', 'checkbox'];
		if (self.registryUsed === true) {
			$pblock = $j(".subPart:eq(0)", $global);
			$pblock.find("li:not(:.subpart)").each(function () {
				var tt = $j(this).data("dtype"), sv = $j(".sysval_use", this).val();
				if (tt && ( $j.inArray(tt, acceptlist) && (sv || (tt === 'plain' || tt === 'numeric') ) && $j(this).find(".rdataName").val() != '' && $j(this) != $exclude )) {
					parent[$j(this).attr("data-rid")] = $j(this);
				}
			});
		} else {
			$pblock = $exclude.prevAll(".prow:not(.indent):first");
			var tt = $pblock.data("dtype"), sv = $j(".sysval_use", $pblock).val();
			if (tt && ( $j.inArray(tt, acceptlist) >= 0 && ( sv || (tt === 'plain' || tt === 'numeric') ) && $pblock.find(".rdataName").val() != '' )) {
				parent[$pblock.attr("data-rid")] = $pblock;
			}
		}
		return parent.length > 0 ? parent : false;
	}

	function grabData(forSend) {
		this.cpass = true;
		var allRows = {};
		$j("#mainList > li", $global).each(function (y) {
			var tcl = this.className, $t = $j(this);
			if (tcl.match(/subPart/)) {
				var subz = {};
				$t.find("ul > li").each(function (sy) {
					var $st = $j(this);
					subz[sy] = validateData($st, false);
				});

				allRows[y] = {
					subs: subz,
					name: $t.find(".secName").val(),
					otm: $t.find(".sfs").is(":checked"),
					tout: $t.find(".stabout").is(":checked")
				};
				if (!allRows[y].name) {
					$t.find(".secName").addClass('alert');
					this.cpass = false;
				} else {
					$t.find(".secName").removeClass('alert');
				}
			} else if (tcl.match(/prow/)) {
				allRows[y] = validateData($t, false);
			}
		});
		if ($j("#fname").val() == '') {
			alert("Please enter name of Form");
			$j("#fname").focus();
			return false;
		}
		/*if ($j("#fforms").val() != '0' && $j("#fcardinality").val() == '') {
			alert("Please choose a relationship type");
			$j("#fcardinality").focus();
			return false;
		}*/

		if (digestUsed === 0 && forSend === true) {
			this.cpass = false;
			this.emsg.push("At least one field should be used for header");
		}
		if (vdate === false) {
			//this.cpass = false;
			//this.emsg.push("Form must have one visit date field");
		}
		if (forSend === true) {
			if (this.cpass === true) {
				$j("#forSend").val(JSON.stringify(allRows));
				document.formform.submit();
			} else {
				this.cpass = true;
				alert("Please fill all highlited fields\n" + this.emsg.join("\n"));
				this.emsg = [];
			}
		}
		if (forSend === false) {
			return allRows;
		}
	}

	function validateData($st, forceReturn) {
		var check = true, res = false,
			vd = {
				vid: $st.attr("data-rid"),
				type: $st.data("dtype"),
				mand: $st.find(".rowMandat").is(":checked"),
				name: $st.find(".rdataName").val(),
				vname: $st.find(".linum").text(),
				sysv: $st.find(".sysval_use").val(),
				numb: $st.find(".rowTextType").val(),
				other: $st.find(".includeOther").is(":checked"),
				chain: $st.find(".upChain").is(":checked"),
				range: {
					start: ($st.find(".rangeStart").val() && $st.find(".rangeStart").val().length > 0 ? $st.find(".rangeStart").val() : undefined),
					end: ($st.find(".rangeEnd").val() && $st.find(".rangeEnd").val().length > 0 ? $st.find(".rangeEnd").val() : undefined)
				},
				smult: $st.find(".rowSelectMulti").is(":checked"),
				child: (function ($s) {
					var res = false;
					if ($s.find(".rowChild").is(":checked")) {
						res = {
							parent: $s.find(".childMaster").val(),
							trigger: $s.find(".parentTriggerValue").val()
						};
						if (!isset(res.parent) || (!isset(res.trigger) || res.trigger == '-1' )) {
							res = false;
						}
					}
					return res;
				})($st),
				dgst: $st.find(".useInDigest").is(":checked")
			};
		if($j('#bigform').is(':checked')){
			vd['mapping'] = [];
			$st.find("ul > li").each(function () {
				console.log($(this));
				vd['mapping'].push({form:$(this).find('.fieldForm').val(),field:$(this).find('.fieldField').val()})
			});
			//vd.mapping
		}
		if (!vd.type && !$j('#bigform').is(':checked')) {
			$st.find(".selDataType").addClass("alert");
			check = false;
		} else if($j('#bigform').is(':checked')){
			$st.find(".selDataType").removeClass("alert");
			vd.type = 'dbtype';
		}else {
			$st.find(".selDataType").removeClass("alert");
		}
		if (!vd.name) {
			$st.find(".rdataName").addClass("alert");
			check = false;
		} else {
			$st.find(".rdataName").removeClass("alert");
		}
		if (vd.sysv == '-1') {
			$st.find(".sysval_use").addClass("alert");
			check = false;
		} else {
			$st.find(".sysval_use").removeClass("alert");
		}
		if (isset(vd.range.start) === false && isset(vd.range.end) === false) {
			vd.range = false;
		} else {
			/*if($st.find(".rangeBlock").find(".indates").length === 0){
			 vd.range.start = parseFloat(vd.range.start);
			 vd.range.end = parseFloat(vd.range.end);
			 }*/
		}
		if (vd.range.end == 0) {
			this.emsg.push("Top limit of range can't be zero");
		}
		if (vd.type === 'entry_date') {
			vdate = true;
			vd.mand = true;
		}
		/*if(vd.range.start >0 && vd.range.end > 0 && vd.range.start >= vd.range.end){
		 $st.find(".rangeStart,.rangeEnd").addClass("alert");
		 this.emsg.push("Range error: end value can't be less or equal to start value");
		 check=false;
		 }else{
		 $st.find(".rangeStart,.rangeEnd").removeClass("alert");
		 }*/
		if (vd.dgst === true) {
			++digestUsed;
		}
		if (check === true || forceReturn === true) {
			return vd;
		} else {
			this.cpass = false;
			return check;
		}
	}

	function fillForm(seta) {
		console.log(seta);
		$j("#fname").val(seta.title);
		console.log(seta.forregistration);
		$j("#checkinscription").val(seta.forregistration);
		if(seta.forregistration==='1'){
			$j("#checkinscription").attr('checked','checked');
		}

		if(seta.typef=='bigform'){
			$j("#bigform").attr('checked','checked');
			self.processFormBigData();
		}
		$j("#ilmultiplicity").val(seta.multiplicity);console.log(seta.multiplicity);
		$j("#ftasks").val(seta.task_id);
		$j("#ftasks").empty();
		$j("#ftasks").append("<option value='"+seta.task_id+"' selected='selected'>"+seta.task_id+"</option>");
		/*if(seta.title){
			$j("#lfprojects").hide();
			$j("#lftasks").hide();
			$j("#fprojects").hide();
			$j("#ftasks").hide();
		}*/
		$j("#fprojects").val(seta.project_id);
		if(seta.project_id){
			$j.get("?m=wizard&pid="+seta.project_id+"&mode=loadtask&&suppressHeaders=1", function (msg) {
				if (msg && msg !== 'fail') {
					console.log("?m=wizard&pid="+seta.project_id+"&mode=loadtask&&suppressHeaders=1");
					msg = $j.parseJSON(msg);
					$j("#ftasks").empty();
					$j("#ftasks").append("<option value='all'>All</option>");
					$.each(msg, function(i,e){
						$j("#ftasks").append("<option value='"+msg[i].task_id+"'>"+msg[i].task_name+"</option>");
					});
					if(seta.task_id)
						$j("#ftasks").val(seta.task_id);
					else if(seta.alltask)
						$j("#ftasks").val('all');
				} else {
					$j("#ftasks").empty();
					$j("#msgbox").text("Can't get activity!").show().delay(2000).fadeOut(3000);
				}
			});
			
			$j.get("?m=wizard&pid="+seta.project_id+"&mode=loadforms&&suppressHeaders=1", function (msg1) {
				//console.log(msg1);
				if (msg1 && msg1 !== 'fail') {
					console.log("?m=wizard&pid="+seta.project_id+"&mode=loadforms&&suppressHeaders=1");
					msg1 = $j.parseJSON(msg1);
					$j("#fforms").empty();
					$j("#fforms").append("<option value='0'></option>");
					$.each(msg1, function(i,e){
						$j("#fforms").append("<option value='"+msg1[i].id+"'>"+msg1[i].title+"</option>");
					});
					$j("#fforms").val(seta.parent_id);
				} else {
					$j("#fforms").empty();
					$j("#msgbox").text("Can't get form!").show().delay(2000).fadeOut(3000);
				}
			});
		}
		
		if(seta.parent_id > 0){
			$j.get("?m=wizard&fid="+seta.parent_id+"&mode=loadfields&&suppressHeaders=1", function (msg) {
				$j("#available_columns").empty();
				$j("#selected_columns").empty();
				if (msg && msg !== 'fail') {
					console.log("?m=wizard&fid="+seta.parent_id+"&mode=loadfields&&suppressHeaders=1");
					msg = $j.parseJSON(msg);
					$.each(msg, function(i,e){
						if( (msg[i].hasOwnProperty('otm') && msg[i].otm == false) || 
							(!msg[i].hasOwnProperty('otm') && msg[i].type != "note") ){
							if(msg[i].hasOwnProperty('otm') && msg[i].otm == false){
								$.each(msg[i].subs, function(j,f){
									$j("#available_columns").append("<option value='"+msg[i].subs[j].dbfld+"'>"+msg[i].subs[j].name+"</option>");
									
									 $.each(seta.digestrel, function(x,r){
										if(r===msg[i].subs[j].dbfld){
											$j("#selected_columns").append("<option value='"+msg[i].subs[j].dbfld+"'>"+msg[i].subs[j].name+"</option>");
										}
									});
								})
							}else{
								$j("#available_columns").append("<option value='"+msg[i].dbfld+"'>"+msg[i].name+"</option>");
								$.each(seta.digestrel, function(x,r){
									if(r===msg[i].dbfld){
										$j("#selected_columns").append("<option value='"+msg[i].dbfld+"'>"+msg[i].name+"</option>");
									}
								});
							}
						}
					});
				} else {
					$j("#available_columns").empty();
					$j("#msgbox").text("Can't get fields!").show().delay(2000).fadeOut(3000);
				}
			});
		}
		
		if (seta.registry > 0) {
			$j("#regForm").trigger("click");
			this.registryUsed = true;
			$j("#viewbut").show();
		}
		var set = seta.rows;
		var isBigform = false;
		if(seta.typef=='bigform'){
			isBigform = true;
		}
		if (amnt(set) > 0) {
			for (var i in set) {
				if (set.hasOwnProperty(i)) {
					if ((set[i]).hasOwnProperty("otm") === true) {
						var vset = set[i].subs, nsec = wzrd.sectionWork('add'), $csec;
						if (nsec !== false) {
							$csec = $j("#" + nsec, $global);
							$csec.find(".secName").val(set[i].name).end();
							if (set[i].otm === true) {
								$csec.find(".sfs").nextAll("button:first").trigger("click").end()
									.find(".subStore").data("otm", true);
							}
							if (set[i].tout === true) {
								$csec.find(".stabout").nextAll("button:first").trigger("click"); //.end()
								//.find(".subStore").data("otm",true);
							}

							for (var s in vset) {
								if (vset.hasOwnProperty(s)) {
									rowDataforEdit(vset[s], $csec.find(".subStore:last"), false, isBigform, seta.touch); ///$j(".subStore:last",$global));
								}
							}
						}
					} else {
						rowDataforEdit(set[i], false, isBigform, seta.touch);
					}
				}
			}
			enumItems();
		}
	}

	function rowDataforEdit(row, $mpar, big, ftouch) {
		if(big){
			row.type = 'dbtype';
			//$t.data("dtype", row.type).find(".selDataType").append('<option value="dbtype">BigDataType</option>').val("dbtype").attr('disabled',"disabled");
		}
		//console.log(row);
		ftouch = (ftouch !== undefined && ftouch.length > 0 ? date2Val(ftouch) : date2Val(today()) );
		var tid = wzrd.rowWork("add", null, row, $mpar), $t = $j("" + tid), toclick = [], reveal = [];
		if (row.type !== 'empty' && row.type !== undefined) {
			$t.data("dtype", row.type)
				.find(".selDataType").val(row.type).trigger("change");
		}
		if(!row.hasOwnProperty('vname') ){
			row.vname = '';
		}
		$t.attr("data-rid", row.vid)
			.find(".rdataName").val(row.name).end()
			.find(".sysval_use").val(row.sysv).end()
			.find(".linum").text(row.vname).end()
			.find(".rowTextType").val(row.numb).trigger("change").end();
		if (row.sysv != '' && valtail[row.sysv] && date2Val(valtail[row.sysv]) > ftouch) {
			$t.find(".expired").removeClass("outspace");
		}
		if (row.mand === true) {
			toclick.push(".mandatq");
		}
		if (row.smult === true) {
			toclick.push(".rowSelectMulti");
		}
		if (row.dgst === true) {
			toclick.push(".bheader");
		}
		if (row.other === true) {
			toclick.push(".bother");
			reveal.push(".bother");
		}
		if (row.chain === true) {
			toclick.push('.rowChnBut');
			reveal.push(".rowChnBut");
		}
		if (toclick.length > 0) {
			$j("" + toclick.join(","), $t).trigger("mouseover").trigger("click");
			toclick = null;
		}
		if (row.range !== false && (row.range.start || row.range.end)) {
			reveal.push(".jbut");
			$t.find(".rangeStart").val(row.range.start).end()
				.find(".rangeEnd").val(row.range.end).end()
				.find(".jbut").toggleClass("brange brange_active").attr("title", function () {
					return composePeriod(row.range.start, row.range.end, (row.type === 'date'));
				});
		}
		if (reveal.length > 0) {
			$j("" + reveal.join(","), $t).removeClass("outspace");
		}
		$t.find(".rowChild").attr("checked", function () {
			var res = false;
			if (row.child !== false) {
				$j(this).closest("li").addClass("indent")
					.find(".ar_right").trigger("click").end()
					.find(".childMaster").val(row.child.parent).data("ftrigger", row.child.trigger).end()
					.find(".parentTriggerValue").eq(0).val(row.child.trigger);
				res = true;
			}
			return res;
		});
	}

	function workChild(e, way) {
		var thid = e.currentTarget,
			chb = $j(thid).parent().attr("data-for"),
			$selfRow = $j("#" + chb),
			ptva = [], $t = $j(thid).closest("li"),
			pnum;
		ptva[-1] = 'Parent Trigger Value';
		if (!$selfRow.is(":checked") && way === 'in') { //event launched before status of checkbox changed
			$selfRow.attr("checked", true);
			var ps = collectParents($t.addClass("indent")), child_sysv = $j(thid).closest("li").find(".sysval_use").val();
			$j("<div/>", {"class": "child_indent"}).html("&nbsp;").prependTo($t);
			if (ps !== false) {
				var $psel = $j("<select class='text childMaster'></select>").change({pcollected: ps}, function (e) {
					var tv = $j(this).val(), $parentRow = $j(e.data.pcollected[tv]),
						stype = $parentRow.data("dtype"), sysv = $parentRow.find(".sysval_use").val(), self = this,
						otheruse = $j(".includeOther").is(":checked");
					if (sysv === 'SysCenters' || sysv === 'SysPositions' || svals.isRelatives(child_sysv, sysv) && (stype === 'radio' || stype === 'select')) {
						$t.find(".rowChnBut").removeClass("outspace");
					} else {
						$t.find(".rowChnBut").addClass("outspace");
					}
					if (tv !== 'false') {
						$t.find(".parentTriggerValue").remove();
						$j.when(svals.getSV(sysv))
							.done(function (msg) {
								if (msg && msg != 'fail') {
									if (typeof msg === 'string' && trim(msg).length > 0) {
										sysvals = $j.parseJSON(msg);
										if (sysvals.hasOwnProperty("data")) {
											sysvals = sysvals.data;
											delete(sysvals.rels);
										}
									} else {
										sysvals = msg;
									}
									if (sysvals || (stype == 'numeric' || stype == 'plain')) {
										if ((typeof sysvals === 'string' && trim(sysvals).length > 0) || typeof sysvals === 'object') {
											ptva = appendArray(ptva, sysvals);
										}
										ptva.any = "Any answer (non-blank)";
										if (otheruse === true) {
											ptva.other = "Other";
										}
										var inid;
										if ($j(self).data("ftrigger")) {
											inid = $j(self).data("ftrigger");
											$j(self).data("ftrigger", '');
										} else {
											inid = -1;
										}
										$j(buildSelectList(ptva, inid, -1, '', 'parentTriggerValue')).appendTo($t.find(".slaveCase"));
									}
								}

							});
					}
				}), $tp;
				$j("<option value='false' disabled selected>--Select Parent field--</option>").appendTo($psel);
				var inval = false;
				for (var i in ps) {
					if (ps[i] !== undefined && !isNaN(i)) {
						$tp = $j(ps[i]);
						if (inval === false) {
							inval = $tp.attr("data-rid");
						}
						$j(["<option value='", $tp.attr("data-rid"), "'>", $tp.find(".rdataName").val(), '</option>'].join("")).appendTo($psel);
					}
				}
				$j("<span class='slaveCase'></span>").append($psel).appendTo($t.find(".btype")); //$selfRow.closest("li");//.find(".rowChildBut"));
				if (this.registryUsed === false) {
					$t.find(".childMaster").val(inval).trigger("change");
				}
			}
		} else if (way === 'out') {
			$selfRow.attr("checked", false).closest("li").find(".slaveCase,.child_indent").remove().end().removeClass("indent");
		}
		enumItems();
	}

	function purge() {
		$mainList.empty();
		$j("#fname").val("");
		$j("#fid").val("");
		rowID = 0;
	}

	function enumItems() {
		var ind = 1, chcount = [], lastparent;
		$j("li:not(.subPart):not(.vtitle)", $global).each(function (i) {
			var $parent = $j(this).closest("ul"), $zrow = $j(this);
			chcount[$j(this).attr("data-rid")] = {vname: ind, kids: 0};
			if ($parent.hasClass("subStore") && $parent.data("otm") !== true || $parent.hasClass("initlist") || self.registryUsed === true) {
				$j(this).find(".linum").text(function (i, x) {
					var myparent = $zrow.find(".childMaster").val();
					if ($zrow.find(".slaveCase").length === 1 && myparent) {
						return ( chcount[myparent].vname + abc[(chcount[myparent].kids++)] + "." );
					} else {
						return (ind++) + '.';
					}
				});
			}
		});
		$j(".subStore", $global).each(function () {
			if ($j(this).data("otm") === true && self.registryUsed === false) {
				ind = 1;
				$j(this).find("li").each(function (ix) {
					var $zrow = $j(this);
					$j(this).find(".linum").text(function (i, x) {
						var myparent = $zrow.find(".childMaster").val();
						if ($zrow.find(".slaveCase").length === 1 && myparent) {
							return ( chcount[myparent].vname + abc[(chcount[myparent].kids++)] + "." );
						} else {
							return (ind++) + '.';
						}
					});
				});
			}
		});
	}

	function pasteRows(pset) {
		var cnt = 0;
		for (var i in pset) {
			if (pset.hasOwnProperty(i)) {
				var ndata = pset[i];
				//<div class="fbutton qticon fedit" onclick="wzrd.fillEdit(',ndata.id,',false)" title="Edit"></div>
				
				if(!ndata.task_name){
					if(ndata.forregistration=='1' || ndata.alltask=='1')
						ndata.task_name = 'All';
					else
						ndata.task_name = 'No activity';
				}
				//if(ndata.forregistration=='1')
					//ndata.task_name = 'All';
				$j("<tr />", {
					"data-id": ndata.id,
					"data-last": date2Val(ndata.touch)
				})
					.append("<td>" + ndata.project_name + "</td>")
					.append("<td>" + ndata.task_name + "</td>")
					.append("<td class='" + (ndata.rows > 0 ? '' : "fedit") + "'>" + ndata.title + "</td>")
					.append("<td>" + ndata.multiplicity + "</td>")
					.append("<td>" + (ndata.registry == 1 ? 'Registry' : 'Continuous') + "</td>")
					.append("<td>" + (ndata.valid == 1 ? 'Active' : 'Inactive') + "</td>")
					.append("<td>" + ndata.valid_change + "</td>")
					.append("<td>" + ndata.rows + "</td>")
					.append(['<td class="alt" style="width: 200px" >',
							(ndata.rows > 0 ? "" : "<div class='fbutton qticon bedit' title='Edit'/>"),
						'<div title="View" class="fbutton qticon eye" onclick="wzrd.viewForm(', ndata.id, ')"/>&nbsp;',
						'<div title="Clone" class="fbutton qticon clone" onclick="wzrd.fillEdit(', ndata.id, ',true)"></div>&nbsp;',
						'<a class="fbutton qticon wordico" href="/?m=wizard&mode=printForm&&suppressHeaders=1&fid=', ndata.id, '" title="Printable">_</a>&nbsp;',
						'<div class="fbutton qticon ', (ndata.valid == 1 ? 'expire' : 'active'), '" onclick="exTurn(', ndata.id, ',this)" data-status="', ndata.valid, '" title="', (ndata.valid == 1 ? 'Deactivate' : 'Activate'), '"></div>',
						(ndata.rows > 0 ?
							['&nbsp;<div class="fbutton qticon trash" onclick="emptyFData(', ndata.id, ',this);" title="Empty form table"></div>&nbsp;'].join("")
							:
							['&nbsp;<div class="fbutton qticon delform" onclick="deleteForm(', ndata.id, ',this);" title="Delete"></div>&nbsp;'].join("")
							),
						'<div class="fbutton qticon export" onclick="wzrd.exportForm(', ndata.id, ')" title="Export"></div>&nbsp;</td>'].join(""))
					.appendTo("#qtable > tbody");
				++cnt;
			}
		}
		if (cnt > 0) {
			$j("#qtable").trigger("update");//.trigger("sorton",[[0,0]]);
		}
		return cnt;
	}

	function buildMockup(set) {
		var frows = [];
		for (var y in set) {
			if (set.hasOwnProperty(y)) {
				var ipart = set[y];
				if (ipart.hasOwnProperty('otm')) {
					var lcode = ['<table border="1" style="border-collapse: collapse;white-space: nowrap; "><thead><tr>'], std = [];
					for (var s in ipart.subs) {
						if (ipart.subs.hasOwnProperty(s)) {
							var zpart = ipart.subs[s];
							if (ipart.subs.hasOwnProperty(s)) {
								lcode.push("<th>" + zpart.name + "</th>");
								std.push("<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>");
							}
						}
					}
					lcode.push("</tr></thead><tbody><tr>" + std.join("") + "</tr></tbody></table>");
					var tc = lcode.join("");
					frows.push(tc);
				} else {
					frows.push(ipart.name + '.......................');
				}
			}
		}
		var content = "<p>" + frows.join("<br>\n ") + "</p>";
		$j("<div title='Form preview' class='lapaper'></div>").append(content).appendTo(document.body).dialog({width: "650px", buttons: {Close: function () {
			$j(this).dialog("close");
		} }});
	}

	function composePeriod(rs0, rs1, datecase) {
		var rtxt = '', vsigns = [' < ', ' > '];
		if (datecase === true) {
			vsigns = [' before ', ' after '];
		}
		if (isset(rs0) && rs0.toString().length > 0 && isset(rs1) && rs1.toString().length > 0) {
			rtxt = rs0 + " - " + rs1;
		} else {
			if ((!isset(rs0) || rs0.toString().length === 0) && isset(rs1) && rs1.toString().length > 0) {
				rtxt = [vsigns[0], rs1].join("");
			}
			if ((!isset(rs1) || rs1.toString().length === 0) && isset(rs0) && rs0.toString().length > 0) {
				rtxt = [vsigns[1], rs0 ].join("");
			}
		}
		return rtxt;
	}

	function composeQ(id) {
		return ["<div class='bware'>" ,
			"<div class='fbutton delRow'></div>",
			"<div class='fbutton hereBug expired outspace'></div>",
			"<input type='checkbox' value='1' class='rowMandat blind' id='rm_", id, "'><div title='Mandatory' data-iclass='mandatq' class='mandatq bbut' data-for='rm_", id, "' ></div>",
			'<input type="checkbox" value="1" class="rowChild blind" id="rch_', id, '">',
			'<input type="checkbox" value="1" class="useInDigest blind" id="rdgs_', id, '"><div data-iclass="bheader" class="bbut bheader rowDGSTBut" data-for="rdgs_' , id , '" title="Header"></div>',
			'<div class="fbutton clone" onclick="wzrd.cloneRow(this);" title="Copy question"></div>',
			'<div data-iclass="bother" class="bbut bother rowOthBut outspace" data-for="roth_' , id , '" title="Other"></div>',
			'<input type="checkbox" value="1" class="upChain blind" id="rchn_', id, '"><div data-iclass="chain" class="bbut chain rowChnBut outspace" data-for="rchn_' , id , '" title="Connect"></div>',
			"<div class='brange jbut outspace'></div>",
			"</div>"].join("");
	}

	return{
		preView: function () {
			buildMockup(grabData(false));
		},
		init: function () {
			self = this;
			tempprojectField = {};
			//$j('#fcardinality').attr('disabled', true);
			$j("#fprojects").live("change", function () {
				var pid = $j("#fprojects").val();
				if(pid){
					msg = null;
					$j("#fforms").empty();
					$j("#ftasks").empty();
					$j.get("?m=wizard&pid="+pid+"&mode=loadtask&suppressHeaders=1", function (msg) {
						if (msg && msg !== 'fail') {
							msg = $j.parseJSON(msg);
							$j("#ftasks").empty();
							$j("#ftasks").append("<option value='all'>All</option>");
							$.each(msg, function(i,e){
								$j("#ftasks").append("<option value='"+msg[i].task_id+"'>"+msg[i].task_name+"</option>");
							});
						} else {
							$j("#ftasks").empty();
							$j("#msgbox").text("Can't get activity!").show().delay(2000).fadeOut(3000);
						}
					});
					listformProject = {};
					msg1 = null;
					$j.get("?m=wizard&pid="+pid+"&mode=loadforms&suppressHeaders=1", function (msg1) {
						//console.log(msg1);
						if (msg1 && msg1 !== 'fail') {
							msg1 = $j.parseJSON(msg1);
							$j("#fforms").empty();
							$j("#fforms").append("<option value='0'></option>");
							$.each(msg1, function(i,e){
								listformProject[msg1[i].id] = msg1[i].title;
								$j("#fforms").append("<option value='"+msg1[i].id+"'>"+msg1[i].title+"</option>");
							});
						} else {
							$j("#fforms").empty();
							$j("#msgbox").text("Can't get form!").show().delay(2000).fadeOut(3000);
						}
					});
					
				}
				
			});
			
			$j("#ftasks").live("change", function () {
				var tid = $j("#ftasks").val();
				var pid = $j("#fprojects").val();
				if(tid && tid != 'all'){
					msg1 = null;
					$j.get("?m=wizard&tid="+tid+"&pid="+pid+"&mode=loadforms1&suppressHeaders=1", function (msg1) {
						//console.log(msg1);
						if (msg1 && msg1 !== 'fail') {
							msg1 = $j.parseJSON(msg1);
							$j("#fforms").empty();
							$j("#fforms").append("<option value='0'></option>");
							$.each(msg1, function(i,e){
								$j("#fforms").append("<option value='"+msg1[i].id+"'>"+msg1[i].title+"</option>");
							});
						} else {
							$j("#fforms").empty();
							$j("#msgbox").text("Can't get form!").show().delay(2000).fadeOut(3000);
						}
					});
				}
			});
			
			$j("#fforms").live("change", function () {
				$j("#available_columns").empty();
				$j("#selected_columns").empty();
				var fid = $j("#fforms").val();
				/*if(fid != '0'){
					$j('#fcardinality').attr('disabled', false);
				}else{
					$j('#fcardinality').attr('disabled', true);
				}*/
				$j.get("?m=wizard&fid="+fid+"&mode=loadfields&suppressHeaders=1", function (msg) {
					if (msg && msg !== 'fail') {
						msg = $j.parseJSON(msg);
						console.log(msg);
						$.each(msg, function(i,e){
							if( (msg[i].hasOwnProperty('otm') && msg[i].otm == false) || 
								(!msg[i].hasOwnProperty('otm') && msg[i].type != "note") ){
								if(msg[i].hasOwnProperty('otm') && msg[i].otm == false){
									$.each(msg[i].subs, function(j,f){
										$j("#available_columns").append("<option value='"+msg[i].subs[j].dbfld+"'>"+msg[i].subs[j].name+"</option>");
									})
								}else{
									$j("#available_columns").append("<option value='"+msg[i].dbfld+"'>"+msg[i].name+"</option>");
								}
							}
						});
					} else {
						$j("#available_columns").empty();
						$j("#msgbox").text("Can't get fields!").show().delay(2000).fadeOut(3000);
					}
				});
			});
			/*$checkinscriptionCtrl.live("click", function () {
				if($checkinscriptionCtrl.is(':checked')){
					$checkinscriptionCtrl.val("1");
					if($mainList.html().trim()==""){
						var url = '/modules/wizard/inscription.json';
						$j.get(url, function (ret) {
							
							//ret = $j.parseJSON(ret);
							//$mainList.empty();
							//fillForm(ret);
							///$temp = $mainList.html();
							$mainList.empty();
							$mainList.append(ret);
							$mainList.append($temp);///
						});
					}
				}else{
					$checkinscriptionCtrl.val("0");
					if($mainList.html().trim()==""){
						$mainList.empty();
					}
				}
				alert($checkinscriptionCtrl.val());
			});*/
			$sectionCtrl.live("click", function () {
				var zcl = $j(this).attr("class"),
					$parent = $j(this).closest('li'),
					$mpos = $parent.find("ul.subStore"),
					otm = $parent.find(".sls").is(":checked");
					console.log($j(this));

				if (zcl.match(/delRow/)) {
					var $tli = $j(this).closest("li"), godel = false;
					if ($tli.closest("ol").hasClass("lastone")) {
						if ($tli.closest("ol").find("li").length > 1) {
							godel = true;
						}
					} else {
						godel = true;
					}
					if (godel === true && confirm("Do you want delete this row?")) {
						$tli.remove();
					}
				} else if (zcl.match(/inc/) && !zcl.match(/qticon/)) {
					wzrd.rowWork('add', null, null, $mpos, otm);
				} else if (zcl.match(/del/) && !zcl.match(/qticon/)) {
					$mpos.closest(".listWrap").addClass("alert");
					if (confirm("Do you want to delete this section?")) {
						wzrd.sectionWork('del', $mpos);
					} else {
						$mpos.closest(".listWrap").removeClass("alert");
					}
				}
				enumItems();
			});


			$j(".myType").live("dblclick", function (e) {
				wzrd.rowWork('add', this, null, null);
			});

			$j(".pureNumeric").live("mouseover", function (e) {
				if (!$j(this).data("ninit")) {
					$j(this).data("ninit", true);
					$j(this).numeric();
				}
			});

			$j(".sfs").live("click", function (e) {
				var nstate = $j(this).is(":checked");
				$j(this).closest("li").find("ul").data("otm", nstate);
				enumItems();
			});

			$j(".newsval").live("click", function (e) {
				wzrd.newSysVal(this);
			});

			$j(".sysval_use").live("change", function (e) {
				if ($j(this).val() != '-1') {
					wzrd.getSysValOpts(this);
				}
			});

			$j("#qtable").tablesorter({headers: {5: {sorter: false}}, widgets: ["fixHeadW"]}).tableStripe();
			if (pforms && amnt(pforms) > 0) {
				pasteRows(pforms);
			}

			$j("#tabs").tabs().css("visibility", "visible");

			$mainList.sortable(sortableOptions).droppable();

			$j("#regForm").flatButton(false, function () {
				wzrd.registryUsed = !wzrd.registryUsed;
				enumItems();
				if (self.registryUsed === true) {
					$j("#viewbut").show();
				} else {
					$j("#viewbut").hide();
				}
			});
			//$j("#regFormCap").click();
			this.registryUsed = false;

			$j(".myimporter").delegate("input:eq(0)", "change",function (e) {
				var fname = $j(this).val();
				if (fname.split('.').pop().toLowerCase() === 'fbn') {
					$j(this).next().attr("disabled", false);
				} else {
					$j(this).val("");
					alert("Please select file with FBN extension for form export/import operations");
				}
			}).next().attr("disabled", true);

			$j(".brange,.brange_active", $global).live("click", function (e) {

				var isDates = $j(this).hasClass("indates"),
					$sbox = $j(this).closest("li").find(".rangeBlock"),
					rstart = $sbox.find(".rangeStart").val(),
					rend = $sbox.find(".rangeEnd").val();
				$j("#rangeDLG").remove();
				$j("<div />", {id: "rangeDLG", title: "Define range for value"})
					.append("<p class='abox '></p><span>Start&nbsp;<input class='text pureNumeric' id='rd0' size='9'>&nbsp;End&nbsp;<input class='text pureNumeric' id='rd1' size='9'></span>")
					.find("#rd0").val(rstart).end()
					.find("#rd1").val(rend).end()
					.appendTo(document.body)
					.dialog({
						buttons: {
							Cancel: function () {
								$j(this).dialog("close");
							},
							Save: function () {
								var rs0 = $j("#rd0").val(),
									rs1 = $j("#rd1").val(),
									ur0 = rs0,
									ur1 = rs1,
									res = true,
									emsg = '',
									rtxt = '';
								if (isDates === true) {
									ur0 = date2Val(rs0);
									ur1 = date2Val(rs1);
								}
								if (rs0.length > 0 && rs1.length > 0) {
									if (parseFloat(ur1) < parseFloat(ur0)) {
										res = false;
										emsg = "End value must be " + (isDates === true ? "later" : "greater") + " than start value";
									}
								}
								if (rs0.length === 0 && rs1.length === 0) {
									//res=false;
									//emsg="You must define at least one of values (Start or End)";
									res = 'clean';
								}
								if (res === false) {
									$j(this).find(".text").addClass("alert").end().find(".abox").text(emsg);
								} else if (res === true) {
									rtxt = composePeriod(rs0, rs1, isDates);
									$sbox
										.find(".rangeStart").val(rs0).end()
										.find(".rangeEnd").val(rs1).end()
										.closest("li").find(".jbut").attr("title", "Validation: " + rtxt).removeClass("brange").addClass("brange_active");
									$j(this).dialog("close").remove();
								} else if (res === 'clean') {
									$sbox.closest("li").find(".jbut").removeClass("brange_active").addClass("brange").attr("title", "Validation").end().end()
										.find(".rangeStart").val("").end()
										.find(".rangeEnd").val("");
									$j(this).dialog("close").remove();
								}
							}
						},
						width: "350px"

					});
				if (isDates === true) {
					$j("#rd0,#rd1").datepick(calObj).blur();
				}
			});

			$j(".wrmoves", $global).live("click", function (e) {
				var $row = $j(this).closest("li"),
					acr = $j(this).attr("class").match(/ar_(\w+)\s/),
					renumber = false;
				var action = acr[1], $parent, position, total;
				if (action === 'up' || action === 'down') {
					$parent = $row.parent();
					total = $parent.find(" > .prow").length;
					position = $parent.find(" > .prow").index($row);
					renumber = true;
				}
				switch (action) {
					case 'up':
						if (total > 1 && position > 0) {
							$parent.find("li").eq(position - 1).before($row);
						}
						break;
					case 'down':
						if (total > 1 && position < total) {
							$parent.find("li").eq(position + 1).after($row);
						}
						break;
					case 'left':
						workChild(e, "out");
						break;
					case 'right':
						workChild(e, "in");
						break;
				}
				if (renumber === true) {
					//enumItems();
				}
			});

			$j(".bbut", $global).live("mouseenter", function (e) {
				if (!$j(this).data("bclck")) {
					$j(this).data("bclck", true).xtraButton();
				}
			});

			//$j(".fedit").live("dblclick", function (e) {
			$j(".bedit").live("click",function(e){
				var rid = $j(this).closest("tr").attr("data-id");
				wzrd.fillEdit(rid, false);
			});

			$j(".expired", $global).live("click", function (e) {
				var $but = $j(this);
				$j("<div id='xpired'></div>")
					.dialog({
						buttons: {
							Close: function () {
								$j(this).dialog("close").add($but).remove();
							}
						},
						closeOnEscape: false,
						close: function (e, ui) {
							$but.remove();
						}
					})
					.append("<p>This value set was changed after form was saved!</p>").end()
					.appendTo(document.body);
			});
		},
		sectionWork: function (mtd, $block) {
			var secsNow = $j(".subPart", $global).length, letgo = false;
			if (this.registryUsed === false || (this.registryUsed === true && secsNow === 0)) {
				letgo = true;
			}
			if (mtd === 'add' && letgo === true) {
				$j(['<li class="subPart" id="secrow_', sectionCount, '"><div class="listWrap"><span class="mholder"></span>',
					'Section Name&nbsp;&nbsp;<input type="text" class="text secName" value="">',
					'<input type="checkbox" value="1" class="sfs" id="sec_', sectionCount, '"><label for="sec_', sectionCount, '">One-to-many</label>',
					'<input type="checkbox" value="1" class="stabout" id="sectab_', sectionCount, '"><label for="sectab_', sectionCount, '">Tabular output</label><br>',
					'<ul class="qlist subStore"></ul></div></li>'].join(""))
					.find("input.text").after($sectionCtrl.not(".qticon").clone(true).show()).end()
					.find("ul.subStore").droppable(dropOptions).sortable(sortableSub).end()
					.appendTo($mainList);
				$j("#sec_" + sectionCount + ", #sectab_" + sectionCount++).flatButton();
			} else if (mtd === 'del' && $block) {
				$block.closest("li").remove();
			}
			enumItems();
			return (letgo === true ? 'secrow_' + (sectionCount - 1) : false);
		},
		populateSelectField: function(elsf,idsf){
			var selfval = elsf.value;
			if(selfval!="") {
				if(tempprojectField.hasOwnProperty(selfval)){
					msg = tempprojectField[selfval];
					$.each(msg, function (i, e) {
						if ((msg[i].hasOwnProperty('otm') && msg[i].otm == false) ||
							(!msg[i].hasOwnProperty('otm') && msg[i].type != "note")) {
							if (msg[i].hasOwnProperty('otm') && msg[i].otm == false) {
								$.each(msg[i].subs, function (j, f) {
									$j("#"+idsf).append("<option value='" + msg[i].subs[j].dbfld + "'>" + msg[i].subs[j].name + "</option>");
								})
							} else {
								$j("#"+idsf).append("<option value='" + msg[i].dbfld + "'>" + msg[i].name + "</option>");
							}
						}
					});
				}else{
					$j.get("?m=wizard&fid=" + selfval + "&mode=loadfields&suppressHeaders=1", function (msg) {
						if (msg && msg !== 'fail') {
							msg = $j.parseJSON(msg);
							tempprojectField[selfval] = msg;
							$.each(msg, function (i, e) {
								if ((msg[i].hasOwnProperty('otm') && msg[i].otm == false) ||
									(!msg[i].hasOwnProperty('otm') && msg[i].type != "note")) {
									if (msg[i].hasOwnProperty('otm') && msg[i].otm == false) {
										$.each(msg[i].subs, function (j, f) {
											$j("#"+idsf).append("<option value='" + msg[i].subs[j].dbfld + "'>" + msg[i].subs[j].name + "</option>");
										})
									} else {
										$j("#"+idsf).append("<option value='" + msg[i].dbfld + "'>" + msg[i].name + "</option>");
									}
								}
							});
						} else {
							$j("#msgbox").text("Can't get fields!").show().delay(2000).fadeOut(3000);
						}
					});
				}

			}
		},
		addField: function(idli){
			var lis = $("#"+idli+" li");
			idli1 = idli+"-"+lis.length+'-select';
			console.log(idli1);
			$j('#'+idli).append('<li class="prow"><div class="fbutton BDdelRow"></div><select class="fieldForm" onchange="self12.populateSelectField(this,\''+idli1+'\')"><option value="">Choose Form</option></select>&emsp;<select class="fieldField" id="'+idli1+'" style="width:150px"><option value="">Choose Field</option></select></li>');
			$('.fieldForm').each(function() {
				if($j(this).find("option").length<2){
					$selectEl = $j(this);
					$.each(listformProject,function(idf,titlef) {
						//$selectEl.html($j(this).html()+'<option value="'+idf+'">'+titlef+'</option>')
						$selectEl.append($('<option>', {value:idf, text:titlef}));
					});
				}
			});
		},
		createBigDataMapping: function ($frow,idli){
			idli1 = 'idli-'+idli+'-select';
			self12 = this;
			$frow.append('<br/><br/><div class="inc" style="cursor:pointer;" onclick="self12.addField(\'idli-'+idli+'\')">+ Add Field</div><br/><br/> <ul id="idli-'+idli+'" style="list-style: none;"><li class="prow"><div class="fbutton BDdelRow"></div><select class="fieldForm" onchange="self12.populateSelectField(this,\''+idli1+'\')"><option value="">Choose Form</option></select>&emsp;<select class="fieldField" id="'+idli1+'" style="width:150px"><option value="">Choose Field</option></select></li></ul><br/><br/>');
		},
		editBigDataMapping: function ($frow,idli){
			idli1 = 'idli-'+idli+'-select';
			self12 = this;
			$frow.append('<br/><br/><div class="inc" style="cursor:pointer;" onclick="self12.addField(\'idli-'+idli+'\')">+ Add Field</div><br/><br/> <ul id="idli-'+idli+'" style="list-style: none;"></li></ul><br/><br/>');
		},
		rowWork: function (mtd, presel, rowd, prnt, otm) {
			console.log(rowd);
			if (mtd === 'add') {
				var $tgt, $zsel = $j("<div/>", { "class": "btype"}).append(buildSelectList(fieldTypes, false, 'empty', 'wzrd.doDataType(this);', 'selDataType'));
				if (presel) {
					var $tli = $j(presel).closest("li");
					$zsel.find("select").val($tli.data('dtype'));
					$tli.find(".btype").replaceWith($zsel);
				} else {
					$zsel.find("select").val("empty");
					if (!prnt) {
						$tgt = $mainList;
					} else {
						$tgt = prnt;
					}
					$frow = $j('<li class="prow" data-rid="' + rowID + '" id="zrow_' + rowID + '"></li>')
						.append(['<div class="rowhead"><span class="mholder"></span><span class="linum"></span>&nbsp;<input type="text" class="text rdataName" size="20" value="">&nbsp;&nbsp;',
							'<div data-for="rch_', rowID, '" class="rowhead"><span title="move up" class="zbutton ar_up wrmoves"/><span title="move down" class="zbutton ar_down wrmoves"/><span title="make parent" class="zbutton ar_left wrmoves"/><span title="make child" class="zbutton ar_right wrmoves"/></div>'].join(""))
						.append($zsel)
						.append(composeQ(rowID));
					if(rowd != null && $j('#bigform').is(':checked')){
						this.editBigDataMapping($frow, rowID);
						//for (var i in set) {
						//	if (set.hasOwnProperty(i)) {
								//addField
						//	}
						//}
					}else if ($j('#bigform').is(':checked'))
						this.createBigDataMapping($frow, rowID);
					$frow.appendTo($tgt);
					if ($j('#bigform').is(':checked')) {
						//$('.btype').hide();
						//.append('<option value="dbtype">BigDataType</option>')
						$frow.find(".selDataType").val("dbtype").attr('disabled',"disabled");
						$('.fieldForm').each(function () {
							if ($j(this).find("option").length < 2) {
								$selectEl = $j(this);
								$.each(listformProject, function (idf, titlef) {
									//$selectEl.html($j(this).html()+'<option value="'+idf+'">'+titlef+'</option>')
									$selectEl.append($('<option>', {value: idf, text: titlef}));
								});
							}
						});
					}

					++rowID;
				}
				enumItems();
				return "#zrow_" + (rowID - 1);
			}
		},
		doDataType: function (xr) {
			var ftype = $j(xr).val(),
				$fname, cval,
				$parli = $j(xr).closest('li'),
				oldtype = $parli.data("dtype"),
				ypos = $parli.attr("data-rid");
			
			$parli.data("dtype", ftype);
			if ($j.inArray(ftype, sysvalDepend) >= 0) {
				if ($j("." + ftype, $parli).length === 0) {
					//alert(ftype);
					$j("<span/>", {"class": "svbox"})
						.append($store.find("." + ftype).clone(true).val("-1"))
						.append(['<input type="checkbox" value="1" class="includeOther blind" id="roth_' + ypos + '">'].join(""))
						.appendTo($parli.find(".btype"));
					$parli.find(".rowOthBut").removeClass("outspace");
				}
			} else {
				$parli.find(".svbox").remove().end().find(".rowOthBut").addClass("outspace");
			}
			if (ftype === 'note') {
				$fname = $parli.addClass("vtitle").find(".linum").val("").hide().end().find(".rdataName");
				cval = $fname.val();
				$fname.replaceWith('<textarea rows="3" cols="50" class="text rdataName">' + cval + '</textarea>');
			} else {
				if (oldtype === 'note') {
					$fname = $parli.removeClass("vtitle").find(".linum").show().end().find(".rdataName");
					cval = $fname.val();
					$fname.replaceWith(['<input type="text" class="text rdataName" size="20" value="', cval, '">'].join(""));
				}
				if ($j.inArray(ftype, textRowTypes) >= 0 /* === 'textRow'*/) {
					if (ftype === 'numeric') {
						$parli
							.find(".rangeBlock").remove().end()
							.find(".jbut").removeClass("outspace indates brange_active").addClass("brange").attr("title", "Validation").end()
							.append("<span class='rangeBlock'>&nbsp;<input type='hidden' class='rangeStart'><input type='hidden' class='rangeEnd'></span>");
					} else {
						$parli.find(".rangeBlock").remove().end().find(".jbut").addClass("outspace");
					}
				} else if (ftype === 'date') {
					$parli.find(".rangeBlock,.strictPart").remove();
					$parli.find(".jbut").addClass("indates brange").removeClass("outspace brange_active").attr("title", "Validation").end()
						.append("<span class='rangeBlock'><input type='hidden' class='rangeStart'><input type='hidden' class='rangeEnd'></span>");
				} else {
					$parli.find(".jbut").addClass("outspace");
				}
			}
			$parli.find("select:eq(0)").replaceWith('<span class="myType">' + fieldTypes[ftype] + '</span>');
		},
		collect: function () {
			grabData(true);
		},
		cloneRow: function (t) {
			$j("<div/>", {id: "cloneDLG"})
				.append("<p><i>Enter number of question copies you need</i></p>")
				.append("<input type='text' class='text pureNumeric' id='ncopies' size='4'>")
				.find("#ncopies").trigger("mouseover").end()
				.dialog({
					buttons: {
						Cancel: function () {
							$j(this).dialog("close").remove();
						},
						Apply: function () {
							var todo = parseInt($j("#ncopies").val());
							if (isNaN(todo) || todo === 0) {
								$j(this).find("p").addClass("alert").text("Please enter number of copies to add to form");
							} else if (todo > 0) {
								var $row = $j(t).closest("li"),
									look = $row.attr("data-rid"),
									lreg = new RegExp("_" + look, "g"),
									$mpar = $row.parent(),
									newid, $nrow,
									predata = validateData($row, true);

								predata.name = '';
								if (predata.type === undefined) {
									predata.type = 'empty';
								}
								for (var i = 0; i < todo; i++) {
									predata.vid = rowID++;
									rowDataforEdit(predata, $mpar, false);
								}
								$j(this).dialog("close").remove();
								enumItems();
							}
						}
					}
				});
		},
		fillEdit: function (id, clone) {
			
			purge();
			$j("#editTab").find("img").show();
			$j.get("?m=wizard&fid=" + id + "&mode=editf&suppressHeaders=1", function (msg) {
				if (msg && msg !== 'fail') {
					//console.log(msg);
					msg = $j.parseJSON(msg);
					if (clone === false) {
						$j("#fid").val(id);
					}
					fillForm(msg);
				} else {
					$j("#msgbox").text("Invalid form selected!"+id).show().delay(2000).fadeOut(3000);
				}
				$j("#editTab").find("img").hide();
				$j("#tabs").toTab(1);
			});
		},
		clean: function () {
			if (confirm("Remove all question data?")) {
				purge();
			}
		},
		newSysVal: function (trbut) {
			svals.editSV(false, trbut);
		},
		exportForm: function (fid) {
			$j("<div>Save to file form info</div>").dialog({
				width: 325,
				modal: true,
				title: "Export form",
				buttons: {
					"Cancel": function () {
						$j(this).dialog("close");
					},
					"Only Form": function () {
						wzrd.exportWork(fid, 0);
						$j(this).dialog("close");
					},
					"With Data": function () {
						wzrd.exportWork(fid, 1);
					}
				}
			});
		},
		exportWork: function (fid, wdata) {
			document.location = ["/?m=wizard&suppressHeaders=1&todo=exportf&fid=", fid, "&wdata=", wdata].join("");
		},
		getSysValOpts: function (svo) {
			var $svb = $j(svo),
				$prow = $svb.closest("li"),
				sv = $svb.val();
			if (sv && sv.length > 0 && sv !== '-1') {
				$j(".sysval_use", $prow).after("<img src='/images/tab_load.gif' id='load_sv_ps'>");
				$j.when(svals.showSV(sv, true, svo))
					.done(function (a) {
						$j("img", $prow).remove();
					});

			}
		},
		addIForm: function (set) {
			if (!set || set === 'fail') {
				info("Import failed", 0);
			} else {
				var ndata = $j.parseJSON(set);
				if (amnt(ndata) > 0) {
					if (ndata.hasOwnProperty('title')) {
						wzrd.finishFormInsert(ndata);
					} else if (ndata.hasOwnProperty('withsets') && ndata.withsets === true) {
						svals.importHalf(JSON.stringify(ndata.sinfo));
					}

				}
				$j("#importbox").toggle();
			}
		},
		finishFormInsert: function (ndata) {
			var res = pasteRows(ndata);
			if (res > 0) {
				info("New form : " + ndata[0].title + " successfully imported", 1);
			}
		},
		viewForm: function(fid){
			window.open('/?m=wizard&a=form_useed&fid='+fid+'&todo=addedit&teaser=1','demoForm');
		},
		processFormBigData: function(){
			$j('#checkinscription,#lcheckinscription,#lftasks,#ftasks,#lfforms,#fforms,#fmultiplicity,#lfmultiplicity,#lreference,#reference,.sectionbtn').hide();
		},
		processFormWizard: function(){
			$j('#checkinscription,#lcheckinscription,#lftasks,#ftasks,#lfforms,#fforms,#fmultiplicity,#lfmultiplicity,#lreference,#reference,.sectionbtn').show();
		}
	};
}(wzrd));

var frm = (function (my) {

	var $wf = $j("#wform"),
		self, registry, refrels, irun = true;

	function parseData() {
		var dirty = 0, res = false;
		$j(":input.mandat:visible", $wf).each(function () {
			if (solveField(this) === false) {
				++dirty;
			}
		});
		$j(".inrange:visible", $wf).each(function () {
			var rngs = $j(this).attr("data-rng").split("|"),
				origval = $j(this).val(),
				tv = parseFloat(origval),
				start = parseFloat(rngs[0]),
				end = parseFloat(rngs[1]),
				tpl = '',
				uvals,
				showtxt;
			if (!isNaN(start) && !isNaN(end)) {
				tpl = 'Value must be in limit between %s and %s';
				showtxt = sprintf(tpl, start, end);
			} else {
				if (isNaN(start) && !isNaN(end)) {
					tpl = "Value must be less than %s";
					uvals = end;
				} else if (!isNaN(start) && isNaN(end)) {
					tpl = "Value must be greater than %s";
					uvals = start;
				}
				showtxt = sprintf(tpl, uvals);
			}
			if (!(isset(origval) && tv > 0 && tv >= start && tv <= end)) {
				$j(this).addClass("alert").parent()
					.find(".rngmsg").remove().end()
					.append(["<span class='alert rngmsg'>", showtxt, "</span>"].join(""));
				++dirty;
			} else {
				$j(this).removeClass("alert").parent().find(".rngmsg").remove();
			}
		});
		if (dirty > 0) {
			alert("Please fill all required fields!");
		} else {
			res = true;
		}
		return res;
	}

	function solveField(obj) {
		var $o = $j(obj), ptype = $o.attr("type").toLowerCase(),
			/*
				ptag = $o[0].nodeName.toLowerCase(),
				pname = $o.attr("name"),
			*/
			pval = $o.val(), res = false,
			$pcontrol = $o.closest("td").find(".fcontrol");

		if (ptype === 'radio') {
			pval = $o.parent().find(":input:checked").length || null;
		}
		if (pval == -1 || !isset(pval) || pval === 'empty' || trim(pval.toString()).length === 0) {
			$pcontrol.removeClass("rowDone").addClass("hereBug");
		} else {
			$pcontrol.removeClass("hereBug").addClass("rowDone");
			res = true;
		}
		return res;
	}

	function prepareMandats() {
		$j("select.mandat", $wf).live("change", function (e) {
			solveField(this);
		});
		/*$j(".mandat:not(select)",$wf).closest("td").focusout(function(e){
		 solveField($j(this).find(":input"));
		 });*/
		$j(".mandat").live("mouseenter", function () {
			if (!$j(this).data("minit")) {
				$j(this).data("minit", true)
					.closest("td")
					.focusout(function (e) {
						solveField($j(this).find(":input"));
					})

			}
		});
		$j(".delRow", $wf).live("click", function (e) {
			console.log("C isit");
			frm.delSubRow(this);
		});
	}

	function buildRels(rels) {
		if (rels && amnt(rels) > 0) {
			var viewopts = [
				['table-row', false],
				['none', true]
			];
			refrels = rels;
			for (var ir in rels) {
				if (rels.hasOwnProperty(ir)) {
					var tr = rels[ir];
					$wf.delegate("" + ir, "mouseenter", {s: tr, o: ir}, function (se) {
						if (!$j(this).data("reinit")) {
							$j(this).data("reinit", true);
							if (this.type.toLowerCase() === "radio") {
								var $radpar = $j(this).parent(), pardi = se.data.o;
								$radpar.find(":input").data("reinit", true);
								$radpar.click({vd: se.data.s, o: se.data.o}, function (e) {
									var adata = e.data,
										$chkd = $j(this).find(":input:checked")[0],
										fp = findTVal($chkd.value, adata.vd, irun);
									/*if($chkd && tgt !== false){
								 disaction=false;
									 }else{
									 tgt=[[],''];
									 for(var x=0,l=e.data.vd.length; x < l; x++){
									 tgt[0].push(e.data.vd[x][0]);
									 }
									 tgt[0]=tgt[0].join(", ");
									 }
									 if(e.data.o.charAt(0) === '#'){
									 $j(""+tgt[0]).css("display",function(){
									 return (disaction === true ? 'none' : 'table-row');
									 }).find(":input").attr("disabled",disaction);
									 }else{
									 $j(this).closest("tr").find(""+tgt[0]).attr("disabled",disaction).end()
									 .find(""+getRest(tgt,e.data.vd)).attr("disabled",true);
									 }*/
									for (var i = 0, l = fp.length; i < l; i++) {
										if (fp[i] && fp[i] != '') {
											if (adata.o.charAt(0) === '#') {
												$j("" + fp[i]).css("display", viewopts[i][0]).find(":input").attr("disabled", viewopts[i][1]);
											} else {
												$j(this).closest("tr").find("" + fp[i]).attr("disabled", viewopts[i][1]).end();
											}
										}
									}
								});
							} else {
								$j(this).change({tset: tr}, function (e) {
									var adata = e.data.tset,
										selectedVal = $j(this).val(), fp;
									if (selectedVal && selectedVal != '-1') {
										fp = findTVal(selectedVal, adata, irun);
										for (var i = 0, l = fp.length; i < l; i++) {
											if (fp[i] && fp[i] != '') {
												if (fp[i].charAt(0) === '#') {
													//if(adata[2] === false){
													$j("" + fp[i]).css("display", viewopts[i][0]).find(":input").attr("disabled", viewopts[i][1]);
													//}else if(adata[2] && adata[2].length > 0){

													//}
												} else {
													$j(this).closest("tr").find("" + fp[i]).attr("disabled", viewopts[i][1]).end();
													/*}else if(i===1){
													 $j(this).closest("tr").find(""+getRest(fp[0],adata)).attr("disabled",true);
													 }*/
												}
											}
										}
									}
								});

							}
						}
					});
					if ($j("" + ir).val() && $j("" + ir).val() != '-1') {
						$j("" + ir).trigger("mouseover").trigger("change");
					}
				}
			}
		}
		irun = false;
	}

	function findTVal(val, arr, lrun) {
		var res = [], newfp = [], nay = [];
		for (var i in arr) {
			if (i && arr.hasOwnProperty(i)) {
				var aid = arr[i];
				if (val === aid[1] || (aid[1] === 'any' && val != '-1' && val.length > 0)) {
					if (aid[1] === 'any' && aid[2] && aid[2].length > 0) {
						var $tobj = $j("" + aid[0]), stock = [], inival = $tobj.find("select").val(), uval;
						$tobj.append($j("<div/>", { "class": "loading"}));
						$j.get("/?m=wizard&suppressHeaders=1&a=sysvals&stype=select&mode=getNSet&sval=" + aid[2] + "&parval=" + val, function (msg) {
							if (msg && msg !== 'fail') {
								var nlist = $j.parseJSON(msg);
								if (nlist.hasOwnProperty("data")) {
									nlist = nlist.data;
								}
								stock.push("<option value='-1' disabled>-- Select --</option>");
								if (amnt(nlist) > 0) {
									for (var ni in nlist) {
										stock.push(["<option value='", ni, "'>", nlist[ni], "</option>"].join(""));
									}
								}
							}
							if (lrun === true) {
								uval = inival;
							} else {
								uval = '-1';
							}
							$tobj.find("select").empty().html(stock.join("")).val(uval).end().find(".loading").remove();
						});
					}
					res.push(aid[0]);
				} else {
					nay.push(aid[0]);
				}
			}
		}
		return [res.join(","), nay.join(",")];
	}

	function getRest(seld, arr) {
		var res = [];
		for (var i in arr) {
			if (i && arr.hasOwnProperty(i)) {
				if ((isArray(seld) && seld !== arr[i]) || seld !== arr[i][0]) {
					res.push(arr[i][0]);
				}
			}
		}
		return res.join(", ");
	}

	function addTabRow($t) {
		var $row = $t.find("tbody > tr:first").clone(), rtop = $t.data("maxRow") || $t.find("tbody > tr").length,
			$tbody = $t.find("tbody"),
			todis = [],
			t = cloneThis(calObj);

		t.defaultDate = "";
		t.onClose = function (dates) {
			$j(this).trigger("focusout");
		};

		for (var ip in refrels) {
			if (refrels.hasOwnProperty(ip)) {
				var rpt = refrels[ip];
				for (var x = 0, l = rpt.length; x < l; x++) {
					todis.push(rpt[x][0]);
				}
			}
		}
		var todis1 = todis.join(", ");

		$t.data("maxRow", ++rtop);
		$row
			.html(function (e, code) {
				code = code.replace(/(fld_(\d+_subs|)\[)\d+\]/g, "$1" + rtop + "]");
				code = code.replace(/rowDone/, "");
				code = code.replace(/hereBug/, "");
				return code;
			})
			.find(":input[type='text']").val("").filter(".spCals").attr("id", "").removeClass("hasDatepick")
			.next("img").remove().end()/*.datepick("destroy")*/.datepick(t).datepick("enable").end().end()
			.find("" + todis1).attr("disabled", true).end()
			.appendTo($tbody);
	}

	function dropSubRow(b) {
		var $row = $j(b).closest("tr"),
			$t = $row.closest("tbody"),
			total = $t.find("tr").length;
		if (total === 1) {
			return false;
		} else {
			$row.remove();
		}
		return true;
	}

	return {
		checkForm: function () {
			if (parseData() === true) {
				$j("#wform").submit();
			}
		},
		init: function (ps, isreg) {
			prepareMandats();
			if (ps > 0) {
				parseData();
			}
			self = this;
			registry = isreg;
		},
		brels: function (rels) {
			buildRels(rels);
		},
		addSubRow: function (bloc) {
			var $table = $j(bloc).closest("td").find(".usub");
			addTabRow($table);
		},
		delSubRow: function (b) {
			dropSubRow(b);
		}
	}
}(frm));

var svals;
svals = (function (my) {
	var loaded = false,
		$ltab = $j("#stable"),
		$lbody = $ltab.find("tbody"),
		blist = false,
		localCache = {},
		dsvals,
		zlname = '',
		exdata = {},
		evbind = false,
		setTypes = {
			"-1": "-- Select --",
			select: "List (single)",
			select_multi: "List (multiple)",
			radio: "Radio",
			checkbox: "Multi-select",
			calculateChoice:'Calculate Choice (single)',
			calculateChoiceMult:'Calculate Choice (multi)'
		},
	/*setLevels={
	 "-1"    :"-- Select --",
	 single : "Single",
	 multi  : "Multiple"
	 },*/
		setStatus = ["Inactive", "Active"],
		osvals = $j("<p/>"),
		todayDate = today(),
		tableOn = false;

	$j("#stock").find(".sysval_use-old").find("option:gt(0):lt(4)").remove().end().clone().find("option:eq(0)")
		.before("<option value='-1' disabled='disabled'>Existing system value</option>").end()
		.appendTo(osvals);


	function valTableIcrement(y, vd, force) {
		if (tableOn === false) {
			blist = false;
			loadVals();
			return;
		}
		if (y === false) {
			y = amnt(dsvals);
		}
		var chld = false,chldCount = 0;
		if (vd.parent != vd.id) {
			chld = true;
		}
		if(vd.hasOwnProperty('childs') && vd.childs > 0){
			chldCount = vd.childs;
		}
		var vopts = vd.options.join("<br>").replace(/\|/g, "."), vcopts, $nrow;
		vopts = vopts.replace(/\d+<#>/g, "<br>");
		vcopts = trimView(vopts);
		$nrow = $j("<tr/>", {id: "svrow_" + y, "data-rid": vd.id, "data-from": vd.gtype })
			.append(["<td class='", (chld === false || vd.gtype !== "local" ? "ledit" : ''), "'>" , vd.title , '</td>'].join(""))
			.append("<td>" + setTypes[vd.vtype] + '</td>')
			.append(["<td>" , (vd.vtype === 'select_multi' ? "Multi" : 'Single') , (vd.id === vd.parent ?
				(chldCount > 0 ? '&nbsp;(parent)' : '')
				:
				'&nbsp;(child)'), '</td>'].join(""))
			.append("<td class='vstat'>" + (vd.gtype === 'local' ? setStatus[vd.status] : '') + '</td>')
			.append("<td>" + (vd.gtype === 'local' ? dateView(vd.touch) : "") + '</td>')
			.append(["<td class='", (vcopts.n === true ? "moreview" : ''), "' data-text='", ( vcopts.n === true ? "inbox" : '' ), "'>" , vcopts.s ,
				"<div class='blind'>",vopts,"</div>",
				'</td>'].join(""));

		if (y === false || force === true) {
			$nrow.appendTo($lbody);
			$j("." + vd.type).append(["<option value='", vd.id, "'>", vd.title, "</option>"].join(""));
		} else {
			$j("#svrow_" + y, $ltab).replaceWith($nrow);
			$j("#stock").find("." + vd.type).find("option[value='" + vd.id + "']").text(vd.title);
		}
		dsvals[y] = vd;
	}

	function fillValSet() {
		var ctab = new RegExp("<#>", "g"), t = $j.get("/?m=wizard&a=sysvals&suppressHeaders=1&mode=loadall", function (m) {
			if (m && m != 'fail') {
				dsvals = $j.parseJSON(m);
				if (amnt(dsvals) > 0) {
					tableOn = true;
					for (var i in dsvals) {
						if (dsvals.hasOwnProperty(i)) {
							//console.log(dsvals[i]);
							//dsvals[i].options = dsvals[i].options.replace(/\|/g,".").split("\n");
							//dsvals[i].options = dsvals[i].options.replace(/\d+\|/g, "").split("\n");
							dsvals[i].options = dsvals[i].options.split("\n");
							var lvs = dsvals[i];
							if (lvs.parent == lvs.id) {
								for (var n = 0, l = lvs.options.length; n < l; n++) {
									
									//dsvals[i].options[n] = (n + 1 + ".") + lvs.options[n];
									dsvals[i].options[n] = lvs.options[n];
								}
							} else {
								
								
								for (var n = 0, l = lvs.options.length, ci = 1; n < l; n++) {
									if (ctab.test(lvs.options[n])) {
										ci = 1;
										dsvals[i].options[n] = lvs.options[n].replace(">", ">" + ci + ".");
									} else {
										dsvals[i].options[n] = (ci + ".") + lvs.options[n];
									}
									++ci;
								}
							}
							valTableIcrement(i, dsvals[i], true);
						}
					}
				}
			}
		});
		return t.promise();
	}

	function loadVals() {
		if (blist === false) {
			blist = true;
			dsvals = [];
			var $zltab = $ltab.detach().find("tbody").empty().end(), ctab = new RegExp("<#>", "g");
			$j("#tabs-3").html("Loading...");
			$j.when(fillValSet())
				.done(function () {
					if (amnt(dsvals) > 0) {
						$j("#tabs-3").empty().append($zltab);
						$j(['<div id="tinbox" class="myimporter">',
							'<form name="uptin" action="/?m=wizard&suppressHeaders=1&a=sysvals&mode=import_init" enctype="multipart/form-data" method="POST" onsubmit="return AIM.submit(this, {\'onStart\' : startCallback, \'onComplete\' : svals.importHalf})">',
							'<input type="file" name="formfile" id="fultra" data-ext="vbn">',
							'<input type="submit" value="Import File" class="button" disabled="disabled" >',
							'</form></div>',
							"<p><button class='text' onclick='svals.editSV(false,this);' >"+add_new_value_set+"</button>&nbsp;",
							"<button class='text' onclick='svals.exportAll();'>"+export_all+"</button>&nbsp;",
							"<button class='text' style='margin-right: 250px;' onclick='svals.importSet(this);'>"+import_+"</button>",
							search_+"&nbsp;<input type='text' class='text' id='sv_filter' value=''><span class='fbutton cleartxt outspace'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>",
							"<img src='images/zload.gif' border='0' class='outspace'></p>"].join(""))
							.find("input").keyup(function (e) {
								$j(this).next("img").toggleClass("outspace");
								var ftext = trim($j(this).val());
								if (ftext === '') {
									$ltab.find("tr").show();
									$j(this).parent().find("span").addClass("outspace");
								} else {
									var ftreg = new RegExp(ftext, "gi");
									//timeDiff.setStartTime();
									for (var y in dsvals) {
										if (dsvals.hasOwnProperty(y) && dsvals[y]) {
											if (ftreg.test(dsvals[y].title) || ftreg.test(dsvals[y].options.join(""))) {
												$j("#svrow_" + y, $ltab).show();
											} else {
												$j("#svrow_" + y, $ltab).hide();
											}
										}
									}
									//alert(timeDiff.getDiff());
									$j(this).parent().find("span").removeClass("outspace");
								}
								$j(this).next("img").toggleClass("outspace");
							}).end()
							.find("span").click(function () {
								$j(this).parent().find("input").val("").trigger("keyup").end().end().addClass("outspace");
							}).end()
							.prependTo("#tabs-3");
						$ltab.show().tablesorter({headers: {4: {sorter: "pdates"}, 5: {sorter: false}}, widgets: ["fixHeadW"]})
								.tableStripe();
					} else {
						$j("#tabs-3").empty().html("<p><button class='text' onclick='svals.editSV(false,this);'>New system value</button></p>");
					}
					if (evbind === false) {
						evbind = true;
						$j(".opmove").live("click", function (e) {
							var $row = $j(this).closest("li"), acr = $j(this).attr("class").match(/ar_(\w+)\s?/), renumber = false;
							var action = acr[1], $parent, position, total, $lrow, $aset;
							if (action === 'up' || action === 'down') {
								$parent = $row.closest("ol");
								$aset = $parent.find(" > li");
								total = $aset.length;
								position = $aset.index($row);
								renumber = true;
							}
							switch (action) {
								case 'up':
									if (total > 1 && position > 0) {
										$parent.find(" > li").eq(position - 1).before($row);
									}
									break;
								case 'down':
									if (total > 1 && position < total) {
										$parent.find(" > li").eq(position + 1).after($row);
									}
									break;
								default:
									break;
							}
						});
						$j(".addRow").live("click", function () {
							var $list = $j(this).closest("ol"), $row = $j(".newsv_row:first", $list).clone(true).find(".clone").removeClass("clone_on").end();
							$row.find("ol").html("").remove().end().find(".delRow").show().end().appendTo($list)
								.find(".text").val("").removeClass("alert").focus().attr("data-prev_rid", "").end();
						});
					}
					/*}else{
					 $j("#tabs-3").html("<i>Failed to load system value</i>");
					 }*/
				});
		}
	}

	function dialogSV(id, trbut, scope) {
		$sstart = $j('<select id="sstart" name="sstart"><option value="0">Start with 0</option><option selected="selected" value="1">Start with 1</option></select>').change(function(e){
			console.log($j("#sstart").val());
			$j("#olopt").attr('start',$j("#sstart").val());
		});
		$j("#dbnewsv").dialog("destroy").remove();
		var $dbox = $j('<div id="dbnewsv" title="' + ( id === false ? 'Create new' : 'Edit'  ) + ' System Value"></div>')
		$j("<form id='formnd'></form>")
			.append("<input type='hidden' name='svid' value=''><input type='hidden' name='sys_old' value=''><input type='hidden' name='chld_data' id='chld_data'><input type='hidden' id='scope' value='" + (typeof scope === "undefined" || id === false ? "local" : scope  )+"'>")
			.append("<p class='namerow'>Name of set&nbsp;&nbsp;<input type='text' class='text push_top' name='sysval_name' id='svname'></p>")
			.append($j("<p/>").append("Type&nbsp;").append(buildSelectList(setTypes, -1, -1, '', 'vtype')))
			// .append($j("<p>").append("Level&nbsp;").append(buildSelectList(setLevels, -1, -1,'','level') ))
			.append($j("<p/>").append($sstart))
			.append("<span class='pwrite'>Options</span>")
			.append("<ol class='lastone' start='1' id='olopt'><li class='newsv_row'> <div class='optbox' ><div class='opmove ar_up' title='Move Up'/>&nbsp;<div class='opmove ar_down' title='Move Down'/></div><input type='text' class='text' name='sysval_opts[]' size='23'>&nbsp;<span class='fbutton delRow' title='Remove'/><span class='fbutton addRow' title='Insert'/><span class='fbutton clone' title='Child'/></li></ol>")
			.appendTo($dbox);
		/*$j("#sstart").change(function(e){
			console.log($j("#sstart").val());
			$j("#olopt").attr('start',$j("#sstart").val());
		});*/
		$dbox.appendTo(document.body).hide().dialog({
			modal: true,
			width: "400px",
			resizable: false,
			autoOpen: false,
			buttons: {
				Cancel: function () {
					$j(this).dialog("close");
					$j("#dbnewsv").remove();
				},
				Save: function () {
					var res = true, $box = $j("#dbnewsv"), collect = {}, lastParent, lastInd;

					if ($j("#svname").val() == '') {
						res = false;
						$j(".namerow", $box).css("background", "red");
					}
					else {
						collect.name = $j("#svname").val();
						$j(".namerow", $box).css("background", "inherit");
					}
					collect.id = parseInt(id);
					collect.start = $j("#sstart").val();
					collect.options = [];
					$j("li > .text", $box).each(function (item) {
						if ($j(this).val() == '' && item != 0) {
							$j(this).addClass("alert");
							res = false;
						}else {
							if (!$j(this).hasClass("sub_opt")) {
								lastParent = this;
								lastInd = collect.options.length;
								collect.options.push([$j(this).attr("data-prev_rid"), $j(this).val()]);
							} else {
								if (!collect.options[lastInd].hasOwnProperty("parent")) {
									collect.options[lastInd] = {parent: collect.options[lastInd], child: []};
								}
								collect.options[lastInd].child.push([$j(this).attr("data-prev_rid"), $j(this).val()]);
							}
							$j(this).removeClass("alert");

						}
					});
					$box.find(":input:hidden").each(function (i) {
						if (i === 1) {
							collect.sys_old = $j(this).val();
						}
					});
					var $mainType = $box.find("#formnd").find(".vtype");
					collect.vtype = $mainType.val();
					if (collect.vtype == -1) {
						$mainType.addClass("alert");
						res = false;
					} else {
						$mainType.removeClass("alert");
					}
					$mainType = undefined;
					collect.level = $j(".level", $box).val();
					collect.scope = $j("#scope",$box).val();
					if (collect.level == -1) {
						$j(".level", $box).addClass("alert");
						res = false;
					} else {
						$j(".level", $box).removeClass("alert");
					}
					collect.status = $j("#sv_active").is(":checked");
					collect.child = {};
					var $cBox = $j("#childbox");
					if ($j("#chld_name_un").val() == '') {
						$j("#chld_name_un").addClass("alert");
					} else {
						$j("#chld_name_un").removeClass("alert");
						collect.child.name = $j("#chld_name_un").val();
					}
					if ($cBox.find("select").val() == '-1') {
						$cBox.find("select").addClass("alert");
					} else {
						$cBox.find("select").removeClass("alert");
						collect.child.type = $cBox.find("select").val();
					}
					$cBox = undefined;
					if ($j("#child_id").val() != '') {
						collect.child.id = $j("#child_id").val();
					}
					if (res === true) {
						$j.post("?m=wizard&suppressHeaders=1&a=sysvals&mode=insval", {
							nsval: JSON.stringify(collect)
						}, function (msg) {
							console.log(msg);
							if (msg && msg !== 'fail') {
								var mecall = trbut.nodeName.toLowerCase();
								/*collect.title=collect.name;
								 collect.status = (collect.status === true ? 1 : 0 );
								 collect.touch = dateStore(todayDate);
								 $j(collect.options).each(function(i){
								 if (this.hasOwnProperty("parent")) {
								 collect.options[i]=[(i + 1), '|' , this.parent[1]].join("");
								 }
								 else {
								 collect.options[i] = [(i + 1), '|' , this[1]].join("");
								 }
								 });
								 //collect.options=collect.options.join("\n");
								 */
								if (id == 0) {
									var $blist = $j("#stock").find(".sysval_use").append("<option value='" + collect.name + "'>" + collect.name + "</option>").clone();
									if (mecall == 'div') {
										$j(trbut).closest("li").find(".sysval_use").replaceWith($blist).clone(true).val(collect.name);
									}
									/*if(loaded === true  && parseInt(msg) > 0){
									 collect.id = parseInt(msg);
									 collect.status=1;
									 valTableIcrement(false,collect,true);
									 }*/
									//loadVals();
								} else {
									if (exdata.name != collect.name) {
										$j(trbut).closest("tr").find("td:first").text(collect.name);
										$j("#stock").find(".sysval_use").find("option[value='" + exdata.name + "']").replaceWith("<option value='" + collect.name + "'>" + collect.name + "</option>");
									}
									/*if(localCache.hasOwnProperty(exdata.name)){
									 delete(localCache[exdata.name]);
									 }
									 valTableIcrement(exdata.row_id,collect,false);
									 if(collect.hasOwnProperty("child") && collect.child.id > 0){
									 if(localCache.hasOwnProperty(collect.child.name))
									 delete(localCache[collect.child.name]);
									 valTableIcrement(collect.child.id,collect,false);
									 }*/
								}
								blist = false;
								loadVals();
								$box.dialog({
									title: 'Value SET Saved!',
									buttons: {}
								}).effect('highlight', false, 1500, function () {
										$box.dialog("close").remove();
									});

							}
						})
					}
				}
			}
		});
		//Data entrance utility - prepare form for edit of sysval

		if (id > 0) {
			$dbox
				.find(":input:hidden:eq(0)").val(id).end()
				.find("#svname").val(exdata.name);
			//.end()
			//.find("ol").prev("span").before("<input type='checkbox' id='sv_active' value='1'/><label for='sv_active'>Active</label><br>");
			//$j("#sv_active",$dbox).button();
			var odata = dsvals[exdata.row_id];
			if (odata.status == 1) {
				$j("#sv_active", $dbox).trigger("click");
			}
			$j(".vtype", $dbox).val(odata.vtype);
			$j(".level", $dbox).val(odata.level);
			var goBuild = $j.Deferred() , ctime;

			var tobj = {
				draw: function(){
					$j.when(fillVals($dbox, exdata.name, false, scope))
						.done(function (msg) {
							$dbox.dialog("open").show();
						});
				}
			};

			goBuild.promise(tobj);

			if(exdata.childExist === true){
				$j.when(exdata.childRequest).done(function(){
						goBuild.resolve();
					});
			}else{
				goBuild.resolve();
			}
			tobj.done(function(){
				tobj.draw();
			});

		} else {
			var $cloneList = $j("<select class='cloneSVS text'></select>").change(function (e) {
				$dbox.dialog("disable");
				var pick = $j(this).val(),
					odata = dsvals[pick];
				if (odata.status == 1) {
					$j("#sv_active", $dbox).trigger("click");
				}
				$j(".vtype", $dbox).val(odata.vtype);
				$j(".level", $dbox).val(odata.level);
				$j.when(fillVals($dbox, odata.title, true)).done(function (msg) {
					//$dbox.dialog("open").show();
					$dbox.dialog("enable");
				});
			});
			if (amnt(dsvals) > 0) {
				$j("<option value='-1' disabled='disabled' selected='selected'> Select Value set to clone </option>").appendTo($cloneList);
				for (var z in dsvals) {
					if (dsvals.hasOwnProperty(z)) {
						$j(["<option value='", z, "'>", dsvals[z].title, "</option>"].join("")).appendTo($cloneList);
					}
				}
				//$cloneList = $j($cloneList[0]).wrap("<p/>");
			} else {
				$cloneList = $j("<span/>");
			}
			$dbox
				.prepend($cloneList).find(".cloneSVS").wrap("<p/>").end()
				.prepend(osvals).find(".sysval_use-old").val("-1").change(function (e) {
					var nsval = $j(this).val();
					$dbox
						.find("#svname").val(nsval).end()
						.find("li").eq(0).find(".text").val("").end().end().filter(":gt(0)").remove();
					fillVals($dbox, nsval, true);
					$j("#formnd").find(":input:hidden:eq(1)").val(nsval);
				}).end()
				.dialog("open");
			//$dbox;
		}
		$j(".lastone").delegate(".clone", "click", function (e) {
			var $inirow = $j(this).removeClass("clone_on").closest("li"), pref = '', $children = $inirow.find(".childblk");
			//Consider existence of block - if yes - then delete existing
			if ($children.length === 1) {
				$children.remove();
				if ($dbox.find(".childblk").length == 0) {
					$j("#childbox").slideUp(500).remove();
					$j("#dbnewsv").data("wchild", false);
					makeProperParent(false);
				}
			}

			else {
				//No block found, so we need create new one
				if (!$j("#dbnewsv").data("wchild")) {
					//TODO add auto multi off so no multi child for multi parent
					$j("<p>", {id: 'childbox'}).append("<label>Child set name&nbsp;<input type='text' class='text' id='chld_name_un'></label><br><br>")
						.append($j("<span/>").append("Child Type&nbsp;").append(buildSelectList(setTypes, -1, -1, '', 'vtype')))
						.append("<input type='hidden' id='child_id'>")
						.insertBefore("#formnd");

					$j("#dbnewsv").data("wchild", true);
				}
				if (!$inirow.attr("data-prev_rid")) {
					pref = '[flow]';
				}
				else {
					pref = '[' + $inirow.attr("data-prev_rid") + ']';
				}
				$j(this).addClass("clone_on");
				$j(["<ol class='childblk'><li class='newsv_row dubdent'>",
					"<div class='optbox'>",
					"<div class='opmove ar_up' title='Move Up'/>&nbsp;<div class='opmove ar_down' title='Move Down'/>",
					"</div>",
					"<input type='text' class='text sub_opt' name='sysval_opts", pref, "[]' size='20'>&nbsp;",
					"<span class='fbutton delRow'/><span class='fbutton addRow'/></li>", "</ol>"].join("")).appendTo($inirow);
				$inirow.find(".text").focus();
				makeProperParent(true);
			}
		});
	}

	function makeProperParent(choice){
		var $parentSelector = $j("#formnd").find(".vtype"), selVal = $parentSelector.val();
		if(choice === true){
			if(selVal == 'select_multi' || selVal == 'checkbox'){
				$parentSelector.val("-1");
				//disable options with multiple choice
				$parentSelector.find("option").each(function(){
					var bv = $j(this).attr("value");
					if(bv == 'select_multi' || bv  =='checkbox'){
						$j(this).attr("disabled",true);
					}
				});
			}
		}else{
			$parentSelector.find("option:gt(0)").attr("disabled",false);
		}
	}

	function clearObjectsArray(ress){
		var  res = {};
		for(var io in ress){
			if(ress.hasOwnProperty(io)){
				if(ress.hasOwnProperty(io) && typeof ress[io] === "object" && ress[io].hasOwnProperty("key")){
					res[""+ress[io].key.toString()] = ress[io].val;
				}else{
					res[""+io.toString()] = ress[io];
				}
			}
		}
		return res;
	}

	function fillVals($obj, zname, dig, scope) {
		scope = scope || 'local';
		var tv = $j.Deferred();
		$j.when(svals.getSV(zname, dig, null, scope))
			.done(function (a) {
				if (typeof a === 'string' && a != 'fail') {
					a = $j.parseJSON(a);
				}
				$j("#dbnewsv").show().dialog("open");
				if (amnt(a) > 0) {
					a = clearObjectsArray(a);
					var first = true, firstChild = false, echild = exdata.child, pset = (a.hasOwnProperty("data") ? a.data : a );
					for (var i in pset) {
						if (pset.hasOwnProperty(i) && !isNaN(i)) {
							if (first === false || $obj.find("li").length === 0) {
								//$obj.parent().find("button:eq(0)").trigger("click");
								$obj.find(".addRow:eq(0)").trigger("click");
							} else {
								first = false;
							}
							$j(".newsv_row:last", $obj).find(":input").val(pset[i]).attr("data-prev_rid", trim(i));
						}
						if (exdata.hasOwnProperty("child") && echild.data.rels && echild.data.rels[i] && echild.data.rels[i].length > 0) {
							var $curli = $j(".newsv_row:last", $obj), tprevid;
							$curli.find(".clone").trigger("click");

							for (var ix = 0, il = echild.data.rels[i].length; ix < il; ix++) {
								if (ix > 0) {
									$curli.find(".dubdent:last > .addRow").trigger("click");
								}
								tprevid = echild.data.rels[i][ix];
								$curli.find(".sub_opt:last").val(echild.data[tprevid]).attr("data-prev_rid", tprevid);
							}

							if (firstChild === false) {
								$j("#chld_name_un").val(findById(echild.id, 'name')).closest("p").find("select").val(findById(echild.id, 'type'));
								$j("#child_id").val(echild.id);
								firstChild = true;
							}
						}
					}
					if (dig === false) {
						$j("#dbnewsv").find(".delRow").hide();
					}
				}
				return tv.resolve();
			});
		return tv.promise();
	}

	function vStatus(o) {
		var tid = $j(o).closest("tr").attr("data-rid"),
			cvtxt = $j(o).text(), cpos = $j.inArray(cvtxt, setStatus), npos = 0;
		$j.get(["?m=wizard&a=sysvals&suppressHeaders=1&mode=tstatus&vid=", tid, "&vtxt=", cvtxt.toLowerCase()].join(""), function (msg) {
			if (msg && msg === 'ok') {
				if (cpos === 0) {
					npos = 1;
				}
				$j(o).text(setStatus[npos]).css("background-color", "red").animate({"background-color": "#ffffff"}, 2000);
			} else {
				return false;
			}
		});


	}

	function findById(id, target) {
		if (parseInt(id) === 0) {
			return false;
		} else {
			for (var i in dsvals) {
				if (dsvals.hasOwnProperty(i)) {
					if (dsvals[i].id == id) {
						var res;
						if (target == 'name') {
							res = dsvals[i].title;
						} else if (target == 'item') {
							return dsvals[i];
						} else {
							res = dsvals[i].vtype;
						}
						return res;
					}
				}
			}
		}
		return false;
	}

	return {
		importSet: function (tb) {
			$j("#tinbox").toggle();
			if ($j("#tinbox").is(":visible")) {
				$j(".myimporter").delegate("input:eq(0)", "change", formFileExt).next().attr("disabled", true);
				/*function(e){
				 var bext=$j(this).attr("data-ext"),
				 rcvd = $j(this).val().split(".").pop();
				 if(rcvd === bext){
				 $j(this).next().attr("disabled",false);
				 }else{
				 $j(this).val("");
				 alert("File for import must have extension "+bext.toUpperCase());
				 }
				 }*/

				//$j(tb).attr("disabled",true);
			}
		},
		importHalf: function (msg) {
			if (msg && msg != 'fail' && msg.length > 0) {
				var pin = $j.parseJSON(msg);
				if (pin.result === true && pin.happen > 0) {
					$j("#tinbox").find("input:eq(0)").val("").end().hide();
					loadVals();
					info("Value sets imported", 1);
				} else {
					if (pin.result === 'partial' && amnt(pin.multi) > 0) {
						var pmult = pin.multi, tbody = [];
						for (var i in pmult) {
							if (pmult.hasOwnProperty(i)) {
								var pl = pmult[i];
								tbody.push(['<tr>',
									'<td>', pl.title, '</td>',
									'<td>', pl.now_touch, '</td>',
									'<td>', pl.in_touch, '</td>',
									'<td class="vcentr"><input type="checkbox" data-prev_id="', pl.now_id, '" data-future_id="', pl.in_id, '">',
									'</td></tr>'].join(""));
							}
						}
						var $ptbox = $j("<div/>", {id: "partSets", title: 'Please select sets to overwrite'})
								.html(["<table class='tdialog'><thead><tr><th>Name</th><th>Last update(local)</th><th>Last update(new)</th><th>Overwrite</th></tr></thead><tbody class='vbd'>", tbody.join(""), "</tbody></table>"].join("")),
							form_use = 0;

						if (pin.form_case === true) {
							form_use = 1;
						}
						$ptbox.dialog({
							closeOnEscape: false,
							modal: true,
							width: 400,
							buttons: {
								Proceed: function () {
									var towrite = {use: {}, leave: {} }, topart;
									$j("#partSets").find("input[type='checkbox']").each(function () {
										topart = 'leave';
										if ($j(this).is(":checked")) {
											topart = 'use';
										}
										towrite[topart][$j(this).attr("data-prev_id")] = $j(this).attr("data-future_id");
									}).end().dialog("buttons", {});
									$j.post("/?m=wizard&suppressHeaders=1&a=sysvals&isform=" + form_use + "&mode=import_finish",
										{parts: JSON.stringify(towrite), wdone: JSON.stringify(pin.done), relvs: JSON.stringify(pin.children)},
										function (msg) {
											$j("#partSets").dialog("close").remove();
											var mparts = msg.split("#@#");
											if (mparts[0] != '0' && amnt(towrite) > 0 || pin.happen > 0) {
												$j("#tinbox").find("input:eq(0)").val("").end().hide();
												loadVals();
												info("Value sets imported", 1);
												if (form_use === 1 && mparts[1] && mparts[1].length > 0) {
													wzrd.finishFormInsert($j.parseJSON(mparts[1]));
												}
											} else if (mparts[0] === '0') {
												info("Value set import failed", 0);
											}

										});
								}
							}
						});
						$j("#partSets").prev().find(".ui-dialog-titlebar-close").hide();//closest(".ui-dialog")
					} else {
						info("Value set import failed", 0);
					}
				}
			}
		},
		init: function () {
			if (loaded === false) {
				loadVals();
				$ltab
					.delegate(".ledit", "dblclick", function () {
						var $tr = $j(this).closest("tr"), svid = $tr.attr("data-rid");
						exdata = {
							name: $j(this).text(),
							id: svid,
							row_id: $tr.attr("id").replace(/\D/g, ""),
							gtype: $tr.attr("data-from"),
							childExist : false
						};
						//search for child dataset
						for (var i = 0, l = dsvals.length; i < l; i++) {
							if (dsvals[i].parent == svid && svid != dsvals[i].id && exdata.gtype === dsvals[i].gtype) {
								exdata.childExist = true;
								(function (i) {
									exdata.childRequest = svals.getSV(dsvals[i].title, false,null, exdata.gtype);
									$j.when(exdata.childRequest)
													//$j.when(svals.getSV(dsvals[i].title, false,null, exdata.gtype))
										.done(function (ms) {
											if (ms && ms != 'fail') {
												var mcv = (typeof ms == 'string' ? $j.parseJSON(ms) : ms);
												exdata.child = {
													id: dsvals[i].id,
													data: mcv['data']
												}
											}
										});
								})(i);
								i = l + 1;
							}
						}
						dialogSV(svid, this,exdata.gtype);
					})
					.delegate(".vstat", 'dblclick', function () {
						vStatus(this);
					});
			}
			loaded = true;

		},
		exportAll: function () {
			$j("<iframe src='/?m=wizard&a=sysvals&mode=exportAll&suppressHeaders=1' style='display:none;width:0; height:0; '/> ").appendTo(document.body);
		},
		getSV: function (s, part, byId, scope) {
			var svw , res ={}, qpart;
			if (!s) {
				return ' ';
			}
			if (byId === true) {
				byId = 1;
			} else {
				byId = 0;
			}
			/*if(localCache.hasOwnProperty(s)){
			 res = localCache[s];
			 //svw.resolveWith(res);
			 return res;
			 }else{*/
			if (part === true || scope !== 'local') {
				qpart = 'getSV';
			} else {
				qpart = 'getNSet';
			}
			svw = $j.get(["?m=wizard&a=sysvals&suppressHeaders=1&mode=", qpart, "&stype=select&wid=", byId, "&sval=", s].join(""));
			svw.done(function (msg) {
				if (msg && msg !== 'fail') {
					console.log("?m=wizard&a=sysvals&suppressHeaders=1&mode=");
					res = $j.parseJSON(msg);
					if (res.hasOwnProperty("data")) {
						localCache[s] = res.data;
					}
				}
			});
			return svw.promise();
			//}
		},
		addSV: function (key, val) {
			localCache[key] = val;
		},
		showSV: function (sv, kamikaze, obj) {
			$j.when(svals.getSV(sv))
				.done(function (opts) {
					var svoffset = $j(obj).offset();
					if (opts != 'fail') {
						if (typeof opts === 'string') {
							opts = $j.parseJSON(opts);
						}
						if (opts.hasOwnProperty("data")) {
							opts = opts.data;
						}
						var $tip = $j("<div id='tipbox'></div>"),
							$sl = $j("<ol></ol>");
						for (var i in opts) {
							if (opts.hasOwnProperty(i) && !isNaN(i)) {
								$j("<li>" + opts[i] + "</li>").appendTo($sl);
							}
						}
						$sl.appendTo($tip);
						$tip.appendTo(document.body);
						$tip.dialog({
							title: "Options",
							position: [(svoffset.left + 10), (svoffset.top + 25)],
							open: function (e, ui) {
								if (kamikaze === true) {
									$j(this).slideDown(1).delay(1000).fadeOut(1500, function () {
										$j(this).dialog("close").dialog("destroy");
									});
								}
							}//,
							//autoOpen: !kamikaze
						});
						//$tip.dialog("open");
					}
					//$j("#tipbox").dialog("destroy");
				});
		},
		editSV: function (id, obj) {
			dialogSV(id, obj);
		},
		isRelatives: function (child, parent) {
			(function (parent) {
				zx = $j.when(fillValSet())
					.done(function (a) {
						var tc = findById(child, "item");
						//tc=localCache[tc_name];
						return tc.parent === parent;
					});
			})(parent);
			return zx.promise();
		}
	};
}(svals));

jQuery.fn.liveStrict = function (action) {
	if (!$j(this).data("inits")) {
		$j(this).data("inits", true);
		eval("$j(this)." + action);
	}
};

function trimView(str, xlength) {
	var res = {};
	str = str.replace(/<br>/g, " ");
	if (!xlength) {
		xlength = 45;
	}
	if (str && str.length > xlength) {
		res = {
			n: true,
			s: ''
		};
		var words = str.split(" "), clen = 0, ind = 0;
		while (clen < xlength) {
			var nast = words[ind] + ' ';
			res.s += nast;
			clen += nast.length;
			++ind;
		}
		if (res.s.length > xlength) {
			res.s = res.s.slice(0, xlength);
		}
		if (res.s.length < str.length) {
			res.s += '...';
		}
	} else {
		res = {n: false, s: str}
	}
	return res;
}

function deleteForm(fid, but) {
	prtfname = prompt("Enter form name to confirm","");
	var $par = $j(but).closest("tr");
	/*$par.fadeOut(800);*/
	if (prtfname==$par.find("td:eq(2)").text()) {
		$j.get('/?m=wizard&todo=del&suppressHeaders=1', {fid: fid}, function (msg) {
			if (msg && msg === 'ok') {
				$par.fadeOut(800);
				info("Form " + $par.find("td:eq(2)").text() + " removed", 1);
			} else {
				info("Failed to remove form", 0);
			}
		});
	}else{
		alert('Incorrect form name! Sorry!');
	}
}

function emptyFData(fid, but) {
	/*if (confirm("Do you want to delete all entries in this form?")) {
		$j.get('/?m=wizard&todo=empty&suppressHeaders=1', {fid: fid}, function (msg) {
			//console.log(msg);
			if (msg && msg === 'ok') {
				var $par = $j(but).closest("td");
				$par.prev().text("0");
				$j(but).replaceWith(['<div class="fbutton qticon delform" onclick="deleteForm(', fid, ',this);" title="Delete"></div>'].join(""));
				info("Form " + $par.find("td:eq(0)").text() + " entries cleaned", 1);
			} else {
				info("Failed to remove form entries ", 0);
				console.log(msg);
			}
		});
	}*/
	prtfname = prompt("Enter form name to confirm","");
	var $par = $j(but).closest("tr");
	/*$par.fadeOut(800);*/
	if (prtfname==$par.find("td:eq(2)").text()) {
		$j.get('/?m=wizard&todo=empty&suppressHeaders=1', {fid: fid}, function (msg) {
			//console.log(msg);
			if (msg && msg === 'ok') {
				var $par1 = $j(but).closest("td");
				$par1.prev().text("0");
				$j(but).replaceWith(['<div class="fbutton qticon delform" onclick="deleteForm(', fid, ',this);" title="Delete"></div>'].join(""));
				info("Form " + $par.find("td:eq(0)").text() + " entries cleaned", 1);
			} else {
				info("Failed to remove form entries ", 0);
				console.log(msg);
			}
		});
	}else{
		alert('Incorrect form name! Sorry!');
	}
}

function exTurn(id, lp) {
	var ival = $j(lp).attr("data-status"), res, tres;
	$j.get("/?m=wizard&suppressHeaders=1&todo=onoff&fid=" + id, function (msg) {
		if (msg && msg === 'ok') {
			if (ival == 0) {
				res = 'Deactivate';
				tres = 'Active';
			} else {
				res = 'Activate';
				tres = 'Disabled';
			}
			info("Form status changed to " + tres.toLowerCase(), 1);
			$j(lp).attr({
				"data-status": (ival == 1 ? 0 : 1),
				"title": res
			}).toggleClass("expire active")
				.closest("tr").find("td:eq(5)").text(tres).next().text(today());

		}
	});
}

function startCallback() {
}

$j.fn.xtraButton = function () {
	$j(this).each(function () {
		var ick = $j(this).attr("data-for"),
			bclass = $j(this).attr("data-iclass");
		$j("#" + ick).hide();
		$j(this)
			.click({ik: ick, cl: bclass}, function (e) {
				var cstate = $j(this).data("checked"), ick = e.data.ik, initc = e.data.cl;
				if (!cstate) {
					$j(this).removeClass(initc).addClass(initc + "_active");
					cstate = false;
				} else {
					$j(this).removeClass(initc + "_active").addClass(initc);
				}
				$j("#" + ick).attr("checked", !cstate);
				$j(this).data("checked", !cstate);
			});
	});
	return this;
}

$j.fn.toTab = function (tid) {
	$j("ul.topnav > li:eq(" + tid + ")", this).find("a").trigger("click");
	return this;
}

$j(".moretable").delegate(".moreview", 'mouseenter mouseleave', function (e) {
	var hover = (e.type === 'mouseenter'),
		mpar = $j(this).closest("tr").attr('id');
	if (hover) {
		var xp = $j(this).offset(), npos = {x: e.pageX, y: e.pageY}, npos0 = cloneThis(npos),
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

		//obtain content of cell for extra-view
		var ctext = $j(this).attr("data-text");
		if(ctext == 'inbox'){
			ctext = $j(this).find(".blind").html();
		}

		// Get element top offset and height
		$smalltip
			//.html($j(this).attr("data-text"))
			.html(ctext)
			.css({visibility: "collapse"})
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

		var xpdelta = {x: (npos0.x - npos.x) + 5, y: (npos0.y - npos.y) + 5};
		$smalltip
			.css({
				left: npos.x,
				top: npos.y,
				visibility: "visible"
			});

		$j(this).add("*", this).bind("mousemove", {pdelta: xpdelta}, function (e) {
			$smalltip.css({
				left: e.pageX - (e.data.pdelta.x - 15),
				top: e.pageY - (e.data.pdelta.y + 5)
			});
		});
	} else {
		$smalltip.hide();
	}
});

var $smalltip = $j("#stip");

Array.prototype.contains = function (needle) {
	for (var i = 0; i < this.length; i++)
		if (this[i] == needle) {
			return true;
		}
	return false;
}

Array.prototype.diff = function (compare) {
	return this.filter(function (elem) {
		return !compare.contains(elem);
	})
}

$j.fn.flatButton = function (off, addFunc) {
	$j(this).each(function () {
		if (off) {
			$j(this).data(state, "off").next("button").removeClass("sbutton sbutton_active").addClass("sbutton_off");
		}
		var self = this, $lab = $j(this).next("label");
		$lab.add(this).hide();
		$j("<button></button>")
			.text($lab.text())
			.addClass("sbutton")
			.data("state", false)
			.click(function (e) {
				var cstate = $j(this).data("state");
				if (cstate !== 'off') {
					$j(this).toggleClass("sbutton sbutton_active").data("state", !cstate);
					$j(self).attr("checked", !cstate);
				}
				if (addFunc) {
					(addFunc)();
				}
			})
			.insertAfter($lab);
	});
}

function sprintf() {	// Return a formatted string
	//
	// +   original by: Ash Searle (http://hexmen.com/blog/)
	// + namespaced by: Michael White (http://crestidg.com)

	var regex = /%%|%(\d+\$)?([-+#0 ]*)(\*\d+\$|\*|\d+)?(\.(\*\d+\$|\*|\d+))?([scboxXuidfegEG])/g;
	var a = arguments, i = 0, format = a[i++];

	// pad()
	var pad = function (str, len, chr, leftJustify) {
		var padding = (str.length >= len) ? '' : Array(1 + len - str.length >>> 0).join(chr);
		return leftJustify ? str + padding : padding + str;
	};

	// justify()
	var justify = function (value, prefix, leftJustify, minWidth, zeroPad) {
		var diff = minWidth - value.length;
		if (diff > 0) {
			if (leftJustify || !zeroPad) {
				value = pad(value, minWidth, ' ', leftJustify);
			} else {
				value = value.slice(0, prefix.length) + pad('', diff, '0', true) + value.slice(prefix.length);
			}
		}
		return value;
	};

	// formatBaseX()
	var formatBaseX = function (value, base, prefix, leftJustify, minWidth, precision, zeroPad) {
		// Note: casts negative numbers to positive ones
		var number = value >>> 0;
		prefix = prefix && number && {'2': '0b', '8': '0', '16': '0x'}[base] || '';
		value = prefix + pad(number.toString(base), precision || 0, '0', false);
		return justify(value, prefix, leftJustify, minWidth, zeroPad);
	};

	// formatString()
	var formatString = function (value, leftJustify, minWidth, precision, zeroPad) {
		if (precision != null) {
			value = value.slice(0, precision);
		}
		return justify(value, '', leftJustify, minWidth, zeroPad);
	};

	// finalFormat()
	var doFormat = function (substring, valueIndex, flags, minWidth, _, precision, type) {
		if (substring == '%%') return '%';

		// parse flags
		var leftJustify = false, positivePrefix = '', zeroPad = false, prefixBaseX = false;
		for (var j = 0; flags && j < flags.length; j++) switch (flags.charAt(j)) {
			case ' ':
				positivePrefix = ' ';
				break;
			case '+':
				positivePrefix = '+';
				break;
			case '-':
				leftJustify = true;
				break;
			case '0':
				zeroPad = true;
				break;
			case '#':
				prefixBaseX = true;
				break;
		}

		// parameters may be null, undefined, empty-string or real valued
		// we want to ignore null, undefined and empty-string values
		if (!minWidth) {
			minWidth = 0;
		} else if (minWidth == '*') {
			minWidth = +a[i++];
		} else if (minWidth.charAt(0) == '*') {
			minWidth = +a[minWidth.slice(1, -1)];
		} else {
			minWidth = +minWidth;
		}

		// Note: undocumented perl feature:
		if (minWidth < 0) {
			minWidth = -minWidth;
			leftJustify = true;
		}

		if (!isFinite(minWidth)) {
			throw new Error('sprintf: (minimum-)width must be finite');
		}

		if (!precision) {
			precision = 'fFeE'.indexOf(type) > -1 ? 6 : (type == 'd') ? 0 : void(0);
		} else if (precision == '*') {
			precision = +a[i++];
		} else if (precision.charAt(0) == '*') {
			precision = +a[precision.slice(1, -1)];
		} else {
			precision = +precision;
		}

		// grab value using valueIndex if required?
		var value = valueIndex ? a[valueIndex.slice(0, -1)] : a[i++];

		switch (type) {
			case 's':
				return formatString(String(value), leftJustify, minWidth, precision, zeroPad);
			case 'c':
				return formatString(String.fromCharCode(+value), leftJustify, minWidth, precision, zeroPad);
			case 'b':
				return formatBaseX(value, 2, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
			case 'o':
				return formatBaseX(value, 8, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
			case 'x':
				return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
			case 'X':
				return formatBaseX(value, 16, prefixBaseX, leftJustify, minWidth, precision, zeroPad).toUpperCase();
			case 'u':
				return formatBaseX(value, 10, prefixBaseX, leftJustify, minWidth, precision, zeroPad);
			case 'i':
			case 'd':
			{
				var number = parseInt(+value);
				var prefix = number < 0 ? '-' : positivePrefix;
				value = prefix + pad(String(Math.abs(number)), precision, '0', false);
				return justify(value, prefix, leftJustify, minWidth, zeroPad);
			}
			case 'e':
			case 'E':
			case 'f':
			case 'F':
			case 'g':
			case 'G':
			{
				var number = +value;
				var prefix = number < 0 ? '-' : positivePrefix;
				var method = ['toExponential', 'toFixed', 'toPrecision']['efg'.indexOf(type.toLowerCase())];
				var textTransform = ['toString', 'toUpperCase']['eEfFgG'.indexOf(type) % 2];
				value = prefix + Math.abs(number)[method](precision);
				return justify(value, prefix, leftJustify, minWidth, zeroPad)[textTransform]();
			}
			default:
				return substring;
		}
	};

	return format.replace(regex, doFormat);
}


function addFileToImport(test,task_id){
	if(test){
		if(!$j("#fileimp").length){
			$j("#formnd").append("<form id='formfileid'  action='/?m=tasks&task_id="+task_id+"&a=clients_import&mode=read' enctype='multipart/form-data' method='POST'><table><tr><td><input type='file' id='fileimp' name='excelfile'  data-ext='xls|xlsx'/></td></tr><tr><td><label  id='duplicateimp'><input type='checkbox' name='duplicate' value='duplicate'/>Merge duplicate data</label></td></tr></table></form>");
			/*$j("#formnd").attr("action","/?m=tasks&mode=uploadfcli");
			$j("#formnd").attr("enctype","multipart/form-data");
			$j("#formnd").attr("method","POST");*/
		}
		
		//action="" enctype="multipart/form-data" method="POST"
	}else{
		if($j("#fileimp").length){
			$j("#fileimp").remove();
			$j("#duplicateimp").remove();
			$j("#formnd").removeAttr("action");
			$j("#formnd").removeAttr("enctype");
			$j("#formnd").removeAttr("method");
		}
	}
}



function valid8Import() {
	var selected = 0, missed = 0, go = false;
	$j("select").each(function () {
		if ($j(this).val() == '-1') {
			$j(this).addClass("alert");
			++missed;
		} else {
			$j(this).removeClass("alert");
			++selected;
		}
	});
	
	var sheet = [];
	var bolmult = false;
	console.log($(".sheet option:selected").size());
	
	$(".sheet option:selected").each(function() {
		if($(this).val() == '-1'){
    		
    	}else{
    		var testin = $.inArray($(this).val(), sheet);
    		console.log('inarray: '+testin);
    		if(testin==-1){
    		    sheet.push($(this).val());
		    }else{
		    	console.log($j(this));
		    	$j(this).closest('select').addClass("alert");
		    	bolmult = true;
		    }
    	}
	   
	});
	if(bolmult){
		bolmult = false;
		console.log(sheet)
		msg = "One sheet many times selected";
		if (missed > 0) {
			msg += '\n'+"There are not selected columns.";
		}
		
		alert(msg);
		return false;
	}
	
	if (selected > 0) {
		if (missed > 0) {
			if (confirm("There are not selected columns. Skip them?")) {
				go = true;
			}
		} else {
			go = true;
		}
		if (go === true) {
			//console.log($(document.forms['finishim']).serializeArray());
			$('#loader').show();
			document.forms['finishim'].submit();
		}
	} else {
		alert("Please select at least one data column");
		return false;
	}
}

function loadCommune(dept_code,$commune){
	console.log(dept_code);
	v = null;
	var $hiddensec = $('#hiddensec');
	var hiddensecval = null;
	if($hiddensec!=null)
		hiddensecval = $hiddensec.val();
	if($commune != null){
		imgid = 'img_'+$commune.attr('id');
		$commune.after('<img src="/modules/outputs/images/loader.gif" id="'+imgid+'"/>');
		$commune.empty();
		$commune.append("<option value='-1'>- Select Commun -</option>");
		$j.get("?m=wizard&ccode="+dept_code+"&mode=loadcommunDept&suppressHeaders=1", function (msg) {
			console.log(dept_code+": "+msg);
			if (msg && msg !== 'fail') {
				msg = $j.parseJSON(msg);
				
				
				$.each(msg, function(key,value){
					
					var select = '';
					if(hiddensecval!=null && hiddensecval==key)
						select = 'selected=selected';
					$commune.append("<option value='"+key+"' "+select+">"+value+"</option>");
				});
				//updateSelect("#administration_sect");
				$('#'+imgid).hide();
			} else {
				$('#'+imgid).hide();
				//$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);
			}
		});
	}
}


function loadSection(commune_code,$section){
	console.log(commune_code);
	v = null;
	var $hiddensec = $('#hiddensec');
	var hiddensecval = null;
	if($hiddensec!=null)
		hiddensecval = $hiddensec.val();
	imgid = 'img_'+$section.attr('id');
	$section.after('<img src="/modules/outputs/images/loader.gif" id="'+imgid+'"/>');
	$section.empty();
	$section.append("<option value='-1'>- Select Communal Section -</option>");
	$j.get("?m=wizard&ccode="+commune_code+"&mode=loadsection&suppressHeaders=1", function (msg) {
		console.log(commune_code+": "+msg);
		if (msg && msg !== 'fail') {
			msg = $j.parseJSON(msg);
			
			
			$.each(msg, function(key,value){
				
				var select = '';
				if(hiddensecval!=null && hiddensecval==key)
					select = 'selected=selected';
				$section.append("<option value='"+key+"' "+select+">"+value+"</option>");
			});
			//updateSelect("#administration_sect");
			$('#'+imgid).hide();
		} else {
			$('#'+imgid).hide();
			//$j("#msgbox").text("Invalid form selected!").show().delay(2000).fadeOut(3000);
		}
	});
}
function populateCommune(department,commune){
	var $commune = $('#'+commune);
	var $department = $('#'+department);
	console.log($department);
	console.log($commune);
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
	var $commune = $('#'+commune);
	var $section = $('#'+section);
	console.log($section);
	console.log($commune);
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
function populateLocal(department,commune,communalSection){
	var $commune = $('#'+commune);
	var $department = $('#'+department);
	var $communalSection = $('#'+communalSection);
	console.log($department);
	console.log($commune);
	console.log($communalSection);
	if($commune != null && $communalSection != null){
		if($commune.val() != '-1'){
			loadSection($commune.val());
		}
		$('#'+commune).change(function () {
			loadSection($('#'+commune).val());
		});
		
	}
	
	if($department != null && $commune != null){
		if($commune.val() != '-1'){
			loadCommune($department.val());
		}
		$('#'+department).change(function () {
			loadCommune($('#'+department).val());
		});
		
	}else{
		console.log('Not');
	}
	
};







