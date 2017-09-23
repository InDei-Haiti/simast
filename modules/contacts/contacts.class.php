<?php 
/**
 *	@package dotProject
 *	@subpackage modules
 *	@version $Revision: 1.14.2.3 $
*/

require_once( $AppUI->getSystemClass ('dp' ) );


$roleLimits = array();
/**
* Contacts class
*/
class CContact extends CDpObject{
	/** @var int */
	var $contact_id = NULL;
	/** @var string */
	var $contact_first_name = NULL;
	/** @var string */
	var $contact_last_name = NULL;
	var $contact_order_by = NULL;
	var $contact_title = NULL;
	var $contact_job = NULL;
	var $contact_company = NULL;
	var $contact_department = NULL;
	var $contact_type = NULL;
	var $contact_email = NULL;
	var $contact_email2 = NULL;
	var $contact_phone = NULL;
	var $contact_phone2 = NULL;
	var $contact_fax = NULL;
	var $contact_mobile = NULL;
	var $contact_address1 = NULL;
	var $contact_address2 = NULL;
	var $contact_city = NULL;
	var $contact_state = NULL;
	var $contact_zip = NULL;
	var $contact_url = NULL;
	var $contact_notes = NULL;
	var $contact_project = NULL;
	var $contact_country = NULL;
	var $contact_icon = NULL;
	var $contact_owner = NULL;
	var $contact_private = NULL;
	var $contact_active = NULL;


	
	function setRoleViewLimits($limit,$offset){
		global $roleLimits;
		$roleLimits['limit']=$limit;
		$roleLimits['offset']=$offset;
	}
	
	function CContact() {
		$this->CDpObject( 'contacts', 'contact_id' );
	}

	function check() {
		//if ($this->contact_id === NULL) {
		//return 'contact id is NULL';
		//}
		// ensure changes of state in checkboxes is captured
		//$this->contact_private = intval( $this->contact_private );
		if (empty($this->contact_private))
		{
			$this->contact_private = '0';
		}
		if (empty($this->contact_owner))
		{
			$this->contact_owner = '0';
		}

		//$this->contact_owner = intval( $this->contact_owner );
		return NULL; // object is ok
	}
	function store()
	{
		//var_dump($this->contact_owner);
		$this->contact_first_name = trim($this->contact_first_name);
		$this->contact_last_name = trim($this->contact_last_name);
		$this->contact_order_by = trim($this->contact_order_by);
		$this->contact_title = trim($this->contact_title);
		if ($this->check() == NULL)
		{
			//var_dump($this->contact_owner);
			if ($this->contact_id)
			{
				$this->_action= 'updated';

				$oContact = new CContact();
				$oContact->load($this->contact_id);

				//update contact type if changed
				//if ($oContact->contact_type != $this->contact_type)
				//{

				//$this->updateContactType($this->contact_type);

				//}

				$ret = db_updateObject('contacts', $this, 'contact_id');
			}
			else
			{
				$this->_action= 'added';
				$ret = db_insertObject( 'contacts', $this, 'contact_id' );
			}

			if( !$ret )
			{
				return get_class( $this )."::store failed <br />" . db_error();
			}
			else
			{
				//store contact types and ids in company_contacts table

			}
		}

	}
	function canDelete( &$msg, $oid=null, $joins=null ) {
		global $AppUI;
		if ($oid) {
			// Check to see if there is a user
			$q = new DBQuery;
			$q->addTable('users');
			$q->addQuery('count(*) as user_count');
			$q->addWhere('user_contact = ' . (int)$oid);
			$user_count = $q->loadResult();
			if ($user_count > 0) {
				$msg =  $AppUI->_('cannot delete, contact is a user');
				return false;
			}
		}
		return parent::canDelete($msg, $oid, $joins);
	}

	function is_alpha($val)
	{
		// If the field consists solely of numerics, then we return it as an integer
		// otherwise we return it as an alpha

		$numval = strtr($val, "012345678", "999999999");
		if (count_chars($numval, 3) == '9')
		return false;
		return true;
	}

	function getCompanyID(){
		$q  = new DBQuery;
		$q->addTable('companies');
		$q->addQuery('company_id');
		$q->addWhere('company_name = '.$this->contact_company);
		$sql = $q->prepare();
		$q->clear();
		$company_id = db_loadResult( $sql );
		return $company_id;
	}
	function getClientID(){
		$q  = new DBQuery;
		$q->addTable('clients');
		$q->addQuery('client_id');
		$q->addWhere('client_name = '.$this->contact_client);
		$sql = $q->prepare();
		$q->clear();
		$client_id = db_loadResult( $sql );
		return $client_id;
	}
	function getCompanyName(){
		$sql = "select company_name from companies where company_id = '" . $this->contact_company . "'";
		$q  = new DBQuery;
		$q->addTable('companies');
		$q->addQuery('company_name');
		$q->addWhere('company_id = '.$this->contact_company);
		$sql = $q->prepare();
		$q->clear();
		$company_name = db_loadResult( $sql );
		return $company_name;
	}
	function getClientName(){
		$sql = "select concat_ws(' ', client_firstname, client_other_name, client_last_name) from clients where client_id = '" . $this->contact_client . "'";
		$q  = new DBQuery;
		$q->addTable('clients');
		$q->addQuery(' concat_ws(" ", client_firstname, client_other_name, client_last_name) ' );
		$q->addWhere('client_id = '.$this->contact_client);
		$sql = $q->prepare();
		$q->clear();
		$client_name = db_loadResult( $sql );
		return $client_name;
	}


