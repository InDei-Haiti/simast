/* {{{ Copyright 2003,2004 Adam Donnison <adam@saki.com.au>

    This file is part of the collected works of Adam Donnison.

    This file is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This file is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

}}} */

// If we are an IE window, set undefined to null.  This is an ECMAscript
// standard, but then MS makes its own standards.
if (navigator.userAgent.indexOf('MSIE') != -1)
  var undefined = null;

/* {{{ function center_window
 * Create the window options clause required to ensure a window is
 * centered over the calling window.  A width or height of 0 or less
 * results in using the corresponding value for the parent window.
 * e.g. 0,0 results in a window exactly the same size and overlapping
 * the parent exactly.
 */

 var calendarField;
 var monIntervaler;
function center_window(width, height)
{
  var ix = window.outerWidth;
  var iy = window.outerHeight;
  var mx = window.screenX;
  var my = window.screenY;
  var result;

  var cx;
  var cy;

  if (width <= 0) {
    width = ix;
    cx = mx;
  } else {
    mx += ( ix / 2 );
    mx -= ( width / 2 );
    cx = Math.round(mx);
  }
  if (height <= 0) {
    cy = my;
    height = iy;
  } else {
    my += ( iy / 2 );
    my -= ( height / 2 );
    cy = Math.round(my);
  }

  result = 'screenX=' + cx + ',screenY=' + cy
  + ',outerHeight=' + height + ',outerWidth=' + width;
  return result;
}
//}}}

//{{{1 Class Comparable
// Define new Comparable object capable of being used to store
// data in an array.

//{{{2 constructor CompItem
function CompItem(key, data)
{
  this.key = key;
  this.data = data;
  this.compare = comp_keys;
  this.equals = comp_equal;
}
//2}}}

//{{{2 function comp_keys
// Compare function to compare two Comparable objects
function comp_keys(target)
{
  if (this.key == target.key)
    return 0;
  if (this.key < target.key)
    return -1;
  return 1;
}
//2}}}

//{{{2 function comp_equal
function comp_equal(target)
{
  if (this.key == target)
    return true;
  return false;
}
//2}}}


// {{{2 Comparison array class constructor
function Comparable()
{
  this.list = new Array();
  this.add = ca_add;
  this.find = ca_find;
  this.length = ca_length;
  this.get = ca_get;
	this.search = ca_search;
  this.count = 0;
}
//2}}}

//{{{2 function ca_add
function ca_add(key, data)
{
	var last_id = this.search(key);
	if (last_id != -1) {
		this.list[last_id] = new CompItem(key, data);
	} else {
		this.list[this.count] = new CompItem(key, data);
		this.count++;
	}
  // this.list.push(new CompItem(key, data));
}
//2}}}

//{{{2 function ca_find
function ca_find(key)
{
  var end = this.list.length;
  for ( var i = 0; i < end; i++)
  {
    cp = this.list[i];
    if (cp.equals(key))
      return cp.data;
  }
  return undefined;
}
//2}}}

//{{{2 function ca_search
function ca_search(key)
{
  var end = this.list.length;
  for ( var i = 0; i < end; i++)
  {
    cp = this.list[i];
    if (cp.equals(key))
      return i;
  }
  return -1;
}
//2}}}

//{{{2 function ca_length
function ca_length()
{
  return this.list.length;
}
//2}}}

//{{{2 function ca_get
function ca_get(id)
{
  return this.list[id];
}
//2}}}
//1}}}

//{{{1 Class HTMLex
//{{{2 Constructor HTMLex
function HTMLex()
{
  this.addTable = _HTMLaddTable;
  this.addRow = _HTMLaddRow;
  this.addHeader = _HTMLaddHeader;
  this.addHeaderNode = _HTMLaddHeaderNode;
  this.addCell = _HTMLaddCell;
  this.addCellNode = _HTMLaddCellNode;
  this.addTextInput = _HTMLaddTextInput;
  this.addHidden = _HTMLaddHidden;
  this.addTextNode = _HTMLaddTextNode;
  this.addNode = _HTMLaddNode;
  this.addSpan = _HTMLaddSpan;
  this.addSelect = _HTMLaddSelect;
  this.addOption = _HTMLaddOption;
}
//2}}}

