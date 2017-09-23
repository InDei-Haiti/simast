/* http://keith-wood.name/datepick.html
   Date picker for jQuery v4.0.5.
   Written by Keith Wood (kbwood{at}iinet.com.au) February 2010.
   Dual licensed under the GPL (http://dev.jquery.com/browser/trunk/jquery/GPL-LICENSE.txt) and
   MIT (http://dev.jquery.com/browser/trunk/jquery/MIT-LICENSE.txt) licenses.
   Please attribute the author if you use it. */

(function($) { // Hide scope, no $ conflict

/* Datepicker manager. */
function Datepicker() {
	this._defaults = {
		pickerClass: '', // CSS class to add to this instance of the datepicker
		showOnFocus: true, // True for popup on focus, false for not
		showTrigger: null, // Element to be cloned for a trigger, null for none
		showAnim: 'show', // Name of jQuery animation for popup, '' for no animation
		showOptions: {}, // Options for enhanced animations
		showSpeed: 'normal', // Duration of display/closure
		popupContainer: null, // The element to which a popup calendar is added, null for body
		alignment: 'bottom', // Alignment of popup - with nominated corner of input:
			// 'top' or 'bottom' aligns depending on language direction,
			// 'topLeft', 'topRight', 'bottomLeft', 'bottomRight'
		fixedWeeks: false, // True to always show 6 weeks, false to only show as many as are needed
		firstDay: 0, // First day of the week, 0 = Sunday, 1 = Monday, ...
		calculateWeek: this.iso8601Week, // Calculate week of the year from a date, null for ISO8601
		monthsToShow: 1, // How many months to show, cols or [rows, cols]
		monthsOffset: 0, // How many months to offset the primary month by
		monthsToStep: 1, // How many months to move when prev/next clicked
		monthsToJump: 12, // How many months to move when large prev/next clicked
		useMouseWheel: true, // True to use mousewheel if available, false to never use it
		changeMonth: true, // True to change month/year via drop-down, false for navigation only
		yearRange: 'c-10:c+10', // Range of years to show in drop-down: 'any' for direct text entry
			// or 'start:end', where start/end are '+-nn' for relative to today
			// or 'c+-nn' for relative to the currently selected date
			// or 'nnnn' for an absolute year
		shortYearCutoff: '+10', // Cutoff for two-digit year in the current century
		showOtherMonths: false, // True to show dates from other months, false to not show them
		selectOtherMonths: false, // True to allow selection of dates from other months too
		defaultDate: null, // Date to show if no other selected
		selectDefaultDate: false, // True to pre-select the default date if no other is chosen
		minDate: null, // The minimum selectable date
		maxDate: null, // The maximum selectable date
		dateFormat: 'mm/dd/yyyy', // Format for dates
		autoSize: false, // True to size the input field according to the date format
		rangeSelect: false, // Allows for selecting a date range on one date picker
		rangeSeparator: ' - ', // Text between two dates in a range
		multiSelect: 0, // Maximum number of selectable dates, zero for single select
		multiSeparator: ',', // Text between multiple dates
		onDate: null, // Callback as a date is added to the datepicker
		onShow: null, // Callback just before a datepicker is shown
		onChangeMonthYear: null, // Callback when a new month/year is selected
		onSelect: null, // Callback when a date is selected
		onClose: null, // Callback when a datepicker is closed
		altField: null, // Alternate field to update in synch with the datepicker
		altFormat: null, // Date format for alternate field, defaults to dateFormat
		constrainInput: true, // True to constrain typed input to dateFormat allowed characters
		commandsAsDateFormat: false, // True to apply formatDate to the command texts
		commands: this.commands // Command actions that may be added to a layout by name
	};
	this.regional = {
		'': { // US/English
			monthNames: ['January', 'February', 'March', 'April', 'May', 'June',
			'July', 'August', 'September', 'October', 'November', 'December'],
			monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
			dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
			dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
			dayNamesMin: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
			dateFormat: 'mm/dd/yyyy', // See options on formatDate
			firstDay: 0, // The first day of the week, Sun = 0, Mon = 1, ...
			renderer: this.defaultRenderer, // The rendering templates
			prevText: '&lt;Prev', // Text for the previous month command
			prevStatus: 'Show the previous month', // Status text for the previous month command
			prevJumpText: '&lt;&lt;', // Text for the previous year command
			prevJumpStatus: 'Show the previous year', // Status text for the previous year command
			nextText: 'Next&gt;', // Text for the next month command
			nextStatus: 'Show the next month', // Status text for the next month command
			nextJumpText: '&gt;&gt;', // Text for the next year command
			nextJumpStatus: 'Show the next year', // Status text for the next year command
			currentText: 'Current', // Text for the current month command
			currentStatus: 'Show the current month', // Status text for the current month command
			todayText: 'Today', // Text for the today's month command
			todayStatus: 'Show today\'s month', // Status text for the today's month command
			clearText: 'Clear', // Text for the clear command
			clearStatus: 'Clear all the dates', // Status text for the clear command
			closeText: 'Close', // Text for the close command
			closeStatus: 'Close the datepicker', // Status text for the close command
			yearStatus: 'Change the year', // Status text for year selection
			monthStatus: 'Change the month', // Status text for month selection
			weekText: 'Wk', // Text for week of the year column header
			weekStatus: 'Week of the year', // Status text for week of the year column header
			dayStatus: 'Select DD, M d, yyyy', // Status text for selectable days
			defaultStatus: 'Select a date', // Status text shown by default
			isRTL: false // True if language is right-to-left
		}};
	$.extend(this._defaults, this.regional['']);
	this._disabled = [];
}

$.extend(Datepicker.prototype, {
	dataName: 'datepick',

	/* Class name added to elements to indicate already configured with datepicker. */
	markerClass: 'hasDatepick',

	_popupClass: 'datepick-popup', // Marker for popup division
	_triggerClass: 'datepick-trigger', // Marker for trigger element
	_disableClass: 'datepick-disable', // Marker for disabled element
	_coverClass: 'datepick-cover', // Marker for iframe backing element
	_monthYearClass: 'datepick-month-year', // Marker for month/year inputs
	_curMonthClass: 'datepick-month-', // Marker for current month/year
	_anyYearClass: 'datepick-any-year', // Marker for year direct input
	_curDoWClass: 'datepick-dow-', // Marker for day of week

	commands: { // Command actions that may be added to a layout by name
		// name: { // The command name, use '{button:name}' or '{link:name}' in layouts
		//		text: '', // The field in the regional settings for the displayed text
		//		status: '', // The field in the regional settings for the status text
		//      // The keystroke to trigger the action
		//		keystroke: {keyCode: nn, ctrlKey: boolean, altKey: boolean, shiftKey: boolean},
		//		enabled: fn, // The function that indicates the command is enabled
		//		date: fn, // The function to get the date associated with this action
		//		action: fn} // The function that implements the action
		prev: {text: 'prevText', status: 'prevStatus', // Previous month
			keystroke: {keyCode: 33}, // Page up
			enabled: function(inst) {
				var minDate = inst.curMinDate();
				return (!minDate || $.datepick.add($.datepick.day(
					$.datepick.add($.datepick.newDate(inst.drawDate),
					1 - inst.get('monthsToStep') - inst.get('monthsOffset'), 'm'), 1), -1, 'd').
					getTime() >= minDate.getTime()); },
			date: function(inst) {
				return $.datepick.day($.datepick.add($.datepick.newDate(inst.drawDate),
					-inst.get('monthsToStep') - inst.get('monthsOffset'), 'm'), 1); },
			action: function(inst) {
				$.datepick.changeMonth(this, -inst.get('monthsToStep')); }
		},
		prevJump: {text: 'prevJumpText', status: 'prevJumpStatus', // Previous year
			keystroke: {keyCode: 33, ctrlKey: true}, // Ctrl + Page up
			enabled: function(inst) {
				var minDate = inst.curMinDate();
				return (!minDate || $.datepick.add($.datepick.day(
					$.datepick.add($.datepick.newDate(inst.drawDate),
					1 - inst.get('monthsToJump') - inst.get('monthsOffset'), 'm'), 1), -1, 'd').
					getTime() >= minDate.getTime()); },
			date: function(inst) {
				return $.datepick.day($.datepick.add($.datepick.newDate(inst.drawDate),
					-inst.get('monthsToJump') - inst.get('monthsOffset'), 'm'), 1); },
			action: function(inst) {
				$.datepick.changeMonth(this, -inst.get('monthsToJump')); }
		},
		next: {text: 'nextText', status: 'nextStatus', // Next month
			keystroke: {keyCode: 34}, // Page down
			enabled: function(inst) {
				var maxDate = inst.get('maxDate');
				return (!maxDate || $.datepick.day($.datepick.add($.datepick.newDate(inst.drawDate),
					inst.get('monthsToStep') - inst.get('monthsOffset'), 'm'), 1).
					getTime() <= maxDate.getTime()); },
			date: function(inst) {
				return $.datepick.day($.datepick.add($.datepick.newDate(inst.drawDate),
					inst.get('monthsToStep') - inst.get('monthsOffset'), 'm'), 1); },
			action: function(inst) {
				$.datepick.changeMonth(this, inst.get('monthsToStep')); }
		},
		nextJump: {text: 'nextJumpText', status: 'nextJumpStatus', // Next year
			keystroke: {keyCode: 34, ctrlKey: true}, // Ctrl + Page down
			enabled: function(inst) {
				var maxDate = inst.get('maxDate');
				return (!maxDate || $.datepick.day($.datepick.add($.datepick.newDate(inst.drawDate),
					inst.get('monthsToJump') - inst.get('monthsOffset'), 'm'), 1).
					getTime() <= maxDate.getTime()); },
			date: function(inst) {
				return $.datepick.day($.datepick.add($.datepick.newDate(inst.drawDate),
					inst.get('monthsToJump') - inst.get('monthsOffset'), 'm'), 1); },
			action: function(inst) {
				$.datepick.changeMonth(this, inst.get('monthsToJump')); }
		},
		current: {text: 'currentText', status: 'currentStatus', // Current month
			keystroke: {keyCode: 36, ctrlKey: true}, // Ctrl + Home
			enabled: function(inst) {
				var minDate = inst.curMinDate();
				var maxDate = inst.get('maxDate');
				var curDate = inst.selectedDates[0] || $.datepick.today();
				return (!minDate || curDate.getTime() >= minDate.getTime()) &&
					(!maxDate || curDate.getTime() <= maxDate.getTime()); },
			date: function(inst) {
				return inst.selectedDates[0] || $.datepick.today(); },
			action: function(inst) {
				var curDate = inst.selectedDates[0] || $.datepick.today();
				$.datepick.showMonth(this, curDate.getFullYear(), curDate.getMonth() + 1); }
		},
		today: {text: 'todayText', status: 'todayStatus', // Today's month
			keystroke: {keyCode: 36, ctrlKey: true}, // Ctrl + Home
			enabled: function(inst) {
				var minDate = inst.curMinDate();
				var maxDate = inst.get('maxDate');
				return (!minDate || $.datepick.today().getTime() >= minDate.getTime()) &&
					(!maxDate || $.datepick.today().getTime() <= maxDate.getTime()); },
			date: function(inst) { return $.datepick.today(); },
			action: function(inst) { $.datepick.showMonth(this); }
		},
		clear: {text: 'clearText', status: 'clearStatus', // Clear the datepicker
			keystroke: {keyCode: 35, ctrlKey: true}, // Ctrl + End
			enabled: function(inst) { return true; },
			date: function(inst) { return null; },
			action: function(inst) { $.datepick.clear(this); }
		},
		close: {text: 'closeText', status: 'closeStatus', // Close the datepicker
			keystroke: {keyCode: 27}, // Escape
			enabled: function(inst) { return true; },
			date: function(inst) { return null; },
			action: function(inst) { $.datepick.hide(this); }
		},
		prevWeek: {text: 'prevWeekText', status: 'prevWeekStatus', // Previous week
			keystroke: {keyCode: 38, ctrlKey: true}, // Ctrl + Up
			enabled: function(inst) {
				var minDate = inst.curMinDate();
				return (!minDate || $.datepick.add($.datepick.newDate(inst.drawDate), -7, 'd').
					getTime() >= minDate.getTime()); },
			date: function(inst) { return $.datepick.add($.datepick.newDate(inst.drawDate), -7, 'd'); },
			action: function(inst) { $.datepick.changeDay(this, -7); }
		},
		prevDay: {text: 'prevDayText', status: 'prevDayStatus', // Previous day
			keystroke: {keyCode: 37, ctrlKey: true}, // Ctrl + Left
			enabled: function(inst) {
				var minDate = inst.curMinDate();
				return (!minDate || $.datepick.add($.datepick.newDate(inst.drawDate), -1, 'd').
					getTime() >= minDate.getTime()); },
			date: function(inst) { return $.datepick.add($.datepick.newDate(inst.drawDate), -1, 'd'); },
			action: function(inst) { $.datepick.changeDay(this, -1); }
		},
		nextDay: {text: 'nextDayText', status: 'nextDayStatus', // Next day
			keystroke: {keyCode: 39, ctrlKey: true}, // Ctrl + Right
			enabled: function(inst) {
				var maxDate = inst.get('maxDate');
				return (!maxDate || $.datepick.add($.datepick.newDate(inst.drawDate), 1, 'd').
					getTime() <= maxDate.getTime()); },
			date: function(inst) { return $.datepick.add($.datepick.newDate(inst.drawDate), 1, 'd'); },
			action: function(inst) { $.datepick.changeDay(this, 1); }
		},
		nextWeek: {text: 'nextWeekText', status: 'nextWeekStatus', // Next week
			keystroke: {keyCode: 40, ctrlKey: true}, // Ctrl + Down
			enabled: function(inst) {
				var maxDate = inst.get('maxDate');
				return (!maxDate || $.datepick.add($.datepick.newDate(inst.drawDate), 7, 'd').
					getTime() <= maxDate.getTime()); },
			date: function(inst) { return $.datepick.add($.datepick.newDate(inst.drawDate), 7, 'd'); },
			action: function(inst) { $.datepick.changeDay(this, 7); }
		}
	},

	/* Default template for generating a datepicker. */
	defaultRenderer: {
		// Anywhere: '{l10n:name}' to insert localised value for name,
		// '{link:name}' to insert a link trigger for command name,
		// '{button:name}' to insert a button trigger for command name,
		// '{popup:start}...{popup:end}' to mark a section for inclusion in a popup datepicker only,
		// '{inline:start}...{inline:end}' to mark a section for inclusion in an inline datepicker only
		// Overall structure: '{months}' to insert calendar months
		picker: '<div class="datepick">' +
		'<div class="datepick-nav">{link:prev}{link:today}{link:next}</div>{months}' +
		'{popup:start}<div class="datepick-ctrl">{link:clear}{link:close}</div>{popup:end}' +
		'<div class="datepick-clear-fix"></div></div>',
		// One row of months: '{months}' to insert calendar months
		monthRow: '<div class="datepick-month-row">{months}</div>',
		// A single month: '{monthHeader:dateFormat}' to insert the month header -
		// dateFormat is optional and defaults to 'MM yyyy',
		// '{weekHeader}' to insert a week header, '{weeks}' to insert the month's weeks
		month: '<div class="datepick-month"><div class="datepick-month-header">{monthHeader}</div>' +
		'<table><thead>{weekHeader}</thead><tbody>{weeks}</tbody></table></div>',
		// A week header: '{days}' to insert individual day names
		weekHeader: '<tr>{days}</tr>',
		// Individual day header: '{day}' to insert day name
		dayHeader: '<th>{day}</th>',
		// One week of the month: '{days}' to insert the week's days, '{weekOfYear}' to insert week of year
		week: '<tr>{days}</tr>',
		// An individual day: '{day}' to insert day value
		day: '<td>{day}</td>',
		// jQuery selector, relative to picker, for a single month
		monthSelector: '.datepick-month',
		// jQuery selector, relative to picker, for individual days
		daySelector: 'td',
		// Class for right-to-left (RTL) languages
		rtlClass: 'datepick-rtl',
		// Class for multi-month datepickers
		multiClass: 'datepick-multi',
		// Class for selectable dates
		defaultClass: '',
		// Class for currently selected dates
		selectedClass: 'datepick-selected',
		// Class for highlighted dates
		highlightedClass: 'datepick-highlight',
		// Class for today
		todayClass: 'datepick-today',
		// Class for days from other months
		otherMonthClass: 'datepick-other-month',
		// Class for days on weekends
		weekendClass: 'datepick-weekend',
		// Class prefix for commands
		commandClass: 'datepick-cmd',
		// Extra class(es) for commands that are buttons
		commandButtonClass: '',
		// Extra class(es) for commands that are links
		commandLinkClass: '',
		// Class for disabled commands
		disabledClass: 'datepick-disabled'
	},

	/* Override the default settings for all datepicker instances.
	   @param  settings  (object) the new settings to use as defaults
	   @return  (Datepicker) this object */
	setDefaults: function(settings) {
		$.extend(this._defaults, settings || {});
		return this;
	},

	_ticksTo1970: (((1970 - 1) * 365 + Math.floor(1970 / 4) - Math.floor(1970 / 100) +
		Math.floor(1970 / 400)) * 24 * 60 * 60 * 10000000),
	_msPerDay: 24 * 60 * 60 * 1000,

	ATOM: 'yyyy-mm-dd', // RFC 3339/ISO 8601
	COOKIE: 'D, dd M yyyy',
	FULL: 'DD, MM d, yyyy',
	ISO_8601: 'yyyy-mm-dd',
	JULIAN: 'J',
	RFC_822: 'D, d M yy',
	RFC_850: 'DD, dd-M-yy',
	RFC_1036: 'D, d M yy',
	RFC_1123: 'D, d M yyyy',
	RFC_2822: 'D, d M yyyy',
	RSS: 'D, d M yy', // RFC 822
	TICKS: '!',
	TIMESTAMP: '@',
	W3C: 'yyyy-mm-dd', // ISO 8601

	/* Format a date object into a string value.
	   The format can be combinations of the following:
	   d  - day of month (no leading zero)
	   dd - day of month (two digit)
	   o  - day of year (no leading zeros)
	   oo - day of year (three digit)
	   D  - day name short
	   DD - day name long
	   w  - week of year (no leading zero)
	   ww - week of year (two digit)
	   m  - month of year (no leading zero)
	   mm - month of year (two digit)
	   M  - month name short
	   MM - month name long
	   yy - year (two digit)
	   yyyy - year (four digit)
	   @  - Unix timestamp (s since 01/01/1970)
	   !  - Windows ticks (100ns since 01/01/0001)
	   '...' - literal text
	   '' - single quote
	   @param  format    (string) the desired format of the date (optional, default datepicker format)
	   @param  date      (Date) the date value to format
	   @param  settings  (object) attributes include:
	                     dayNamesShort    (string[]) abbreviated names of the days from Sunday (optional)
	                     dayNames         (string[]) names of the days from Sunday (optional)
	                     monthNamesShort  (string[]) abbreviated names of the months (optional)
	                     monthNames       (string[]) names of the months (optional)
						 calculateWeek    (function) function that determines week of the year (optional)
	   @return  (string) the date in the above format */
	formatDate: function(format, date, settings) {
		if (typeof format != 'string') {
			settings = date;
			date = format;
			format = '';
		}
		if (!date) {
			return '';
		}
		format = format || this._defaults.dateFormat;
		settings = settings || {};
		var dayNamesShort = settings.dayNamesShort || this._defaults.dayNamesShort;
		var dayNames = settings.dayNames || this._defaults.dayNames;
		var monthNamesShort = settings.monthNamesShort || this._defaults.monthNamesShort;
		var monthNames = settings.monthNames || this._defaults.monthNames;
		var calculateWeek = settings.calculateWeek || this._defaults.calculateWeek;
		// Check whether a format character is doubled
		var doubled = function(match, step) {
			var matches = 1;
			while (iFormat + matches < format.length && format.charAt(iFormat + matches) == match) {
				matches++;
			}
			iFormat += matches - 1;
			return Math.floor(matches / (step || 1)) > 1;
		};
		// Format a number, with leading zeroes if necessary
		var formatNumber = function(match, value, len, step) {
			var num = '' + value;
			if (doubled(match, step)) {
				while (num.length < len) {
					num = '0' + num;
				}
			}
			return num;
		};
		// Format a name, short or long as requested
		var formatName = function(match, value, shortNames, longNames) {
			return (doubled(match) ? longNames[value] : shortNames[value]);
		};
		var output = '';
		var literal = false;
		for (var iFormat = 0; iFormat < format.length; iFormat++) {
			if (literal) {
				if (format.charAt(iFormat) == "'" && !doubled("'")) {
					literal = false;
				}
				else {
					output += format.charAt(iFormat);
				}
			}
			else {
				switch (format.charAt(iFormat)) {
					case 'd': output += formatNumber('d', date.getDate(), 2); break;
					case 'D': output += formatName('D', date.getDay(),
						dayNamesShort, dayNames); break;
					case 'o': output += formatNumber('o', this.dayOfYear(date), 3); break;
					case 'w': output += formatNumber('w', calculateWeek(date), 2); break;
					case 'm': output += formatNumber('m', date.getMonth() + 1, 2); break;
					case 'M': output += formatName('M', date.getMonth(),
						monthNamesShort, monthNames); break;
					case 'y':
						output += (doubled('y', 2) ? date.getFullYear() :
							(date.getFullYear() % 100 < 10 ? '0' : '') + date.getFullYear() % 100);
						break;
					case '@': output += Math.floor(date.getTime() / 1000); break;
					case '!': output += date.getTime() * 10000 + this._ticksTo1970; break;
					case "'":
						if (doubled("'")) {
							output += "'";
						}
						else {
							literal = true;
						}
						break;
					default:
						output += format.charAt(iFormat);
				}
			}
		}
		return output;
	},

	/* Parse a string value into a date object.
	   See formatDate for the possible formats, plus:
	   * - ignore rest of string
	   @param  format    (string) the expected format of the date ('' for default datepicker format)
	   @param  value     (string) the date in the above format
	   @param  settings  (object) attributes include:
	                     shortYearCutoff  (number) the cutoff year for determining the century (optional)
	                     dayNamesShort    (string[]) abbreviated names of the days from Sunday (optional)
	                     dayNames         (string[]) names of the days from Sunday (optional)
	                     monthNamesShort  (string[]) abbreviated names of the months (optional)
	                     monthNames       (string[]) names of the months (optional)
	   @return  (Date) the extracted date value or null if value is blank
	   @throws  errors if the format and/or value are missing,
	            if the value doesn't match the format,
	            or if the date is invalid */
	parseDate: function(format, value, settings) {
		if (value == null) {
			throw 'Invalid arguments';
		}
		value = (typeof value == 'object' ? value.toString() : value + '');
		if (value == '') {
			return null;
		}
		format = format || this._defaults.dateFormat;
		settings = settings || {};
		var shortYearCutoff = settings.shortYearCutoff || this._defaults.shortYearCutoff;
		shortYearCutoff = (typeof shortYearCutoff != 'string' ? shortYearCutoff :
			this.today().getFullYear() % 100 + parseInt(shortYearCutoff, 10));
		var dayNamesShort = settings.dayNamesShort || this._defaults.dayNamesShort;
		var dayNames = settings.dayNames || this._defaults.dayNames;
		var monthNamesShort = settings.monthNamesShort || this._defaults.monthNamesShort;
		var monthNames = settings.monthNames || this._defaults.monthNames;
		var year = -1;
		var month = -1;
		var day = -1;
		var doy = -1;
		var shortYear = false;
		var literal = false;
		// Check whether a format character is doubled
		var doubled = function(match, step) {
			var matches = 1;
			while (iFormat + matches < format.length && format.charAt(iFormat + matches) == match) {
				matches++;
			}
			iFormat += matches - 1;
			return Math.floor(matches / (step || 1)) > 1;
		};
		// Extract a number from the string value
		var getNumber = function(match, step) {
			doubled(match, step);
			var size = [2, 3, 4, 11, 20]['oy@!'.indexOf(match) + 1];
			var digits = new RegExp('^-?\\d{1,' + size + '}');
			var num = value.substring(iValue).match(digits);
			if (!num) {
				throw 'Missing number at position {0}'.replace(/\{0\}/, iValue);
			}
			iValue += num[0].length;
			return parseInt(num[0], 10);
		};
		// Extract a name from the string value and convert to an index
		var getName = function(match, shortNames, longNames, step) {
			var names = (doubled(match, step) ? longNames : shortNames);
			for (var i = 0; i < names.length; i++) {
				if (value.substr(iValue, names[i].length) == names[i]) {
					iValue += names[i].length;
					return i + 1;
				}
			}
			throw 'Unknown name at position {0}'.replace(/\{0\}/, iValue);
		};
		// Confirm that a literal character matches the string value
		var checkLiteral = function() {
			if (value.charAt(iValue) != format.charAt(iFormat)) {
				throw 'Unexpected literal at position {0}'.replace(/\{0\}/, iValue);
			}
			iValue++;
		};
		var iValue = 0;
		for (var iFormat = 0; iFormat < format.length; iFormat++) {
			if (literal) {
				if (format.charAt(iFormat) == "'" && !doubled("'")) {
					literal = false;
				}
				else {
					checkLiteral();
				}
			}
			else {
				switch (format.charAt(iFormat)) {
					case 'd': day = getNumber('d'); break;
					case 'D': getName('D', dayNamesShort, dayNames); break;
					case 'o': doy = getNumber('o'); break;
					case 'w': getNumber('w'); break;
					case 'm': month = getNumber('m'); break;
					case 'M': month = getName('M', monthNamesShort, monthNames); break;
					case 'y':
						var iSave = iFormat;
						shortYear = !doubled('y', 2);
						iFormat = iSave;
						year = getNumber('y', 2);
						break;
					case '@':
						var date = this._normaliseDate(new Date(getNumber('@') * 1000));
						year = date.getFullYear();
						month = date.getMonth() + 1;
						day = date.getDate();
						break;
					case '!':
						var date = this._normaliseDate(
							new Date((getNumber('!') - this._ticksTo1970) / 10000));
						year = date.getFullYear();
						month = date.getMonth() + 1;
						day = date.getDate();
						break;
					case '*': iValue = value.length; break;
					case "'":
						if (doubled("'")) {
							checkLiteral();
						}
						else {
							literal = true;
						}
						break;
					default: checkLiteral();
				}
			}
		}
		if (iValue < value.length) {
			throw 'Additional text found at end';
		}
		if (year == -1) {
			year = this.today().getFullYear();
		}
		else if (year < 100 && shortYear) {
			year += (shortYearCutoff == -1 ? 1900 : this.today().getFullYear() -
				this.today().getFullYear() % 100 - (year <= shortYearCutoff ? 0 : 100));
		}
		if (doy > -1) {
			month = 1;
			day = doy;
			for (var dim = this.daysInMonth(year, month); day > dim;
					dim = this.daysInMonth(year, month)) {
				month++;
				day -= dim;
			}
		}
		var date = this.newDate(year, month, day);
		if (date.getFullYear() != year || date.getMonth() + 1 != month || date.getDate() != day) {
			throw 'Invalid date';
		}
		return date;
	},

	/* A date may be specified as an exact value or a relative one.
	   @param  dateSpec     (Date or number or string) the date as an object or string
	                        in the given format or an offset - numeric days from today,
	                        or string amounts and periods, e.g. '+1m +2w'
	   @param  defaultDate  (Date) the date to use if no other supplied, may be null
	   @param  currentDate  (Date) the current date as a possible basis for relative dates,
	                        if null today is used (optional)
	   @param  dateFormat   (string) the expected date format - see formatDate above (optional)
	   @param  settings     (object) attributes include:
	                        shortYearCutoff  (number) the cutoff year for determining the century (optional)
	                        dayNamesShort    (string[7]) abbreviated names of the days from Sunday (optional)
	                        dayNames         (string[7]) names of the days from Sunday (optional)
	                        monthNamesShort  (string[12]) abbreviated names of the months (optional)
	                        monthNames       (string[12]) names of the months (optional)
	   @return  (Date) the decoded date */
	determineDate: function(dateSpec, defaultDate, currentDate, dateFormat, settings) {
		if (currentDate && typeof currentDate != 'object') {
			settings = dateFormat;
			dateFormat = currentDate;
			currentDate = null;
		}
		if (typeof dateFormat != 'string') {
			settings = dateFormat;
			dateFormat = '';
		}
		var offsetString = function(offset) {
			try {
				return $.datepick.parseDate(dateFormat, offset, settings);
			}
			catch (e) {
				// Ignore
			}
			offset = offset.toLowerCase();
			var date = (offset.match(/^c/) && currentDate ? $.datepick.newDate(currentDate) : null) ||
				$.datepick.today();
			var pattern = /([+-]?[0-9]+)\s*(d|w|m|y)?/g;
			var matches = pattern.exec(offset);
			while (matches) {
				date = $.datepick.add(date, parseInt(matches[1], 10), matches[2] || 'd');
				matches = pattern.exec(offset);
			}
			return date;
		};
		defaultDate = (defaultDate ? $.datepick.newDate(defaultDate) : null);
		dateSpec = (dateSpec == null ? defaultDate :
			(typeof dateSpec == 'string' ? offsetString(dateSpec) : (typeof dateSpec == 'number' ?
			(isNaN(dateSpec) || dateSpec == Infinity || dateSpec == -Infinity ? defaultDate :
			$.datepick.add($.datepick.today(), dateSpec, 'd')) : $.datepick._normaliseDate(dateSpec))));
		return dateSpec;
	},

	/* Find the number of days in a given month.
	   @param  year   (Date) the date to get days for or
	                  (number) the full year
	   @param  month  (number) the month (1 to 12)
	   @return  (number) the number of days in this month */
	daysInMonth: function(year, month) {
		month = (year.getFullYear ? year.getMonth() + 1 : month);
		year = (year.getFullYear ? year.getFullYear() : year);
		return this.newDate(year, month + 1, 0).getDate();
	},

	/* Calculate the day of the year for a date.
	   @param  year   (Date) the date to get the day-of-year for or
	                  (number) the full year
	   @param  month  (number) the month (1-12)
	   @param  day    (number) the day
	   @return  (number) the day of the year */
	dayOfYear: function(year, month, day) {
		var date = (year.getFullYear ? year : this.newDate(year, month, day));
		var newYear = this.newDate(date.getFullYear(), 1, 1);
		return Math.floor((date.getTime() - newYear.getTime()) / this._msPerDay) + 1;
	},

	/* Set as calculateWeek to determine the week of the year based on the ISO 8601 definition.
	   @param  year   (Date) the date to get the week for or
	                  (number) the full year
	   @param  month  (number) the month (1-12)
	   @param  day    (number) the day
	   @return  (number) the number of the week within the year that contains this date */
	iso8601Week: function(year, month, day) {
		var checkDate = (year.getFullYear ?
			new Date(year.getTime()) : this.newDate(year, month, day));
		// Find Thursday of this week starting on Monday
		checkDate.setDate(checkDate.getDate() + 4 - (checkDate.getDay() || 7));
		var time = checkDate.getTime();
		checkDate.setMonth(0, 1); // Compare with Jan 1
		return Math.floor(Math.round((time - checkDate) / 86400000) / 7) + 1;
	},

	/* Return today's date.
	   @return  (Date) today */
	today: function() {
		return this._normaliseDate(new Date());
	},

	/* Return a new date.
	   @param  year   (Date) the date to clone or
					  (number) the year
	   @param  month  (number) the month (1-12)
	   @param  day    (number) the day
	   @return  (Date) the date */
	newDate: function(year, month, day) {
		return (!year ? null : (year.getFullYear ? this._normaliseDate(new Date(year.getTime())) :
			new Date(year, month - 1, day, 12)));
	},

	/* Standardise a date into a common format - time portion is 12 noon.
	   @param  date  (Date) the date to standardise
	   @return  (Date) the normalised date */
	_normaliseDate: function(date) {
		if (date) {
			date.setHours(12, 0, 0, 0);
		}
		return date;
	},

	/* Set the year for a date.
	   @param  date  (Date) the original date
	   @param  year  (number) the new year
	   @return  the updated date */
	year: function(date, year) {
		date.setFullYear(year);
		return this._normaliseDate(date);
	},

	/* Set the month for a date.
	   @param  date   (Date) the original date
	   @param  month  (number) the new month (1-12)
	   @return  the updated date */
	month: function(date, month) {
		date.setMonth(month - 1);
		return this._normaliseDate(date);
	},

	/* Set the day for a date.
	   @param  date  (Date) the original date
	   @param  day   (number) the new day of the month
	   @return  the updated date */
	day: function(date, day) {
		date.setDate(day);
		return this._normaliseDate(date);
	},

	/* Add a number of periods to a date.
	   @param  date    (Date) the original date
	   @param  amount  (number) the number of periods
	   @param  period  (string) the type of period d/w/m/y
	   @return  the updated date */
	add: function(date, amount, period) {
		if (period == 'd' || period == 'w') {
			this._normaliseDate(date);
			date.setDate(date.getDate() + amount * (period == 'w' ? 7 : 1));
		}
		else {
			var year = date.getFullYear() + (period == 'y' ? amount : 0);
			var month = date.getMonth() + (period == 'm' ? amount : 0);
			date.setTime($.datepick.newDate(year, month + 1,
				Math.min(date.getDate(), this.daysInMonth(year, month + 1))).getTime());
		}
		return date;
	},

	/* Attach the datepicker functionality to an input field.
	   @param  target    (element) the control to affect
	   @param  settings  (object) the custom options for this instance */
	_attachPicker: function(target, settings) {
		target = $(target);
		if (target.hasClass(this.markerClass)) {
			return;
		}
		target.addClass(this.markerClass);
		var inst = {target: target, selectedDates: [], drawDate: null, pickingRange: false,
			inline: ($.inArray(target[0].nodeName.toLowerCase(), ['div', 'span']) > -1),
			get: function(name) { // Get a setting value, defaulting if necessary
				var value = this.settings[name] !== undefined ?
					this.settings[name] : $.datepick._defaults[name];
				if ($.inArray(name, ['defaultDate', 'minDate', 'maxDate']) > -1) { // Decode date settings
					value = $.datepick.determineDate(
						value, null, this.selectedDates[0], this.get('dateFormat'), inst.getConfig());
				}
				return value;
			},
			curMinDate: function() {
				return (this.pickingRange ? this.selectedDates[0] : this.get('minDate'));
			},
			getConfig: function() {
				return {dayNamesShort: this.get('dayNamesShort'), dayNames: this.get('dayNames'),
					monthNamesShort: this.get('monthNamesShort'), monthNames: this.get('monthNames'),
					calculateWeek: this.get('calculateWeek'),
					shortYearCutoff: this.get('shortYearCutoff')};
			}
		};
		$.data(target[0], this.dataName, inst);
		var inlineSettings = ($.fn.metadata ? target.metadata() : {});
		inst.settings = $.extend({}, settings || {}, inlineSettings || {});
		if (inst.inline) {
			this._update(target[0]);
			if ($.fn.mousewheel) {
				target.mousewheel(this._doMouseWheel);
			}
		}
		else {
			this._attachments(target, inst);
			target.bind('keydown.' + this.dataName, this._keyDown).
				bind('keypress.' + this.dataName, this._keyPress).
				bind('keyup.' + this.dataName, this._keyUp);
			if (target.attr('disabled')) {
				this.disable(target[0]);
			}
		}
	},

	/* Retrieve the settings for a datepicker control.
	   @param  target  (element) the control to affect
	   @param  name    (string) the name of the setting (optional)
	   @return  (object) the current instance settings (name == 'all') or
	            (object) the default settings (no name) or
	            (any) the setting value (name supplied) */
	options: function(target, name) {
		var inst = $.data(target, this.dataName);
		return (inst ? (name ? (name == 'all' ?
			inst.settings : inst.settings[name]) : $.datepick._defaults) : {});
	},

	/* Reconfigure the settings for a datepicker control.
	   @param  target    (element) the control to affect
	   @param  settings  (object) the new options for this instance or
	                     (string) an individual property name
	   @param  value     (any) the individual property value (omit if settings is an object) */
	option: function(target, settings, value) {
		target = $(target);
		if (!target.hasClass(this.markerClass)) {
			return;
		}
		settings = settings || {};
		if (typeof settings == 'string') {
			var name = settings;
			settings = {};
			settings[name] = value;
		}
		var inst = $.data(target[0], this.dataName);
		var dates = inst.selectedDates;
		extendRemove(inst.settings, settings);
		this.setDate(target[0], dates, null, false, true);
		inst.pickingRange = false;
		inst.drawDate = $.datepick.newDate(this._checkMinMax(
			(settings.defaultDate ? inst.get('defaultDate') : inst.drawDate) ||
			inst.get('defaultDate') || $.datepick.today(), inst));
		if (!inst.inline) {
			this._attachments(target, inst);
		}
		if (inst.inline || inst.div) {
			this._update(target[0]);
		}
	},

	/* Attach events and trigger, if necessary.
	   @param  target  (jQuery) the control to affect
	   @param  inst    (object) the current instance settings */
	_attachments: function(target, inst) {
		target.unbind('focus.' + this.dataName);
		if (inst.get('showOnFocus')) {
			target.bind('focus.' + this.dataName, this.show);
		}
		if (inst.trigger) {
			inst.trigger.remove();
		}
		var trigger = inst.get('showTrigger');
		inst.trigger = (!trigger ? $([]) :
			$(trigger).clone().removeAttr('id').addClass(this._triggerClass)
				[inst.get('isRTL') ? 'insertBefore' : 'insertAfter'](target).
				click(function() {
					if (!$.datepick.isDisabled(target[0])) {
						$.datepick[$.datepick.curInst == inst ?
							'hide' : 'show'](target[0]);
					}
				}));
		this._autoSize(target, inst);
		var dates = this._extractDates(inst, target.val());
		if (dates) {
			this.setDate(target[0], dates, null, true);
		}
		if (inst.get('selectDefaultDate') && inst.get('defaultDate') &&
				inst.selectedDates.length == 0) {
			this.setDate(target[0], $.datepick.newDate(inst.get('defaultDate') || $.datepick.today()));
		}
	},

	/* Apply the maximum length for the date format.
	   @param  inst  (object) the current instance settings */
	_autoSize: function(target, inst) {
		if (inst.get('autoSize') && !inst.inline) {
			var date = $.datepick.newDate(2009, 10, 20); // Ensure double digits
			var dateFormat = inst.get('dateFormat');
			if (dateFormat.match(/[DM]/)) {
				var findMax = function(names) {
					var max = 0;
					var maxI = 0;
					for (var i = 0; i < names.length; i++) {
						if (names[i].length > max) {
							max = names[i].length;
							maxI = i;
						}
					}
					return maxI;
				};
				date.setMonth(findMax(inst.get(dateFormat.match(/MM/) ? // Longest month
					'monthNames' : 'monthNamesShort')));
				date.setDate(findMax(inst.get(dateFormat.match(/DD/) ? // Longest day
					'dayNames' : 'dayNamesShort')) + 20 - date.getDay());
			}
			inst.target.attr('size', $.datepick.formatDate(dateFormat, date, inst.getConfig()).length);
		}
	},

	/* Remove the datepicker functionality from a control.
	   @param  target  (element) the control to affect */
	destroy: function(target) {
		target = $(target);
		if (!target.hasClass(this.markerClass)) {
			return;
		}
		var inst = $.data(target[0], this.dataName);
		if (inst.trigger) {
			inst.trigger.remove();
		}
		target.removeClass(this.markerClass).empty().unbind('.' + this.dataName);
		if (inst.inline && $.fn.mousewheel) {
			target.unmousewheel();
		}
		if (!inst.inline && inst.get('autoSize')) {
			target.removeAttr('size');
		}
		$.removeData(target[0], this.dataName);
	},

	/* Apply multiple event functions.
	   Usage, for example: onShow: multipleEvents(fn1, fn2, ...)
	   @param  fns  (function...) the functions to apply */
	multipleEvents: function(fns) {
		var funcs = arguments;
		return function(args) {
			for (var i = 0; i < funcs.length; i++) {
				funcs[i].apply(this, arguments);
			}
		};
	},

	/* Enable the datepicker and any associated trigger.
	   @param  target  (element) the control to use */
	enable: function(target) {
		var $target = $(target);
		if (!$target.hasClass(this.markerClass)) {
			return;
		}
		var inst = $.data(target, this.dataName);
		if (inst.inline)
			$target.children('.' + this._disableClass).remove().end().
				find('button,select').attr('disabled', '').end().
				find('a').attr('href', 'javascript:void(0)');
		else {
			target.disabled = false;
			inst.trigger.filter('button.' + this._triggerClass).
				attr('disabled', '').end().
				filter('img.' + this._triggerClass).
				css({opacity: '1.0', cursor: ''});
		}
		this._disabled = $.map(this._disabled,
			function(value) { return (value == target ? null : value); }); // Delete entry
	},

	/* Disable the datepicker and any associated trigger.
	   @param  target  (element) the control to use */
	disable: function(target) {
		var $target = $(target);
		if (!$target.hasClass(this.markerClass))
			return;
		var inst = $.data(target, this.dataName);
		if (inst.inline) {
			var inline = $target.children(':last');
			var offset = inline.offset();
			var relOffset = {left: 0, top: 0};
			inline.parents().each(function() {
				if ($(this).css('position') == 'relative') {
					relOffset = $(this).offset();
					return false;
				}
			});
			var zIndex = $target.css('zIndex');
			zIndex = (zIndex == 'auto' ? 0 : parseInt(zIndex, 10)) + 1;
			$target.prepend('<div class="' + this._disableClass + '" style="' +
				'width: ' + inline.outerWidth() + 'px; height: ' + inline.outerHeight() +
				'px; left: ' + (offset.left - relOffset.left) + 'px; top: ' +
				(offset.top - relOffset.top) + 'px; z-index: ' + zIndex + '"></div>').
				find('button,select').attr('disabled', 'disabled').end().
				find('a').removeAttr('href');
		}
		else {
			target.disabled = true;
			inst.trigger.filter('button.' + this._triggerClass).
				attr('disabled', 'disabled').end().
				filter('img.' + this._triggerClass).
				css({opacity: '0.5', cursor: 'default'});
		}
		this._disabled = $.map(this._disabled,
			function(value) { return (value == target ? null : value); }); // Delete entry
		this._disabled.push(target);
	},

	/* Is the first field in a jQuery collection disabled as a datepicker?
	   @param  target  (element) the control to examine
	   @return  (boolean) true if disabled, false if enabled */
	isDisabled: function(target) {
		return (target && $.inArray(target, this._disabled) > -1);
	},

	/* Show a popup datepicker.
	   @param  target  (event) a focus event or
	                   (element) the control to use */
	show: function(target) {
		target = target.target || target;
		var inst = $.data(target, $.datepick.dataName);
		if ($.datepick.curInst == inst) {
			return;
		}
		if ($.datepick.curInst) {
			$.datepick.hide($.datepick.curInst, true);
		}
		if (inst) {
			// Retrieve existing date(s)
			inst.lastVal = null;
			inst.selectedDates = $.datepick._extractDates(inst, $(target).val());
			inst.pickingRange = false;
			inst.drawDate = $.datepick._checkMinMax($.datepick.newDate(inst.selectedDates[0] ||
				inst.get('defaultDate') || $.datepick.today()), inst);
			inst.prevDate = $.datepick.newDate(inst.drawDate);
			$.datepick.curInst = inst;
			// Generate content
			$.datepick._update(target, true);
			// Adjust position before showing
			var offset = $.datepick._checkOffset(inst);
			inst.div.css({left: offset.left, top: offset.top});
			// And display
			var showAnim = inst.get('showAnim');
			var showSpeed = inst.get('showSpeed');
			showSpeed = (showSpeed == 'normal' && $.ui && $.ui.version >= '1.8' ?
				'_default' : showSpeed);
			var postProcess = function() {
				var borders = $.datepick._getBorders(inst.div);
				inst.div.find('.' + $.datepick._coverClass). // IE6- only
					css({left: -borders[0], top: -borders[1],
						width: inst.div.outerWidth() + borders[0],
						height: inst.div.outerHeight() + borders[1]});
			};
			if ($.effects && $.effects[showAnim]) {
				var data = inst.div.data(); // Update old effects data
				for (var key in data) {
					if (key.match(/^ec\.storage\./)) {
						data[key] = inst._mainDiv.css(key.replace(/ec\.storage\./, ''));
					}
				}
				inst.div.data(data).show(showAnim, inst.get('showOptions'), showSpeed, postProcess);
			}
			else {
				inst.div[showAnim || 'show']((showAnim ? showSpeed : ''), postProcess);
			}
			if (!showAnim) {
				postProcess();
			}
		}
	},

	/* Extract possible dates from a string.
	   @param  inst  (object) the current instance settings
	   @param  text  (string) the text to extract from
	   @return  (CDate[]) the extracted dates */
	_extractDates: function(inst, datesText) {
		if (datesText == inst.lastVal) {
			return null;
		}
		inst.lastVal = datesText;
		var dateFormat = inst.get('dateFormat');
		var multiSelect = inst.get('multiSelect');
		var rangeSelect = inst.get('rangeSelect');
		datesText = datesText.split(multiSelect ? inst.get('multiSeparator') :
			(rangeSelect ? inst.get('rangeSeparator') : '\x00'));
		var dates = [];
		for (var i = 0; i < datesText.length; i++) {
			try {
				var date = $.datepick.parseDate(dateFormat, datesText[i], inst.getConfig());
				if (date) {
					var found = false;
					for (var j = 0; j < dates.length; j++) {
						if (dates[j].getTime() == date.getTime()) {
							found = true;
							break;
						}
					}
					if (!found) {
						dates.push(date);
					}
				}
			}
			catch (e) {
				// Ignore
			}
		}
		dates.splice(multiSelect || (rangeSelect ? 2 : 1), dates.length);
		if (rangeSelect && dates.length == 1) {
			dates[1] = dates[0];
		}
		return dates;
	},

	/* Update the datepicker display.
	   @param  target  (event) a focus event or
	                   (element) the control to use
	   @param  hidden  (boolean) true to initially hide the datepicker */
	_update: function(target, hidden) {
		target = $(target.target || target);
		var inst = $.data(target[0], $.datepick.dataName);
		if (inst) {
			if (inst.inline || $.datepick.curInst == inst) {
				var onChange = inst.get('onChangeMonthYear');
				if (onChange && (!inst.prevDate ||
						inst.prevDate.getFullYear() != inst.drawDate.getFullYear() ||
						inst.prevDate.getMonth() != inst.drawDate.getMonth())) {
					onChange.apply(target[0], [inst.drawDate.getFullYear(), inst.drawDate.getMonth() + 1]);
				}
			}
			if (inst.inline) {
				target.html(this._generateContent(target[0], inst));
			}
			else if ($.datepick.curInst == inst) {
				if (!inst.div) {
					inst.div = $('<div></div>').addClass(this._popupClass).
						css({display: (hidden ? 'none' : 'static'), position: 'absolute',
							left: target.offset().left,
							top: target.offset().top + target.outerHeight()}).
						appendTo($(inst.get('popupContainer') || 'body'));
					if ($.fn.mousewheel) {
						inst.div.mousewheel(this._doMouseWheel);
					}
				}
				inst.div.html(this._generateContent(target[0], inst));
				target.focus();
			}
		}
	},

	/* Update the input field and any alternate field with the current dates.
	   @param  target  (element) the control to use
	   @param  keyUp   (boolean, internal) true if coming from keyUp processing */
	_updateInput: function(target, keyUp) {
		var inst = $.data(target, this.dataName);
		if (inst) {
			var value = '';
			var altValue = '';
			var sep = (inst.get('multiSelect') ? inst.get('multiSeparator') :
				inst.get('rangeSeparator'));
			var dateFormat = inst.get('dateFormat');
			var altFormat = inst.get('altFormat') || dateFormat;
			for (var i = 0; i < inst.selectedDates.length; i++) {
				value += (keyUp ? '' : (i > 0 ? sep : '') + $.datepick.formatDate(
					dateFormat, inst.selectedDates[i], inst.getConfig()));
				altValue += (i > 0 ? sep : '') + $.datepick.formatDate(
					altFormat, inst.selectedDates[i], inst.getConfig());
			}
			if (!inst.inline && !keyUp) {
				$(target).val(value);
			}
			$(inst.get('altField')).val(altValue);

			var onSelect = inst.get('onSelect');
			if (onSelect && !keyUp && !inst.inSelect) {
				inst.inSelect = true; // Prevent endless loops
				onSelect.apply(target, [inst.selectedDates]);
				inst.inSelect = false;
			}
		}
	},

	/* Retrieve the size of left and top borders for an element.
	   @param  elem  (jQuery) the element of interest
	   @return  (number[2]) the left and top borders */
	_getBorders: function(elem) {
		var convert = function(value) {
			var extra = ($.browser.msie ? 1 : 0);
			return {thin: 1 + extra, medium: 3 + extra, thick: 5 + extra}[value] || value;
		};
		return [parseFloat(convert(elem.css('border-left-width'))),
			parseFloat(convert(elem.css('border-top-width')))];
	},

	/* Check positioning to remain on the screen.
	   @param  inst  (object) the current instance settings
	   @return  (object) the updated offset for the datepicker */
	_checkOffset: function(inst) {
		var base = (inst.target.is(':hidden') && inst.trigger ? inst.trigger : inst.target);
		var offset = base.offset();
		var isFixed = false;
		$(inst.target).parents().each(function() {
			isFixed |= $(this).css('position') == 'fixed';
			return !isFixed;
		});
		if (isFixed && $.browser.opera) { // Correction for Opera when fixed and scrolled
			offset.left -= document.documentElement.scrollLeft;
			offset.top -= document.documentElement.scrollTop;
		}
		var browserWidth = (!$.browser.mozilla || document.doctype ?
			document.documentElement.clientWidth : 0) || document.body.clientWidth;
		var browserHeight = (!$.browser.mozilla || document.doctype ?
			document.documentElement.clientHeight : 0) || document.body.clientHeight;
		if (browserWidth == 0) {
			return offset;
		}
		var alignment = inst.get('alignment');
		var isRTL = inst.get('isRTL');
		var scrollX = document.documentElement.scrollLeft || document.body.scrollLeft;
		var scrollY = document.documentElement.scrollTop || document.body.scrollTop;
		var above = offset.top - inst.div.outerHeight() -
			(isFixed && $.browser.opera ? document.documentElement.scrollTop : 0);
		var below = offset.top + base.outerHeight();
		var alignL = offset.left;
		var alignR = offset.left + base.outerWidth() - inst.div.outerWidth() -
			(isFixed && $.browser.opera ? document.documentElement.scrollLeft : 0);
		var tooWide = (offset.left + inst.div.outerWidth() - scrollX) > browserWidth;
		var tooHigh = (offset.top + inst.target.outerHeight() + inst.div.outerHeight() -
			scrollY) > browserHeight;
		if (alignment == 'topLeft') {
			offset = {left: alignL, top: above};
		}
		else if (alignment == 'topRight') {
			offset = {left: alignR, top: above};
		}
		else if (alignment == 'bottomLeft') {
			offset = {left: alignL, top: below};
		}
		else if (alignment == 'bottomRight') {
			offset = {left: alignR, top: below};
		}
		else if (alignment == 'top') {
			offset = {left: (isRTL || tooWide ? alignR : alignL), top: above};
		}
		else { // bottom
			offset = {left: (isRTL || tooWide ? alignR : alignL),
				top: (tooHigh ? above : below)};
		}
		offset.left = Math.max((isFixed ? 0 : scrollX), offset.left - (isFixed ? scrollX : 0));
		offset.top = Math.max((isFixed ? 0 : scrollY), offset.top - (isFixed ? scrollY : 0));
		return offset;
	},

	/* Close date picker if clicked elsewhere.
	   @param  event  (MouseEvent) the mouse click to check */
	_checkExternalClick: function(event) {
		if (!$.datepick.curInst) {
			return;
		}
		var target = $(event.target);
		if (!target.parents().andSelf().hasClass($.datepick._popupClass) &&
				!target.hasClass($.datepick.markerClass) &&
				!target.parents().andSelf().hasClass($.datepick._triggerClass)) {
			$.datepick.hide($.datepick.curInst);
		}
	},

	/* Hide a popup datepicker.
	   @param  target     (element) the control to use or
	                      (object) the current instance settings
	   @param  immediate  (boolean) true to close immediately without animation */
	hide: function(target, immediate) {
		var inst = $.data(target, this.dataName) || target;
		if (inst && inst == $.datepick.curInst) {
			var showAnim = (immediate ? '' : inst.get('showAnim'));
			var showSpeed = inst.get('showSpeed');
			showSpeed = (showSpeed == 'normal' && $.ui && $.ui.version >= '1.8' ?
				'_default' : showSpeed);
			var postProcess = function() {
				inst.div.remove();
				inst.div = null;
				$.datepick.curInst = null;
				var onClose = inst.get('onClose');
				if (onClose) {
					onClose.apply(target, [inst.selectedDates]);
				}
			};
			inst.div.stop();
			if ($.effects && $.effects[showAnim]) {
				inst.div.hide(showAnim, inst.get('showOptions'), showSpeed, postProcess);
			}
			else {
				var hideAnim = (showAnim == 'slideDown' ? 'slideUp' :
					(showAnim == 'fadeIn' ? 'fadeOut' : 'hide'));
				inst.div[hideAnim]((showAnim ? showSpeed : ''), postProcess);
			}
			if (!showAnim) {
				postProcess();
			}
		}
	},

	/* Handle keystrokes in the datepicker.
	   @param  event  (KeyEvent) the keystroke
	   @return  (boolean) true if not handled, false if handled */
	_keyDown: function(event) {
		var target = event.target;
		var inst = $.data(target, $.datepick.dataName);
		var handled = false;
		if (inst.div) {
			if (event.keyCode == 9) { // Tab - close
				$.datepick.hide(target);
			}
			else if (event.keyCode == 13) { // Enter - select
				$.datepick.selectDate(target,
					$('a.' + inst.get('renderer').highlightedClass, inst.div)[0]);
				handled = true;
			}
			else { // Command keystrokes
				var commands = inst.get('commands');
				for (var name in commands) {
					var command = commands[name];
					if (command.keystroke.keyCode == event.keyCode &&
							!!command.keystroke.ctrlKey == !!(event.ctrlKey || event.metaKey) &&
							!!command.keystroke.altKey == event.altKey &&
							!!command.keystroke.shiftKey == event.shiftKey) {
						$.datepick.performAction(target, name);
						handled = true;
						break;
					}
				}
			}
		}
		else { // Show on 'current' keystroke
			var command = inst.get('commands').current;
			if (command.keystroke.keyCode == event.keyCode &&
					!!command.keystroke.ctrlKey == !!(event.ctrlKey || event.metaKey) &&
					!!command.keystroke.altKey == event.altKey &&
					!!command.keystroke.shiftKey == event.shiftKey) {
				$.datepick.show(target);
				handled = true;
			}
		}
		inst.ctrlKey = ((event.keyCode < 48 && event.keyCode != 32) ||
			event.ctrlKey || event.metaKey);
		if (handled) {
			event.preventDefault();
			event.stopPropagation();
		}
		return !handled;
	},

	/* Filter keystrokes in the datepicker.
	   @param  event  (KeyEvent) the keystroke
	   @return  (boolean) true if allowed, false if not allowed */
	_keyPress: function(event) {
		var target = event.target;
		var inst = $.data(target, $.datepick.dataName);
		if (inst && inst.get('constrainInput')) {
			var ch = String.fromCharCode(event.keyCode || event.charCode);
			var allowedChars = $.datepick._allowedChars(inst);
			return (event.metaKey || inst.ctrlKey || ch < ' ' ||
				!allowedChars || allowedChars.indexOf(ch) > -1);
		}
		return true;
	},

	/* Determine the set of characters allowed by the date format.
	   @param  inst  (object) the current instance settings
	   @return  (string) the set of allowed characters, or null if anything allowed */
	_allowedChars: function(inst) {
		var dateFormat = inst.get('dateFormat');
		var allowedChars = (inst.get('multiSelect') ? inst.get('multiSeparator') :
			(inst.get('rangeSelect') ? inst.get('rangeSeparator') : ''));
		var literal = false;
		var hasNum = false;
		for (var i = 0; i < dateFormat.length; i++) {
			var ch = dateFormat.charAt(i);
			if (literal) {
				if (ch == "'" && dateFormat.charAt(i + 1) != "'") {
					literal = false;
				}
				else {
					allowedChars += ch;
				}
			}
			else {
				switch (ch) {
					case 'd': case 'm': case 'o': case 'w':
						allowedChars += (hasNum ? '' : '0123456789'); hasNum = true; break;
					case 'y': case '@': case '!':
						allowedChars += (hasNum ? '' : '0123456789') + '-'; hasNum = true; break;
					case 'J':
						allowedChars += (hasNum ? '' : '0123456789') + '-.'; hasNum = true; break;
					case 'D': case 'M': case 'Y':
						return null; // Accept anything
					case "'":
						if (dateFormat.charAt(i + 1) == "'") {
							allowedChars += "'";
						}
						else {
							literal = true;
						}
						break;
					default:
						allowedChars += ch;
				}
			}
		}
		return allowedChars;
	},

	/* Synchronise datepicker with the field.
	   @param  event  (KeyEvent) the keystroke
	   @return  (boolean) true if allowed, false if not allowed */
	_keyUp: function(event) {
		var target = event.target;
		var inst = $.data(target, $.datepick.dataName);
		if (inst && !inst.ctrlKey && inst.lastVal != inst.target.val()) {
			try {
				var dates = $.datepick._extractDates(inst, inst.target.val());
				if (dates.length > 0) {
					$.datepick.setDate(target, dates, null, true);
				}
			}
			catch (event) {
				// Ignore
			}
		}
		return true;
	},

	/* Increment/decrement month/year on mouse wheel activity.
	   @param  event  (event) the mouse wheel event
	   @param  delta  (number) the amount of change */
	_doMouseWheel: function(event, delta) {
		var target = ($.datepick.curInst && $.datepick.curInst.target[0]) ||
			$(event.target).closest('.' + $.datepick.markerClass)[0];
		if ($.datepick.isDisabled(target)) {
			return;
		}
		var inst = $.data(target, $.datepick.dataName);
		if (inst.get('useMouseWheel')) {
			delta = ($.browser.opera ? -delta : delta);
			delta = (delta < 0 ? -1 : +1);
			$.datepick.changeMonth(target,
				-inst.get(event.ctrlKey ? 'monthsToJump' : 'monthsToStep') * delta);
		}
		event.preventDefault();
	},

	/* Clear an input and close a popup datepicker.
	   @param  target  (element) the control to use */
	clear: function(target) {
		var inst = $.data(target, this.dataName);
		if (inst) {
			inst.selectedDates = [];
			this.hide(target);
			if (inst.get('selectDefaultDate') && inst.get('defaultDate')) {
				this.setDate(target,
					$.datepick.newDate(inst.get('defaultDate') ||$.datepick.today()));
			}
			else {
				this._updateInput(target);
			}
		}
	},

	/* Retrieve the selected date(s) for a datepicker.
	   @param  target  (element) the control to examine
	   @return  (CDate[]) the selected date(s) */
	getDate: function(target) {
		var inst = $.data(target, this.dataName);
		return (inst ? inst.selectedDates : []);
	},

	/* Set the selected date(s) for a datepicker.
	   @param  target   (element) the control to examine
	   @param  dates    (CDate or number or string or [] of these) the selected date(s)
	   @param  endDate  (CDate or number or string) the ending date for a range (optional)
	   @param  keyUp    (boolean, internal) true if coming from keyUp processing
	   @param  setOpt   (boolean, internal) true if coming from option processing */
	setDate: function(target, dates, endDate, keyUp, setOpt) {
		var inst = $.data(target, this.dataName);
		if (inst) {
			if (!$.isArray(dates)) {
				dates = [dates];
				if (endDate) {
					dates.push(endDate);
				}
			}
			var dateFormat = inst.get('dateFormat');
			var minDate = inst.get('minDate');
			var maxDate = inst.get('maxDate');
			var curDate = inst.selectedDates[0];
			inst.selectedDates = [];
			for (var i = 0; i < dates.length; i++) {
				var date = $.datepick.determineDate(
					dates[i], null, curDate, dateFormat, inst.getConfig());
				if (date) {
					if ((!minDate || date.getTime() >= minDate.getTime()) &&
							(!maxDate || date.getTime() <= maxDate.getTime())) {
						var found = false;
						for (var j = 0; j < inst.selectedDates.length; j++) {
							if (inst.selectedDates[j].getTime() == date.getTime()) {
								found = true;
								break;
							}
						}
						if (!found) {
							inst.selectedDates.push(date);
						}
					}
				}
			}
			var rangeSelect = inst.get('rangeSelect');
			inst.selectedDates.splice(inst.get('multiSelect') ||
				(rangeSelect ? 2 : 1), inst.selectedDates.length);
			if (rangeSelect) {
				switch (inst.selectedDates.length) {
					case 1: inst.selectedDates[1] = inst.selectedDates[0]; break;
					case 2: inst.selectedDates[1] =
						(inst.selectedDates[0].getTime() > inst.selectedDates[1].getTime() ?
						inst.selectedDates[0] : inst.selectedDates[1]); break;
				}
				inst.pickingRange = false;
			}
			inst.prevDate = (inst.drawDate ? $.datepick.newDate(inst.drawDate) : null);
			inst.drawDate = this._checkMinMax($.datepick.newDate(inst.selectedDates[0] ||
				inst.get('defaultDate') || $.datepick.today()), inst);
			if (!setOpt) {
				this._update(target);
				this._updateInput(target, keyUp);
			}
		}
	},

	/* Determine whether a date is selectable for this datepicker.
	   @param  target  (element) the control to check
	   @param  date    (Date or string or number) the date to check
	   @return  (boolean) true if selectable, false if not */
	isSelectable: function(target, date) {
		var inst = $.data(target, this.dataName);
		if (!inst) {
			return false;
		}
		date = $.datepick.determineDate(date, inst.selectedDates[0] || this.today(), null,
			inst.get('dateFormat'), inst.getConfig());
		return this._isSelectable(target, date, inst.get('onDate'),
			inst.get('minDate'), inst.get('maxDate'));
	},

	/* Internally determine whether a date is selectable for this datepicker.
	   @param  target   (element) the control to check
	   @param  date     (Date) the date to check
	   @param  onDate   (function or boolean) any onDate callback or callback.selectable
	   @param  mindate  (Date) the minimum allowed date
	   @param  maxdate  (Date) the maximum allowed date
	   @return  (boolean) true if selectable, false if not */
	_isSelectable: function(target, date, onDate, minDate, maxDate) {
		var dateInfo = (typeof onDate == 'boolean' ? {selectable: onDate} :
			(!onDate ? {} : onDate.apply(target, [date, true])));
		return (dateInfo.selectable != false) &&
			(!minDate || date.getTime() >= minDate.getTime()) &&
			(!maxDate || date.getTime() <= maxDate.getTime());
	},

	/* Perform a named action for a datepicker.
	   @param  target  (element) the control to affect
	   @param  action  (string) the name of the action */
	performAction: function(target, action) {
		var inst = $.data(target, this.dataName);
		if (inst && !this.isDisabled(target)) {
			var commands = inst.get('commands');
			if (commands[action] && commands[action].enabled.apply(target, [inst])) {
				commands[action].action.apply(target, [inst]);
			}
		}
	},

	/* Set the currently shown month, defaulting to today's.
	   @param  target  (element) the control to affect
	   @param  year    (number) the year to show (optional)
	   @param  month   (number) the month to show (1-12) (optional)
	   @param  day     (number) the day to show (optional) */
	showMonth: function(target, year, month, day) {
		var inst = $.data(target, this.dataName);
		if (inst && (day != null ||
				(inst.drawDate.getFullYear() != year || inst.drawDate.getMonth() + 1 != month))) {
			inst.prevDate = $.datepick.newDate(inst.drawDate);
			var show = this._checkMinMax((year != null ?
				$.datepick.newDate(year, month, 1) : $.datepick.today()), inst);
			inst.drawDate = $.datepick.newDate(show.getFullYear(), show.getMonth() + 1,
				(day != null ? day : Math.min(inst.drawDate.getDate(),
				$.datepick.daysInMonth(show.getFullYear(), show.getMonth() + 1))));
			this._update(target);
		}
	},

	/* Adjust the currently shown month.
	   @param  target  (element) the control to affect
	   @param  offset  (number) the number of months to change by */
	changeMonth: function(target, offset) {
		var inst = $.data(target, this.dataName);
		if (inst) {
			var date = $.datepick.add($.datepick.newDate(inst.drawDate), offset, 'm');
			this.showMonth(target, date.getFullYear(), date.getMonth() + 1);
		}
	},

	/* Adjust the currently shown day.
	   @param  target  (element) the control to affect
	   @param  offset  (number) the number of days to change by */
	changeDay: function(target, offset) {
		var inst = $.data(target, this.dataName);
		if (inst) {
			var date = $.datepick.add($.datepick.newDate(inst.drawDate), offset, 'd');
			this.showMonth(target, date.getFullYear(), date.getMonth() + 1, date.getDate());
		}
	},

	/* Restrict a date to the minimum/maximum specified.
	   @param  date  (CDate) the date to check
	   @param  inst  (object) the current instance settings */
	_checkMinMax: function(date, inst) {
		var minDate = inst.get('minDate');
		var maxDate = inst.get('maxDate');
		date = (minDate && date.getTime() < minDate.getTime() ? $.datepick.newDate(minDate) : date);
		date = (maxDate && date.getTime() > maxDate.getTime() ? $.datepick.newDate(maxDate) : date);
		return date;
	},

	/* Retrieve the date associated with an entry in the datepicker.
	   @param  target  (element) the control to examine
	   @param  elem    (element) the selected datepicker element
	   @return  (CDate) the corresponding date, or null */
	retrieveDate: function(target, elem) {
		var inst = $.data(target, this.dataName);
		return (!inst ? null : this._normaliseDate(
			new Date(parseInt(elem.className.replace(/^.*dp(-?\d+).*$/, '$1'), 10))));
	},

	/* Select a date for this datepicker.
	   @param  target  (element) the control to examine
	   @param  elem    (element) the selected datepicker element */
	selectDate: function(target, elem) {
		var inst = $.data(target, this.dataName);
		if (inst && !this.isDisabled(target)) {
			var date = this.retrieveDate(target, elem);
			var multiSelect = inst.get('multiSelect');
			var rangeSelect = inst.get('rangeSelect');
			if (multiSelect) {
				var found = false;
				for (var i = 0; i < inst.selectedDates.length; i++) {
					if (date.getTime() == inst.selectedDates[i].getTime()) {
						inst.selectedDates.splice(i, 1);
						found = true;
						break;
					}
				}
				if (!found && inst.selectedDates.length < multiSelect) {
					inst.selectedDates.push(date);
				}
			}
			else if (rangeSelect) {
				if (inst.pickingRange) {
					inst.selectedDates[1] = date;
				}
				else {
					inst.selectedDates = [date, date];
				}
				inst.pickingRange = !inst.pickingRange;
			}
			else {
				inst.selectedDates = [date];
			}
			inst.prevDate = $.datepick.newDate(date);
			this._updateInput(target);
			if (inst.inline || inst.pickingRange || inst.selectedDates.length <
					(multiSelect || (rangeSelect ? 2 : 1))) {
				this._update(target);
			}
			else {
				this.hide(target);
			}
		}
	},

	/* Generate the datepicker content for this control.
	   @param  target  (element) the control to affect
	   @param  inst    (object) the current instance settings
	   @return  (jQuery) the datepicker content */
	_generateContent: function(target, inst) {
		var renderer = inst.get('renderer');
		var monthsToShow = inst.get('monthsToShow');
		monthsToShow = ($.isArray(monthsToShow) ? monthsToShow : [1, monthsToShow]);
		inst.drawDate = this._checkMinMax(
			inst.drawDate || inst.get('defaultDate') || $.datepick.today(), inst);
		var drawDate = $.datepick.add(
			$.datepick.newDate(inst.drawDate), -inst.get('monthsOffset'), 'm');
		// Generate months
		var monthRows = '';
		for (var row = 0; row < monthsToShow[0]; row++) {
			var months = '';
			for (var col = 0; col < monthsToShow[1]; col++) {
				months += this._generateMonth(target, inst, drawDate.getFullYear(),
					drawDate.getMonth() + 1, renderer, (row == 0 && col == 0));
				$.datepick.add(drawDate, 1, 'm');
			}
			monthRows += this._prepare(renderer.monthRow, inst).replace(/\{months\}/, months);
		}
		var picker = this._prepare(renderer.picker, inst).replace(/\{months\}/, monthRows).
			replace(/\{weekHeader\}/g, this._generateDayHeaders(inst, renderer)) +
			($.browser.msie && parseInt($.browser.version, 10) < 7 && !inst.inline ?
			'<iframe src="javascript:void(0);" class="' + this._coverClass + '"></iframe>' : '');
		// Add commands
		var commands = inst.get('commands');
		var asDateFormat = inst.get('commandsAsDateFormat');
		var addCommand = function(type, open, close, name, classes) {
			if (picker.indexOf('{' + type + ':' + name + '}') == -1) {
				return;
			}
			var command = commands[name];
			var date = (asDateFormat ? command.date.apply(target, [inst]) : null);
			picker = picker.replace(new RegExp('\\{' + type + ':' + name + '\\}', 'g'),
				'<' + open +
				(command.status ? ' title="' + inst.get(command.status) + '"' : '') +
				' class="' + renderer.commandClass + ' ' +
				renderer.commandClass + '-' + name + ' ' + classes +
				(command.enabled(inst) ? '' : ' ' + renderer.disabledClass) + '">' +
				(date ? $.datepick.formatDate(inst.get(command.text), date, inst.getConfig()) :
				inst.get(command.text)) + '</' + close + '>');
		};
		for (var name in commands) {
			addCommand('button', 'button type="button"', 'button', name,
				renderer.commandButtonClass);
			addCommand('link', 'a href="javascript:void(0)"', 'a', name,
				renderer.commandLinkClass);
		}
		picker = $(picker);
		if (monthsToShow[1] > 1) {
			var count = 0;
			$(renderer.monthSelector, picker).each(function() {
				var nth = ++count % monthsToShow[1];
				$(this).addClass(nth == 1 ? 'first' : (nth == 0 ? 'last' : ''));
			});
		}
		// Add datepicker behaviour
		var self = this;
		picker.find(renderer.daySelector + ' a').hover(
				function() { $(this).addClass(renderer.highlightedClass); },
				function() {
					(inst.inline ? $(this).parents('.' + self.markerClass) : inst.div).
						find(renderer.daySelector + ' a').
						removeClass(renderer.highlightedClass);
				}).
			click(function() {
				self.selectDate(target, this);
			}).end().
			find('select.' + this._monthYearClass + ':not(.' + this._anyYearClass + ')').
			change(function() {
				var monthYear = $(this).val().split('/');
				self.showMonth(target, parseInt(monthYear[1], 10), parseInt(monthYear[0], 10));
			}).end().
			find('select.' + this._anyYearClass).
			click(function() {
				$(this).css('visibility', 'hidden').
					next('input').css({left: this.offsetLeft, top: this.offsetTop,
					width: this.offsetWidth, height: this.offsetHeight}).show().focus();
			}).end().
			find('input.' + self._monthYearClass).
			change(function() {
				try {
					var year = parseInt($(this).val(), 10);
					year = (isNaN(year) ? inst.drawDate.getFullYear() : year);
					self.showMonth(target, year, inst.drawDate.getMonth() + 1, inst.drawDate.getDate());
				}
				catch (e) {
					alert(e);
				}
			}).keydown(function(event) {
				if (event.keyCode == 13) { // Enter
					$(event.target).change();
				}
				else if (event.keyCode == 27) { // Escape
					$(event.target).hide().prev('select').css('visibility', 'visible');
					inst.target.focus();
				}
			});
		// Add command behaviour
		picker.find('.' + renderer.commandClass).click(function() {
				if (!$(this).hasClass(renderer.disabledClass)) {
					var action = this.className.replace(
						new RegExp('^.*' + renderer.commandClass + '-([^ ]+).*$'), '$1');
					$.datepick.performAction(target, action);
				}
			});
		// Add classes
		if (inst.get('isRTL')) {
			picker.addClass(renderer.rtlClass);
		}
		if (monthsToShow[0] * monthsToShow[1] > 1) {
			picker.addClass(renderer.multiClass);
		}
		var pickerClass = inst.get('pickerClass');
		if (pickerClass) {
			picker.addClass(pickerClass);
		}
		// Resize
		$('body').append(picker);
		var width = 0;
		picker.find(renderer.monthSelector).each(function() {
			width += $(this).outerWidth();
		});
		picker.width(width / monthsToShow[0]);
		// Pre-show customisation
		var onShow = inst.get('onShow');
		if (onShow) {
			onShow.apply(target, [picker, inst]);
		}
		return picker;
	},

	/* Generate the content for a single month.
	   @param  target    (element) the control to affect
	   @param  inst      (object) the current instance settings
	   @param  year      (number) the year to generate
	   @param  month     (number) the month to generate
	   @param  renderer  (object) the rendering templates
	   @param  first     (boolean) true if first of multiple months
	   @return  (string) the month content */
	_generateMonth: function(target, inst, year, month, renderer, first) {
		var daysInMonth = $.datepick.daysInMonth(year, month);
		var monthsToShow = inst.get('monthsToShow');
		monthsToShow = ($.isArray(monthsToShow) ? monthsToShow : [1, monthsToShow]);
		var fixedWeeks = inst.get('fixedWeeks') || (monthsToShow[0] * monthsToShow[1] > 1);
		var firstDay = inst.get('firstDay');
		var leadDays = ($.datepick.newDate(year, month, 1).getDay() - firstDay + 7) % 7;
		var numWeeks = (fixedWeeks ? 6 : Math.ceil((leadDays + daysInMonth) / 7));
		var showOtherMonths = inst.get('showOtherMonths');
		var selectOtherMonths = inst.get('selectOtherMonths') && showOtherMonths;
		var dayStatus = inst.get('dayStatus');
		var minDate = (inst.pickingRange ? inst.selectedDates[0] : inst.get('minDate'));
		var maxDate = inst.get('maxDate');
		var rangeSelect = inst.get('rangeSelect');
		var onDate = inst.get('onDate');
		var showWeeks = renderer.week.indexOf('{weekOfYear}') > -1;
		var calculateWeek = inst.get('calculateWeek');
		var today = $.datepick.today();
		var drawDate = $.datepick.newDate(year, month, 1);
		$.datepick.add(drawDate, -leadDays - (fixedWeeks && (drawDate.getDay() == firstDay) ? 7 : 0), 'd');
		var ts = drawDate.getTime();
		// Generate weeks
		var weeks = '';
		for (var week = 0; week < numWeeks; week++) {
			var weekOfYear = (!showWeeks ? '' : '<span class="dp' + ts + '">' +
				(calculateWeek ? calculateWeek(drawDate) : 0) + '</span>');
			var days = '';
			for (var day = 0; day < 7; day++) {
				var selected = false;
				if (rangeSelect && inst.selectedDates.length > 0) {
					selected = (drawDate.getTime() >= inst.selectedDates[0] &&
						drawDate.getTime() <= inst.selectedDates[1]);
				}
				else {
					for (var i = 0; i < inst.selectedDates.length; i++) {
						if (inst.selectedDates[i].getTime() == drawDate.getTime()) {
							selected = true;
							break;
						}
					}
				}
				var dateInfo = (!onDate ? {} :
					onDate.apply(target, [drawDate, drawDate.getMonth() + 1 == month]));
				var selectable = (selectOtherMonths || drawDate.getMonth() + 1 == month) &&
					this._isSelectable(target, drawDate, dateInfo.selectable, minDate, maxDate);
				days += this._prepare(renderer.day, inst).replace(/\{day\}/g,
					(selectable ? '<a href="javascript:void(0)"' : '<span') +
					' class="dp' + ts + ' ' + (dateInfo.dateClass || '') +
					(selected && (selectOtherMonths || drawDate.getMonth() + 1 == month) ?
					' ' + renderer.selectedClass : '') +
					(selectable ? ' ' + renderer.defaultClass : '') +
					((drawDate.getDay() || 7) < 6 ? '' : ' ' + renderer.weekendClass) +
					(drawDate.getMonth() + 1 == month ? '' : ' ' + renderer.otherMonthClass) +
					(drawDate.getTime() == today.getTime() && (drawDate.getMonth() + 1) == month ?
					' ' + renderer.todayClass : '') +
					(drawDate.getTime() == inst.drawDate.getTime() && (drawDate.getMonth() + 1) == month ?
					' ' + renderer.highlightedClass : '') + '"' +
					(dateInfo.title || (dayStatus && selectable) ? ' title="' +
					(dateInfo.title || $.datepick.formatDate(
					dayStatus, drawDate, inst.getConfig())) + '"' : '') + '>' +
					(showOtherMonths || (drawDate.getMonth() + 1) == month ?
					dateInfo.content || drawDate.getDate() : '&nbsp;') +
					(selectable ? '</a>' : '</span>'));
				$.datepick.add(drawDate, 1, 'd');
				ts = drawDate.getTime();
			}
			weeks += this._prepare(renderer.week, inst).replace(/\{days\}/g, days).
				replace(/\{weekOfYear\}/g, weekOfYear);
		}
		var monthHeader = this._prepare(renderer.month, inst).match(/\{monthHeader(:[^\}]+)?\}/);
		monthHeader = (monthHeader[0].length <= 13 ? 'MM yyyy' :
			monthHeader[0].substring(13, monthHeader[0].length - 1));
		monthHeader = (first ? this._generateMonthSelection(
			inst, year, month, minDate, maxDate, monthHeader, renderer) :
			$.datepick.formatDate(monthHeader, $.datepick.newDate(year, month, 1), inst.getConfig()));
		var weekHeader = this._prepare(renderer.weekHeader, inst).
			replace(/\{days\}/g, this._generateDayHeaders(inst, renderer));
		return this._prepare(renderer.month, inst).replace(/\{monthHeader(:[^\}]+)?\}/g, monthHeader).
			replace(/\{weekHeader\}/g, weekHeader).replace(/\{weeks\}/g, weeks);
	},

	/* Generate the HTML for the day headers.
	   @param  inst      (object) the current instance settings
	   @param  renderer  (object) the rendering templates
	   @return  (string) a week's worth of day headers */
	_generateDayHeaders: function(inst, renderer) {
		var firstDay = inst.get('firstDay');
		var dayNames = inst.get('dayNames');
		var dayNamesMin = inst.get('dayNamesMin');
		var header = '';
		for (var day = 0; day < 7; day++) {
			var dow = (day + firstDay) % 7;
			header += this._prepare(renderer.dayHeader, inst).replace(/\{day\}/g,
				'<span class="' + this._curDoWClass + dow + '" title="' +
				dayNames[dow] + '">' + dayNamesMin[dow] + '</span>');
		}
		return header;
	},

	/* Generate selection controls for month.
	   @param  inst         (object) the current instance settings
	   @param  year         (number) the year to generate
	   @param  month        (number) the month to generate
	   @param  minDate      (CDate) the minimum date allowed
	   @param  maxDate      (CDate) the maximum date allowed
	   @param  monthHeader  (string) the month/year format
	   @return  (string) the month selection content */
	_generateMonthSelection: function(inst, year, month, minDate, maxDate, monthHeader) {
		if (!inst.get('changeMonth')) {
			return $.datepick.formatDate(
				monthHeader, $.datepick.newDate(year, month, 1), inst.getConfig());
		}
		// Months
		var monthNames = inst.get('monthNames' + (monthHeader.match(/mm/i) ? '' : 'Short'));
		var html = monthHeader.replace(/m+/i, '\\x2E').replace(/y+/i, '\\x2F');
		var selector = '<select class="' + this._monthYearClass +
			'" title="' + inst.get('monthStatus') + '">';
		for (var m = 1; m <= 12; m++) {
			if ((!minDate || $.datepick.newDate(year, m, $.datepick.daysInMonth(year, m)).
					getTime() >= minDate.getTime()) &&
					(!maxDate || $.datepick.newDate(year, m, 1).getTime() <= maxDate.getTime())) {
				selector += '<option value="' + m + '/' + year + '"' +
					(month == m ? ' selected="selected"' : '') + '>' +
					monthNames[m - 1] + '</option>';
			}
		}
		selector += '</select>';
		html = html.replace(/\\x2E/, selector);
		// Years
		var yearRange = inst.get('yearRange');
		if (yearRange == 'any') {
			selector = '<select class="' + this._monthYearClass + ' ' + this._anyYearClass +
				'" title="' + inst.get('yearStatus') + '">' +
				'<option>' + year + '</option></select>' +
				'<input class="' + this._monthYearClass + ' ' + this._curMonthClass +
				month + '" value="' + year + '">';
		}
		else {
			yearRange = yearRange.split(':');
			var todayYear = $.datepick.today().getFullYear();
			var start = (yearRange[0].match('c[+-].*') ? year + parseInt(yearRange[0].substring(1), 10) :
				((yearRange[0].match('[+-].*') ? todayYear : 0) + parseInt(yearRange[0], 10)));
			var end = (yearRange[1].match('c[+-].*') ? year + parseInt(yearRange[1].substring(1), 10) :
				((yearRange[1].match('[+-].*') ? todayYear : 0) + parseInt(yearRange[1], 10)));
			selector = '<select class="' + this._monthYearClass +
				'" title="' + inst.get('yearStatus') + '">';
			start = $.datepick.add($.datepick.newDate(start + 1, 1, 1), -1, 'd');
			end = $.datepick.newDate(end, 1, 1);
			var addYear = function(y) {
				if (y != 0) {
					selector += '<option value="' + month + '/' + y + '"' +
						(year == y ? ' selected="selected"' : '') + '>' + y + '</option>';
				}
			};
			if (start.getTime() < end.getTime()) {
				start = (minDate && minDate.getTime() > start.getTime() ? minDate : start).getFullYear();
				end = (maxDate && maxDate.getTime() < end.getTime() ? maxDate : end).getFullYear();
				for (var y = start; y <= end; y++) {
					addYear(y);
				}
			}
			else {
				start = (maxDate && maxDate.getTime() < start.getTime() ? maxDate : start).getFullYear();
				end = (minDate && minDate.getTime() > end.getTime() ? minDate : end).getFullYear();
				for (var y = start; y >= end; y--) {
					addYear(y);
				}
			}
			selector += '</select>';
		}
		html = html.replace(/\\x2F/, selector);
		return html;
	},

	/* Prepare a render template for use.
	   Exclude popup/inline sections that are not applicable.
	   Localise text of the form: {l10n:name}.
	   @param  text  (string) the text to localise
	   @param  inst  (object) the current instance settings
	   @return  (string) the localised text */
	_prepare: function(text, inst) {
		var replaceSection = function(type, retain) {
			while (true) {
				var start = text.indexOf('{' + type + ':start}');
				if (start == -1) {
					return;
				}
				var end = text.substring(start).indexOf('{' + type + ':end}');
				if (end > -1) {
					text = text.substring(0, start) +
						(retain ? text.substr(start + type.length + 8, end - type.length - 8) : '') +
						text.substring(start + end + type.length + 6);
				}
			}
		};
		replaceSection('inline', inst.inline);
		replaceSection('popup', !inst.inline);
		var pattern = /\{l10n:([^\}]+)\}/;
		var matches = null;
		while (matches = pattern.exec(text)) {
			text = text.replace(matches[0], inst.get(matches[1]));
		}
		return text;
	}
});