	function getCompanyDetails() {
		$result = array('company_id' => 0, 'company_name' => '');
		if (! $this->contact_company)
		return $result;

		$q  = new DBQuery;
		$q->addTable('companies');
		$q->addQuery('company_id, company_name');
		if ($this->is_alpha($this->contact_company)) {
			$q->addWhere('company_name = '.$q->quote($this->contact_company));
		} else {
			$q->addWhere("company_id = '".$this->contact_company."'");
		}
		$sql = $q->prepare();
		$q->clear();
		db_loadHash($sql, $result);
		return $result;
	}

	function getClientDetails() {
		$result = array('client_id' => 0, 'client_name' => '');
		if (! $this->contact_client)
		return $result;

		$q  = new DBQuery;
		$q->addTable('clients');
		$q->addQuery('client_id, concat_ws(" ", client_firstname, client_other_name, client_last_name)');
		if ($this->is_alpha($this->contact_client)) {
			$q->addWhere('client_name = '.$q->quote($this->contact_client));
		} else {
			$q->addWhere("client_id = '".$this->contact_client."'");
		}
		$sql = $q->prepare();
		$q->clear();
		db_loadHash($sql, $result);
		return $result;
	}
	function getClientDetail($client_id) {
		$result = array('client_id' => 0, 'client_name' => '');
		if (! $client_id)
		return $result;

		$q  = new DBQuery;
		$q->addTable('clients');
		$q->addQuery('client_id, concat_ws(" ", client_firstname, client_other_name, client_last_name)');
		$q->addWhere("client_id = '".$client_id."'");

		$sql = $q->prepare();
		$q->clear();
		db_loadHash($sql, $result);
		return $result;
	}


	function getDepartmentDetails() {
		$result = array('dept_id' => 0, 'dept_name' => '');
		if (! $this->contact_department)
		return $result;
		$sql = "select dept_id, dept_name from departments";
		$q  = new DBQuery;
		$q->addTable('departments');
		$q->addQuery('dept_id, dept_name');
		if ($this->is_alpha($this->contact_department))
		$q->addWhere('dept_name = ' . $q->quote($this->contact_department));
		else
		$q->addWhere("dept_id = '" . $this->contact_department . "'");

		$sql = $q->prepare();
		$q->clear();
		db_loadHash($sql, $result);
		return $result;
	}

	function getContactFullName()
	{
		return $this->contact_title . " " . $this->contact_first_name . " " .$this->contact_other_name . " ".  $this->contact_last_name;
	}

