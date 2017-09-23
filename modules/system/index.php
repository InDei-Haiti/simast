<br/>
<div class="card">
<?php /* SYSTEM $Id: index.php,v 1.18 2005/02/23 01:19:06 gregorerhardt Exp $ */
if($_GET['mode'] === 'cltmp'){
	$res = dirCleaner($baseDir.'/files/tmp');
	echo ($res === true ? 'ok' : 'fail');
	return;
}

$AppUI->savePlace();

$titleBlock = new CTitleBlock('System Administration', $m.".png", "$m.$a");
$titleBlock->show();
$tempDirSize = bit2text( getDirSize($baseDir.'/files/tmp'));
?>
<!-- <p> -->
<table width="50%" border="0" cellpadding="0" cellspacing="5" align="left">
	<!--
<tr>
	<td>&nbsp;</td>
	<td align="left" class="subtitle">
		<?php echo $AppUI->_('Data Management');?>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=training"><?php echo $AppUI->_('Manage Trainings');?></a>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=system&a=excelexport&suppressHeaders=true&todo=plain"><?php echo $AppUI->_('Export Data');?></a>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=files"><?php echo $AppUI->_('Import Data');?></a>
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=clientmove"><?php echo $AppUI->_('Client Transfers');?><b><?php echo ($lfort > 0 ? ' ( ' . $lfort . ' )' : '');?></b></a>
	</td>
</tr>

<tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=masteredit"><?php echo $AppUI->_('Data Cleaning');?></a>
	</td>
</tr>
 <tr>
	<td>&nbsp;</td>
	<td align="left">
		<a href="?m=analysis"><?php echo $AppUI->_('Reporting');?></a>
	</td>
</tr> -->
<!--Fin data Management-->
	<!--<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td align="left" class="subtitle">
			<?php echo $AppUI->_('Administration');?>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=admin"><?php echo $AppUI->_('Users');?></a>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=system&u=roles"><?php echo $AppUI->_('User Roles');?></a>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=system&a=contacts_ldap"><?php echo $AppUI->_('Import Contacts');?></a>
		</td>
	</tr>
	 <tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=system&a=definition"><?php echo $AppUI->_('REGULAR definition');?></a>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=wizard"><?php echo $AppUI->_('Form Wizard');?></a>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=system&a=staff_pos"><?php echo $AppUI->_('Positions for Staff');?></a>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<A href="?m=system&a=creports"><?php echo $AppUI->_("Public Reports") ?></A>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="#" onclick="cleanTMP()" ><?php echo $AppUI->_("Clean temp dir").' <b> <span id="tmp_size">' . $tempDirSize.' </span></b>'?></a>
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=system&a=cleandups" ><?php echo $AppUI->_("Remove duplicates from LTP") ?></a>
		</td>
	</tr>-->

	<?php
	if ($dPconfig['regular_lvd'] == 0) {
		?>
		<tr>
			<td>&nbsp;</td>
			<td align="left">
				<span onclick="makeLVD();"
				      style="cursor: pointer;"><u><?php echo $AppUI->_('LVD collector');?></u></span>
				<img src="/images/tab_load.gif" style="display:none;" id="load_mon">
			</td>
		</tr>
		<?php
	}
	?>
	<!--<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td width="42">

		</td>
		<td align="left" class="subtitle">
			<?php echo $AppUI->_('Language Support');?>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=system&a=translate"><?php echo $AppUI->_('Translation Management');?></a>
		</td>
	</tr>-->

	<!--<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td>

		</td>
		<td align="left" class="subtitle">
			<?php echo $AppUI->_('Preferences');?>
		</td>
	</tr>


	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=system&a=systemconfig"><?php echo $AppUI->_('System Configuration');?></a>
			<br/><a href="?m=system&a=addeditpref"><?php echo $AppUI->_('Default User Preferences');?></a>
			<br/><a href="?m=system&u=syskeys&a=keys"><?php echo $AppUI->_('System Lookup Keys');?></a>
			<br/><a href="?m=system&u=syskeys"><?php echo $AppUI->_('System Lookup Values');?></a>
			<br/><a href="?m=system&a=sareas"><?php echo $AppUI->_( 'Strategic Areas' );?></a>
			<br/><a href="?m=companies"><?php echo $AppUI->_( 'Agencies' );?></a>
			<br/><a href="?m=system&a=custom_field_editor"><?php echo $AppUI->_('Custom Field Editor');?></a>
			<br/><a href="?m=system&a=billingcode"><?php echo $AppUI->_('Billing Code Table');?></a>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
	</tr>

	<tr>
		<td>

		</td>
		<td align="left" class="subtitle">
			<?php echo $AppUI->_('Modules');?>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td align="left">
			<a href="?m=system&a=viewmods"><?php echo $AppUI->_('View Modules');?></a>
		</td>
	</tr>-->


