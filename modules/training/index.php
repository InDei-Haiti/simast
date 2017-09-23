<?php
$AppUI->savePlace ();
//require_once ($AppUI->getModuleClass('activity'));
require_once ("activity.class.php");
// retrieve any state parameters
if (isset ( $_GET ['orderby'] )) {
	$orderdir = $AppUI->getState ( 'TrainingIdxOrderDir' ) ? ($AppUI->getState ( 'TrainingIdxOrderDir' ) == 'asc' ? 'desc' : 'asc') : 'desc';
	$AppUI->setState ( 'TrainingIdxOrderBy', $_GET ['orderby'] );
	$AppUI->setState ( 'TrainingIdxOrderDir', $orderdir );
}
$orderby = $AppUI->getState ( 'TrainingIdxOrderBy' ) ? $AppUI->getState ( 'TrainingIdxOrderBy' ) : 'training_name';
$orderdir = $AppUI->getState ( 'TrainingIdxOrderDir' ) ? $AppUI->getState ( 'TrainingIdxOrderDir' ) : 'asc';
$canEdit = $perms->checkModuleItem ( $m, 'edit' );
if (isset ( $_GET ['where'] )) {
	$AppUI->setState ( 'TrainingIdxWhere', $_GET ['where'] );
}

$orderby_act = $AppUI->getState ( 'ActivityIdxOrderBy' ) ? $AppUI->getState ( 'ActivityIdxOrderBy' ) : 'activity_description';
$orderdir_act = $AppUI->getState ( 'ActivityIdxOrderDir' ) ? $AppUI->getState ( 'ActivityIdxOrderDir' ) : 'asc';
// retrieve any state parameters


if ($AppUI->user_type != 1) //not admin user type
{
	//get allowed clinics if user is not an admin
	$q = new DBQuery ();
	$q->addTable ( "users" );
	$q->addQuery ( "users.user_clinics" );
	$q->addWhere ( "users.user_id = " . $AppUI->user_id );
	$allowedClinics = $q->loadResult ();

}

// load the centers
$q = new DBQuery ();
$q->addTable ( 'clinics' );
$q->addQuery ( 'clinic_id, clinic_name' );
if (! empty ( $allowedClinics ))
	$q->addWhere ( 'clinic_id IN (' . $allowedClinics . ')' );
$q->addOrder ( 'clinic_name' );

$types = dPgetSysVal ( 'CurriculumTypes' );
$act_types = array ();

// get any records denied from viewing
$obj = new CTraining ();
$deny = $obj->getDeniedRecords ( $AppUI->user_id );

$obj_act = new CActivity ();
$deny_act = $obj_act->getDeniedRecords ( $AppUI->user_id );

$canEdit = ! getDenyEdit ( $m );
// retrieve list of records


$perms = & $AppUI->acl ();

if (isset ( $_GET ['tab'] )) {
	$AppUI->setState ( 'TrainingIdxTab', $_GET ['tab'] );
}
$trainingTypeTab = defVal ( $AppUI->getState ( 'TrainingIdxTab' ), 0 );

// $tabTypes = array(getCompanyTypeID('Client'), getCompanyTypeID('Supplier'), 0);
$trainingType = $trainingTypeTab;

$let = ':';

$q = new DBQuery ();
if (isset ( $trainingTypeTab ) && $trainingTypeTab > 0) {
	$q->addTable ( 'activity' );
	$q->addQuery ( "DISTINCT UPPER(SUBSTRING(activity_description,1,1)) as L" );
	$titleText = "Activities";
} else {
	$q->addTable ( 'trainings' );
	$q->addQuery ( "DISTINCT UPPER(SUBSTRING(training_name,1,1)) as L" );
	$titleText = "Trainings";
}

/*
if ($trainingType > 0)
{
	if ($types[$trainingType] == NULL) 
	{
		$q->addWhere("training_status <> 1");
	}
	else
	{
		$q->addWhere("ci.counselling_clinic = $trainingType");
	}
}
*/