//{{{2 function _HTMLaddTable
function _HTMLaddTable(id, width, border)
{
  var c = new Comparable;
  if (width)
    c.add('width', width);
  if (border)
    c.add('border', border);
  if (id)
    c.add('id', id);
  return this.addNode('TABLE', false, c);
}
//2}}}

//{{{2 function _HTMLaddRow
function _HTMLaddRow(id)
{
  var tr = document.createElement('TR');
  if (id)
    tr.setAttribute('id', id);
  return tr;
}
//2}}}

//{{{2 function _HTMLaddHeaderNode
function _HTMLaddHeaderNode(node, id, width)
{
  var c = new Comparable;
  if (id)
    c.add('id', id);
  if (width)
    c.add('width', width);
  return this.addNode('TH', node, c);
}
//2}}}

//{{{2 function _HTMLaddHeader
function _HTMLaddHeader(text, id, width)
{
  var c = new Comparable;
  if (id)
    c.add('id', id);
  if (width)
    c.add('width', width);
  return this.addTextNode('TH', text, c);
}
//2}}}

//{{{2 function _HTMLaddCell
function _HTMLaddCell(text, id, width, bold)
{
  var c = new Comparable;
  if (id)
    c.add('id', id);
  if (width)
    c.add('width', width);
  return this.addTextNode('TD', text, c, bold);
}
//2}}}

//{{{2 function _HTMLaddSpan
function _HTMLaddSpan(text, id)
{
  var c = new Comparable;
  if (id)
    c.add('id', id);
  return this.addTextNode('SPAN', text, c);
}
//2}}}

//{{{2 function _HTMLaddCellNode
function _HTMLaddCellNode(node, id, width)
{
  var c = new Comparable;
  if (id)
    c.add('id', id);
  if (width)
    c.add('width', width);
  return this.addNode('TD', node, c);
}
//2}}}
//{{{2 function _HTMLaddTextNode
function _HTMLaddTextNode(type, text, args, bold)
{
  var node = document.createElement(type);
  if (bold) {
    var b = node.appendChild(document.createElement('B'));
    if (text)
      b.appendChild(document.createTextNode(text));
  } else {
    if (text)
      node.appendChild(document.createTextNode(text));
  }
  var i;
  if (args) {
    for (i = args.length() -1; i >=0; i--) {
      var elem = args.get(i);
      node.setAttribute(elem.key, elem.data);
    }
  }
  return node;
}
//2}}}

//{{{2 function _HTMLaddNode
function _HTMLaddNode(type, child, args)
{
  var node = document.createElement(type);
  if (child)
    node.appendChild(child);
  var i;
  for (i = args.length() -1; i >=0; i--) {
    var elem = args.get(i);
    node.setAttribute(elem.key, elem.data);
  }
  return node;
}
//2}}}

//{{{2 function _HTMLaddTextInput
function _HTMLaddTextInput(id, value, size, maxlength)
{
  var c = new Comparable;
  c.add('id', id);
  c.add('name', id);
  c.add('type', 'text');
  if (size)
    c.add('size', size);
  if (maxlength)
    c.add('maxlength', maxlength);
  if (value)
    c.add('value', value);
  return this.addNode('INPUT', false, c);
}
//2}}}

//{{{2 function _HTMLaddHidden
function _HTMLaddHidden(id, value)
{
   if (window.navigator.userAgent.toLowerCase().match(/gecko/)) {navigator.family = "gecko"}
   var c = new Comparable
   c.add('id', id);
   c.add('name', id);
   if (navigator.family == "gecko"){
         c.add('type', 'hidden');
         type = 'INPUT';
   } else {
         type = 'TEXTAREA';
         c.add('className', 'hidden');
   }
   c.add('value', value);
   return this.addNode(type, false, c);
}
//2}}}