/* jQuery extend now ignores nulls!
   @param  target  (object) the object to extend
   @param  props   (object) the new settings
   @return  (object) the updated object */
function extendRemove(target, props) {
	$.extend(target, props);
	for (var name in props)
		if (props[name] == null || props[name] == undefined)
			target[name] = props[name];
	return target;
};

/* Attach the datepicker functionality to a jQuery selection.
   @param  command  (string) the command to run (optional, default 'attach')
   @param  options  (object) the new settings to use for these instances (optional)
   @return  (jQuery) for chaining further calls */
$.fn.datepick = function(options) {
	var otherArgs = Array.prototype.slice.call(arguments, 1);
	if ($.inArray(options, ['getDate', 'isDisabled', 'isSelectable', 'options', 'retrieveDate']) > -1) {
		return $.datepick[options].apply($.datepick, [this[0]].concat(otherArgs));
	}
	return this.each(function() {
		if (typeof options == 'string') {
			$.datepick[options].apply($.datepick, [this].concat(otherArgs));
		}
		else {
			$.datepick._attachPicker(this, options || {});
		}
	});
};

/* Initialise the datepicker functionality. */
$.datepick = new Datepicker(); // singleton instance

$(function() {
	$(document).mousedown($.datepick._checkExternalClick).
		resize(function() { $.datepick.hide($.datepick.curInst); });
});

})(jQuery);