	function getContactRoles($client_id = NULL){
		
		global $roleLimits;
		if (!isset($this->contact_id)){
			return null;
		}

		$sql = "SELECT SQL_CALC_FOUND_ROWS c.client_id,c.client_adm_no, c.client_first_name, c.client_last_name, 'Intake Officer' AS role "
		. "FROM clients c INNER JOIN counselling_info ci ON ci.counselling_client_id = c.client_id "
		. "INNER JOIN contacts si ON ci.counselling_staff_id = si.contact_id "
		. "where si.contact_id = $this->contact_id "
		. "UNION "
		. "SELECT c.client_id,c.client_adm_no, c.client_first_name, c.client_last_name, 'Admission Officer' AS role "
		. "FROM clients c "
		. "INNER JOIN admission_info ai on ai.admission_client_id = c.client_id "
		. "INNER JOIN contacts sa ON ai.admission_staff_id = sa.contact_id "
		. "WHERE sa.contact_id =$this->contact_id "
		. "UNION "
		. "SELECT c.client_id,c.client_adm_no, c.client_first_name, c.client_last_name, 'CHW' AS role "
		. "FROM clients c "
		. "INNER JOIN admission_info ai on ai.admission_client_id = c.client_id "
		. "INNER JOIN contacts sa ON ai.admission_chw = sa.contact_id "
		. "WHERE sa.contact_id =$this->contact_id "
		. "UNION "
		. "SELECT c.client_id,c.client_adm_no, c.client_first_name, c.client_last_name, 'Clinical Visit: Clinician' AS role "
		. "FROM clients c "
		. "INNER JOIN clinical_visits cv on cv.clinical_client_id = c.client_id "
		. "INNER JOIN contacts sc ON cv.clinical_staff_id = sc.contact_id "
		. "WHERE sc.contact_id =$this->contact_id "
		. "UNION "
		. "SELECT c.client_id,c.client_adm_no, c.client_first_name, c.client_last_name, 'Counselling Visit: Counsellor' AS role "
		. "FROM clients c "
		. "INNER JOIN counselling_visit cv on cv.counselling_client_id = c.client_id "
		. "INNER JOIN contacts sc ON cv.counselling_staff_id = sc.contact_id "
		. "WHERE sc.contact_id =$this->contact_id "
		. "UNION "
		. "SELECT c.client_id,c.client_adm_no, c.client_first_name, c.client_last_name, 'Medical Assessment: Clinician' AS role "
		. "FROM clients c "
		. "INNER JOIN medical_assessment ma on ma.medical_client_id = c.client_id "
		. "INNER JOIN contacts sc ON ma.medical_staff_id = sc.contact_id "
		. "WHERE sc.contact_id =$this->contact_id "
		. "UNION "
		. "SELECT c.client_id,c.client_adm_no, c.client_first_name, c.client_last_name, 'Nutrition Visit: Nutritionist' AS role "
		. "FROM clients c "
		. "INNER JOIN nutrition_visit nv on nv.nutrition_client_id = c.client_id "
		. "INNER JOIN contacts sc ON nv.nutrition_staff_id = sc.contact_id "
		. "WHERE sc.contact_id =$this->contact_id "
		. "UNION "
		. "SELECT c.client_id,c.client_adm_no, c.client_first_name, c.client_last_name, 'Social Visit: Social Worker' AS role "
		. "FROM clients c "
		. "INNER JOIN social_visit sv on sv.social_client_id = c.client_id "
		. "INNER JOIN contacts sc ON sv.social_staff_id = sc.contact_id "
		. "WHERE sc.contact_id =$this->contact_id "
		. "ORDER BY role ";
		if((int)$roleLimits['limit'] > 0 && !is_null($roleLimits['offset'])){
			$sql.=' LIMIT '.$roleLimits['offset'].','.$roleLimits['limit'];
		}
		$qid = db_exec($sql);
		return $qid;

	}
	function getContactRolesDesc($client_id = NULL)
	{
		$roleStr = '';
		$roles  = $this->getContactRoles($client_id);
		if (isset($roles))
		{
			foreach ($roles as $role_desc)
			{
				$roleStr  .= $role_desc . "<br />";
			}

		}
		return $roleStr;
	}
	function updateContactType($contact_type, $contact_id = NULL, $client_id = NULL)
	{

		if (is_null($contact_id))
		$contact_id = $this->contact_id;
		if (is_null($client_id))
		$client_id = $this->contact_client_id;

		$sql = "UPDATE client_contacts SET client_contacts_contact_type = '$contact_type' where client_contacts_contact_id='$contact_id' and client_contacts_client_id = '$client_id'";
		db_exec($sql);
	}

	function getClients()
	{
		$clinics = NULL;

		if (isset($this->contact_id))
		{
			$sql = "SELECT clinic_contacts_client_id FROM client_contacts WHERE client_contacts_contact_id = $this->contact_id";
			$companies = db_loadColumn($sql);
		}

		return $companies;

	}
	function getFullname()
	{
		return $this->contact_title . " " . $this->contact_first_name . " " .$this->contact_other_name . " ".  $this->contact_last_name;
	}
	function getRoleCount()	{
		$roles = $this->getContactRoles();
		$res=db_exec('select found_rows()');
		if($res){
			$xr=db_fetch_array($res);
			$drows=$xr[0];
		}
		return $drows;//db_num_rows($roles);
	}
	function getUrl($urlType='view', $client_id = NULL)
	{

		$url_array = array(
		"view" => "./index.php?m=contacts&a=view&contact_id=$this->contact_id",
		"add" => "./index.php?m=contacts&a=addedit"
		);

		if (isset($client_id))
		{

			if (count($roles = $this->getContactRoles()) > 0)
			{
				foreach ($roles as $role)
				{
					switch ($role)
					{
						case 18 :
						case 19 :
						case 11 :
						case 10 :
						case 16 :
						case 15 :
						case 13 :
						case 14 :
						case 20 :
						case 21 : $url_array = array(
						"view" => "./index.php?m=relatives&a=view&contact_id=$this->contact_id&client_id=$client_id",
						"add" => "./index.php?m=relatives&a=addedit"
						);
						break;
						case 1 :
						case 17 : $url_array = array(
						"view" => "./index.php?m=caregivers&a=view&contact_id=$this->contact_id&client_id=$client_id",
						"add" => "./index.php?m=caregivers&a=addedit"
						);
						break;

						default :$url_array = array(
						"view" => "./index.php?m=contacts&a=view&contact_id=$this->contact_id",
						"add" => "./index.php?m=contacts&a=addedit"
						);
					}
				}
			}
		}
		return $url_array[$urlType];
	}
}
?>