//{{{2 function _HTMLaddSelect
function _HTMLaddSelect(id, cls, multi)
{
  var c = new Comparable;
  c.add('id', id);
  c.add('name', id);
	if (cls)
		c.add('class', cls);
	if (multi)
		c.add('multiple', 'multiple');
  return this.addNode('SELECT', false, c);
}
//2}}}

//{{{2 function _HTMLaddOption
function _HTMLaddOption(value, text, selected)
{
  var c = new Comparable;
  c.add('value', value);
	if (selected)
		c.add('selected', 'selected');
  return this.addTextNode('OPTION', text, c);
}
//2}}}

//1}}}

// class CommonEvent {{{

function CommonEvent(e)
{
  // Handle IE, standard Javascript, and passable fields for non-events.
  // Tuned to run with NS 4 and above and IE 4 and above.
  // Tested with Mozilla 1.7, Firefox 0.8, and IE 5
  var target = null;
  var x = 0;
  var y = 0;
  var type = null;
  var button = null;
  var keycode = null;
  var altKey = false;
  var shiftKey = false;
  var ctrlKey = false;
  var metaKey = false;

  if (e) {
    if (e.target) {
      this.target = e.target;
      this.type = e.type;
      this.x = e.x;
      this.y = e.y;
      if (e.modifiers) {
	this.altKey = (e.modifiers & ALT_MASK) ? true : false;
	this.ctrlKey = (e.modifiers & CONTROL_MASK) ? true : false;
	this.shiftKey = (e.modifiers & SHIFT_MASK) ? true : false;
	this.metaKey = (e.modifiers & META_MASK) ? true : false;
      } else {
	if (e.altKey) this.altKey = true;
	if (e.shiftKey) this.shiftKey = true;
	if (e.ctrlKey) this.ctrlKey = true;
	if (e.metaKey) this.metaKey = true;
      }
      if (e.type.substr(0,3).toLowerCase() == 'key') {
	this.keycode = e.which;
      } else {
	this.button = e.which;
      }
    } else {
      this.target = e;
      this.type = 'field';
    }
  } else if (event) {
    this.target = event.srcElement;
    this.type = event.type;
    this.x = event.x;
    this.y = event.y;
    this.button = event.button;
    this.keycode = event.keyCode;
    this.altKey = event.altKey;
    this.shiftKey = event.shiftKey;
    this.ctrlKey = event.ctrlKey;
  }
}

//}}}

//{{{ function ucfirst
function ucfirst(s, delim)
{
  if (!delim)
    delim = ' ';
  var a = s.split(delim);
  var res = "";
  var start = false;
  for (var i = 0; i < a.length; i++) {
    if (start)
      res += " ";
    else
      start = true;
    res += a[i].substr(0, 1).toUpperCase() + a[i].substr(1);
  }
  return res;
}
//}}}


/**{{{ function clear_span
 * Removes any children of an element by ID.
 */
function clear_span(id)
{
  var span = document.getElementById(id);
  if (span) {
    if (span.hasChildNodes()) {
      for (var i = span.childNodes.length - 1; i >= 0; i--)
	span.removeChild(span.childNodes.item(i));
    }
  }
  return span;
}
//}}}

//{{{ function show_message
function show_message(fname, txt)
{
  display_message(txt, fname + '_message');
}
//}}}

//{{{ function show_instruction
function show_instruction(txt)
{
  display_message(txt, 'instruct');
}
//}}}

/** {{{ function display_message
 * Generic message display.  This looks for the required element on
 * the page and if found it adds a text node, (or changes an existing
 * one) to the text required.  Used by show_message and show_instruction.
 * The element name is supplied with the 'id=' attribute of the
 * HTML.
 */
