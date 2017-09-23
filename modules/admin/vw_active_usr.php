<?php /* ADMIN $Id: vw_active_usr.php 4885 2007-04-12 17:32:28Z caseydk $ */
if (!defined('DP_BASE_DIR')){
	die('You should not access this file directly.');
}

require_once( $AppUI->getModuleClass( 'companies' ) );
GLOBAL $dPconfig, $canEdit, $stub, $where, $orderby;
$perms = & $AppUI->acl ();
$q  = new DBQuery;
$q->addTable('users', 'u');
$q->addQuery('DISTINCT(user_id), user_username,user_create_by,user_type, contact_last_name, contact_first_name,
	permission_user, contact_email, company_name, contact_company');
$q->addJoin('contacts', 'con', 'user_contact = contact_id');
$q->addJoin('companies', 'com', 'contact_company = company_id');
$q->addJoin('permissions', 'per', 'u.user_id = permission_user');

//$q->addWhere('pf.user_id='.$AppUI->user_id);
//$q->addWhere('pf.form=task_id');
//$q->addWhere('pf.module="activity"');
//$option = $perms->getAcoIdByValue('view');
//$q->addWhere('find_in_set("'.$option.'",replace(type, " ", ","))');

$obj = new CCompany();
$companies = $obj->getAllowedRecords( $AppUI->user_id, 'company_id,company_name', 'company_name' );
if (count($companies) > 0) {
    $companyList = '0';
    foreach($companies as $k => $v) {
    	$companyList .= ', '.$k;
    }
    $q->addWhere('user_company in (' . $companyList . ')'); 
}

if ($stub) {
	$q->addWhere("(UPPER(user_username) LIKE '$stub%' or UPPER(contact_first_name) LIKE '$stub%' OR UPPER(contact_last_name) LIKE '$stub%')");
} else if ($where) {
	$where = $q->quote("%$where%");
	$q->addWhere("(UPPER(user_username) LIKE $where or UPPER(contact_first_name) LIKE $where OR UPPER(contact_last_name) LIKE $where)");
}

$q->addOrder($orderby);
$users = $q->loadList();
$canLogin = true;

require DP_BASE_DIR.'/modules/admin/vw_usr.php';
?>