/* http://keith-wood.name/datetimeEntry.html
   Date and time entry for jQuery v1.0.0.
   Written by Keith Wood (kbwood{at}iinet.com.au) September 2010.
   Dual licensed under the GPL (http://dev.jquery.com/browser/trunk/jquery/GPL-LICENSE.txt) and
   MIT (http://dev.jquery.com/browser/trunk/jquery/MIT-LICENSE.txt) licenses.
   Please attribute the author if you use it. */

/* Turn an input field into an entry point for a date and/or time value.
   The date/time can be entered via directly typing the value,
   via the arrow keys, or via spinner buttons.
   It is configurable to reorder the fields, to enforce a minimum
   and/or maximum date/time, and to change the spinner image.
   Attach it with $('input selector').datetimeEntry(); for default settings,
   or configure it with options like:
   $('input selector').datetimeEntry(
      {spinnerImage: 'spinnerSquare.png', spinnerSize: [20, 20, 0]}); */

(function($) { // Hide scope, no $ conflict

/* DatetimeEntry manager.
   Use the singleton instance of this class, $.datetimeEntry, to interact with the date
   entry functionality. Settings for fields are maintained in an instance object,
   allowing multiple different settings on the same page. */
function DatetimeEntry() {
	this._disabledInputs = []; // List of datetime inputs that have been disabled
	this.regional = []; // Available regional settings, indexed by language code
	this.regional[''] = { // Default regional settings
		datetimeFormat: 'O/D/Y H:Ma', // The format of the date text:
			// 'y' for short year, 'Y' for full year, 'o' for month, 'O' for two-digit month,
			// 'n' for abbreviated month name, 'N' for full month name,
			// 'd' for day, 'D' for two-digit day, 'w' for abbreviated day name and number,
			// 'W' for full day name and number), 'h' for hour, 'H' for two-digit hour,
			// 'm' for minute, 'M' for two-digit minutes, 's' for seconds,
			// 'S' for two-digit seconds, 'a' for AM/PM indicator (omit for 24-hour)
		datetimeSeparators: '.', // Additional separators between datetime portions
		monthNames: ['January', 'February', 'March', 'April', 'May', 'June',
			'July', 'August', 'September', 'October', 'November', 'December'], // Names of the months
		monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
			'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], // Abbreviated names of the months
		// Names of the days
		dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'], // Abbreviated names of the days
		ampmNames: ['AM', 'PM'], // Names of morning/evening markers
		// The popup texts for the spinner image areas
		spinnerTexts: ['Today', 'Previous field', 'Next field', 'Increment', 'Decrement'],
		isRTL: false // Does this language run right-to-left?
	};
	this._defaults = {
		appendText: '', // Display text following the input box, e.g. showing the format
		initialField: 0, // The field to highlight initially, 0 = hours, 1 = minutes, ...
		useMouseWheel: true, // True to use mouse wheel for increment/decrement if possible,
			// false to never use it
		shortYearCutoff: '+10', // The century cutoff for two-digit years,
			// absolute (numeric) or relative (string)
		defaultDatetime: null, // The date to use if none has been set, leave at null for now
		minDatetime: null, // The earliest selectable datetime, or null for no limit
		maxDatetime: null, // The latest selectable datetime, or null for no limit
		minTime: null, // The earliest selectable time regardless of date, or null for no limit
		maxTime: null, // The latest selectable time regardless of date, or null for no limit
		timeSteps: [1, 1, 1], // Steps for each of hours/minutes/seconds when incrementing/decrementing
		spinnerImage: '/images/spinnerDefault.png', // The URL of the images to use for the date spinner
			// Seven images packed horizontally for normal, each button pressed, and disabled
		spinnerSize: [20, 20, 8], // The width and height of the spinner image,
			// and size of centre button for current date
		spinnerBigImage: '', // The URL of the images to use for the expanded date spinner
			// Seven images packed horizontally for normal, each button pressed, and disabled
		spinnerBigSize: [40, 40, 16], // The width and height of the expanded spinner image,
			// and size of centre button for current date
		spinnerIncDecOnly: false, // True for increment/decrement buttons only, false for all
		spinnerRepeat: [500, 250], // Initial and subsequent waits in milliseconds
			// for repeats on the spinner buttons
		beforeShow: null, // Function that takes an input field and
			// returns a set of custom settings for the date entry
		altField: null, // Selector, element or jQuery object for an alternate field to keep synchronised
		altFormat: null // A separate format for the alternate field
	};
	$.extend(this._defaults, this.regional['']);
}

var PROP_NAME = 'datetimeEntry';

$.extend(DatetimeEntry.prototype, {
	/* Class name added to elements to indicate already configured with datetime entry. */
	markerClassName: 'hasDatetimeEntry',

	/* Override the default settings for all instances of the datetime entry.
	   @param  options  (object) the new settings to use as defaults (anonymous object)
	   @return  (DatetimeEntry) this object */
	setDefaults: function(options) {
		extendRemove(this._defaults, options || {});
		return this;
	},

	/* Attach the datetime entry handler to an input field.
	   @param  target   (element) the field to attach to
	   @param  options  (object) custom settings for this instance */
	_connectDatetimeEntry: function(target, options) {
		var input = $(target);
		if (input.hasClass(this.markerClassName)) {
			return;
		}
		var inst = {};
		inst.options = $.extend({}, options);
		inst._selectedYear = 0; // The currently selected year
		inst._selectedMonth = 0; // The currently selected month
		inst._selectedDay = 0; // The currently selected day
		inst._selectedHour = 0; // The currently selected hour
		inst._selectedMinute = 0; // The currently selected minute
		inst._selectedSecond = 0; // The currently selected second
		inst._field = 0; // The selected subfield
		this._decodeDatetimeFormat(inst);
		inst.input = $(target); // The attached input field
		$.data(target, PROP_NAME, inst);
		var spinnerImage = this._get(inst, 'spinnerImage');
		var spinnerText = this._get(inst, 'spinnerText');
		var spinnerSize = this._get(inst, 'spinnerSize');
		var appendText = this._get(inst, 'appendText');
		var spinner = (!spinnerImage ? null :
			$('<span class="datetimeEntry_control" style="display: inline-block; ' +
			'background: url(\'' + spinnerImage + '\') 0 0 no-repeat; ' +
			'width: ' + spinnerSize[0] + 'px; height: ' + spinnerSize[1] + 'px;' +
			($.browser.mozilla && $.browser.version < '1.9' ? // FF 2- (Win)
			' padding-left: ' + spinnerSize[0] + 'px; padding-bottom: ' +
			(spinnerSize[1] - 18) + 'px;' : '') + '"></span>'));
		input.wrap('<span class="datetimeEntry_wrap"></span>').
			after(appendText ? '<span class="datetimeEntry_append">' +
			appendText + '</span>' : '').after(spinner || '');
		input.addClass(this.markerClassName).
			bind('focus.datetimeEntry', this._doFocus).
			bind('blur.datetimeEntry', this._doBlur).
			bind('click.datetimeEntry', this._doClick).
			bind('keydown.datetimeEntry', this._doKeyDown).
			bind('keypress.datetimeEntry', this._doKeyPress);
		// Check pastes
		if ($.browser.mozilla) {
			input.bind('input.datetimeEntry',
				function(event) { $.datetimeEntry._extractDatetime(inst); });
		}
		if ($.browser.msie) {
			input.bind('paste.datetimeEntry', function(event) {
				setTimeout(function() { $.datetimeEntry._extractDatetime(inst); }, 1); });
		}
		// Allow mouse wheel usage
		if (this._get(inst, 'useMouseWheel') && $.fn.mousewheel) {
			input.mousewheel(this._doMouseWheel);
		}
		if (spinner) {
			spinner.mousedown(this._handleSpinner).mouseup(this._endSpinner).
				mouseover(this._expandSpinner).mouseout(this._endSpinner).
				mousemove(this._describeSpinner);
		}
	},

	/* Enable a datetime entry input and any associated spinner.
	   @param  input  (element) single input field */
	_enableDatetimeEntry: function(input) {
		this._enableDisable(input, false);
	},

	/* Disable a datetime entry input and any associated spinner.
	   @param  input  (element) single input field */
	_disableDatetimeEntry: function(input) {
		this._enableDisable(input, true);
	},

	/* Enable or disable a datetime entry input and any associated spinner.
	   @param  input    (element) single input field
	   @param  disable  (boolean) true to disable, false to enable */
	_enableDisable: function(input, disable) {
		var inst = $.data(input, PROP_NAME);
		if (!inst) {
			return;
		}
		input.disabled = disable;
		if (input.nextSibling && input.nextSibling.nodeName.toLowerCase() == 'span') {
			$.datetimeEntry._changeSpinner(inst, input.nextSibling, (disable ? 5 : -1));
		}
		$.datetimeEntry._disabledInputs = $.map($.datetimeEntry._disabledInputs,
			function(value) { return (value == input ? null : value); }); // Delete entry
		if (disable) {
			$.datetimeEntry._disabledInputs.push(input);
		}
	},

	/* Check whether an input field has been disabled.
	   @param  input  (element) input field to check
	   @return  (boolean) true if this field has been disabled, false if it is enabled */
	_isDisabledDatetimeEntry: function(input) {
		return $.inArray(input, this._disabledInputs) > -1;
	},

	/* Reconfigure the settings for a datetime entry field.
	   @param  input    (element) input field to change
	   @param  options  (object) new settings to add or
	                    (string) an individual setting name
	   @param  value    (any) the individual setting's value */
	_changeDatetimeEntry: function(input, options, value) {
		var inst = $.data(input, PROP_NAME);
		if (inst) {
			if (typeof options == 'string') {
				var name = options;
				options = {};
				options[name] = value;
			}
			var currentDate = this._parseDatetime(inst, $(input).val());
			extendRemove(inst.options, options || {});
			this._decodeDatetimeFormat(inst);
			if (currentDate) {
				this._setDatetime(inst, currentDate);
			}
		}
		$.data(input, PROP_NAME, inst);
	},

	/* Locate fields within the datetime format.
	   @param  inst  (object) the instance settings */
	_decodeDatetimeFormat: function(inst) {
		var datetimeFormat = this._get(inst, 'datetimeFormat');
		inst._fields = [];
		inst._ampmField = -1;
		for (var i = 0; i < datetimeFormat.length; i++) {
			if (datetimeFormat.charAt(i).match(/y|Y|o|O|n|N|d|D|w|W|h|H|m|M|s|S|a/)) {
				inst._fields.push(i);
			}
			if (datetimeFormat.charAt(i) == 'a') {
				inst._ampmField = inst._fields.length - 1;
			}
		}
	},

	/* Remove the datetime entry functionality from an input.
	   @param  input  (element) input field to affect */
	_destroyDatetimeEntry: function(input) {
		$input = $(input);
		if (!$input.hasClass(this.markerClassName)) {
			return;
		}
		$input.removeClass(this.markerClassName).unbind('.datetimeEntry');
		if ($.fn.mousewheel) {
			$input.unmousewheel();
		}
		this._disabledInputs = $.map(this._disabledInputs,
			function(value) { return (value == input ? null : value); }); // Delete entry
		$input.parent().replaceWith($input);
		$.removeData(input, PROP_NAME);
	},

	/* Initialise the current datetime for a datetime entry input field.
	   @param  input  (element) input field to update
	   @param  time   (Date) the new datetime or null for now */
	_setDatetimeDatetimeEntry: function(input, datetime) {
		var inst = $.data(input, PROP_NAME);
		if (inst) {
			this._setDatetime(inst, datetime ? (typeof datetime == 'object' ?
				new Date(datetime.getTime()) : datetime) : null);
		}
	},

	/* Retrieve the current datetime for a datetime entry input field.
	   @param  input  (element) input field to update
	   @return  (Date) current datetime or null if none */
	_getDatetimeDatetimeEntry: function(input) {
		var inst = $.data(input, PROP_NAME);
		return (inst ? this._parseDatetime(inst, $(input).val()) : null);
	},

	/* Retrieve the millisecond offset for the current time.
	   @param  input  (element) input field to examine
	   @return  (number) the time as milliseconds offset or zero if none */
	_getOffsetDatetimeEntry: function(input) {
		var inst = $.data(input, PROP_NAME);
		var time = (inst ? $.datetimeEntry._parseDatetime(inst, inst.input.val()) : null);
		return (!time ? 0 :
			(time.getHours() * 3600 + time.getMinutes() * 60 + time.getSeconds()) * 1000);
	},

	/* Initialise datetime entry.
	   @param  target  (element) the input field or
	                   (event) the focus event */
	_doFocus: function(target) {
		var input = (target.nodeName &&
			target.nodeName.toLowerCase() == 'input' ? target : this);
		if ($.datetimeEntry._lastInput == input ||
				$.datetimeEntry._isDisabledDatetimeEntry(input)) {
			$.datetimeEntry._focussed = false;
			return;
		}
		var inst = $.data(input, PROP_NAME);
		$.datetimeEntry._focussed = true;
		$.datetimeEntry._lastInput = input;
		$.datetimeEntry._blurredInput = null;
		var beforeShow = $.datetimeEntry._get(inst, 'beforeShow');
		extendRemove(inst.options, (beforeShow ? beforeShow.apply(input, [input]) : {}));
		$.data(input, PROP_NAME, inst);
		$.datetimeEntry._extractDatetime(inst);
		setTimeout(function() { $.datetimeEntry._showField(inst); }, 10);
	},

	/* Note that the field has been exited.
	   @param  event  (event) the blur event */
	_doBlur: function(event) {
		$.datetimeEntry._blurredInput = $.datetimeEntry._lastInput;
		$.datetimeEntry._lastInput = null;
	},

	/* Select appropriate field portion on click, if already in the field.
	   @param  event  (event) the click event */
	_doClick: function(event) {
		var input = event.target;
		var inst = $.data(input, PROP_NAME);
		if (!$.datetimeEntry._focussed) {
			var datetimeFormat = $.datetimeEntry._get(inst, 'datetimeFormat');
			inst._field = 0;
			if (input.selectionStart != null) { // Use input select range
				var end = 0;
				for (var i = 0; i < datetimeFormat.length; i++) {
					end += $.datetimeEntry._fieldLength(inst, datetimeFormat.charAt(i));
					if (input.selectionStart < end) {
						break;
					}
					inst._field += (datetimeFormat.charAt(i).match(/[yondwhmsa]/i) ? 1 : 0);
				}
			}
			else if (input.createTextRange) { // Check against bounding boxes
				var src = $(event.srcElement);
				var range = input.createTextRange();
				var convert = function(value) {
					return {thin: 2, medium: 4, thick: 6}[value] || value;
				};
				var offsetX = event.clientX + document.documentElement.scrollLeft -
					(src.offset().left + parseInt(convert(src.css('border-left-width')), 10)) -
					range.offsetLeft; // Position - left edge - alignment
				var end = 0;
				for (var i = 0; i < datetimeFormat.length; i++) {
					end += $.datetimeEntry._fieldLength(inst, datetimeFormat.charAt(i));
					range.collapse();
					range.moveEnd('character', end);
					if (offsetX < range.boundingWidth) { // And compare
						break;
					}
					inst._field += (datetimeFormat.charAt(i).match(/[yondwhmsa]/i) ? 1 : 0);
				}
			}
		}
		$.data(input, PROP_NAME, inst);
		$.datetimeEntry._showField(inst);
		$.datetimeEntry._focussed = false;
	},

	/* Handle keystrokes in the field.
	   @param  event  (event) the keydown event
	   @return  (boolean) true to continue, false to stop processing */
	_doKeyDown: function(event) {
		if (event.keyCode >= 48) { // >= '0'
			return true;
		}
		var inst = $.data(event.target, PROP_NAME);
		switch (event.keyCode) {
			case 9: return (event.shiftKey ?
						// Move to previous datetime field, or out if at the beginning
						$.datetimeEntry._changeField(inst, -1, true) :
						// Move to next datetime field, or out if at the end
						$.datetimeEntry._changeField(inst, +1, true));
			case 35: if (event.ctrlKey) { // Clear datetime on ctrl+end
						$.datetimeEntry._setValue(inst, '');
					}
					else { // Last field on end
						inst._field = inst._fields.length - 1;
						$.datetimeEntry._adjustField(inst, 0);
					}
					break;
			case 36: if (event.ctrlKey) { // Current datetime on ctrl+home
						$.datetimeEntry._setDatetime(inst);
					}
					else { // First field on home
						inst._field = 0;
						$.datetimeEntry._adjustField(inst, 0);
					}
					break;
			case 37: $.datetimeEntry._changeField(inst, -1, false); break; // Previous field on left
			case 38: $.datetimeEntry._adjustField(inst, +1); break; // Increment date field on up
			case 39: $.datetimeEntry._changeField(inst, +1, false); break; // Next field on right
			case 40: $.datetimeEntry._adjustField(inst, -1); break; // Decrement datetime field on down
			case 46: $.datetimeEntry._setValue(inst, ''); break; // Clear datetime on delete
		}
		return false;
	},

	/* Disallow unwanted characters.
	   @param  event  (event) the keypress event
	   @return  (boolean) true to continue, false to stop processing */
	_doKeyPress: function(event) {
		var chr = String.fromCharCode(event.charCode == undefined ? event.keyCode : event.charCode);
		if (chr < ' ') {
			return true;
		}
		var inst = $.data(event.target, PROP_NAME);
		$.datetimeEntry._handleKeyPress(inst, chr);
		return false;
	},

	/* Increment/decrement on mouse wheel activity.
	   @param  event  (event) the mouse wheel event
	   @param  delta  (number) the amount of change */
	_doMouseWheel: function(event, delta) {
		if ($.datetimeEntry._isDisabledDatetimeEntry(event.target)) {
			return;
		}
		delta = ($.browser.opera ? -delta / Math.abs(delta) :
			($.browser.safari ? delta / Math.abs(delta) : delta));
		var inst = $.data(event.target, PROP_NAME);
		inst.input.focus();
		if (!inst.input.val()) {
			$.datetimeEntry._extractDatetime(inst);
		}
		$.datetimeEntry._adjustField(inst, delta);
		event.preventDefault();
	},

	/* Expand the spinner, if possible, to make it easier to use.
	   @param  event  (event) the mouse over event */
	_expandSpinner: function(event) {
		var spinner = $.datetimeEntry._getSpinnerTarget(event);
		var inst = $.data($.datetimeEntry._getInput(spinner), PROP_NAME);
		var spinnerBigImage = $.datetimeEntry._get(inst, 'spinnerBigImage');
		if (spinnerBigImage) {
			inst._expanded = true;
			var offset = $(spinner).offset();
			var relative = null;
			$(spinner).parents().each(function() {
				var parent = $(this);
				if (parent.css('position') == 'relative' ||
						parent.css('position') == 'absolute') {
					relative = parent.offset();
				}
				return !relative;
			});
			var spinnerSize = $.datetimeEntry._get(inst, 'spinnerSize');
			var spinnerBigSize = $.datetimeEntry._get(inst, 'spinnerBigSize');
			$('<div class="datetimeEntry_expand" style="position: absolute; left: ' +
				(offset.left - (spinnerBigSize[0] - spinnerSize[0]) / 2 -
				(relative ? relative.left : 0)) + 'px; top: ' + (offset.top -
				(spinnerBigSize[1] - spinnerSize[1]) / 2 - (relative ? relative.top : 0)) +
				'px; width: ' + spinnerBigSize[0] +
				'px; height: ' + spinnerBigSize[1] + 'px; background: #fff url(' +
				spinnerBigImage + ') no-repeat 0px 0px; z-index: 10;"></div>').
				mousedown($.datetimeEntry._handleSpinner).
				mouseup($.datetimeEntry._endSpinner).
				mouseout($.datetimeEntry._endExpand).
				mousemove($.datetimeEntry._describeSpinner).
				insertAfter(spinner);
		}
	},

	/* Locate the actual input field from the spinner.
	   @param  spinner  (element) the current spinner
	   @return  (element) the corresponding input */
	_getInput: function(spinner) {
		return $(spinner).siblings('.' + $.datetimeEntry.markerClassName)[0];
	},

	/* Change the title based on position within the spinner.
	   @param  event  (event) the mouse move event */
	_describeSpinner: function(event) {
		var spinner = $.datetimeEntry._getSpinnerTarget(event);
		var inst = $.data($.datetimeEntry._getInput(spinner), PROP_NAME);
		spinner.title = $.datetimeEntry._get(inst, 'spinnerTexts')
			[$.datetimeEntry._getSpinnerRegion(inst, event)];
	},

	/* Handle a click on the spinner.
	   @param  event  (event) the mouse click event */
	_handleSpinner: function(event) {
		var spinner = $.datetimeEntry._getSpinnerTarget(event);
		var input = $.datetimeEntry._getInput(spinner);
		if ($.datetimeEntry._isDisabledDatetimeEntry(input)) {
			return;
		}
		if (input == $.datetimeEntry._blurredInput) {
			$.datetimeEntry._lastInput = input;
			$.datetimeEntry._blurredInput = null;
		}
		var inst = $.data(input, PROP_NAME);
		$.datetimeEntry._doFocus(input);
		var region = $.datetimeEntry._getSpinnerRegion(inst, event);
		$.datetimeEntry._changeSpinner(inst, spinner, region);
		$.datetimeEntry._actionSpinner(inst, region);
		$.datetimeEntry._timer = null;
		$.datetimeEntry._handlingSpinner = true;
		var spinnerRepeat = $.datetimeEntry._get(inst, 'spinnerRepeat');
		if (region >= 3 && spinnerRepeat[0]) { // Repeat increment/decrement
			$.datetimeEntry._timer = setTimeout(
				function() { $.datetimeEntry._repeatSpinner(inst, region); },
				spinnerRepeat[0]);
			$(spinner).one('mouseout', $.datetimeEntry._releaseSpinner).
				one('mouseup', $.datetimeEntry._releaseSpinner);
		}
	},

	/* Action a click on the spinner.
	   @param  inst    (object) the instance settings
	   @param  region  (number) the spinner "button" */
	_actionSpinner: function(inst, region) {
		if (!inst.input.val()) {
			$.datetimeEntry._extractDatetime(inst);
		}
		switch (region) {
			case 0: this._setDatetime(inst); break;
			case 1: this._changeField(inst, -1, false); break;
			case 2: this._changeField(inst, +1, false); break;
			case 3: this._adjustField(inst, +1); break;
			case 4: this._adjustField(inst, -1); break;
		}
	},

	/* Repeat a click on the spinner.
	   @param  inst    (object) the instance settings
	   @param  region  (number) the spinner "button" */
	_repeatSpinner: function(inst, region) {
		if (!$.datetimeEntry._timer) {
			return;
		}
		$.datetimeEntry._lastInput = $.datetimeEntry._blurredInput;
		this._actionSpinner(inst, region);
		this._timer = setTimeout(
			function() { $.datetimeEntry._repeatSpinner(inst, region); },
			this._get(inst, 'spinnerRepeat')[1]);
	},

	/* Stop a spinner repeat.
	   @param  event  (event) the mouse event */
	_releaseSpinner: function(event) {
		clearTimeout($.datetimeEntry._timer);
		$.datetimeEntry._timer = null;
	},

	/* Tidy up after an expanded spinner.
	   @param  event  (event) the mouse event */
	_endExpand: function(event) {
		$.datetimeEntry._timer = null;
		var spinner = $.datetimeEntry._getSpinnerTarget(event);
		var input = $.datetimeEntry._getInput(spinner);
		var inst = $.data(input, PROP_NAME);
		$(spinner).remove();
		inst._expanded = false;
	},

	/* Tidy up after a spinner click.
	   @param  event  (event) the mouse event */
	_endSpinner: function(event) {
		$.datetimeEntry._timer = null;
		var spinner = $.datetimeEntry._getSpinnerTarget(event);
		var input = $.datetimeEntry._getInput(spinner);
		var inst = $.data(input, PROP_NAME);
		if (!$.datetimeEntry._isDisabledDatetimeEntry(input)) {
			$.datetimeEntry._changeSpinner(inst, spinner, -1);
		}
		if ($.datetimeEntry._handlingSpinner) {
			$.datetimeEntry._lastInput = $.datetimeEntry._blurredInput;
		}
		if ($.datetimeEntry._lastInput && $.datetimeEntry._handlingSpinner) {
			$.datetimeEntry._showField(inst);
		}
		$.datetimeEntry._handlingSpinner = false;
	},

	/* Retrieve the spinner from the event.
	   @param  event  (event) the mouse click event
	   @return  (element) the target field */
	_getSpinnerTarget: function(event) {
		return event.target || event.srcElement;
	},

	/* Determine which "button" within the spinner was clicked.
	   @param  inst   (object) the instance settings
	   @param  event  (event) the mouse event
	   @return  (number) the spinner "button" number */
	_getSpinnerRegion: function(inst, event) {
		var spinner = this._getSpinnerTarget(event);
		var pos = ($.browser.opera || $.browser.safari ?
			$.datetimeEntry._findPos(spinner) : $(spinner).offset());
		var scrolled = ($.browser.safari ? $.datetimeEntry._findScroll(spinner) :
			[document.documentElement.scrollLeft || document.body.scrollLeft,
			document.documentElement.scrollTop || document.body.scrollTop]);
		var spinnerIncDecOnly = this._get(inst, 'spinnerIncDecOnly');
		var left = (spinnerIncDecOnly ? 99 : event.clientX + scrolled[0] -
			pos.left - ($.browser.msie ? 2 : 0));
		var top = event.clientY + scrolled[1] - pos.top - ($.browser.msie ? 2 : 0);
		var spinnerSize = this._get(inst, (inst._expanded ? 'spinnerBigSize' : 'spinnerSize'));
		var right = (spinnerIncDecOnly ? 99 : spinnerSize[0] - 1 - left);
		var bottom = spinnerSize[1] - 1 - top;
		if (spinnerSize[2] > 0 && Math.abs(left - right) <= spinnerSize[2] &&
				Math.abs(top - bottom) <= spinnerSize[2]) {
			return 0; // Centre button
		}
		var min = Math.min(left, top, right, bottom);
		return (min == left ? 1 : (min == right ? 2 : (min == top ? 3 : 4))); // Nearest edge
	},

	/* Change the spinner image depending on button clicked.
	   @param  inst     (object) the instance settings
	   @param  spinner  (element) the spinner control
	   @param  region   (number) the spinner "button" */
	_changeSpinner: function(inst, spinner, region) {
		$(spinner).css('background-position', '-' + ((region + 1) *
			this._get(inst, (inst._expanded ? 'spinnerBigSize' : 'spinnerSize'))[0]) + 'px 0px');
	},

	/* Find an object's position on the screen.
	   @param  obj  (element) the control
	   @return  (object) position as .left and .top */
	_findPos: function(obj) {
		var curLeft = curTop = 0;
		if (obj.offsetParent) {
			curLeft = obj.offsetLeft;
			curTop = obj.offsetTop;
			while (obj = obj.offsetParent) {
				var origCurLeft = curLeft;
				curLeft += obj.offsetLeft;
				if (curLeft < 0) {
					curLeft = origCurLeft;
				}
				curTop += obj.offsetTop;
			}
		}
		return {left: curLeft, top: curTop};
	},

	/* Find an object's scroll offset on the screen.
	   @param  obj  (element) the control
	   @return  (number[]) offset as [left, top] */
	_findScroll: function(obj) {
		var isFixed = false;
		$(obj).parents().each(function() {
			isFixed |= $(this).css('position') == 'fixed';
		});
		if (isFixed) {
			return [0, 0];
		}
		var scrollLeft = obj.scrollLeft;
		var scrollTop = obj.scrollTop;
		while (obj = obj.parentNode) {
			scrollLeft += obj.scrollLeft || 0;
			scrollTop += obj.scrollTop || 0;
		}
		return [scrollLeft, scrollTop];
	},

	/* Get a setting value, defaulting if necessary.
	   @param  inst  (object) the instance settings
	   @param  name  (string) the setting name
	   @return  (any) the setting value */
	_get: function(inst, name) {
		return (inst.options[name] != null ?
			inst.options[name] : $.datetimeEntry._defaults[name]);
	},

	/* Extract the datetime value from the input field, or default to now.
	   @param  inst  (object) the instance settings */
	_extractDatetime: function(inst) {
		var currentDatetime = this._parseDatetime(inst, $(inst.input).val()) || this._normaliseDatetime(
			this._determineDatetime(inst, this._get(inst, 'defaultDatetime')) || new Date());
		var fields = this._constrainTime(inst, [currentDatetime.getHours(),
			currentDatetime.getMinutes(), currentDatetime.getSeconds()]);
		inst._selectedYear = currentDatetime.getFullYear();
		inst._selectedMonth = currentDatetime.getMonth();
		inst._selectedDay = currentDatetime.getDate();
		inst._selectedHour = fields[0];
		inst._selectedMinute = fields[1];
		inst._selectedSecond = fields[2];
		inst._lastChr = '';
		inst._field = Math.max(0, this._get(inst, 'initialField'));
		if (inst.input.val() != '') {
			this._showDatetime(inst);
		}
	},

	/* Parse the datetime value from the given text.
	   @param  inst   (object) the instance settings
	   @param  value  (string) the value to parse
	   @return  (Date) the retrieved datetime or null if no value */
	_parseDatetime: function(inst, value) {
		if (!value) {
			return null;
		}
		var year = 0,month = 0,day = 0,hour = 0,minute = 0,second = 0,index = 0,datetimeFormat = this._get(inst, 'datetimeFormat'),
			skipNumber = function() {
				while (index < value.length && value.charAt(index).match(/^[0-9]/)) {
					index++;
				}
			},i;
		for (i = 0; i < datetimeFormat.length && index < value.length; i++) {
			var field = datetimeFormat.charAt(i),num = parseInt(value.substring(index), 10);
			if (field.match(/y|o|d|h|m|s/i) && isNaN(num)) {
				throw 'Invalid date';
			}
			num = (isNaN(num) ? 0 : num);
			switch (field) {
				case 'y': case 'Y':
					year = num;
					skipNumber();
					break;
				case 'o': case 'O':
					month = num;
					skipNumber();
					break;
				case 'n': case 'N':
					var monthNames = this._get(inst, field == 'N' ? 'monthNames' : 'monthNamesShort');
					for (var j = 0; j < monthNames.length; j++) {
						if (value.substring(index).substr(0, monthNames[j].length).toLowerCase() ==
								monthNames[j].toLowerCase()) {
							month = j + 1;
							index += monthNames[j].length;
							break;
						}
					}
					break;
				case 'w': case 'W':
					var dayNames = this._get(inst, field == 'W' ? 'dayNames' : 'dayNamesShort');
					for (var j = 0; j < dayNames.length; j++) {
						if (value.substring(index).substr(0, dayNames[j].length).toLowerCase() ==
								dayNames[j].toLowerCase()) {
							index += dayNames[j].length + 1;
							break;
						}
					}
					num = parseInt(value.substring(index), 10);
					num = (isNaN(num) ? 0 : num);
					// Fall through
				case 'd': case 'D':
					day = num;
					skipNumber();
					break;
				case 'h': case 'H':
					hour = num;
					skipNumber();
					break;
				case 'm': case 'M':
					minute = num;
					skipNumber();
					break;
				case 's': case 'S':
					second = num;
					skipNumber();
					break;
				case 'a':
					var ampmNames = this._get(inst, 'ampmNames');
					var pm = (value.substr(index, ampmNames[1].length).toLowerCase() ==
						ampmNames[1].toLowerCase());
					hour = (hour == 12 ? 0 : hour) + (pm ? 12 : 0);
					index += ampmNames[0].length;
					break;
				default:
					index++;
			}
		}
		if (i < datetimeFormat.length) {
			throw 'Invalid date';
		}
		year += (year >= 100 || datetimeFormat.indexOf('y') == -1 ? 0 :
			(year > this._shortYearCutoff(inst) ? 1900 : 2000));
		var fields = this._constrainTime(inst, [hour, minute, second]);
		var date = new Date(year, Math.max(0, month - 1), day, fields[0], fields[1], fields[2]);
		if (datetimeFormat.match(/y|o|n|d|w/i) && (date.getFullYear() != year ||
				date.getMonth() + 1 != month || date.getDate() != day)) {
			throw 'Invalid date';
		}
		return date;
	},

	/* Set the selected date/time into the input field.
	   @param  inst  (object) the instance settings */
	_showDatetime: function(inst) {
		this._setValue(inst, this._formatDatetime(inst, this._get(inst, 'datetimeFormat')));
		this._showField(inst);
	},

	/* Format a date/time as requested.
	   @param  inst    (object) the instance settings
	   @param  format  (string) the date/time format to use
	   @return  (string) the formatted date/time */
	_formatDatetime: function(inst, format) {
		var currentDatetime = '';
		var ampm = format.indexOf('a') > -1;
		for (var i = 0; i < format.length; i++) {
			var field = format.charAt(i);
			switch (field) {
				case 'y':
					currentDatetime += this._formatNumber(inst._selectedYear % 100);
					break;
				case 'Y':
					currentDatetime += this._formatNumber(inst._selectedYear, 4);
					break;
				case 'o': case 'O':
					currentDatetime += this._formatNumber(inst._selectedMonth + 1, field == 'o' ? 1 : 2);
					break;
				case 'n': case 'N':
					currentDatetime += this._get(inst, (field == 'N' ?
						'monthNames' : 'monthNamesShort'))[inst._selectedMonth];
					break;
				case 'd': case 'D':
					currentDatetime += this._formatNumber(inst._selectedDay, field == 'd' ? 1 : 2);
					break;
				case 'w': case 'W':
					currentDatetime += this._get(inst, (field == 'W' ? 'dayNames' : 'dayNamesShort'))
						[new Date(inst._selectedYear, inst._selectedMonth, inst._selectedDay).getDay()] +
						' ' + this._formatNumber(inst._selectedDay);
					break;
				case 'h': case 'H':
					currentDatetime += this._formatNumber(!ampm ? inst._selectedHour :
						inst._selectedHour % 12 || 12, field == 'h' ? 1 : 2);
					break;
				case 'm': case 'M':
					currentDatetime += this._formatNumber(inst._selectedMinute, field == 'm' ? 1 : 2);
					break;
				case 's': case 'S':
					currentDatetime += this._formatNumber(inst._selectedSecond, field == 's' ? 1 : 2);
					break;
				case 'a':
					currentDatetime += this._get(inst, 'ampmNames')[inst._selectedHour < 12 ? 0 : 1];
					break;
				default:
					currentDatetime += field;
					break;
			}
		}
		return currentDatetime;
	},

	/* Highlight the current datetime field.
	   @param  inst  (object) the instance settings */
	_showField: function(inst) {
		var input = inst.input[0];
		if (inst.input.is(':hidden') || $.datetimeEntry._lastInput != input) {
			return;
		}
		var datetimeFormat = this._get(inst, 'datetimeFormat');
		var start = 0;
		for (var i = 0; i < inst._fields[inst._field]; i++) {
			start += this._fieldLength(inst, datetimeFormat.charAt(i));
		}
		var end = start + this._fieldLength(inst, datetimeFormat.charAt(i));
		if (input.setSelectionRange) { // Mozilla
			input.setSelectionRange(start, end);
		}
		else if (input.createTextRange) { // IE
			var range = input.createTextRange();
			range.moveStart('character', start);
			range.moveEnd('character', end - inst.input.val().length);
			range.select();
		}
		if (!input.disabled) {
			input.focus();
		}
	},

	/* Calculate the field length.
	   @param  inst    (object) the instance settings
	   @param  format  (char) the format character
	   @return  (number) the length of this subfield */
	_fieldLength: function(inst, format) {
		switch (format) {
			case 'Y':
				return 4;
			case 'n': case 'N':
				return this._get(inst, (format == 'N' ? 'monthNames' : 'monthNamesShort'))
					[inst._selectedMonth].length;
			case 'w': case 'W':
				return this._get(inst, (format == 'W' ? 'dayNames' : 'dayNamesShort'))
					[new Date(inst._selectedYear, inst._selectedMonth, inst._selectedDay).
					getDay()].length + 3;
			case 'y': case 'O': case 'D': case 'H': case 'M': case 'S':
				return 2;
			case 'o':
				return ('' + (inst._selectedMonth + 1)).length;
			case 'd':
				return ('' + inst._selectedDay).length;
			case 'h':
				return ('' + (inst._ampmField == -1 ?
					inst._selectedHour : inst._selectedHour % 12 || 12)).length;
			case 'm':
				return ('' + inst._selectedMinute).length;
			case 's':
				return ('' + inst._selectedSecond).length;
			case 'a':
				return this._get(inst, 'ampmNames')[0].length;
			default:
				return 1;
		}
	},

	/* Ensure displayed number is a certain length.
	   @param  value   (number) current value
	   @param  length  (number) the minimum length (optional, default 2)
	   @return  (string) number with at least length digits */
	_formatNumber: function(value, length) {
		value = '' + value;
		length = length || 2;
		while (value.length < length) {
			value = '0' + value;
		}
		return value;
	},

	/* Update the input field and notify listeners.
	   @param  inst   (object) the instance settings
	   @param  value  (string) the new value */
	_setValue: function(inst, value) {
		if (value != inst.input.val()) {
			var altField = this._get(inst, 'altField');
			if (altField) {
				$(altField).val(!value ? '' : this._formatDatetime(inst,
					this._get(inst, 'altFormat') || this._get(inst, 'datetimeFormat')));
			}
			inst.input.val(value).trigger('change');
		}
	},

	/* Move to previous/next field, or out of field altogether if appropriate.
	   @param  inst     (object) the instance settings
	   @param  offset   (number) the direction of change (-1, +1)
	   @param  moveOut  (boolean) true if can move out of the field
	   @return  (boolean) true if exitting the field, false if not */
	_changeField: function(inst, offset, moveOut) {
		var atFirstLast = (inst.input.val() == '' ||
			inst._field == (offset == -1 ? 0 : inst._fields.length - 1));
		if (!atFirstLast) {
			inst._field += offset;
		}
		this._showField(inst);
		inst._lastChr = '';
		$.data(inst.input[0], PROP_NAME, inst);
		return (atFirstLast && moveOut);
	},

	/* Update the current field in the direction indicated.
	   @param  inst    (object) the instance settings
	   @param  offset  (number) the amount to change by */
	_adjustField: function(inst, offset) {
		if (inst.input.val() == '') {
			offset = 0;
		}
		var field = this._get(inst, 'datetimeFormat').charAt(inst._fields[inst._field]);
		var year = inst._selectedYear + (field.match(/y/i) ? offset : 0);
		var month = inst._selectedMonth + (field.match(/o|n/i) ? offset : 0);
		var day = (field.match(/d|w/i) ? inst._selectedDay + offset :
			Math.min(inst._selectedDay, this._getDaysInMonth(year, month)));
		var timeSteps = this._get(inst, 'timeSteps');
		var hour = inst._selectedHour + (field.match(/h/i) ? offset * timeSteps[0] : 0) +
			(field == 'a' && offset != 0 ? (inst._selectedHour < 12 ? +12 : -12) : 0);
		var minute = inst._selectedMinute + (field.match(/m/i) ? offset * timeSteps[1] : 0);
		var second = inst._selectedSecond + (field.match(/s/i) ? offset * timeSteps[2] : 0);
		this._setDatetime(inst, new Date(year, month, day, hour, minute, second));
	},

	/* Find the number of days in a given month.
	   @param  year   (number) the full year
	   @param  month  (number) the month (0 to 11)
	   @return  (number) the number of days in this month */
	_getDaysInMonth: function(year, month) {
		return new Date(year, month + 1, 0).getDate();
	},

	/* Check against minimum/maximum and display datetime.
	   @param  inst      (object) the instance settings
	   @param  datetime  (Date) an actual date or
	                     (number) offset in days from now or
					     (string) units and periods of offsets from now */
	_setDatetime: function(inst, datetime) {
		// Normalise to base time
		datetime = this._normaliseDatetime(this._determineDatetime(inst,
			datetime || this._get(inst, 'defaultDatetime')) || new Date());
		var fields = this._constrainTime(inst,
			[datetime.getHours(), datetime.getMinutes(), datetime.getSeconds()]);
		datetime.setHours(fields[0], fields[1], fields[2]);
		var minDatetime = this._normaliseDatetime(
			this._determineDatetime(inst, this._get(inst, 'minDatetime')));
		var maxDatetime = this._normaliseDatetime(
			this._determineDatetime(inst, this._get(inst, 'maxDatetime')));
		var minTime = this._normaliseDatetime(
			this._determineDatetime(inst, this._get(inst, 'minTime')), 'd');
		var maxTime = this._normaliseDatetime(
			this._determineDatetime(inst, this._get(inst, 'maxTime')), 'd');
		// Ensure it is within the bounds set
		datetime = (minDatetime && datetime < minDatetime ? minDatetime :
			(maxDatetime && datetime > maxDatetime ? maxDatetime : datetime));
		if (minTime && this._normaliseDatetime(new Date(datetime.getTime()), 'd') < minTime) {
			this._copyTime(minTime, datetime);
		}
		if (maxTime && this._normaliseDatetime(new Date(datetime.getTime()), 'd') > maxTime) {
			this._copyTime(maxTime, datetime);
		}
		inst._selectedYear = datetime.getFullYear();
		inst._selectedMonth = datetime.getMonth();
		inst._selectedDay = datetime.getDate();
		inst._selectedHour = datetime.getHours();
		inst._selectedMinute = datetime.getMinutes();
		inst._selectedSecond = datetime.getSeconds();
		this._showDatetime(inst);
		$.data(inst.input[0], PROP_NAME, inst);
	},

	/* Copy just the date portion of a date/time.
	   @param  dateFrom  (Date) the date/time to copy from
	   @param  dateTo    (Date) the date/time to copy to */
	_copyDate: function(dateFrom, dateTo) {
		dateTo.setFullYear(dateFrom.getFullYear());
		dateTo.setMonth(dateFrom.getMonth());
		dateTo.setDate(dateFrom.getDate());
	},

	/* Copy just the time portion of a date/time.
	   @param  timeFrom  (Date) the date/time to copy from
	   @param  timeTo    (Date) the date/time to copy to */
	_copyTime: function(timeFrom, timeTo) {
		timeTo.setHours(timeFrom.getHours());
		timeTo.setMinutes(timeFrom.getMinutes());
		timeTo.setSeconds(timeFrom.getSeconds());
	},

	/* A datetime may be specified as an exact value or a relative one.
	   @param  inst     (object) the instance settings
	   @param  setting  (Date) an actual datetime or
	                    (number) offset in seconds/days from now or
	                    (string) units and periods of offsets from now
	   @return  (Date) the calculated datetime */
	_determineDatetime: function(inst, setting) {
		var offsetNumeric = function(offset) { // E.g. +300, -2
			var datetime = new Date();
			datetime.setSeconds(datetime.getSeconds() + offset);
			return datetime;
		};
		var offsetString = function(offset) { // E.g. '+2m', '-4h', '+3h +30m'
			var datetime;
			try { // Check for string in current datetime format
				datetime = $.datetimeEntry._parseDatetime(inst, offset);
				if (datetime) {
					return datetime;
				}
			}
			catch (e) {
				// Ignore
			}
			offset = offset.toLowerCase();
			datetime = new Date();
			var year = datetime.getFullYear(),month = datetime.getMonth(),day = datetime.getDate(),hour = datetime.getHours(),minute = datetime.getMinutes(),second = datetime.getSeconds(),
				pattern = /([+-]?[0-9]+)\s*(s|m|h|d|w|o|y)?/g,matches = pattern.exec(offset);
			while (matches) {
				switch (matches[2] || 's') {
					case 's':
						second += parseInt(matches[1], 10); break;
					case 'm':
						minute += parseInt(matches[1], 10); break;
					case 'h':
						hour += parseInt(matches[1], 10); break;
					case 'd':
						day += parseInt(matches[1], 10); break;
					case 'w':
						day += parseInt(matches[1], 10) * 7; break;
					case 'o':
						month += parseInt(matches[1], 10); break;
					case 'y':
						year += parseInt(matches[1], 10); break;
				}
				matches = pattern.exec(offset);
			}
			return new Date(year, month, day, hour, minute, second);
		};
		return (setting ? (typeof setting == 'string' ? offsetString(setting) :
			(typeof setting == 'number' ? offsetNumeric(setting) : setting)) : null);
	},

	/* Normalise datetime object.
	   @param  datetime  (Date) the original date/time
	   @param  type      (string) 'd' for retain date only, 't' for retain time only, null for neither
	   @return  (Date) the normalised datetime */
	_normaliseDatetime: function(datetime, type) {
		if (!datetime) {
			return null;
		}
		if (type == 'd') {
			datetime.setFullYear(0);
			datetime.setMonth(0);
			datetime.setDate(0);
		}
		if (type == 't') {
			datetime.setHours(0);
			datetime.setMinutes(0);
			datetime.setSeconds(0);
		}
		datetime.setMilliseconds(0);
		return datetime;
	},

	/* Update datetime based on keystroke entered.
	   @param  inst  (object) the instance settings
	   @param  chr   (ch) the new character */
	_handleKeyPress: function(inst, chr) {
		chr = chr.toLowerCase();
		var datetimeFormat = this._get(inst, 'datetimeFormat');
		var datetimeSeps = this._get(inst, 'datetimeSeparators');
		var field = datetimeFormat.charAt(inst._fields[inst._field]);
		var sep = datetimeFormat.charAt(inst._fields[inst._field] + 1);
		sep = ('yYoOnNdDwWhHmMsSa'.indexOf(sep) == -1 ? sep : '');
		if ((datetimeSeps + sep).indexOf(chr) > -1) {
			this._changeField(inst, +1, false);
		}
		else if (chr >= '0' && chr <= '9') { // Allow direct entry of datetime
			var key = parseInt(chr, 10);
			var value = parseInt(inst._lastChr + chr, 10);
			var year = (!field.match(/y/i) ? inst._selectedYear : value);
			var month = (!field.match(/o|n/i) ? inst._selectedMonth + 1 :
				(value >= 1 && value <= 12 ? value : (key > 0 ? key : inst._selectedMonth + 1)));
			var day = (!field.match(/d|w/i) ? inst._selectedDay :
				(value >= 1 && value <= this._getDaysInMonth(year, month - 1) ?
				value : (key > 0 ? key : inst._selectedDay)));
			var hour = (!field.match(/h/i) ? inst._selectedHour : (inst._ampmField == -1 ?
				(value < 24 ? value : key) : (value >= 1 && value <= 12 ? value :
				(key > 0 ? key : inst._selectedHour)) % 12 + (inst._selectedHour >= 12 ? 12 : 0)));
			var minute = (!field.match(/m/i) ? inst._selectedMinute : (value < 60 ? value : key));
			var second = (!field.match(/s/i) ? inst._selectedSecond : (value < 60 ? value : key));
			var fields = this._constrainTime(inst, [hour, minute, second]);
			var shortYearCutoff = this._shortYearCutoff(inst);
			this._setDatetime(inst, new Date(
				year + (year >= 100 || field != 'y' ? 0 : (year > shortYearCutoff ? 1900 : 2000)),
				month - 1, day, fields[0], fields[1], fields[2]));
			inst._lastChr = (field != 'Y' ? '' :
				inst._lastChr.substr(Math.max(0, inst._lastChr.length - 2))) + chr;
		}
		else if (field.match(/n/i)) { // Allow text entry by month name
			inst._lastChr += chr;
			var names = this._get(inst, (field == 'n' ? 'monthNamesShort' : 'monthNames'));
			var findMonth = function() {
				for (var i = 0; i < names.length; i++) {
					if (names[i].toLowerCase().substring(0, inst._lastChr.length) == inst._lastChr) {
						return i;
						break;
					}
				}
				return -1;
			};
			var month = findMonth();
			if (month == -1) {
				inst._lastChr = chr;
				month = findMonth();
			}
			if (month == -1) {
				inst._lastChr = '';
			}
			else {
				var year = inst._selectedYear;
				var day = Math.min(inst._selectedDay, this._getDaysInMonth(year, month));
				this._setDatetime(inst, this._normaliseDatetime(new Date(year, month, day,
					inst._selectedHour, inst._selectedMinute, inst._selectedSecond)));
			}
		}
		else if (inst._ampmField > -1) { // Set am/pm based on first char of names
			var ampmNames = this._get(inst, 'ampmNames');
			if ((chr == ampmNames[0].substring(0, 1).toLowerCase() &&
					inst._selectedHour >= 12) ||
					(chr == ampmNames[1].substring(0, 1).toLowerCase() &&
					inst._selectedHour < 12)) {
				var saveField = inst._field;
				inst._field = inst._ampmField;
				this._adjustField(inst, +1);
				inst._field = saveField;
				this._showField(inst);
			}
		}
	},

	/* Retrieve the short year cutoff value.
	   @param  inst    (object) the instance settings
	   @return  (number) the calculated cutoff year */
	_shortYearCutoff: function(inst) {
		var cutoff = this._get(inst, 'shortYearCutoff');
		if (typeof cutoff == 'string') {
			cutoff = new Date().getFullYear() + parseInt(cutoff, 10);
		}
		return cutoff % 100;
	},

	/* Constrain the given/current time to the time steps.
	   @param  inst    (object) the instance settings
	   @param  fields  (number[3]) the current time components (hours, minutes, seconds)
	   @return  (number[3]) the constrained time components (hours, minutes, seconds) */
	_constrainTime: function(inst, fields) {
		var specified = (fields != null);
		if (!specified) {
			var now = this._determineTime(inst, this._get(inst, 'defaultTime')) || new Date();
			fields = [now.getHours(), now.getMinutes(), now.getSeconds()];
		}
		var reset = false;
		var timeSteps = this._get(inst, 'timeSteps');
		for (var i = 0; i < timeSteps.length; i++) {
			if (reset) {
				fields[i] = 0;
			}
			else if (timeSteps[i] > 1) {
				fields[i] = Math.round(fields[i] / timeSteps[i]) * timeSteps[i];
				reset = true;
			}
		}
		return fields;
	}
});

/* jQuery extend now ignores nulls!
   @param  target  (object) the object to update
   @param  props   (object) the new settings
   @return  (object) the updated object */
function extendRemove(target, props) {
	$.extend(target, props);
	for (var name in props) {
		if (props[name] == null) {
			target[name] = null;
		}
	}
	return target;
}

var getters = ['getDatetime', 'getOffset', 'isDisabled'];

/* Attach the datetime entry functionality to a jQuery selection.
   @param  command  (string) the command to run (optional, default 'attach')
   @param  options  (object) the new settings to use for these countdown instances (optional)
   @return  (jQuery) for chaining further calls */
$.fn.datetimeEntry = function(options) {
	var otherArgs = Array.prototype.slice.call(arguments, 1);
	if (typeof options == 'string' && $.inArray(options, getters) > -1) {
		return $.datetimeEntry['_' + options + 'DatetimeEntry'].
			apply($.datetimeEntry, [this[0]].concat(otherArgs));
	}
	return this.each(function() {
		var nodeName = this.nodeName.toLowerCase();
		if (nodeName == 'input') {
			if (typeof options == 'string') {
				$.datetimeEntry['_' + options + 'DatetimeEntry'].
					apply($.datetimeEntry, [this].concat(otherArgs));
			}
			else {
				// Check for settings on the control itself
				var inlineSettings = ($.fn.metadata ? $(this).metadata() : {});
				$.datetimeEntry._connectDatetimeEntry(
					this, $.extend(inlineSettings, options));
			}
		}
	});
};

/* Initialise the datetime entry functionality. */
$.datetimeEntry = new DatetimeEntry(); // Singleton instance

})(jQuery);