<!-- Debut ajout tabs with Bootstrap -->
<ul class="nav nav-tabs">
  <li class="active"><a data-toggle="tab" href="#home"><strong><?php echo $AppUI->_('Administration');?></strong></a></li>
  <li><a data-toggle="tab" href="#menu1"><strong><?php echo $AppUI->_('Language Support');?></strong></a></li>
  <li><a data-toggle="tab" href="#menu2"><strong><?php echo $AppUI->_('Preferences');?></strong></a></li>
	<li><a data-toggle="tab" href="#menu3"><strong><?php echo $AppUI->_('Modules');?></strong></a></li>
</ul>

<div class="tab-content">
  <div id="home" class="tab-pane fade in active">
		<div class="rpa_row">
			<h2><?php echo $AppUI->_('Administration');?></h2>

			<div class="col-xs-6 col-md-3">

					 <div class="panel status panel-primary">
							 <div class="panel-heading">
									 <h1 class="panel-title text-center"><i class="fa fa-users"></i></h1>
							 </div>
							 <div class="panel-body text-center">
									 <strong><a href="?m=admin"><?php echo $AppUI->_('Users');?></a></strong>
							 </div>
					 </div>

			 </div>

			 <div class="col-xs-6 col-md-3">

						<div class="panel status panel-primary">
								<div class="panel-heading">
										<h1 class="panel-title text-center"><i class="fa fa-key"></i></h1>
								</div>
								<div class="panel-body text-center">
										<strong><a href="?m=system&u=roles"><?php echo $AppUI->_('User Roles');?></a></strong>
								</div>
						</div>

				</div>

			 <div class="col-xs-6 col-md-3">

						<div class="panel status panel-primary">
								<div class="panel-heading">
										<h1 class="panel-title text-center"><i class="fa fa-book"></i></h1>
								</div>
								<div class="panel-body text-center">
										<strong><a href="?m=system&a=contacts_ldap"><?php echo $AppUI->_('Import Contacts');?></a></strong>
								</div>
						</div>

				</div>

				<div class="col-xs-6 col-md-3">

						 <div class="panel status panel-primary">
								 <div class="panel-heading">
										 <h1 class="panel-title text-center"><i class="fa fa-table"></i></h1>
								 </div>
								 <div class="panel-body text-center">
										 <strong><a href="?m=wizard"><?php echo $AppUI->_('Form Wizard');?></a></strong>
								 </div>
						 </div>

				 </div>

				 <div class="col-xs-6 col-md-3">

							<div class="panel status panel-primary">
									<div class="panel-heading">
											<h1 class="panel-title text-center"><i class="fa fa-book"></i></h1>
									</div>
									<div class="panel-body text-center">
											<strong><a href="?m=system&a=staff_pos"><?php echo $AppUI->_('Positions for Staff');?></a></strong>
									</div>
							</div>

					</div><br /> <br /><br />

					<div class="col-xs-6 col-md-3">

							 <div class="panel status panel-primary">
									 <div class="panel-heading">
											 <h1 class="panel-title text-center"><i class="fa fa-circle"></i></h1>
									 </div>
									 <div class="panel-body text-center">
											 <strong><a href="?m=system&a=creports"><?php echo $AppUI->_("Public Reports") ?></a></strong>
									 </div>
							 </div>

					 </div>

					 <div class="col-xs-6 col-md-3">

								<div class="panel status panel-primary">
										<div class="panel-heading">
												<h1 class="panel-title text-center"><i class="fa fa-book"></i></h1>
										</div>
										<div class="panel-body text-center">
												<strong><a href="#" onclick="cleanTMP()" ><?php echo $AppUI->_("Clean temp dir").' <b> <span id="tmp_size">' . $tempDirSize.' </span></b>'?></a></strong>
										</div>
								</div>

						</div>

						<div class="col-xs-6 col-md-3">

								 <div class="panel status panel-primary">
										 <div class="panel-heading">
												 <h1 class="panel-title text-center"><i class="fa fa-book"></i></h1>
										 </div>
										 <div class="panel-body text-center">
												 <strong><a href="?m=system&a=cleandups" ><?php echo $AppUI->_("Remove duplicates from LTP") ?></a></strong>
										 </div>
								 </div>

						 </div>



		</div>
  </div>
  <div id="menu1" class="tab-pane fade">
		<div class="rpa_row">
			<h2><?php echo $AppUI->_('Language Support');?></h2>
			<div class="col-xs-6 col-md-3">

					 <div class="panel status panel-primary">
							 <div class="panel-heading">
									 <h1 class="panel-title text-center"><i class="fa fa-book"></i></h1>
							 </div>
							 <div class="panel-body text-center">
									 <strong><a href="?m=system&a=translate"><?php echo $AppUI->_('Translation Management');?></a></strong>
							 </div>
					 </div>

			 </div>
		</div>

  </div>
  <div id="menu2" class="tab-pane fade">
			<div class="rpa_row">
				<h2><?php echo $AppUI->_('Preferences');?></h2>
				<div class="col-xs-6 col-md-3">

						 <div class="panel status panel-primary">
								 <div class="panel-heading">
										 <h1 class="panel-title text-center">XX</h1>
								 </div>
								 <div class="panel-body text-center">
										 <strong><a href="?m=system&a=systemconfig"><?php echo $AppUI->_('System Configuration');?></a></strong>
								 </div>
						 </div>

				 </div>

				 <div class="col-xs-6 col-md-3">

	 					 <div class="panel status panel-primary">
	 							 <div class="panel-heading">
	 									 <h1 class="panel-title text-center">XX</h1>
	 							 </div>
	 							 <div class="panel-body text-center">
	 									 <strong><a href="?m=system&a=addeditpref"><?php echo $AppUI->_('Default User Preferences');?></a></strong>
	 							 </div>
	 					 </div>

	 			 </div>

				 <div class="col-xs-6 col-md-3">

	 					 <div class="panel status panel-primary">
	 							 <div class="panel-heading">
	 									 <h1 class="panel-title text-center">XX</h1>
	 							 </div>
	 							 <div class="panel-body text-center">
	 									 <strong><a href="?m=system&u=syskeys&a=keys"><?php echo $AppUI->_('System Lookup Keys');?></a></strong>
	 							 </div>
	 					 </div>

	 			 </div>

				 <div class="col-xs-6 col-md-3">

	 					 <div class="panel status panel-primary">
	 							 <div class="panel-heading">
	 									 <h1 class="panel-title text-center">XX</h1>
	 							 </div>
	 							 <div class="panel-body text-center">
	 									 <strong><a href="?m=system&u=syskeys"><?php echo $AppUI->_('System Lookup Values');?></a></strong>
	 							 </div>
	 					 </div>

	 			 </div>

				 <div class="col-xs-6 col-md-3">

	 					 <div class="panel status panel-primary">
	 							 <div class="panel-heading">
	 									 <h1 class="panel-title text-center">XX</h1>
	 							 </div>
	 							 <div class="panel-body text-center">
	 									 <strong><a href="?m=system&a=sareas"><?php echo $AppUI->_( 'Strategic Areas' );?></a></strong>
	 							 </div>
	 					 </div>

	 			 </div>

				 <div class="col-xs-6 col-md-3">

	 					 <div class="panel status panel-primary">
	 							 <div class="panel-heading">
	 									 <h1 class="panel-title text-center">XX</h1>
	 							 </div>
	 							 <div class="panel-body text-center">
	 									 <strong><a href="?m=companies"><?php echo $AppUI->_( 'Agencies' );?></a></strong>
	 							 </div>
	 					 </div>

	 			 </div>

				 <div class="col-xs-6 col-md-3">

	 					 <div class="panel status panel-primary">
	 							 <div class="panel-heading">
	 									 <h1 class="panel-title text-center">XX</h1>
	 							 </div>
	 							 <div class="panel-body text-center">
	 									 <strong><a href="?m=system&a=custom_field_editor"><?php echo $AppUI->_('Custom Field Editor');?></a></strong>
	 							 </div>
	 					 </div>

	 			 </div>

				 <div class="col-xs-6 col-md-3">

	 					 <div class="panel status panel-primary">
	 							 <div class="panel-heading">
	 									 <h1 class="panel-title text-center">XX</h1>
	 							 </div>
	 							 <div class="panel-body text-center">
	 									 <strong><a href="?m=system&a=billingcode"><?php echo $AppUI->_('Billing Code Table');?></a></strong>
	 							 </div>
	 					 </div>

	 			 </div>
			</div>
  </div>

	<div id="menu3" class="tab-pane fade">
		<div class="rpa_row">
			<h2><?php echo $AppUI->_('Modules');?></h2>
			<div class="col-xs-6 col-md-3">

					<div class="panel status panel-primary">
							<div class="panel-heading">
									<h1 class="panel-title text-center">XX</h1>
							</div>
							<div class="panel-body text-center">
									<strong><a href="?m=system&a=viewmods"><?php echo $AppUI->_('View Modules');?></a></strong>
							</div>
					</div>

			</div>
		</div>

  </div>
</div>

</table>
<!-- </p> -->
<script>
	var ran_clean = false;
	function makeLVD() {
		var $icon = $j("#load_mon").show();
		$j.get("?m=manager&a=centers&suppressHeaders=1", function (msg) {
			if (msg == 'ok') {
				$icon.closest("td").html("<span>LVD data collected.</span>").closest("tr").fadeOut(4000);
			}
		});
	}

	function cleanTMP(){
		if(ran_clean === true){
			return;
		}
		$j("<img/>",{src : "/images/zload.gif", id: "load_clean"}).insertAfter("#tmp_size");
		$j.get("?m=system&mode=cltmp&suppressHeaders=1",function(msg){
			if(msg || msg == 'fail'){
				if(msg == 'ok'){
					$j("#tmp_size").text("- empty");
					$j("#load_clean").hide();
					info("Temp directory cleaned",1);
					ran_clean = true;
				}
			}else{
				info("Failed to clean temp files", 0);
			}
		});
	}
</script>
</div>
