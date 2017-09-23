<?php
global $AppUI,$dPconfig,$loadFromTab;
global $AppUI, $company_id, $obj,$tab;

require_once $AppUI->getModuleClass('contacts');

if (isset($obj))
	$contacts = $obj->getContacts();

if (count($contacts) > 0 && $company_id > 0)
{
	$contactsList = implode(",", $contacts);
	$q = new DBQuery;
	$q->addTable('client_contacts', 'cc');
	$q->leftJoin ('contact_types', 'ct', 'ct.typ_id = cc.client_contacts_contact_type');
	$q->leftJoin ('contacts', 'c', 'c.contact_id = cc.company_contacts_contact_id');
	$q->addQuery ('c.contact_id, c.contact_title, c.contact_first_name, c.contact_last_name, c.contact_email, ct.typ_desc');
	$q->addWhere("cc.client_contacts_client_id = $client_id");
	$q->addWhere("cc.client_contacts_contact_id in ($contactsList)");


	$w='';
/*
SELECT c.contact_first_name, c.contact_last_name, c.contact_email, ct.typ_desc
FROM company_contacts cc
LEFT JOIN contact_types ct ON ct.typ_id = cc.company_contacts_contact_type
LEFT JOIN contacts c ON c.contact_id = cc.company_contacts_contact_id
WHERE cc.company_contacts_company_id =140
LIMIT 0 , 30*/
	$sql= $q->prepare();
	//print_r($sql);
	$q->clear();
	$contacts = db_loadList( $sql );
	
}
?>
<script language="javascript">
var contact_unique_update = document.editFrm.insert_id.value;
function popAddContacts() 
{
	window.open('./index.php?m=public&a=contact_adder&type_ui_active=1&dialog=1&contact_unique_update='+contact_unique_update, 'contacts','height=600,width=800,resizable,scrollbars=yes');
}
</script>

<table width="100%" border="0" cellpadding="2" cellspacing="1" class="tbl">
<form name="contactsFrm" action="?m=companies&u=blue&a=addedit_ks&company_id=<?php echo $company_id; ?>" method="post">
<?php
if (count($contacts) == 0)
{
  echo $AppUI->_('No data available') . '<br />' .  $AppUI->getMsg();
}
else
{
?>
<tr>
	<th><?php echo $AppUI->_( 'Name' );?></td>
	<th><?php echo $AppUI->_( 'Contact e-mail' );?></td>
	<th><?php echo $AppUI->_( 'Contact Role' );?></td>
</tr>
<?php
	foreach ($contacts as $contact)
    {
        
		$w .= '<tr><td>';
		$w .= '<a href="./index.php?m=contacts&a=addedit&contact_id='.$contact["contact_id"].'&company_id='.$company_id. '">'. $contact["contact_title"].  $contact["contact_first_name"]." ". $contact["contact_last_name"].'</a>';
		$w .= '<td><a href="mailto:'.$contact["contact_email"] .'">' .$contact["contact_email"] .'</a></td>';
		//$w .= '<td>'.$dept_detail['dept_name'] .'</td>';
		$w .= '<td>'.$contact["typ_desc"] .'</td>';
		$w .= '</tr>';
	}
}


	$w .= '<tr><td colspan="3" align="right" valign="top" style="background-color:#ffffff">';
	$w .= '<input type="button" class=button value="'.$AppUI->_( 'add new contact' ).'" onClick="javascript:popAddContacts()">';
	$w .= '&nbsp;<input type="button" class=button value="'.$AppUI->_( 'select pre-existing contact' ).'" onClick="javascript:window.location=\'./index.php?m=contacts&a=addedit&client_id='.$client_id.'&client_name='.$obj->client_name.'\'">';
	$w .= '</td></tr>';
	echo $w;
?>
</form>
</table>
<script language="javascript">
 subForm.push(new FormDefinition(<?php echo $tab;?>, document.contactsFrm, checkDetail, saveDetail));
</script>