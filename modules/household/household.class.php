<?php /* HOUSEHOLD MEMBER $Id: medical.class.php,v 1.9 2004/01/29 06:30:43 ajdonnison Exp $ */
/**
 *	@package dotProject
 *	@subpackage modules
 *	@version $Revision: 1.9 $
*/

require_once( $AppUI->getSystemClass ('dp' ) );

/**
 *	MedicalAssessment Class
 *	
 */
class CHouseholdMember extends CDpObject {

var $household_id  = NULL;
var $household_client_id  = NULL;
var $household_medical_id  = NULL;
var $household_name  = NULL;
var $household_yob   = NULL;
var $household_relationship   = NULL;
var $household_gender   = NULL;
var $household_notes   = NULL;
var $household_custom  = NULL;

	



	function CHouseholdMember() {
		$this->CDpObject( 'household_info', 'household_id' );
	}

// overload check
	function check() 
	{
		/*if ($this->medical_id === NULL) {
			return 'counselling id is NULL';
		}
		$this->medical_id = intval( $this->medical_id );*/
				
		
		/*if (empty($this->medical_tb_date_diagnosed)) 
		{
			$this->medical_tb_date_diagnosed = NULL;
		}*/		
	
		return NULL; // object is ok
	}

// overload canDelete
	function canDelete( &$msg, $oid=null ) 
	{
		
	}
	function getContacts($type = NULL)
	{
		$contacts = NULL;
		$q = new DBQuery;
		
		if (isset($this->company_id))
		{
			$q->addTable('company_contacts');
			$q->addQuery('company_contacts_contact_id');
			$q->addWhere("company_contacts_company_id = $this->company_id");
			if ($type)
			   $q->addWhere("company_contacts_contact_type = $type");
			   
			$contacts = $q->loadColumn();
		}
		//if (count($contacts)==1)
		   //$contacts = $contacts[0];
		   
		return $contacts;

	}
	function getUrl($urlType='view', $companyType = NULL)
	{
		if ($companyType == NULL) $companyType = $this->company_type;
		
		
		$modules = dPgetSysVal('CompanyModules');
		$unit = $modules[$companyType];
		$url_array = array(
		"view" => "./index.php?m=counsellinginfo&a=view&company_id=$this->company_id",
		"add" => "./index.php?m=counsellinginfo&a=addedit&company_type=$companyType",
		"edit"=> "./index.php?m=counsellinginfo&a=addedit&company_id=$this->company_id"
		);
		return $url_array[$urlType];
	}
	function getDescription()
	{
		static $types; 
		if (!isset($types)) 
		{
			$types = dPgetSysVal('CompanyType');
		}
		$desc = $types[$this->company_type];
		return $desc;
	}
	function getCount($type = NULL)
	{
				if (!empty($type))
				{
					$sql = "SELECT COUNT(*) FROM companies WHERE company_type IS NOT NULL AND company_type = $type";
				}
				else
				{
					$sql = "SELECT COUNT(*)  FROM companies WHERE company_type IS NOT NULL";
				}
		$count = db_loadResult($sql);
		return $count;
  }
  
  function store()
  {
		global $AppUI;
		
		//$importing_tasks = false;
		$msg = $this->check();
		if( $msg ) 
		{
			$return_msg = array(get_class($this) . '::store-check',  'failed',  '-');
			if (is_array($msg))
				return array_merge($return_msg, $msg);
			else 
			{
				array_push($return_msg, $msg);
				return $return_msg;
			}
		}
		
		if( ($this->household_id) && ($this->household_id > 0)) 
		{
			
			addHistory('householdmember', $this->household_id, 'update', $this->household_id);
			$this->_action = 'updated';

			$ret = db_updateObject( 'household_info', $this, 'household_id', false );


		} 
		else 
		{
		    
			$this->_action = 'added';
			$ret = db_insertObject( 'household_info', $this, 'household_id' );
			addHistory('household_info', $this->household_id, 'add', $this->household_id);

		}
		
		if( !$ret ) 
		{
			return get_class( $this )."::store failed <br />" . db_error();
		} 
		else 
		{
			return NULL;
		}
	}
}
?>
