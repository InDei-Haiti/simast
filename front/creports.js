/**
 * Created with JetBrains PhpStorm.
 * User: stig
 * Date: 21.04.12
 * Time: 20:47
 */

function bstrap() {
	rbr.prepareSelects();
}

function fake() {
}


var rbr = (function (my) {

	var selectLevel = [];
	var pdfCookieCheck;
	var $content = $j("#crdata");
	var $config = $j("#all-wrap");
	var filledList = false;
	var dialogOfReports = false;
	var tabId = 1;
	var $tabs = $j("#tabs");
	var reportUrl = '/front/index.php?mode=dig&xmode=get_report_body&rep_id=';
	var seeTabs = [];

	function loadData(id, title) {
		$tabs.css("visibility", "visible");
		if ($j.inArray(id, seeTabs) < 0 ) {
			$tabs.trigger('addTab', [reportUrl + id, title]);
			/*if (!$j("#tabs-" + id).attr("data-loaded")) {
				$j("#tabs-" + id).load("/front/" + id + ".html");
				$j("#tabs-" + id).attr("data-loaded", "1");
			}*/
			$tabs.find("li:last").attr("id", "tab_" + id).attr("data-loaded", "1");
			seeTabs.push(id);
		} else {
			var index = $j("#tab_"+id).parent().index();
					//$tabs.find('a[href="#tabs-' + id + '"]').parent().index();
			$tabs.trigger('selectTab', [index]);
		}
	}

	function dCollector() {
		var timeLimits = {};
		$j("#rqvals").find(".repdt_lims").each(function () {
			var $tl = $j(this), meType = $tl.attr("data-type"),
					end = $tl.closest(".period_sel").attr("data-type");
			if (!timeLimits.hasOwnProperty(end)) {
				timeLimits[end] = {};
			}
			timeLimits[end][meType] = $tl.find(":input").val();
		});
		//perform check for logic  start should be < stop
		if (timeLimits['start'] && timeLimits['stop']) {
			if (parseInt(timeLimits['start']['year'] + timeLimits['start']['mon']) <=
					parseInt(timeLimits['stop']['year'] + timeLimits['stop']['mon'])) {
				alert("Period error!\nStart date should be earlier then stop date");
				return false;
			}
		}
		return {
			dept:$j(".dept-place .sel_li", $config).attr("data-item"),
			cntr:$j(".center-place .sel_li", $config).attr("data-item"),
			/*mon:$j("#rep_mon").val(),
			 year:$j("#rep_year").val()*/
			limits:timeLimits
		};
	}

	function viewReportButton(list) {
		if (list && list.length > 0 && list != 'fail') {
			var plist = $j.parseJSON(list);
			var show_rep = tmpl("item_tmpl"), html = [];
			for (var k in plist) {
				if (plist.hasOwnProperty(k)) {
					html.push(show_rep(plist[k]));
				}
			}
			$j("#dialog").find("tbody").html(html.join(""));
			filledList = (html.length > 0);
		}
		$j("#rep_but").each(function () {
			if (filledList === true) {
				$j(this).show();
			} else {
				$j(this).hide();
			}
		});
	}

	function servLoad(item) {
		var rQ = false, rD = {}, rAt, rAct = false, tv;
		$j("#loading").fadeIn(1).show();
		switch (item) {
			case 'rep-list':
				rQ = true;
				rD = {
					xmode:'reports'
				};
				rAt = $j("#brdata");
				break;
			case 'rep-items':
				rQ = true;
				rD = {
					xmode:'report-items',
					rpid:selectLevel[0]
				};
				rAt = $j("#brdata");
				break;
			case 'center-list':
				rQ = true;
				tv = dCollector();
				if (tv === false) {
					rQ = false;
				}
				rD = {
					//xmode:'crdata_filter',
					xmode:'crdata_search',
					rtype:'complete',
					vals:JSON.stringify(tv)
				};
				rAt = $content;
				rAct = 'revealReports(msg);';
				break;
			case 'center-data':
				rQ = true;
				tv = dCollector();
				if (tv === false) {
					rQ = false;
				}
				rD = {
					xmode:'crdata_search',
					rtype:'complete',
					vals:JSON.stringify(tv)
				};
				rAt = $content;
				rAct = 'viewReportButton(msg);';
				break;
			case 'rep-load':
				tv = itemsToShow();
				if (tv !== false) {
					rAt = $content;
					rQ = true;
					rD = {
						xmode:'crdata_load',
						rtype:'complete',
						wrap:'0',
						vals:JSON.stringify(tv)
					};
					rAct = '$j(rAt).hide().append(msg).show();';
				}
				break;
			default:
				break;
		}
		if (rQ === true) {
			$j.post("/front/index.php?mode=dig", rD, function (msg) {
				if (msg != 'fail') {
					if (msg == '') {
						$j("#rempty").fadeIn(1200).delay(2000).fadeOut(1200);
					}
					//$j(rAt).append(msg);
					if (rAct !== false) {
						eval(rAct + '');
					}
				} else {
					$j("#fail").dialog({
						modal:true,
						buttons:{
							Ok:function () {
								$j(this).dialog("close");
							}
						}
					});
				}
				$j("#loading").fadeOut("slow",function(){
					$j(this).hide();
				})
			});
		}
	}

	function itemsToShow() {
		var picked = [];
		$j("#dialog").find("input:checked").each(function () {
			picked.push(this.value);
		});
		return (picked.length > 0 ? picked : false);
	}

	function mtRand() {
		return Math.floor(Math.random() * 99999);
	}

	function initTriggers() {
		$j("#rep_selected").live("change", function () {
			if ($j(this).val() > 0) {
				selectLevel[0] = $j(this).val();
				servLoad("rep-items");
			}
		});
		$j("#rep_sitems").live("change", function () {
			var sval = $j(this).val();
			if (sval >= 0) {
				selectLevel[1] = sval;
				servLoad('center-data');
			}
		});

		$j(".bkill").live("click", function () {
			$j(this).closest(".cr_item").slideUp("fast", function () {
				$j(this).remove();
			});
		});

		$j(".period_sel select").change(function(){
			servLoad("center-list");
		});

		$j(".top_sels")
				.each(function () {
					$j(this).find("li:first").addClass("sel_li");
				}).end()
				.delegate("li", "click", function () {
					$j(this).parent().find("li").removeClass("sel_li").end().end().addClass("sel_li");
					servLoad('center-list');
				});

		$j(".pdf_but").click(function () {
			rbr.pdfFill(this);
		});

		$j("#clear_but").click(function () {
			$content.empty();
			$j("#dialog").find("tbody").html("").end().hide();
			$j("#rep_but").hide();

			filledList = false;
			dialogOfReports = false;
		});

		$j("#show_but").click(function () {
			servLoad("center-data");
		});

		$j("#view_but").click(function(){
			var selectedRefs = [];
			$j("#rqvals").find(":input:checked:visible").each(function(){
				var rid = this.value;
				loadData(rid, $j(this).next().text() );
				selectedRefs.push(rid);
			});
			if(selectedRefs.length == 0){
				resultIsEmpty();
			}
		});

		$j("#rep_but").click(function () {
			if (filledList === true) {
				if (dialogOfReports === false) {
					$j("#dialog").dialog({
						autoOpen:false,
						height:300,
						width:500,
						modal:false,
						//position: "bottom",
						autoResize:true,
						zIndex:3999,
						buttons:{
							"Send" : function () {
								$j("#dialog").dialog("close");
								servLoad("rep-load");
								dialogOfReports = false;
							},
							"Cancel" : function () {
								$j("#dialog").dialog("close");
							}
						},
						close:function () {
							$j("#dialog").dialog("destroy");
							dialogOfReports = false;
						}
					});
					dialogOfReports = true;
				}
				$j("#dialog").dialog("open").trigger("resize").show();

			} else {
				alert("No reports found.");
			}
		});

		$j("#rep_selector").dropdownchecklist({ emptyText:"Select report", width:210 });

		$j('#addTab').click(function () {
			loadData(tabId, "Tab " + tabId);
			++tabId;
		});

		$tabs.tabs({
			tabTemplate:"<li><a href='#{href}'>#{label}</a> <span class='ui-icon ui-icon-close' style='float: right;'>Remove Tab</span></li>"
		}).scrolltab();

		$j(".ui-icon-close").live("click",function(){
			var killId = $j(this).closest("li").attr("id").replace(/\D/g,"");
			var pos = $j.inArray(killId,seeTabs);
			seeTabs.splice(pos,1);
		});
	}


	function resultIsEmpty(){
		$j("#rempty").fadeIn(700).delay(500).fadeOut(500);
	}

	function revealReports(list) {
		if(typeof list === "string" && list != 'fail'){
			list = $j.parseJSON(list);
		}
		var found = 0;
		$j("#rep_selector")
				.find("option").attr("checked",false)
				.each(function () {
					if ($j.inArray(this.value, list) < 0) {
						$j(this).attr("disabled", true);
						$j("#tab_"+this.value).tabs().hide();
					} else {
						$j(this).attr("disabled", false);
						$j("#tab_" + this.value).tabs().show();
						++found;
					}
				});
		if(list.length === 0){
			resultIsEmpty();
		}
		$j("#rep_selector").dropdownchecklist("refresh");
		if($tabs.find(".ui-tabs:visible").length > 0){
			var tvl = $tabs.find(".ui-tabs:visible:last"),
				tvIn = $tabs.find(".ui-tabs").index(tvl);
				$tabs.find(".ui-tabs-panel").show().end()
						.tabs('select',tvIn);
		}else{
			$j(".ui-tabs-panel",$tabs).hide();
		}
		if($j(".ui-tabs:visible",$tabs).length > 0){
			$j(".pdf_but").addClass("pdf_on");
		}else{
			$j(".pdf_but").addClass("pdf_off");
		}
	}


	return {
		prepareSelects:function () {
			initTriggers();
		},
		pdfFin:function () {
			$j(".pdf_but").toggleClass("pdf_on pdf_off").next().hide();
		},
		pdfFill:function (obj) {
			if ($j(obj).hasClass("pdf_on")) {
				$j(".pdf_loading").show().prev().toggleClass("pdf_on pdf_off");
				var vpcode = $j(".ui-tabs-panel:visible",$tabs).html();
				if (vpcode && vpcode.length > 0) {
					$j("#pdata_bnk").val(vpcode);
					vpcode = null;
					$j.cookie("filePDF", 0);
					$j("#startPDF").trigger("click");

					pdfCookieCheck = window.setInterval(function () {
						var cv = $j.cookie("filePDF");
						if (cv == '1') {
							window.clearInterval(pdfCookieCheck);
							rbr.pdfFin();
						}
					}, 300);
				} else {
					rbr.pdfFin();
					alert("Please fill content for building PDF");
				}
			}
		}
	}
})
		(rbr);