$arr = $q->loadList ();
/*
foreach ( $arr as $L ) {
	$let .= $L ['L'];
}

$a2z = "\n<table cellpadding=\"2\" cellspacing=\"1\" border=\"0\">";
$a2z .= "\n<tr>";
$a2z .= "<td width='100%' align='right'>" . $AppUI->_ ( 'Show' ) . ": </td>";
$a2z .= '<td><a href="./index.php?m=training&where=0">' . $AppUI->_ ( 'All' ) . '</a></td>';

for($c = 65; $c < 91; $c ++) {
	
	$cu = chr ( $c );
	$cell = strpos ( $let, "$cu" ) > 0 ? "<a href=\"?m=training&where=$cu\">$cu</a>" : "<font color=\"#999999\">$cu</font>";
	
	if ($cu == $where)
		$a2z .= "\n\t<td class=\"selected\">$cell</td>";
	else
		$a2z .= "\n\t<td>$cell</td>";
}

$a2z .= "\n</tr>\n<tr><td colspan='28'>$form</td></tr></table>";

// setup the title block
$titleBlock = new CTitleBlock ( $titleText, NULL, $m, "$m.$a" );
$titleBlock->addCell ( $a2z );

$titleBlock->addCell ( "<form name='searchform' action='?m=search' method='post'>
                        <table>
                         <tr>
                          <td>						   
                              <input class = 'text' type='text' name ='search_string' value='$search_string' />
						   </td>
						   <td>
							  <input type='submit' value='" . $AppUI->_ ( 'search' ) . "' class='button' />
						   </td>
						  </tr>
                         </table>
                        </form>" );
if ($canEdit) {
	/*$titleBlock->addCell();
	$titleBlock->addCell(
		'<input type="submit" class="button" value="'.$AppUI->_('new training').'" />', '',
		'<form action="?m=training&a=add" method="post">', '</form>'
	); */
//}

/*$titleBlock->addCell("<form name='searchform' action='?m=search&amp;search_string=$search_string' method='post'>
						<table>
							<tr>
                      			<td>
                                    <strong>".$AppUI->_('Search')."</strong>
                                    <input class='text' type='text' name='search_string' value='$search_string' /><br />
							</tr>
						</table>
                      </form>");
*/
//$search_string = addslashes ( $search_string );
//$titleBlock->show ();

$tabBox = new CTabBox ( "?m=training", dPgetConfig ( 'root_dir' ) . "/modules/training/", $trainingTypeTab );

if ($tabbed = $tabBox->isTabbed ()) {
	$add_na = false;
	if (isset ( $types [0] )) { // They have a Not Applicable entry.
		$add_na = false;
		$types [] = $types [0];
	}
	//$types = arrayMerge(array(0=>"All Trainings"), $types);
	$types = array (0 => "All Trainings" );
	if ($add_na) {
		$types [] = "Not Applicable";
	}
}

$add_na = false;
if (isset ( $act_types [0] )) { // They have a Not Applicable entry.
	$add_na = false;
	$act_types [] = $act_types [0];
}
$act_types = arrayMerge ( array (0 => "All Activities" ), $act_types );
//$types = array(0=>"All Activities");
if ($add_na) {
	$act_types [] = "Not Applicable";
}

//pager settings
$page = dPgetParam ( $_GET, 'page', 1 );
$limit = intval ( $dPconfig ['max_limit'] );

$type_filter = array ();

foreach ( $act_types as $type => $type_name ) {
	$type_filter [] = $type;
	
	if (strncmp ( $type_name, "Not Applicable", strlen ( "Not Applicable" ) ) == 0)
		$type = NULL;
	if (strncmp ( $type_name, "Not Active", strlen ( "Not Active" ) ) == 0)
		$type = 99;
	
	$tabBox->add ( "vw_activities_new", $type_name . " (" . $obj_act->getCount ( $type ) . ") " );
}

foreach ( $types as $type => $type_name ) {
	$type_filter [] = $type;
	
	if (strncmp ( $type_name, "Not Applicable", strlen ( "Not Applicable" ) ) == 0)
		$type = NULL;
	if (strncmp ( $type_name, "Not Active", strlen ( "Not Active" ) ) == 0)
		$type = 99;
	
	$tabBox->add ( "vw_trainings_new", $type_name . " (" . $obj->getCount ( $type ) . ") " );
}

$type_filter = array ();

$tabBox->show ();