/* Copyright (c) 2009 Brandon Aaron (http://brandonaaron.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 *
 * Version: 3.0.2
 *
 * Requires: 1.2.2+
 */
(function($){
	var mw="mousewheel",a=["DOMMouseScroll",mw];
	$.event.special.mousewheel={
		setup:function(){
			if(this.addEventListener){
				for(var d=a.length;d;){
					this.addEventListener(a[--d],b,false)
				}
			}else{
				this.onmousewheel=b
			}
		},
		teardown:function(){
			if(this.removeEventListener){
				for(var d=a.length;d;){
					this.removeEventListener(a[--d],b,false)
				}
			}else{
				this.onmousewheel=null
			}
		}
	};
	$.fn.extend({
		mousewheel:function(d){
			return d?this.bind(mw,d):this.trigger(mw)
		},
		unmousewheel:function(d){
			return this.unbind(mw,d)
		}
	});
	function b(f){
		var d=[].slice.call(arguments,1),g=0,e=true;
		f=$.event.fix(f||window.event);
		f.type=mw;
		if(f.wheelDelta){
			g=f.wheelDelta/120
		}if(f.detail){
			g=-f.detail/3
		}
		d.unshift(f,g);
		return $.event.handle.apply(this,d);
	}
})(jQuery);
/*
 *
 * Copyright (c) 2011 Cloudgen Wong (<a href="http://www.cloudgen.w0ng.hk">Cloudgen Wong</a>)
 * Licensed under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 */