function display_message(txt, elem)
{
  var span = document.getElementById(elem);
  if (span == null)
    return;

  var text;
  if (span.hasChildNodes()) {
    text = span.childNodes.item(0);
    text.nodeValue = txt;
  } else {
    text = span.appendChild(document.createTextNode(txt));
  }
}
//}}}

//{{{ clear_message, reset_message, default_instruction
function clear_message(fname)
{
  reset_message( fname + '_message');
}

function clear_instruction()
{
  reset_message('instruct');
}

//}}}

/** {{{ function reset_message
 * Function to clear the text node associated with an element.
 * The element name is supplied with the 'id=' attribute of the
 * HTML.  This can be used to remove text on any node that supports it.
 */
function reset_message(elem)
{
  var span = document.getElementById(elem);
  if (span == null)
    return;

  var text;
  if (span.hasChildNodes()) {
    text = span.childNodes.item(0);
    text.nodeValue = '';
  } else {
    text = span.appendChild(document.createTextNode(''));
  }
}
//}}}

// function find_anchor {{{
// The find_anchor function is usually called when the browser
// is IE based and therefore doesn't use the name to provide
// an index into document.anchors.  Should probably be replaced
// to use getElementById or getElementsByTagName instead of
// relying on browser-specific extensions.
function find_anchor(a)
{
  for (var i = 0; i < document.anchors.length; i++) {
    if (document.anchors[i].name == a)
      return true;
  }
  return false;
}
//}}}

// {{{ function getInnerHeight
function getInnerHeight(win) {
var winHeight;
  if (win.innerHeight) {
    winHeight = win.innerHeight;
  }
  else if (win.document.documentElement && win.document.documentElement.clientHeight) {
    winHeight = win.document.documentElement.clientHeight;
  }
  else if (win.document.body) {
    winHeight = win.document.body.clientHeight;
  }
  else {
    winHeight = 0; // This should never happens
  }
  return winHeight;
}
//}}}

// {{{ function validDate
function checkValidDate(date)
{
		msg = "";

		var dateformat = /^\d{2}\/\d{2}\/\d{4}$/
		if (!dateformat.test(date))
		{
			msg = "Invalid date. Please use 'dd/mm/yyyy format'";
			return msg;
		}
		dar = date.split("/");
        if (dar.length < 3)
		{
            msg = "Invalid";
            return msg;
        }
		else if (isNaN(parseInt(dar[0],10)) || isNaN(parseInt(dar[1],10)) || isNaN(parseInt(dar[2],10)))
		{
            msg= " Invalid";
            return msg;
        }
		else if (parseInt(dar[1],10) < 1 || parseInt(dar[1],10) > 12)
		{
            msg = "Invalid";
            return msg;
        }
		else if (parseInt(dar[0],10) < 1 || parseInt(dar[0],10) > 31)
		{
            msg = "Invalid";
			return msg;

        }
		else if(parseInt(dar[2],10) < 1900 || parseInt(dar[2],10) > 2030)
		{

            msg = "Invalid"
			return msg;
        }
		return msg;
}
//}}}

// {{{ function validYear
function checkValidYear(year)
{
		msg = "";
		if (isNaN(parseInt(year,10)) )
		{
			msg = "Invalid year";
			return msg;
		}
		if(parseInt(year,10) < 1900 || parseInt(year,10) > 2020)
		{

            msg = "Invalid year";
			return msg;
        }
		return msg;
}
//}}}


// {{{ function makeSelections - select all checkboxes and radio buttons in a form
function makeSelection(form){
	var el = form.elements
	for (i = 0,edl=el.length; i < edl; i++) {
		if (!el[i].disabled) {
			if (el[i].type == "checkbox") {
				el[i].checked = true;
			}
			if (el[i].type == "radio") {
				el[i].checked = true;
			}
		}
	}
}

