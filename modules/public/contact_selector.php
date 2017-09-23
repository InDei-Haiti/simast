<?php
  $company_id 			= dPgetParam($_REQUEST, "company_id", 0);
  $contact_id 			= dPgetParam($_POST, "contact_id", 0);
  $call_back 			= dPgetParam($_GET, "call_back", NULL);
  $contacts_submited 	= dPgetParam($_POST, "contacts_submited", 0);
  $selected_contacts_id = dPgetParam($_GET, "selected_contacts_id", "");
  
  //print "post\n";
  //print_r($_POST);
  //print "\n";
  
  //print "get\n";
  //print_r($_GET);
  //print "\n";
  //var_dump($call_back);
	
  if ($contacts_submited == 1)
  {
      $contacts_id = "";
	  
	  if (is_array($contact_id))
	  {
		  
	      $contacts_id = implode(",", $contact_id);
		  print "contacts_id:";
		  print_r($contacts_id);
	  }
	  
	  $call_back_string = !is_null($call_back) ? "window.opener.$call_back('$contacts_id');" : "";
	  
?>
	<script language="javascript">
		<?php echo $call_back_string;  ?>
		self.close();
	</script>
<?php
  }
  
  $contacts_id = explode(",", $selected_contacts_id	);
  
  /*if (! $company_id)
  {
	//show contacts from all allowed companies
	require_once ($AppUI->getModuleClass('companies'));
	
	$oCpy = new CCompany();
	$aCpies = $oCpy->getAllowedRecords($AppUI->user_id, "company_id, company_name","company_name");
	
	$where = "company_id = '' OR (company_id IN ('" .
           implode('\',\'' , array_keys($aCpies)) .
           "'))";
	$company_name = $AppUI->_('Allowed Companies');
	//$where = "";
  }
  else
  {
     //contacts for this company only
	 $sql = "select c.company_name from companies c where c.company_id = $company_id";
	 $company_name = db_loadResult($sql);
	 //$where = " ( contact_company = '$company_name' or contact_company = '$company_id' )";
	 $where = "(company_id = $company_id)";
  }*/
  	
	$q =& new DBQuery;
	$q->addTable('contacts', 'a');
	//$q->leftJoin('company_contacts', 'b', 'company_contacts_contact_id = contact_id');
	//$q->leftJoin('companies', 'c', 'company_contacts_company_id = company_id');
	$q->addQuery('contact_id, contact_first_name, contact_last_name');
	//$q->addQuery('company_name');
	//$q->addWhere($where);
	//$q->addOrder("company_name"); // May need to review this.
	$q->addOrder('contact_last_name');
	$q->addWhere('contact_id <> "13"');

	$sql = $q->prepare();

	
	$contacts = $q->loadHashList("contact_id");
?>

<h2><?php echo $AppUI->_("Contacts") ;
// for $company_name 
?></h2>

<form action='index.php?m=public&a=contact_selector&dialog=1&<?php if (!is_null($call_back)) echo "call_back=$call_back&"; ?>company_id=<?php echo $company_id;?>' method='post' name='frmContactSelect'>
<?php
   $actual_company = "";   
   /*if (!$company_id)
   {

      $companies_names = array(0 => $AppUI->_("Select a company")) + $aCpies;
	  echo arraySelect($companies_names, "company_id", "onchange=\"document.frmContactSelect.contacts_submited.value=0; document.frmContactSelect.submit();\"", 0) . "<hr />";
   }
   else
   {*/

//      <a href='index.php?m=public&a=contact_selector&dialog=1&<?php if (!is_null($call_back)) echo "call_back = $call_back&;" '><?php echo $AppUI->_("View all allowed companies"); </a>

	     foreach ($contacts as $contact_id => $contact_data)
		 {
			/*if (! $contact_data["company_name"])
				$company_name = $AppUI->_("Unassigned");
			else
				$company_name = $contact_data["company_name"];
	   
			if($company_name  && $company_name != $actual_company)
			{
				echo "<h4>$company_name</h4>";
				$actual_company = $company_name;
			}*/
			$checked = in_array($contact_id, $contacts_id) ? "checked" : "";
	   
			echo "<label><input type='checkbox' name='contact_id[]' value='$contact_id' $checked />";
			echo $contact_data["contact_first_name"] . " " . $contact_data["contact_last_name"];
			echo "</label><br />";
		}

   //}
 ?>
 <hr />
 <input name='contacts_submited' type='hidden' value='1' />
 <input type='submit' value='<?php echo $AppUI->_("Continue"); ?>' class='button' />
</form>	