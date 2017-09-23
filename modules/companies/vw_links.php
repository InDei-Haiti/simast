<?php /* COMPANIES $Id: vw_active.php 5443 2007-10-18 14:27:11Z nybod $ */
if (! defined ( 'DP_BASE_DIR' )) {
	die ( 'You should not access this file directly.' );
}

##
##	Companies: View Projects sub-table
##
GLOBAL $AppUI, $company_id, $pstatus, $dPconfig,$m;

if($company_id > 0){
	$f2=array($company_id);
	require( DP_BASE_DIR . '/modules/links/agency_tab.links.php' );
	
}


?>