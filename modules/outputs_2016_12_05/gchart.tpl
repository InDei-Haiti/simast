<!-- <link rel="stylesheet" type="text/css" href="/modules/outputs/jquery-ui.css"/>
<link rel="stylesheet" type="text/css" href="/modules/outputs/gchart.css"/>
<script type="text/javascript" src="/modules/outputs/gchart.js"></script>
<script type="text/javascript" src="/modules/outputs/jquery9.min.js"></script>
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAXBklWqmHO8dubF66h9VEBlRQnuLS9P_g&sensor=false"></script>
<div id="awrapper">
	<div id="bset">
		<h3 style="padding: 5px;">Projects</h3>

		<div id="bparts">
			<p>
				@@PROJECTS@@
			</p>
		</div>
		<h3 style="padding: 5px;">
			Activity
		</h3>

		<div id="filters">
			<!--<div class="dataset">
				<select class="area_select">
					<option value="-1" selected>-- Select --</option>
					@@datasets@@
				</select>

			</div>-->
			<div class="list" id="tasks">
				
			</div>
		</div>
		<h3 style="padding: 5px;"> Forms</h3>

		<div>
			<div>
			
				<select class="instance_select" id="forms">
						q
				</select>

			</div>
			<ul id="fields">

			</ul>
		</div>
	</div>
	<div id="chart_div" style="width: 700px; height: 400px;float: right;">

	</div>
</div>
<div style="float: left; clear: left;">
	<button id="doChart" class="button_link">Map</button>
</div>

<div id="bay" style="display: none;">
	<div class="loadingz">
		<img src="/modules/outputs/images/report-load.gif">
	</div>

	<div id="fld_tpl">
		<li>
			<label><input type="checkbox" name="{{=it.fld}}" data-forms="{{=it.tbl}}">
				<span>{{=it.title}}</span>
			</label>
		</li>
	</div>
</div>
<script>
	window.onload = up;
	function up() {
		charter.init();
	}
	var alevels = @@alevels@@;
</script>