/**
 * Created by JetBrains PhpStorm.
 * User: stig
 */

var pf = (function(my) {

	var self = this;
	var iorder = ['id', 'pdesc', 'pname', 'start_data', 'end_date', 'sectors', 'agency', 'program'], acols, allRows = [], currentPage = 0, numPerPage = 50, clastpage = 0, realHeadId = [], colType = [], multies = [], lrefs = [], plects = [], lectsr = [], $table, $tbody, $thead, spre = "headerSort", $fb, lind = 0, $slist, thar = [], thl = 0, offed = false, $lbody, elink, clltype = [], $div, liveObjs = [], wmode, buts = [], numRows = 0, numPages = 0, cRows = [], filteredRows = [], acolumns, lects = [], lectsv = [], lectsHTML = [], heads = [], sortMethods = [], curMethod = '', curKey = 0, curWay = 'desc', cleanSet = new RegExp("[\\s-]", "g"), visible = [], head_active, sl_active, fillects, cellOldText, plurst = '', fakes = [], topheads;


	function pasteRow(rdata, id) {
		var $nr = $("<tr/>", {"id":"row_" + id});
		for (var i in acolumns) {
			if (acolumns.hasOwnProperty(i)) {
				var tcol = acolumns[i];
				/*if (tcol.link === false) {
					$nr.append(["<td align='left'>", rdata[tcol.val], "</td>"].join(""));
				} else {*/
					$nr.append([
							"<td class='", (tcol.class ? tcol.class : ""), (tcol.link ? " plink" : ""), "'",
							( (tcol.extra && tcol.extra.val) ? "data-detail=\"" + /*rdata[tcol.extra.val]*/"" + "\"" : ""),
							(tcol.link === true && elink[i] ? ["data-pid='" , rdata[elink[i].val] , "' data-link='" , i , "'"
						].join("") : ""), " >", rdata[tcol.val], "</td>"].join(""));
				//}
			}
		}
		$nr.appendTo($tbody);
	}

	function hideMenu() {
		if (!fillects) {
			$fb.hide();
			filmter.hideAll();
		}
		fillects = false;
		$j(".head_menu", $thead).each(function(inx) {
			$j(this).removeClass("head_menu_on menu_stay").data("cact", false).prev().removeClass("head_sel_act");
		});
	}

	function arrows() {
		if (numPages > 0) {
			if (currentPage > 0) {
				if ((currentPage - 1) >= 0) {
					$j(".prev_page").removeClass("hide_butt");
				}
				else {
					$j(".prev_page").addClass("hide_butt");
				}
				if (currentPage > 0) {
					$j(".first_page").removeClass("hide_butt");
				}
				else {
					$j(".first_page").addClass("hide_butt");
				}
			}
			else {
				$j(".prev_page").addClass("hide_butt");
				$j(".first_page").addClass("hide_butt");
			}
			if ((currentPage + 1) < numPages) {
				$j(".next_page").removeClass("hide_butt");
			}
			else {
				$j(".next_page").addClass("hide_butt");
			}
			if (currentPage < numPages - 1) {
				$j(".last_page").removeClass("hide_butt");
			}
			else {
				$j(".last_page").addClass("hide_butt");
			}
		}
	}

	function navgt(met) {
		clastpage = currentPage;
		switch (met.data.action) {
			case 'first':
				currentPage = 0;
				break;
			case 'last':
				currentPage = numPages - 1;
				break;
			case 'next':
				if (currentPage < (numPages - 1)) {
					currentPage++;
				}
				break;
			case 'prev':
				if (currentPage > 0) {
					currentPage--;
				}
				break;
			default:
				break;

		}
		hideMenu();
		$j(".pinfor").find("span.curp").text(currentPage + 1);
		$table.trigger('repaginate');
	}

	function cleaner(e) {
		var obj = $j(e.target).parent(), hid = $j(obj).data("hid"), nname = $j(obj).closest("div.dgetter").attr("id");
		nname = nname.replace("box", '');
		if (nname == 'r') {
			nname = 'row';
		} else if (nname == 'c') {
			nname = 'col';
		}
		var pos = $j.inArray(hid, areas[nname]);
		areas[nname].splice(pos, 1);

		$j(obj).closest("li").remove();
	}

	function justHideMenu() {
		if ($thead) {
			$j("th > div", $thead).removeClass("head_menu_on hstat_menu_on menu_stay");
		}
		if ($fb) {
			$fb.hide();
		}
		if ($slist) {
			$slist.hide();
		}
	}

	function closeMenu() {
		if (head_active) {
			head_active.data("cact", 0).removeClass("menu_stay head_menu_on").parent().data("skey", false).removeClass('head_sel_act');
		}
		if (sl_active) {
			sl_active.removeClass("menu_stay hstat_menu_on");
		}
		justHideMenu();
		filmter.hideAll();
		$j(document).unbind("click");
	}

	function menuKiller(ev) {
		var et = ev.currentTarget;
		var p1 = $j(ev.target).closest("div.filter_box");
		if (et != head_active && (!p1 || p1.length == 0)) {
			closeMenu();
		}
	}

	function updatePages() {
		numPages = Math.ceil(numRows / numPerPage);
		$j(".pinfor").find("span.totp").text(numPages);
		$j(".xcnt").text(numRows);
		navgt({
			data:{
				action:'first'
			}
		});
	}

	function evHandler(mode) {
		var action = 'live';
		if (mode !== true) {
			action = 'die';
		}
		if (liveObjs.length > 0) {
			for (var i = 0, j = liveObjs.length; i < j; i++) {
				eval(['$j(', liveObjs[i][0], ').', action , '("', liveObjs[i][1], '",', liveObjs[i][2], ')'].join(""));
			}
		}
	}

	function getHeadDataStat() {
		var items = ["col", 'row'];
		var res = {row:[], col:[]};
		for (var c = 0; c < 2; c++) {
			if (areas[items[c]].length > 0) {
				var ll = areas[items[c]].length;
				for (var i = 0; i < ll; i++) {
					res[items[c]].push({
						type:colType(areas[items[c]][i]),
						title:$j("th:eq(" + areas[items[c]][i] + ")", $thead).text(),
						id:areas[items[c]][i]
					});
				}
			}
		}
		return res;
	}

	function collector() {
		var trst = $j("tr", $tbody), itd = trst.length, prow;
		while (itd--) {
			visible.push(itd);
		}
		prow = null;

		btr = null;
		lets = null;
		grows = null;
		heads = [];
		for (var hid = 0; hid < thl; hid++) {
			var cthis = thar[hid], txt = $j(cthis).text(), cw = $j(cthis).width();
			filar[hid] = {methods:{}, mvals:[], state:false, plects:[]};

			$j(cthis)
					.bind('mouseenter', function() {
						var mp = $j(this).offset(), mw = $j(this).outerWidth();
						$j(".head_menu", this).addClass("head_menu_on").removeClass("head_menu_sort").css({
							left:((mp.left + mw) - 18),
							top:mp.top + 1
						});
						/*$j(".hstat_menu",this).addClass("hstat_menu_on").removeClass("head_menu_sort").css({
						 left: (mp.left+1),
						 top: mp.top
						 });*/
						$j(this).addClass("head_act");
					})
					.bind("mouseleave", {hdi:hid}, function(x) {
						var me = this, uid = x.data.hdi, getout;
						$j(this).removeClass("head_act")
								.find("div.head_menu").each(function() {
									var cst = $j(this).data('cact');
									if (!cst || cst == 0) {
										$j(this).removeClass("head_menu_on");
										var pcl = heads[uid];
										if ($j(this).hasClass(spre + pcl)) {
											$j(this).addClass("head_menu_sort");
											getout = true;
										}
									}
									else {
										$j(this).addClass('head_sel_act');
										getout = false;
									}
								})
						/*.end()
						 .find("div.hstat_menu").each(function(){
						 $j(this).removeClass("hstat_menu_on");
						 if(getout){
						 $j(this).addClass("head_menu_sort");
						 }
						 })*/
					})
					.bind("click", {
						'head_id':hid
					}, function(xd) {
						if (xd.target.className.match(/head_menu/g)) {
							headMenuWork(xd);
							return;
						}
						var mp = $(this).closest("th").data("resize"), heid = xd.data.head_id;
						if (mp === true) {
							return false;
						}
						//add sorting in here
						if (!heads[heid]) {
							heads[heid] = 'desc';
						}
						var oway = heads[heid], nway = oppoWay(oway);
						msort(heid, nway);
						$j(this).removeClass("head_act");
					})
					.data("ow", $j(cthis).width());
		}
		$j("div.head_menu", thar).live("click", function(df) {
			headMenuWork(df);
		});
	}


	function headMenuWork(df) {
		var $ard = $j(df.target), $hcell = $ard.parent(), heid = $hcell.attr("data-thid");//df.data.head_id;
		lector(heid);
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
			head_active = $ard;
			$ard.data("cact", 1).addClass("menu_stay");
			var pp = $hcell.offset();
			$fb.show();
			var np = {
				x:(pp.left + $hcell.width() - $fb.width()),
				y:(pp.top + $ard.height() + 5)
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
				left:np.x,
				top:np.y
			}).data("skey", heid).show();
			$j(document).bind("click", function(e) {
				menuKiller(e);
			});
			$j("#fil_on", $fb).attr({
				"checked":cbon,
				"disabled":!cben
			});
		}
		else {
			closeMenu();
		}
		df.stopPropagation();
		return false;
	}

	function findlect(key, arr) {
		if (arr.length == 0) {
			return false;
		}
		else {
			for (var i = 0, ll = arr.length; i < ll; i++) {
				if (arr[i] && arr[i].r == key) {
					return true;
				}
			}
		}
	}

	function lectSort(a, b) {
		var x = a.r, y = b.r;
		return x - y;
	}

	function getLects(i) {
		return lects[i];
	}

	function ascSort(a, b) {
		return a - b;
	}

	function lector(i) {
		var ul, li, cb, sp;
		if (!lectsHTML[i]) {
			ul = $j("<ul class='tobs' id='outf'></ul>");
		}
		else {
			return false;
		}
		var ll = lects.length, frag = document.createDocumentFragment();
		li = $j("<li class='ffbc fil_line'></li>");
		cb = $j("<input type='checkbox'>");
		sp = $j("<span class='sline'></span>");
		var tar = lectsv[i], rtar = lectsr[i], x = 0;
		//for (var x = 0; x < tar.length; x++) {
		for (var tx in tar) {
			if (tar.hasOwnProperty(tx)) {
				//var val = tar[tx].r, vval = tar[tx].v;
				var val = tar[tx], rval = rtar[tx];
				if (val || val === false) {
					//var t = $j(cb).clone(true);
					var t = $j(cb).clone(true).attr("data-col", i);
					//$j(t).bind(be, {

					$j(t).attr({
						"data-cact":val,
						'data-cact_id':tx,
						'data-cact_r':rval
					});
					var t1 = $j(li).clone(true), t2 = $j(sp).clone(true).text(val);
					if (val === false) {
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
		lectsHTML[i] = ul;
		ul = null;
		frag = null;
		//}
	}

	function colVals(key) {
		return $j(lectsHTML[key]).clone(true);
	}

	function oppoWay(way) {
		var nway;
		if (way == 'desc') {
			nway = 'asc';
		}
		else {
			nway = 'desc';
		}
		return nway;
	}

	function cleanHeadSort(cur, nway) {
		var ul = 0;
		if (heads.length == 0) {
			ul = cur + 1;
		}
		else {
			ul = heads.length;
		}
		for (var z = 0; z < ul; z++) {
			if (z != cur) {
				heads[z] = '';
				$j("th", $thead).removeClass([spre , "asc" , ' ', spre , "desc"].join("")).next().removeClass("head_menu_sort");
			}
			else {
				heads[z] = nway;
			}
		}
	}

	function ifsort(way) {
		curKey = $j("#filbox").data("skey");
		msort(curKey, way);
		hideMenu();
		filmter.hideAll();
	}

	function msort(key, way) {
		memo.toggle();
		setTimeout(function() {
			var frag = document.createDocumentFragment();
			curKey = realHeadId[key];
			curMethod = colType[key];
			curWay = way;
			cleanHeadSort(key, way);
			//allRows.sort(iterer);
			var tar = allRows.slice(0);
			tar.sort(iterer);
			var $lbody = $tbody.detach().empty(), cv = 0;
			visible = [];
			for (var i = 0, tll = tar.length; i < tll; i++) {
				var obj = tar[i], $nr = $j(obj['item']).clone(true);
				//var nr=tar[i]['item'];
				if (cv >= numPerPage) {
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
					visible.push(obj.uid);
				}
				$nr.prependTo($lbody);
			}
			$table.append($lbody);
			$j("th:eq(" + key + ")", $thead).removeClass(spre + '' + oppoWay(way)).addClass(spre + way).find("div.head_menu", this).addClass("head_menu_sort");
			visible.reverse();
			navgt({
				data:{
					action:'first'
				}
			});
			tar = null;
			memo.toggle();
		}, 20);
	}

	function iterer(a, b) {
		var x = a[curKey], y = b[curKey], r1, r2, r3;
		if (isNaN(x) && curMethod != 'string') {
			x = 0;
		}
		if (isNaN(y) && curMethod != 'string') {
			y = 0;
		}
		if (x === false && y === false) {
			return 0;
		} else if (x === false) {
			return -1;
		} else if (y === false) {
			return 1;
		}
		if (curWay == "desc") {
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

	function treatVal(way, val) {
		if (val === undefined || val === null) {
			return '';
		}
		if (way == 'int' || way == 'date') {
			if (way === 'date' && val.length > 0) {
				val = parseInt(val.split("/").reverse().join(""));
			}
			else if (way == 'int' && val && val.length > 0) {
				val = parseInt(val.replace(cleanSet, ''));
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

	function initz() {
		wmode = 'out';
		$fb = $j("#filbox");
		$slist = $j("#fil_stats");
		numRows = $j('tr', $tbody).length;
		numPages = Math.ceil(numRows / numPerPage);
		var $span = $j("<span/>");
		$j(["<div class='page_count' title='Total'>Records:&nbsp;<span class='xcnt'>", numRows, "</span></div>"].join("")).appendTo($span);
		$j("<div class='navs first_page' title='First'></div>").bind('click', {
			action:'first'
		}, navgt).appendTo($span);
		$j("<div class='navs prev_page' title='Previous'></div>").bind('click', {
			action:'prev'
		}, navgt).appendTo($span);
		$j(["<div class='pinfor' style='float:left;'><span class='curp'>" , (currentPage + 1) , "</span> of <span class='totp'>" , (numPages) , "</span></div>"].join(""))
				.appendTo($span);
		$j("<div class='navs next_page' title='Next'></div>").bind('click', {
			action:'next'
		}, navgt).appendTo($span);
		$j("<div class='navs last_page' title='Last'></div>").bind('click', {
			action:'last'
		}, navgt).appendTo($span);

		$span.appendTo($div);
		$table.bind('repaginate', function() {
			var st = 0, lowend = (currentPage * numPerPage), highend = ((currentPage + 1) * numPerPage - 1), hiter = false, $lbody, trar = $j('tr', $tbody), trl = trar.length, ofc = new RegExp("offwall", "i"), oftd = new RegExp("offview", "i");
			for (var i = 0; i < trl; i++) {
				var $tr = $j(trar[i]);
				if (hiter === true) {
					$j(trar).filter(":gt(" + (i - 1) + ")").addClass("offview");
					i = trl;
				}
				else {
					var cc = $tr.attr("class"), tcl = ofc.test(cc), todo = oftd.test(cc);//tcl = cc.match(ofc), todo = cc.match(oftd);
					if (!tcl) {
						if (st >= lowend && st <= highend) {
							if (todo) {
								$tr.removeClass("offview");
							}
						}
						else
						if (!todo) {
							$tr.addClass("offview");
						}
						st++;
						if (st > highend) {
							hiter = true;
						}
					}
				}
			}

			tr = null;
			trar = null;
			trl = null;
			i = null;
			lowend = null;
			highend = null;
			arrows();
			$lbody = null;
			fCleaner();
		});

		if (numPages > 1) {
			arrows();
		}
		thar = $j("tr:first > th", $thead);
		$(thar).each(function(i) {
			$(this).attr("data-thid", i);
		});
		thl = thar.length;
		$table
				.trigger('repaginate')
				.attr("class", "rtable")
				.disableSelection();

		acols = [];
		var colz = $j("colgroup > col", $table);
		var cl = colz.length;
		for (var i = 0; i < cl; i++) {
			$j(colz[i]).attr("data-thid", i);
			acols.push(colz[i]);
		}
		colz = null;
		cl = null;
		$table.appendTo("#mholder").show();
		evHandler(true);
		collector();
		$("#tbl").delegate(".plink", "click", function() {
			var tid = $(this).attr("data-pid");
			document.location = elink[$(this).attr("data-link")]['url'].replace("#0#", tid);
		});
		filmter.init();

		$j("#filin_list").delegate(":checkbox", be,
				function(x) {
					var st = $j(this).is(":checked"), tobj = this, cx = $j(this).attr("data-col"), colRowVal = $j(tobj).attr("data-cact_r"), colRowId = $j(tobj).attr("data-cact_id");
					if ($j("ul#outf").find("input:checked").length > 0) {
						if (!st) {
							fl++;
						}
						filmter.setColValues(cx, colRowVal, st);
					}
					else {
						filar[cx].mvals = [];
						filar[cx].state = false;
						filar[cx].plects = [];
					}
					memo.toggle();
					fillects = true;
					setTimeout(function() {
						runFilters();
						//pickLects();
						//wagCrosses(cx,colRowId);
						memo.toggle();
					}, 20);
				});
		//sort selected lects so list will look already sorted :)
		for (var il = 0, zl = lects.length; il < zl; il++) {
			var tar = lectsr[il].slice(0).sort(), np, nwar = [], nlist = [], ind = 0, nvlist = [];
			if (colType[il] == 'int') {
				tar.sort(ascSort);
			}
			for (var xt in tar) {
				if (tar.hasOwnProperty(xt)) {
					np = $j.inArray(tar[xt], lectsr[il]);
					nwar[ind] = tar[xt];
					nvlist[ind] = lectsv[il][np];
					nlist[ind++] = lects[il][np];
				}
			}
			lects[il] = nlist;
			lectsv[il] = nvlist;
			lectsr[il] = nwar;
		}
		nlist = nwar = null;
		$j(".pagebox").clone(true).insertAfter($table);
		$j(".cleanbox").show();
		memo.toggle();

	}

	function prepareForSort(rdata, row_id) {
		var preVal;
		for (var i = 0, l = colType.length; i < l; i++) {
			preVal = rdata[realHeadId[i]];
			if (colType[i] == 'int') {
				preVal = parseInt(preVal);
			}
			rdata[realHeadId[i]] = treatVal(colType[i], rdata[realHeadId[i]]);
			if (clltype[i] === 'list') {
				var parts = removeHTMLTags(preVal).split(",").map(trim), rparts = removeHTMLTags(rdata[realHeadId[i]]).split(",").map(trim);
			} else {
				parts = [preVal];
			}
			if (!lectsv[i]) {
				lectsv[i] = [];
				lects[i] = [];
				lectsr[i] = [];
			}
			if (colType [i] == 'int') {
				parts = [rdata[acolumns[i].val]];
			}
			for (var z = 0, l2 = parts.length; z < l2; z++) {
				var lp = $j.inArray(parts[z], lectsv[i]), np;
				if (lp >= 0) {

				} else {
					lp = lectsv[i].push(parts[z]);
					--lp;
				}
				if (!lects[i][lp]) {
					lects[i][lp] = [];
					lectsr[i][lp] = [];
				}
				if ($j.inArray(row_id, lects[i][lp]) < 0) {
					lects[i][lp].push(row_id);
				}
				if (clltype[i] !== 'list') {
					if (colType[i] == 'int') {
						lectsr[i][lp] = preVal;
					} else {
						lectsr[i][lp] = rdata[realHeadId[i]];
					}
				} else {
					lectsr[i][lp] = rparts[z];
				}
			}
		}
		return rdata;
	}

	function wagCrosses(col, val) {
		runFilters();
		var blines = lects[col][val];
		blines = filar[col].plects.concat(blines);
		filar[col].plects = blines;
	}

	function runFilters(event) {
		var tfs = 0, utext, odm = false, tr_del = false, killed = 0, i = 0, met = 0, alive = 0, tstr, t, zcl, sVal, tcl, once = false, wildCardPatt = new RegExp(regexEscape("#"), 'g');
		var $lbody = $tbody.detach();
		visible = [];
		var fillength = filar.length, tlength = fillength;
		//$j(filar).each(function(er){
		while (tlength--) {
			if (filar[tlength] && filar[tlength].state === true) {
				tfs++;
			}
		}//);
		var tar = $j("tr", $lbody);
		if (tfs == 0) {
			var ltt = allRows.length;
			$j(tar).removeClass("offwall");
			numRows = ltt;
			while (ltt--) {
				allRows[ltt]['hidden'] = false;
				visible.push(ltt);
			}
			updatePages();
			$lbody.appendTo($table);
			return;
		}
		cRows = [];

		var avoidMust = [], onceAdded = [];
		for (var y = 0, tl = tar.length; y < tl; y++) {
			var self1 = tar[y], ind = self1.id.replace(/[^\d]+/g, ''), hits = 0, Row = allRows[ind];
			while (!Row) {
				Row = allRows[++ind];
			}
			var fakes = /* Row['fake']*/ [], upret;
			for (var iCC = 0; iCC < fillength; iCC++) {
				var zcol = filar[iCC], myequ = [], tcase = null, must = [], fpos = $j.inArray(iCC, fakes), zmtds = zcol.methods, zvals = zcol.mvals, rowsViewByLect = zcol.plects;
				if (zcol && zcol.state && fpos < 0) {
					once = true;
					var tds = iCC, tec = false, ztype = colType[iCC], pret, dval, tstr = '';
					if (ztype == 'string') {
						dval = '';
						pret = '"';
					}
					else {
						pret = '';
						dval = 0;
					}
					var sFilterTxt;
					if ((zmtds && zmtds['match']) || colType[iCC] === 'string') {
						if (zmtds['match'] && zmtds['match'].length > 0) {
							if (fchange === iCC && !zcol.state) {
								utext = "";
								tec = true;
							}
							else {
								utext = zmtds['match'];
							}
						}
						if (!utext || avoidMust[iCC] || (zvals.length > 0 && utext != '')) {
							if (!onceAdded[iCC] && utext) {
								zvals.push(utext);
								onceAdded[iCC] = true;
							}
							utext = '(' + zvals.join("|") + ')';
							avoidMust[iCC] = true;
							sFilterTxt = utext;
						} else {
							sFilterTxt = regexEscape("#" + utext, "#").replace(wildCardPatt, '.*?');
							sFilterTxt = sFilterTxt || '.*';
							sFilterTxt = '^' + sFilterTxt;
						}
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

					if (zvals && zvals.length > 0 && !avoidMust[iCC]) {
						for (var cv = 0; cv < zvals.length; cv++) {
							if (zvals[cv] == "false") {
								must.push(' == false');
							}
							else {
								must.push([' == ' , pret , zvals[cv] , pret].join(""));
							}
						}
					}
					sVal = Row[realHeadId[iCC]];
					var bMatch = true, bOddRow = true, smar = [], notArr = isArray(sVal), usVal;
					tr_del = false;


					if (ztype == 'string' && sVal.length > 0 && sVal != 'false') {
						if (notArr) {
							usVal = sVal[1];
						} else {
							usVal = sVal;
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
						} else if (!isNaN(sVal) && !notArr) {
							tstr = sVal + '';
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
						$j(myequ).each(function(zs) {
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
						var xt = '', resl, umv;
						for (var i = 0; i < must.length; i++) {
							umv = must[i];
							if (notArr && sVal.length == 2) {
								if (sVal[1] === false) {
									xt += [sVal[1] , ' ' , umv , ' || '].join("");
								} else {
									var lar = sVal[0];
									for (var ic = 0, il = lar.length; ic < il; ic++) {
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
				allRows[ind]['hidden'] = false;
				visible.push(ind);
				//}
				i = 0;
				alive++;
			}
			else {
				//if (!allRows[ind]['hidden']) {
				$j(self1, $lbody).addClass("offwall");
				allRows[ind]['hidden'] = true;
				//}
			}
			//}
		}//);


		$table.append($lbody);
		$lbody = null;
		if (once) {
			//if (alive > 0) {
			numRows = alive;
			updatePages();
		}
		else {
			$table.trigger("repaginate");
		}
		//alert(timeDiff.getDiff());
	}

	function showslist(hid) {
		var rowe = $j.inArray(hid, areas.row), cole = $j.inArray(hid, areas.col), mr = {
			c:false,
			d:false
		}, mc = mr, $po = $j("#head_" + hid), pp = $po.offset(), np = {
			x:pp.left,
			y:(pp.top + 28)
		};
		sl_active = $j(".hstat_menu", $po);
		sl_active.addClass("menu_stay");

		if (rowe >= 0) {
			mr = {
				c:true,
				d:false
			};
			mc = {
				c:false,
				d:true
			};
		} else if (cole >= 0) {
			mr = {
				d:true,
				c:false
			};
			mc = {
				d:false,
				c:true
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
				.css({left:np.x, top:np.y})
				.data("key", hid)
				.show();
		$j(document).bind("click", function(e) {
			menuKiller(e);
		});
	}

	function recrute(ev, obj) {
		var hid = $slist.data("key"), state = $j(obj).is(":checked"), cl = obj.className;
		cl = cl.replace("_check", '');
		if (state) {
			areas[cl].push(hid);
		} else {
			var pos = $j.inArray(hid, areas[cl]);
			areas[cl].splice(pos, 1);
		}
		sl_upd = true;
		showslist(hid);
	}

	function createTable(theads) {
		var $bplace = $j("#mholder");
		var $intab = $j("<table>", {width:"100%", border:'0', cellpadding:"3", cellspacing:"1", class:"tbl tablesorter", id:"tbl"});
		$j("<thead/>", {title:"Click to sort"}).appendTo($intab);
		$j("<tbody/>").appendTo($intab);
		var $thi = $intab.find("thead");
		var $hrow = $j("<tr>");
		for (var i = 0, m = theads.length; i < m; i++) {
			if (theads[i].title.length > 0) {
				$j("<th nowrap='nowrap' class='head' width='" + (theads[i].width ? theads[i].width : "auto") + "'>" + theads[i].title + "</th>")
						.append("<div class='head_menu'></div>")
						.appendTo($hrow);
			} else {
				$j("<th/>").appendTo($hrow);
			}

		}
		$hrow.appendTo($thi);
		$intab.appendTo($bplace);
		//$intab = null;
		var $pbd = $j("<div/>", {class:"pagebox"});
		$j("<span/>", {class:"pgbs"}).appendTo($pbd);
		var $svel = $j("<select/>", {name:'npp', class:"rcpp"}).change(function(e) {
			pf.reorder(this);
		}), linums = [10, 20, 50, 100, 200, 500, -1];
		for (var i = 0; i < linums.length; i++) {
			$j("<option value='" + linums[i] + "' " + (linums[i] == 50 ? "selected='selected'" : '') + ">" + (linums[i] == -1 ? 'All' : linums[i]) + "</option>").appendTo($svel);
		}
		$j("<span/>", {class:"inpb2"}).html("&nbsp;&nbsp;&nbsp;&nbsp;Rows per page").append($svel).appendTo($pbd);
		$j("<div/>", {class:"cleanbox"})
				.append('<span class="fmonitor fmbox"></span>')
				.append('<input type="button" class="button fclean" onclick="cleanAllF();" disabled="disabled" value="Clear Filters">')
				.appendTo($pbd);
		$pbd.appendTo($bplace);
	}

	function toggleColumn(column,hide){
		if(column.length > 0){
			memo.toggle();
			//$table.detach();
			if(hide === true){
				//hide
				$thead.find("th:eq("+column+")").addClass("col_off");
			}else{
				$thead.find("th:eq("+column+")").removeClass("col_off");
			}

			$table.find("tbody > tr").each(function(){
				var $tcel=$j("td:eq("+column+")",this);
				if(hide === true){
					$tcel.addClass("col_off");
				}else{
					$tcel.removeClass("col_off");
				}
				var rid = $j(this).attr("id").replace("row_","");
				allRows[rid]['item']=$j(this).clone();
			});
			memo.toggle();
		}
	}

	return {
		init:function(drct) {
			memo.init();
			//memo.toggle();
			topheads = drct.heads;
			createTable(topheads);
			$table = $j("#tbl").detach();
			$tbody = $("tbody", $table);
			$thead = $("thead", $table);
			$j(".pagebox").show();
			$div = $j(".pgbs");
			var prow;
			colType = drct.type;
			realHeadId = drct.cdata;
			multies = drct.multi;
			elink = drct.links;
			acolumns = drct.columns;
			clltype = drct.lects;
			if (rawlist) {
				for (var i = 0, l = rawlist.length; i < l; i++) {
					pasteRow(rawlist[i], i);
					prow = prepareForSort(rawlist[i], i);
					allRows[i] = null;
					prow['item'] = $j("#row_" + i, $tbody).clone();
					prow['hidden'] = false;
					prow['uid'] = i;
					allRows[i] = prow;
				}
			}
			prow = null;
			rawlist = null;
			initz();
			memo.toggle();
		},
		getColType:function(hid) {
			return colType[hid];
		},
		getColumns:function() {
			return topheads;
		},
		reorder:function(obj) {
			var tt = $j(obj).val();
			$(".rcpp").val(tt);
			if (tt == -1) {
				numPerPage = numRows;
			} else {
				numPerPage = tt;
			}
			memo.toggle();
			updatePages();
			memo.toggle();
		},
		ifsort:function(way) {
			ifsort(way);
		},
		filWork:function(e) {
			runFilters(e);
		},
		getLects:function(col) {
			lector(col);
			return lectsHTML[col];
		},
		clearHList:function(col) {
			$(lectsHTML[col]).find(":checkbox:checked").attr("checked", false);
		},
		colView : function(column, todo){
			toggleColumn(column, todo);
		}

	}
})(pf);

var obj, filter, fist = false, garr, fl = 0, pfl = 0, filar = [], fchange, fstat, today = todayDate().split("/").reverse().join("-"), fakes, btr, heads, lets, qsaved, selects, calif, calwined, areas = {row:[], col:[], subj:[]}, $gtabs, stater, st_do, fields = [], dstp, chex, rrr, st_upd = false, $smalltip = $j("#stip"), dmarker = false, tgt, rsip = false, aname = 'name', dw, aopen, tabevent = false, be;
if ($j.browser.msie) {
	be = "click";
} else {
	be = "change";
}
if ($j.browser.msie) {
	aname = 'submitName';
}
function datesoff() {
	$j("input.datepicker").val("");
	return false;
}

var pager = function() {


	this.init = function(tid, mode) {
		//timeDiff.setStartTime();
		$tbody = $j("tbody", $table);
		$thead = $j("thead", $table);

		//alert(timeDiff.getDiff());
	}

	this.showslist = function(hid) {
		var rowe = $j.inArray(hid, areas.row), cole = $j.inArray(hid, areas.col), mr = {
			c:false,
			d:false
		}, mc = mr, $po = $j("#head_" + hid), pp = $po.offset(), np = {
			x:pp.left,
			y:(pp.top + 28)
		};
		self.sl_active = $j(".hstat_menu", $po);
		self.sl_active.addClass("menu_stay");

		if (rowe >= 0) {
			mr = {
				c:true,
				d:false
			};
			mc = {
				c:false,
				d:true
			};
		} else if (cole >= 0) {
			mr = {
				d:true,
				c:false
			};
			mc = {
				d:false,
				c:true
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
				.css({left:np.x, top:np.y})
				.data("key", hid)
				.show();
		$j(document).bind("click", function(e) {
			self.menuKiller(e);
		});
	}

	this.recrute = function(ev, obj) {
		var hid = $slist.data("key"), state = $j(obj).is(":checked"), cl = obj.className;
		cl = cl.replace("_check", '');
		if (state) {
			areas[cl].push(hid);
		} else {
			var pos = $j.inArray(hid, areas[cl]);
			areas[cl].splice(pos, 1);
		}
		sl_upd = true;
		self.showslist(hid);
	}

	this.pickLects = function(i) {

	}

	this.getVisibles = function() {
		return this.visible;
	}

	this.saveTable = function() {

		var fname = prompt("Please enter name for table file");
		if (fname === null) {
			return false;
		}
		while (!fname || fname.length == 0 || trim(fname) == '') {
			fname = prompt("Please enter valid name for table file!");
			if (fname === null) {
				return false;
			}
		}
		$j("#stabbox").val(JSON.stringify(self.getVisibles()));
		document.saveme.fname.value = fname;
		document.saveme.submit();
	}

	this.startss = function() {
		if (rrr > 0) {
			gpgr.justHideMenu();
			if (!stater) {
				stater = new sFrames();
				stater.init();
				grapher.init();
				reporter.reget();
				$j("#tabs> ul > li:eq(3)").removeClass("tabs-disabled");
			}
			sl_upd = false;
			$j("#tabs").toTab(3);
		}
	}

}

function fCleaner() {
	var r = 0, fll = filar.length;
	while (fll--) {
		if (filar[fll].state == true) {
			r++;
		}
	}
	if (r > 0) {
		$j(".fclean").attr("disabled", false);
		$j(".fmbox").css("background-position", "0px -13px");
	}
	else {
		$j(".fclean").attr("disabled", true);
		$j(".fmbox").css("background-position", "0px 0px");
	}
}

function cleanAllF() {
	var fll = filar.length;
	while (fll--) {
		filar[fll] = {
			methods:{},
			mvals:[],
			state:false,
			plects:[]
		};
		pf.clearHList(fll);
	}
	memo.toggle();
	setTimeout(function() {
		pf.filWork();
		memo.toggle();
	}, 10);
}

function progress() {
	this.msg = 'Loading...';
	this.mode = 0;
	this.$box;
}

progress.prototype.init = function() {
	this.$box = $j("#mbox").text(this.msg).center();
}

progress.prototype.toggle = function() {
	if (this.mode == 0) {
		this.$box.show();
		this.mode = 1;
	}
	else {
		this.$box.hide();
		this.mode = 0;
	}
}

progress.prototype.banner = function(ntxt) {
	if (ntxt && ntxt.length > 0) {
		this.msg = ntxt;
	}
	else {
		this.msg = 'Rendering';
	}
	this.init();
}


var filtersClass;
filtersClass = function() {
	this.numberfil = [];
	this.numberfil[0] = {
		"title":'more',
		"html":"gt",
		"func":">"
	};
	this.numberfil[1] = {
		"title":'less',
		"html":"lt",
		"func":"<"
	};
	this.numberfil[2] = {
		"title":'equal',
		"html":"eq",
		"func":"=="
	};
	this.numberfil[3] = {
		"title":'not equal',
		"html":"ne",
		"func":"!="
	};

	this.number_block;
	this.text_block;
	this.date_block;
	this.calendarField = '';
	this.$filters = $j("#fil_list");
	this.$uniques = $j("#filin_list");
	this.$colhost = $j("#col_list");
	this.filterBox = document.createElement('input');
	this.dateBox = $j("<div class='dbox'></div>");
	this.columns = false;
	this.columnsHidden = [];
	var self = this;
};

filtersClass.prototype.getFilters = function() {
	return filar;
}

filtersClass.prototype.setColValues = function(col, val, add) {
	if (add) {
		if (!filar[col]) {
			filar[col] = {
				mvals:[],
				state:true
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
			filar[col].plects = [];
		}
	}

}

filtersClass.prototype.me = function() {
	return this;
}

filtersClass.prototype.launchFilter = function(ffoc) {
	memo.toggle();
	setTimeout(function() {
		if ($j.browser.msie) {
			$j(ffoc).parent().focus().end().focus();
		}
		pf.filWork();
		ffoc = false;
		memo.toggle();
	}, 100);
}

filtersClass.prototype.init = function() {
	var self = this;
	$j(self.filterBox).attr('type', 'text').addClass('_filterText box').blur(
			function() {
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
			}).focus(
			function() {
				var $fpar = $j(this).parent().parent(), fid = $j("#filbox").data("skey"), xval = filar[fid].methods, self1 = this;
				$fpar.find("input").each(function() {
					var cmd = $j(this).attr("data-method");
					if (self1 != this && (!xval[cmd] || xval[cmd] == "")) {
						$j(this).removeClass("filter_work").addClass("box");
					}
					else {
						$j(this).removeClass("box").addClass("filter_work");
					}
				});
				this.id = '_filterText' + fid;

			}).keypress(function(e) {
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
						'disabled':!ust,
						'checked':ust
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
			.bind('refresh',
			function() {
				var self = filmter.me(), $dad = $j(this).parent(), $fpar = $dad.parent().parent(), ust, fmtd = $j(this).removeClass("boxd").addClass("filter_work_date").attr("data-method"), mename = $j(this).attr(aname), hv = $dad.find("input[" + aname + "='filter_" + mename + "']"), self1 = this, lval = this.value;
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
				meobj:this
			}, function(x) {
				var $me = $j(this), $tp = $me.parent(), cmtd = $me.attr("data-method");
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
			$j("input[type='text']", $tc1).attr("data-method", lv.func).each(function() {
				$j(this).attr(aname, lv.html);
			});
			$tc1.find("a").bind('click', {
				fname:lv.html,
				loc:$tc1
			}, function(ev) {
				self.popCalendar(ev);
			});
			$j("input[type='hidden']", $tc1).attr(aname, "filter_" + lv.html);
			$t1.append($tc1);
			$j(this.date_block).append($t1);
			$tc1 = null;
			$t1 = null;
		}
	}

	var $t = $j("<li class='ffbb fil_line'></li>");
	var s = $j("<span class='comsign ts'></span>");
	$t.append(s).append(this.filterBox);
	$j(this.text_block).append($t);

	if ($j.browser.msie) {
		$j("input#fil_on").click(function() {
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

filtersClass.prototype.checkFilter = function(cbox) {
	var self = this, area = $j("#filbox").data("skey");
	memo.toggle();
	setTimeout(function() {
		filar[area] = {
			methods:{},
			mvals:[],
			state:false,
			plects:[]
		};
		pf.clearHList(area);
		pf.filWork();
		memo.toggle();
	}, 50);
}

filtersClass.prototype.hideAll = function() {
	this.$uniques.hide();
	this.$filters.hide();
	this.$colhost.hide();
}

filtersClass.prototype.showfils = function(cdiv) {
	this.hideAll();
	var self = this, tdsc = $j("#filbox").data("skey"), fdht = "", uval, ind, z = 0, ftype = pf.getColType(tdsc), poss = $j(cdiv).offset(), posw = $j(cdiv).outerWidth(), lop = false, cval;
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
		$j(fdht).find("input").each(function() {
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
			$j("input", this.number_block).each(function() {
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
			$j("input[type!='hidden']", this.date_block).each(function() {
				var cn = $j(this).attr("data-method");
				var mnm = $j(this).attr("name");
				if (cn) {
					if (cval && cval[cn] && cval[cn].r.length > 0) {
						uval = cval[cn].v;
						$j("input[name='filter_" + mnm + "']", self.date_block).val(cval[cn].r);
					}
					else {
						uval = "";
						if (!filar[tdsc]) {
							filar[tdsc] = {
								methods:{},
								state:false
							};
						}

						filar[tdsc].methods[cn] = {
							r:'',
							v:''
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
		visibility:'collapse'
	}).html(fdht).show();

	var winHeight = $j(window).height(), winWidth = $j(window).width(), winTop = this.pageYOffset || $j.boxModel && document.documentElement.scrollTop || document.body.scrollTop, winLeft = this.pageXOffset || $j.boxModel && document.documentElement.scrollLeft || document.body.scrollLeft, winBottom = winHeight + winTop, winRight = winWidth + winLeft, docHeight = $j(document).height(), docWidth = $j(document).width(), deltay = 0, deltax = 0, newpos = {x:(poss.left + posw + 2), y:poss.top}, elHeight = parseInt(self.$filters.height()), elWidth = parseInt(self.$filters.width()), elBottom = newpos.y + elHeight, elMargin = (poss.left + posw + 2 ) + elWidth, percentage = 0, hiddenTop = 0, hiddenBottom = 0, hiddenLeft = 0, hiddenRight = 0;
	if (newpos.x < winLeft) {
		hiddenLeft = winLeft - parseInt(newpos.x);
	}
	if (elMargin > winRight) {
		hiddenRight = elMargin - winRight;
		if (hiddenRight > 5) {
			newpos.x = newpos.x - elWidth - posw - 5;
		}
	}
	if (hiddenLeft > 5) {
		newpos.x = newpos.x + hiddenLeft + 20;
	}
	self.$filters.css({
		"left":newpos.x,
		"top":poss.top,
		"visibility":"visible"
	});
};

filtersClass.prototype.popCalendar = function(f) {
	this.calendarField = f.data.fname;
	var idate = this.$filters.find("input[" + aname + "='filter_" + this.calendarField + "']").val();
	if (!idate) {
		idate = today;
	}
	window.open('index.php?m=public&a=calendar&dialog=1&callback=filmter.setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
};

filtersClass.prototype.setCalendar = function(idate, fdate) {
	this.$filters
			.find(["input[", aname, "='filter_" , this.calendarField , "']"].join("")).val(idate).end()
			.find(["input[", aname, "='" , this.calendarField , "']"].join("")).val(fdate).trigger("refresh");
};

filtersClass.prototype.bindActionColumn = function(){
	var self=this;
	$j(".ul_col_list").delegate(".col_item","click",function(){
		var $chbox = $j(this),
			state = $chbox.attr("checked"),
			val = $chbox.val(),
			hpos = $j.inArray(val,self.columnsHidden);
		if(state === true){
			if(hpos < 0 ){
				self.columnsHidden.push(val);
			}
		}else{
			self.columnsHidden.splice(hpos,1);
		}
		//check for amount of left visible columns
		var $parnt = $chbox.closest(".ul_col_list"),
			$allcls = $parnt.find(":checkbox");
		if($allcls.length == $allcls.filter(":checked").length){
			//no we can't let hide all columns
			$chbox.attr("checked",false);
		}else{
			pf.colView(val, state);
		}
	});
};

filtersClass.prototype.columnsList = function(cdiv) {
	this.hideAll();
	var self = this,
		theads = pf.getColumns(), $clist = $j("<ul/>", {id:"collist", class:"tobs ul_col_list"});
	for (var i = 0, hl = theads.length; i < hl; i++) {
		if (theads[i].title.length > 0) {
			$j(["<li class='ffbc fil_line'><input type='checkbox' value='", i, "' class='col_item' ><span class='sline'>", theads[i].title, "</span></li>"].join("")).appendTo($clist);
		}
	}
	if(self.columnsHidden.length > 0){
		$clist.find("input").each(function(){
			var iv = $j(this).val(),
				toShow = ($j.inArray(iv, self.columnsHidden) >= 0);
			$j(this).attr("checked",toShow);
		});
	}
	self.bestPosition(cdiv, "#col_list", $clist);
	self.bindActionColumn();
};

filtersClass.prototype.bestPosition = function(cdiv, filist, $nht) {
	var poss = $j(cdiv).offset(), posw = $j(cdiv).outerWidth(), winHeight = $j(window).height(), winWidth = $j(window).width(), winTop = this.pageYOffset || $j.boxModel && document.documentElement.scrollTop || document.body.scrollTop, winLeft = this.pageXOffset || $j.boxModel && document.documentElement.scrollLeft || document.body.scrollLeft, winBottom = winHeight + winTop, winRight = winWidth + winLeft, docHeight = $j(document).height(), docWidth = $j(document).width(), deltay = 0, deltax = 0, $filinList = $j("" + filist);

	$filinList
			.css({visibility:"collapse"})
			.empty().html($nht).show();

	var newpos = {x:(poss.left + posw + 2), y:poss.top}, elHeight = parseInt($filinList.height()), elWidth = parseInt($filinList.width()), elBottom = newpos.y + elHeight, elMargin = (poss.left + posw + 2 ) + elWidth, percentage = 0, hiddenTop = 0, hiddenBottom = 0, hiddenLeft = 0, hiddenRight = 0;
	if (newpos.x < winLeft) {
		hiddenLeft = winLeft - parseInt(newpos.x);
	}

	if (elMargin > winRight) {
		hiddenRight = elMargin - winRight;
		if (hiddenRight > 5) {
			newpos.x = newpos.x - elWidth - posw - 5;
		}
	}

	if (hiddenLeft > 5) {
		newpos.x = newpos.x + hiddenLeft + 20;
	}
	$filinList
			.css({
		"left":newpos.x,
		"top":poss.top,
		"visibility":'visible'
	});
};

filtersClass.prototype.lects = function(cdiv) {
	var self = this;
	this.hideAll();
	var zkey = $j("#filbox").data("skey"), $nht = pf.getLects(zkey), bkeys = filar[zkey].mvals;
	if (filar[zkey] && filar[zkey].mvals && filar[zkey].mvals.length > 0) {
		var tinar = $j("input", $nht), tl = tinar.length, cthis, cv;
		while (tl--) {
			cthis = tinar[tl];
			cv = $j(cthis).attr('data-cact');
			if ($j.inArray(cv, bkeys) >= 0) {
				$j(cthis).attr("checked", true);
			}
		}
	}
	bkeys = null;
	tinar = null;
	cthis = null;
	this.bestPosition(cdiv, "#filin_list", $nht);
};


function trimView(str, xlength) {
	var res = {};
	if (!xlength) {
		xlength = 45;
	}
	if (str && str.length > xlength) {
		res = {
			n:true,
			s:''
		};
		var words = str.split(" "), clen = 0, ind = 0;
		while (clen < xlength) {
			var nast = words[ind] + ' ';
			res.s += nast;
			clen += nast.length;
			ind++;
		}
		if (res.s.length > xlength) {
			res.s = res.s.slice(0, xlength);
		}
		if (res.s.length < str.length) {
			res.s += '...';
		}
	} else {
		res = {n:false, s:str}
	}
	return res;
}

function extend(Child, Parent) {
	var F = function() {
	}
	F.prototype = Parent.prototype
	Child.prototype = new F()
	Child.prototype.constructor = Child
	Child.superclass = Parent.prototype
}


var gpgr = new pager;
var memo = new progress();
var filmter = new filtersClass();
var onelist = true;


function startCallback() {
	// make something useful before submit (onStart)
	return true;
}

function filTool(val, add, mtd, vval) {
	var key = $j("#filbox").data("skey");
	var mode = pf.getColType(key), cnt = 0, res;

	if (add) {
		if (!filar[key]) {
			filar[key] = {};
		}
		if (!filar[key].methods) {
			filar[key].methods = {};
		}
		if (mode == 'date') {
			filar[key].methods[mtd] = {
				r:val,
				v:vval
			};
		}
		else {
			filar[key].methods[mtd] = val;
		}
		res = true;
	}
	else {
		var fkey = filar[key], fmtd = fkey.methods;
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
								r:'',
								v:''
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
		'disabled':!res,
		'checked':res
	});
}

$j.fn.eraser = function(state) {
	var self = this, $par = $j(self).parent();
	if (state) {
		$par.find("div.clfld").addClass("clflda").attr("title", "Clear").bind("click", function(ev) {
			$j(this).parent().find("input[type!='hidden']").each(function() {
				$j(this).trigger("cleanDate");
			});
		});
	}
	else {
		$j("div.clfld", $par).removeClass("clflda").attr("title", '').unbind("click");
		$j("input[type!='hidden']", $par).each(function() {
			$j(this).val("");
			var mnm = $j(this).attr("name");
			$j(self).find("input[name='filter_" + mnm + "']").val("");
		});
	}
	return self;
}

function tabPrepare(stg) {
	if (stg) {
		tgt = stg;
	}
	$j("#tabs").tabs().show().toTab(tgt);
}

function prePage(mode) {
	shadow = $j("div#shadow");
	$j(shadow).fadeTo(1, 0.5).hide();
	dw = ($j(document).width() + '');
	dw = dw.replace(/\d\d$/, "");
	$j(".mtab").width(dw + '00');
	if (mode === undefined) {
		mode = 'mas';
	}
	if (rrr > 0 || multistart) {
		$fcol = $j("#folder");
		if (mode == 'mas') {
			$j("<div style='float:left;'>Forms<div class='colic'></div></div>").data("mode", 'off').click(
					function() {
						toggleForms(this);
					}).insertBefore($fcol);
			$j(".mutedate").live("click", function(e) {
				popPCalendar(this);
			});
		}
		memo.init();
		memo.toggle();
		setTimeout(function() {
			filmter.init();
			gpgr.init('rtable', mode);
			if (tgt == 3) {
				$j("#tabs > ul > li:eq(3)").removeClass("tabs-disabled");
				stater = new sFrames;
				stater.init();
				grapher.init();
				if (multistart !== false && multistart > 0) {
					tabPrepare(tgt);
					stater.run();
					if (multistart === 2 && chartMode) {
						//launch graph build with saved parameters
						grapher.inject(chartMode);
					}
				}
				reporter.init();

			}

			memo.toggle();
			memo.banner();
		}, 5);
	} else {
		if (tgt == 3) {
			$j(".purestat").attr("disabled", false);
		}
	}
	//qurer.listUpdate();

	$j(".moretable").delegate(".moreview", 'mouseenter mouseleave', function(e) {
		var hover = (e.type === 'mouseenter');
		var mpar = $j(this).closest("tr").attr('id');
		if (hover) {
			var xp = $j(this).offset(), npos = {x:e.pageX, y:e.pageY}, npos0 = cloneThis(npos), winHeight = $j(window).height(), winWidth = $j(window).width(), winTop = this.pageYOffset || $j.boxModel && document.documentElement.scrollTop || document.body.scrollTop, winLeft = this.pageXOffset || $j.boxModel && document.documentElement.scrollLeft || document.body.scrollLeft, winBottom = winHeight + winTop, winRight = winWidth + winLeft, docHeight = $j(document).height(), docWidth = $j(document).width(), deltay = 0, deltax = 0;
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
			var elTop = npos.y, elHeight = parseInt($smalltip.height()), elWidth = parseInt($smalltip.width()), elBottom = elTop + elHeight, elMargin = npos.x + $smalltip.width(), percentage = 0, hiddenTop = 0, hiddenBottom = 0, hiddenLeft = 0, hiddenRight = 0;

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

			$j(this).add("*", this).bind("mousemove", {pdelta:xpdelta}, function(e) {
				$smalltip.css({
					left:e.pageX - (e.data.pdelta.x - 15),
					top:e.pageY - (e.data.pdelta.y + 5)
				});
			});
		} else {
			$smalltip.hide();
		}
	});

	$j(".qeditor", $j("#qtable")[0]).live('click', function(e) {
		var $tr = $j(this).closest("tr"), qid = $j(this).attr("data-id"), qname, qdesc, qstart = {}, qend = {}, zmode, tds = $j("td", $tr), str;
		for (var i = 0, j = tds.length; i < j; i++) {
			//$j("td", $tr).each(function(i){
			var $tdo = $j(tds[i]);
			switch (i) {
				case 1:
					qname = $tdo.attr("data-text");
					break;
				case 2:
					zmode = $tdo.text().toLowerCase();
				case 3:
					qdesc = $tdo.attr("data-text");
					break;
				case 4:
					qstart.r = $j("input", $tdo).val();
					qstart.v = $j(".stdw", $tdo).text();
					if (trim(qstart.v) == "N/D") {
						qstart = {
							r:0,
							v:''
						}
					}
					break;
				case 5:
					qend.r = $j("input", $tdo).val();
					qend.v = $j(".stdw", $tdo).text();
					if (trim(qend.v) == "N/D") {
						qend = {
							r:0,
							v:''
						}
					}
					break;
				default:
					break;
			}
		}//);
		var $zd = $j("#debox").dialog({
			width:350,
			height:270,
			resizable:false
		}).find("#qname").val(qname).end()
				.find("#qdesc").val(qdesc).end()
				.find("#qstart_date").val(qstart.v).end()
				.find("#qend_date").val(qend.v).end()
				.find("#quid").val(qid).end()
				.find(".datepicker[name^='filter_']").each(
				function() {
					if ($j(this).attr("name") == "filter_qstart") {
						$j(this).val(qstart.r);
					}
					else {
						$j(this).val(qend.r);
					}
				}).end()
				.find('#brest').each(
				function() {
					if (zmode == 'stats') {
						eval("str = " + $tr.attr("data-showr") + ";");
						$j(this).attr("checked", str).show().parent().show();
					} else {
						$j(this).hide().parent().hide();
					}
				}).end()
				.data("row", $tr.attr("id"))
				.data("stype", zmode)
				.show();
		/*if(zmode == "stats"){
		 $j("table",$zd).hide();
		 }else{*/
		$j("table", $zd).show();
		//}
	});

	$j(".jcheck", $j("#sendAll")[0]).live(be, function() {
		var st = $j(this).is(":checked"), $bbb = $j("#fcleaner"), bst = $bbb.attr("disabled");
		if (st) {
			bst = false;
		} else {
			if ($j(".jcheck:checked").length > 0) {
				bst = false;
			} else {
				bst = true;
			}
		}
		$bbb.attr('disabled', bst);
	});

	$j("div.exborder").css("display", "inline");
	makeView('hands');

	$j(".alltag").bind(be, function(e) {
		var cstate = $j(this).is(":checked"), fval, fst;
		$j(this).closest(".cblox")
				.find("li > label > input").attr("checked", cstate);
	});

	$j(".myimporter").delegate("input:eq(0)", "change",
			function(e) {
				var bext = $j(this).attr("data-ext"), rcvd = $j(this).val().split(".").pop();
				if (rcvd === bext) {
					$j(this).next().attr("disabled", false);
				} else {
					$j(this).val("");
					alert("File for import must have extension " + bext.toUpperCase());
				}
			}).next().attr("disabled", true);

	var $qtb = $j("#qtable");
	if ($j("tbody > tr", $qtb).length > 0) {
		addQTlook();
	}

	$j("#more_flip").click(function(e) {
		$j("#more_opts").toggle();
		$j(this).toggleClass("result_opts-more result_opts-less");
	});
	//$j("#rtable").show();
}

function addQTlook() {
	tabPrepare(0);
	$j("#qtable").tablesorter({
		headers:{
			0:{
				sorter:false
			},
			4:{
				sorter:"size"
			},
			5:{
				sorter:"size"
			},
			6:{
				sorter:false
			},
			7:{
				sorter:false
			}
		},
		widgets:["fixHead"]
	});
	tabevent = true;
	return;
}

function xtraSubmit() {
	$j('<iframe name="uploadQ" src="about:blank" width="0" height="0" style="display:none;" id="queryloader" ></iframe>').append(document.body);
	document.upq.submit();
}

function toggleForms(obj) {
	var $pt = $j(obj), cst = $pt.data("mode");
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

function makeView(cln) {
	if (cln != '') {
		var $but = $j("<div class='switch'></div>");
		var tar = $j("." + cln);
		for (var i = 0, j = tar.length; i < j; i++) {
			//$j("." + cln).each(function(){
			var $sobj = $j(tar[i]), bid = $sobj.attr("data-col"), tid = $sobj.attr('id'), $but1 = $but.clone();
			$but1.data("tgt", bid).data("vstt", 0).click(function(ev) {
				onf(this);
				ev.stopPropagation();
			});
			$sobj.click(function(ev) {
				onf($j("div", this));
				ev.stopPropagation();
			});
			$but1.prependTo($sobj);
			var dshow;
			if (isArray(aopen) && aopen.length > 0) {
				dshow = $j.inArray(tid, aopen);
			} else {
				dshow = -1;
			}
			if (dshow >= 0) {
				onf($j(".switch", $sobj));
			}
			else {
				$j("#block_" + bid).hide();
			}
			$but1 = null;
			//$sobj.next().addClass("exborderv");
		}//);
	}
}

function onf(self) {
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

function shCl(id) {
	document.location.href = "/?m=clients&a=view&client_id=" + id;
	document.location.go();
}

function popCalendarEd(field, value) {
	if (calwined) {
		calwined.close();
	}
	calif = field;
	window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendarEd&date=' + value, 'calwined', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendarEd(idate, fdate) {
	$j(".date_edit_" + calif).val(fdate).trigger("refresh");
}


function popPCalendar(field) {
	calendarField = field;
	idate = $j(field).val();
	window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendarP&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendarP(idate, fdate) {
	$j(calendarField).val(fdate);
}

function popCalendar(field) {
	calendarField = field;
	idate = eval('document.xform.filter_' + field + '.value');
	window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendar(idate, fdate) {
	fld_date = eval('document.xform.filter_' + calendarField);
	/*fld_fdate = eval('document.xform.' + calendarField);*/
	fld_date.value = idate;
	$j("input[name=" + calendarField + "]").val(fdate);
	//fld_fdate.value = fdate;
}

function popRCalendar(field) {
	calendarField = field;
	idate = $j(".datepicker[name='filter_" + field + "']").val();
	if (idate == 0) {
		idate = today;
	}
	window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendarR&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendarR(idate, fdate) {
	$j("#" + calendarField + "_date").val(fdate);
	$j(".datepicker[name='filter_" + calendarField + "']").val(idate);
}

function popTCalendar(field) {
	calendarField = field;
	idate = $j("#" + field).val();
	if (idate == 0) {
		idate = today;
	}
	window.open('index.php?m=public&a=calendar&dialog=1&callback=setCalendarT&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setCalendarT(idate, fdate) {
	var pts = calendarField.split("_");
	var $pr = $j("#qsr_" + pts[1]), ins = 0;
	if (pts[0] == 'start') {
		ins = 4;
	} else {
		ins = 5;
	}
	$j("td:eq(" + ins + ")", $pr)
			.find("div.stdw").text(fdate).end()
			.find("input").val(idate);

}


function checkDate() {
	if (document.xform.filter_finisher.value != "" && document.xform.filter_beginner.value != "" &&
			document.xform.filter_finisher.value < document.xform.filter_beginner.value) {
		return false;
	}
	else {
		return true;
	}
}

function clearData() {
	$j(".jcheck").attr("checked", false);
	datesoff();

}

function getData() {
	//var acl = $j(".jcheck:checked").length;
	var cnt = $j("#cboxes").find("input:checked").length;
	cnt += $j(".jcheck:checked").length;
	if (cnt > 0) {
		$j("#sendAll")
				.find("input.hasDatepick").attr("disabled", false).end()
				.attr("onsubmit", "").submit();
	}
	else {
		alert("Please select at least one field for result table");
		return false;
	}
}

function countMethods(key) {
	var r = 0;
	var t = pf.getColType(key);
	for (var c in filar[key].methods) {
		if (t == 'date') {
			if (filar[key].methods[c].r > 0) {
				r++;
			}
		} else {
			if (filar[key].methods[c].length > 0) {
				r++;
			}
		}
	}
	if (filar[key].mvals && filar[key].mvals.length > 0) {
		r++;
	}
	return r;
}

function regexEscape(txt, omit) {
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

function flipSel(path) {
	if (!path)
		return false;
	var $zt = $j(path.panel).parent().find("ul");
	$zt.find("li").each(function(x) {
		var self = this;
		$j(self).find("img").each(function() {
			$j(this).attr("src", function() {
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

$j.fn.blink = function(times, finalview) {
	var self = $j(this);
	var i = 0;
	self.fadeOut(100).show();
	for (i = 0; i < times; i++) {
		self.animate({
			opacity:0
		}, 600)
				.animate({
					opacity:1
				}, 600);
	}
	self.animate({opacity:0}, 500);
	return self;

}

$j.fn.cellType = function() {
	var cellp = this;
	if ($j(this)[0].tagName.toLowerCase() === 'div') {
		cellp = $j(this).closest("td");
	}
	return cellp;
}

$j.fn.toTab = function(tid) {
	$j("ul.topnav > li:eq(" + tid + ")", this).find("a").trigger("click");
	return this;
}

function markAll(obj) {
	var on = $j(obj).is(":checked");
	$j(obj).parent().parent().find("input:gt(0)").attr("checked", on);
}

function randomString() {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz", string_length = 5, randomstring = [];
	for (var i = 0; i < string_length; i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring.push(chars.substring(rnum, rnum + 1));
	}
	return randomstring.join("");
}

function listaMatic() {
	$j(".cblox").each(function() {
		var bpos = $j(this).offset().top, bhgt = $j(this).height(), inep = 0, $mblock = $j(this), move = false;
		$mblock.add("li", $mblock).css("visibility", "hidden").show();
		$j("li", $mblock).each(function() {
			var diff = (($j(this).offset().top + $j(this).height()) - bpos), scpart = 0, clist = {}, mdelta = 0;
			if (diff > 300 || move === true) {
				scpart = (parseInt(diff / 300) + inep);
				if (scpart > 0 || move === true) {
					if (scpart > inep) {
						clist['margin-top'] = (-285 ) + "px";
						if (!move) {
							inep = scpart;
							move = true;
						} else {
							scpart = ++inep;
						}
					} else {
						delete clist.margin - top;
					}
					if (move === true && scpart === 0) {
						scpart = inep;
					}
					clist['margin-left'] = (240 * scpart) + "px";

					$j(this).css(clist);

				}
			}
		});
		$mblock.add("li", $mblock).css("visibility", "inherit");
	});
	$j(".exborder").css("visibility", "inherit");
}