// {{{ function clearSelections - clear all checkboxes and radio buttons in a form
function clearSelection(form){
	var el = form.elements
	for (i = 0,edl=el.length; i < edl; i++) {
		if (!el[i].disabled) {
			if (el[i].type == "checkbox") {

				el[i].checked = false;
			}
			if (el[i].type == "radio") {

				el[i].checked = false;

			}
		}
	}
}

function popXCalendar(field){
    calendarField = field;
    idate = $j('input[name="'+field+'"]').val();
    window.open('index.php?m=public&a=calendar&dialog=1&callback=setXCalendar&date=' + idate, 'calwin', 'top=250,left=250,width=251, height=220, scollbars=false');
}

function setXCalendar(idate, fdate){
	var $vis_exist = $j("input[name=" + calendarField + "_visual]");
	if ($vis_exist && $vis_exist.length == 1) {
		$vis_exist.val(fdate);
		$j("input[name=" + calendarField + "]").val(idate);
	}
	else {
		$j("input[name=" + calendarField + "]").val(fdate);
	}
}

var calerC = function (){
    this.cnt=0;
    this.iter = function (){
        this.cnt ++;
        return this.cnt;
     }
}

var doCal = new calerC;

function attachPicker(obj,odate){
	obj.datepick(calObj).datepick({defaultDate: odate});
}

function cloneThis(o)  {
  if(!o || 'object' !== typeof o)  {
    return o;
  }
  var c = 'function' === typeof o.pop ? [] : {};
  var p, v;
  for(p in o) {
    if(o.hasOwnProperty(p)) {
      v = o[p];
      if(v && 'object' === typeof v) {
	c[p] = cloneThis(v);
      }
      else {
	c[p] = v;
      }
    }
  }
  return c;
}

function manField(id){
	if($j("#"+id).find("option:selected").index() == 0){
		$j("#"+id).focus();
		return false;
	}else{
		return true;
	}
}

function manDateField(fld,vint){
	var fieldToCheck = $j('#'+fld),res=false;flet=fieldToCheck.attr("disabled");
	if(flet === true){
	    return true;
	}
	if(fieldToCheck.val() == ""){
	    return true;
	}
	if (fieldToCheck.length === 1) {
		if (fieldToCheck.val().length > 0) {
			if (vint === true) {
				if (!isNaN(parseInt(fieldToCheck.val(), 10))) {
					res=true;
				}
			}
			else {
				res = true;
			}
		}
	}
	if(res === false){
		$j(fieldToCheck).focus();
	}
	return res;
}

function checkMandatFields(){
	var msg = '', res = true;
	$j(".mandat-field").each(function(){
		if ($j(this).val() == '' || $j(this).val() == -1) {
			var title = $j(this).closest("td").focus().addClass("attn").closest("tr").find("td:eq(0)").text();
			msg = 'Please fill value for ' + title;
		}
		else {
			$j(this).closest("td").removeClass("attn");
		}

	});
	if (msg.length > 0) {
		alert(msg);
		res = false;
	}
	return res;
}

/**
*
*  URL encode / decode
*  http://www.webtoolkit.info/
*
**/

var Url = {

	// публичная функция для кодирования URL
	encode : function (string) {
		return escape(this._utf8_encode(string));
	},

	// публичная функция для декодирования URL
	decode : function (string) {
		return this._utf8_decode(unescape(string));
	},

	// приватная функция для кодирования URL
	_utf8_encode : function (string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	},

	// приватная функция для декодирования URL
	_utf8_decode : function (utftext) {
		var string = "";
		var i = 0;
		var c = c1 = c2 = 0;

		while ( i < utftext.length ) {

			c = utftext.charCodeAt(i);

			if (c < 128) {
				string += String.fromCharCode(c);
				i++;
			}
			else if((c > 191) && (c < 224)) {
				c2 = utftext.charCodeAt(i+1);
				string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
				i += 2;
			}
			else {
				c2 = utftext.charCodeAt(i+1);
				c3 = utftext.charCodeAt(i+2);
				string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
				i += 3;
			}

		}

		return string;
	}

}