// version 1.05
// fix the problem of jQuery 1.5 when using .val()
// fix the problem when precision has been set and the input start with decimal dot or comma ,e.g. precision set to 3 and input with ".1234"
var email={tldn:new RegExp("^[^\@]+\@[^\@]+\.(A[C-GL-OQ-UWXZ]|B[ABD-JM-OR-TVWYZ]|C[ACDF-IK-ORUVX-Z]|D[EJKMOZ]|E[CEGR-U]|F[I-KMOR]|G[ABD-IL-NP-UWY]|H[KMNRTU]|I[DEL-OQ-T]|J[EMOP]|K[EG-IMNPRWYZ]|L[A-CIKR-VY]|M[AC-EGHK-Z]|N[ACE-GILOPRUZ]|OM|P[AE-HKL-NR-TWY]|QA|R[EOSUW]|S[A-EG-ORT-VYZ]|T[CDF-HJ-PRTVWZ]|U[AGKMSYZ]|V[ACEGINU]|W[FS]|XN|Y[ETU]|Z[AMW]|AERO|ARPA|ASIA|BIZ|CAT|COM|COOP|EDU|GOV|INFO|INT|JOBS|MIL|MOBI|MUSEUM|NAME|NET|ORG|PRO|TEL|TRAVEL)$","i")};
(function($){
  $.extend($.expr[":"],{
    regex:function(d,a,c){
      var e=new RegExp(c[3],"g");
      var b=("text"===d.type)?d.value:d.innerHTML;
      return(b=="")?true:(e.exec(b))
    }
  });
  $.fn.output=function(d){
    if(typeof d=="undefined")
      return (this.is(":text"))?this.val():this.html();
    else
      return (this.is(":text"))?this.val(d):this.html(d);
  };
  formatter={
    getRegex:function(settings){
      var settings=$.extend({type:"decimal",precision:5,decimal:'.',allow_negative:true},settings);
      var result="";
      if(settings.type=="decimal"){
        var e=(settings.allow_negative)?"-?":"";
        if(settings.precision>0)
          result="^"+e+"\\d+$|^"+e+"\\d*"+settings.decimal+"\\d{1,"+settings.precision+"}$";
        else result="^"+e+"\\d+$"
      }else if(settings.type=="phone-number"){
        result="^\\d[\\d\\-]*\\d$"
      }else if(settings.type=="alphabet"){
        result="^[A-Za-z]+$"
      }
      return result
    },
    isEmail:function(d){
      var a=$(d).output();
      var c=false;
      var e=true;
      var e=new RegExp("[\s\~\!\#\$\%\^\&\*\+\=\(\)\[\]\{\}\<\>\\\/\;\:\,\?\|]+");
      if(a.match(e)!=null){
        return c
      }
      if(a.match(/((\.\.)|(\.\-)|(\.\@)|(\-\.)|(\-\-)|(\-\@)|(\@\.)|(\@\-)|(\@\@))+/)!=null){
        return c
      }
      if(a.indexOf("\'")!=-1){
        return c
      }
      if(a.indexOf("\"")!=-1){
        return c
      }
      if(email.tldn&&a.match(email.tldn)==null){
        return c
      }
      return e
    },
    formatString:function(target,settings){
      var settings=$.extend({type:"decimal",precision:5,decimal:'.',allow_negative:true},settings);
      var oldText=$(target).output();
      var newText=oldText;
      if(settings.type=="decimal"){
        if(newText!=""){
          var g;
          var h=(settings.allow_negative)?"\\-":"";
          var i="\\"+settings.decimal;
          g=new RegExp("[^\\d"+h+i+"]+","g");
          newText=newText.replace(g,"");
          var h=(settings.allow_negative)?"\\-?":"";
          if(settings.precision>0)
            g=new RegExp("^("+h+"\\d*"+i+"\\d{1,"+settings.precision+"}).*");
          else g=new RegExp("^("+h+"\\d+).*");
          newText=newText.replace(g,"$1")
        }
      }else if(settings.type=="phone-number"){
        newText=newText.replace(/[^\-\d]+/g,"").replace(/^\-+/,"").replace(/\-+/,"-")
      }else if(settings.type=="alphabet"){
        newText=newText.replace(/[^A-Za-z]+/g,"")
      }
      if(newText!=oldText)
        $(target).output(newText)
    }
  };
  $.fn.format=function(settings,wrongFormatHandler){
    var settings=$.extend({type:"decimal",precision:5,decimal:".",allow_negative:true,autofix:false},settings);
    var decimal=settings.decimal;
    wrongFormatHandler=typeof wrongFormatHandler=="function"?wrongFormatHandler:function(){};
    this.keypress(function(d){
      $(this).data("old-value",$(this).val());
      var a=d.charCode?d.charCode:d.keyCode?d.keyCode:0;
      if(a==13&&this.nodeName.toLowerCase()!="input"){return false}
      if((d.ctrlKey&&(a==97||a==65||a==120||a==88||a==99||a==67||a==122||a==90||a==118||a==86||a==45))||(a==46&&d.which!=null&&d.which==0))
        return true;
      if(a<48||a>57){
        if(settings.type=="decimal"){
          if(settings.allow_negative&&a==45&&this.value.length==0)return true;
          if(a==decimal.charCodeAt(0)){
            if(settings.precision>0&&this.value.indexOf(decimal)==-1)return true;
            else return false
          }
          if(a!=8&&a!=9&&a!=13&&a!=35&&a!=36&&a!=37&&a!=39){return false}
          return true
        }else if(settings.type=="email"){
          if(a==8||a==9||a==13||(a>34&&a<38)||a==39||a==45||a==46||(a>64&&a<91)||(a>96&&a<123)||a==95){return true}
          if(a==64&&this.value.indexOf("@")==-1)return true;
          return false
        }else if(settings.type=="phone-number"){
          if(a==45&&this.value.length==0)return false;
          if(a==8||a==9||a==13||(a>34&&a<38)||a==39||a==45){return true}
          return false
        }else if(settings.type=="alphabet"){
          if(a==8||a==9||a==13||(a>34&&a<38)||a==39||(a>64&&a<91)||(a>96&&a<123))
          return true
        }else return false
      }else{
        if(settings.type=="alphabet"){
          return false
        }else return true
      }
    })
    .blur(function(){
      if(settings.type=="email"){
        if(!formatter.isEmail(this)){
          wrongFormatHandler.apply(this)
        }
      }else{
        if(!$(this).is(":regex("+formatter.getRegex(settings)+")")){
          wrongFormatHandler.apply(this)
        }
      }
    })
    .focus(function(){
      $(this).select()
    });
    if(settings.autofix){
      this.keyup(function(d){
        if($(this).data("old-value")!=$(this).val())
          formatter.formatString(this,settings)
        }
      )
    }
    return this
  }
})(jQuery);

var calObj = {
	pickerClass: "datepick-jumps",renderer: $j.extend({}, $j.datepick.defaultRenderer,
	{
		picker: $j.datepick.defaultRenderer.picker
				.replace(/\{link:prev\}/, "{link:prevJump}")
				.replace(/\{link:next\}/,"{link:nextJump}")
				.replace(/\{link:close\}/,"")
				.replace(/\{link:today\}/, "")
		}),
		yearRange: "c-5:c+5",
		showTrigger: "#calImg",
		selectDefaultDate: true,
		dateFormat: "dd/mm/yyyy"
};