var timeDiff  =  {
    setStartTime:function (){
        d = new Date();
        time  = d.getTime();
    },

    getDiff:function (){
        d = new Date();
        return (d.getTime()-time);
    }
}

function popTable(table,mode){
	var add1,tw,pbutt,add='<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"><head><meta name="Description" content="dotProject Default Style" />	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />',
		$code1=$j("#"+table).clone(),clm= new RegExp("tbl","g");
	var code=$code1.find('table').attr("class",function(i,cl){
		if(!clm.test(cl)){
			return cl+' tbl';
		}
	}).end().html(),cdfm,cssyle=[];
	cssyle.push('<style TYPE="text/css" media="screen"> table.tbl {background: #a5cbf7;}table.tbl TH {background-color: #08245b !important;color: #ffffff;font-size:9pt;list-style-type: disc;list-style-position: inside;border: outset #D1D1CD 1px !important;font-weight: normal;text-align:center;}table.tbl td {font-size:8pt;background-color:#fff;}.vdata,.summr{text-align: right;}</style>');
	cssyle.push('<style TYPE="text/css" media="print"> table {background-color:#000000 !important;border:1px solid #000000;border-spacing:0 !important;}table.tbl td, th {border:1px outset #000000;border-spacing:0 !important;font-size:8pt;padding:1px;}.bhides{display:none !important;}.vdata,.summr{text-align: right;}</style>');
	cssyle.push('<style TYPE="text/css" MEDIA="screen, print"> .offwall{display :none !important;}.vdata,.summr{text-align: right;}</style></head>');
	pbutt='&nbsp;&nbsp;<input type="button" value="Print" class="bhides" onclick="window.print();">';
	tw=window.open("","_blank","menubar=yes,location=no,resizable=yes,scrollbars=yes,status=no");
	cdfm=[cssyle.join("\n") ,  "<body><div id='bhides'><input type='button' onclick='window.close();' value='Close' class='bhides'>",pbutt,"</div>",code,"</body></html>"].join("");
	tw.document.write(cdfm);
	tw.focus();
}

function monitorPs(key,pcnt){
	monIntervaler = setInterval('mPSf("'+key+'","'+pcnt+'")',3000);
}

function mPSf(key,pcnt){
	var res = 0;
	$j.ajax({
		async: false,
		url: ["/mon_exe.php?key=",key,"&bbk=",Math.random()].join(""),
		type: "GET",
		success: function(msg){
			res = parseInt(msg);
			if (isNaN(res)) {
				res = 0;
			}
			$j("#" + pcnt).text(res);

			//setTimeout("monitorPs('"+key+"','"+pcnt+"');", 3000);
			//}

			//else {
			if (res === 100) {
				clearInterval(monIntervaler);
				monIntervaler = false;
				return true;
			}
		}
	});
}

function buildSelectList(arr,selected,disabled,action,uClass){
	var first=true,fval,$zsel=$j('<select class="text '+(uClass ? uClass : '')+'"></select>').change({action:action},function(e){
			if(action){
				//	wzrd.doDataType(this);
				eval(action);
			}
		});
	for(var i in arr ){
		if(arr.hasOwnProperty(i)){
			if(first === true){
				fval=i;
			}
			$j('<option value="'+i+'">'+arr[i]+'</option>')
			.attr("disabled",function(){
				if(disabled  == i  || (i === 'empty')){
					return true;
				}else{
					return false;
				}
			})
			.attr("selected",function(){
				if(isset(selected) && selected == i || (selected === 'initList' && fval === i)){
					return true;
				}else{
					return false;
				}
			})
			.appendTo($zsel);
			first=false;
		}
	}

    $j('<option value="dbtype">BigDataType</option>').appendTo($zsel);
	if(!selected){
		$zsel.val(fval);
	}
	return $zsel;
}

function isset(val){
	var res=false;
	if(val != null && val != undefined ){
		res = true;
	}
	return res;
}

function sortObject(o) {
    var sorted = {}, key, a = [];
    for (key in o) {
        if (o.hasOwnProperty(key)) {
                a.push(key);
        }
    }
    a.sort(function(a,b){return a - b});
    for (key = 0; key < a.length; key++) {
        sorted[a[key]] = o[a[key]];
    }
    return sorted;
}

function appendArray(a1,a2){
	for(var i in a2){
		if(a2.hasOwnProperty(i) && a2[i] !== undefined){
			i = i.toString();
			a1[i]=a2[i];
		}
	}
	return a1;
}
function today(){
    var d = new Date,
	month =(d.getMonth()+1),
	day =d.getDate(),
	year =d.getFullYear();
    return [day , "/" , (month < 10 ? '0'+month : month) , "/" , year].join("");
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
		d.innerHTML = ['<iframe style="display:none" src="about:blank" id="',n,'" name="',n,'" onload="AIM.loaded(\'',n,'\')"></iframe>'].join("");
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

function date2Val(dt){
	var dps,res='';
	if(dt && dt.length === 10){
		if(dt.indexOf("/") >= 0){
			res=dt.split("/").reverse().join("");
		}else if(dt.indexOf("-") >= 0){
			res=dt.split("-").join("");
		}
	}
	return res;
}

function dateView(date){
	var pts=[],res='';
	if(date && date.length === 10){
		res=date.split("-").reverse().join("/");
	}
	return res;
}

function dateStore(date){
	var pts=[],res='';
	if(date && date.length === 10){
		res=date.split("/").reverse().join("-");
	}
	return res;
}

function stripTags(strInputCode) {
	strInputCode = strInputCode.replace(/&(lt|gt);/g, function (strMatch, p1) {
		return (p1 == "lt") ? "<" : ">";
	});
	return  strInputCode.replace(/<\/?[^>]+(>|$)/g, "");
}

function cleanSelection() {
	if (window.getSelection) {
		if (window.getSelection().empty) {  // Chrome
			window.getSelection().empty();
		} else if (window.getSelection().removeAllRanges) {  // Firefox
			window.getSelection().removeAllRanges();
		}
	} else if (document.selection) {  // IE?
		document.selection.empty();
	}
}

function loadjscssfile(filename, filetype){
 if (filetype=="js"){ //if filename is a external JavaScript file
  var fileref=document.createElement('script')
  fileref.setAttribute("type","text/javascript")
  fileref.setAttribute("src", filename)
 }
 else if (filetype=="css"){ //if filename is an external CSS file
  var fileref=document.createElement("link")
  fileref.setAttribute("rel", "stylesheet")
  fileref.setAttribute("type", "text/css")
  fileref.setAttribute("href", filename)
 }
 if (typeof fileref!="undefined")
  document.getElementsByTagName("head")[0].appendChild(fileref)
}


function arrayElement(action, el, arr){
	if(action === 'add'){
		arr.push(el);
	}else if(action === 'del'){
		var pos = $j.inArray(el, arr);
		if(pos >= 0 )
			arr.splice(pos, 1);
	}
	arr.sort();
	return arr;
}
function moveUpDown(select,move){
	 var $opts = $j('#'+select+' option:selected');
	 
	 if($opts.length){
	     (move == 'Up') ? 
	         $opts.first().prev().before($opts) : 
	         $opts.last().next().after($opts);
	 }
}

function addRemoveOption(ori,des){
	var $opts = $j('#'+ori+' option:selected');
	if($opts.length){
		for(var i=0;i<$opts.length;i++){
			$j('#'+des).append($opts[i]);
		}
		
	}
}
function returnStr(str){
	return ""+str;
}