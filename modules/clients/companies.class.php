<?php

require_once( $AppUI->getSystemClass('dp'));
require_once( $AppUI->getModuleClass('contacts'));

class CCompany extends CDpObject
{
  var $company_id = NULL;
  var $company_name = NULL;
  var $company_status = NULL;
  var $company_priority = NULL;
  var $company_phone1 = NULL;
  var $company_fax = NULL;
  var $company_address1 = NULL;
  var $company_address2 = NULL;
  var $company_email = NULL;
  var $company_city = NULL;
  var $company_phone2 = NULL;
  var $company_primary_url = NULL;
  var $company_description = NULL;
  var $company_directions = NULL;
  var $company_type = NULL;
  var $company_date_entered = NULL;
  var $company_last_update = NULL;
  
  function CCompany()
  {
    $this->CDpObject('companies', 'company_id');
  }
  
  function reset()
  {
     $this->company_id = NULL;
     $this->company_name = NULL;
     $this->company_status = NULL;
     $this->company_priority = NULL;
     $this->company_phone1 = NULL;
     $this->company_fax = NULL;
     $this->company_address1 = NULL;
     $this->company_address2 = NULL;
     $this->company_email = NULL;
     $this->company_city = NULL;
     $this->company_phone2= NULL;
     $this->company_primary_url = NULL;
     $this->company_description = NULL;
     $this->company_directions = NULL;
     $this->company_type = NULL;
     $this->company_date_entered = NULL;
     $this->company_last_update = NULL;
  }
  function check()
  {
    //if ($this->company_id == NULL)
    //{
      //return 'company id is null';
    //}
    
    //$this->company_id = intval($this->company_id);
    
    return NULL;
  }
  
  function canDelete(&$msg, $oid=NULL)
  {
		//$tables[] = array( 'label' => 'Departments', 'name' => 'departments', 'idfield' => 'dept_id', 'joinfield' => 'dept_company' );
		//$tables[] = array( 'label' => 'Users', 'name' => 'users', 'idfield' => 'user_id', 'joinfield' => 'user_company' );
        //return CDpObject::canDelete( $msg, $oid, $tables );
		return true;
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
		
		if( ($this->company_id) && ($this->company_id > 0)) 
		{
			
			addHistory('company', $this->company_id, 'update', $this->company_name);
			$this->_action = 'updated';

			$ret = db_updateObject( 'companies', $this, 'company_id', false );


		} 
		else 
		{
		    
			$this->_action = 'added';
			$ret = db_insertObject( 'companies', $this, 'company_id' );
			addHistory('companies', $this->company_id, 'add', $this->company_name);

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
	function delete() 
	{
		static $net_config = array (2=>'company_network_info', 3=>'blue_network_info' );
		static $terminal_equip_config = array (2=>'company_term_equip', 3=>'blue_term_equip',4=>'kdn_term');

		$contacts = $this->getContacts();
		//var_dump($contacts);
		
		
		// delete linked information
		if ((count($contacts) > 0) && ($contacts != NULL))
		{
			foreach ($contacts as $contact)
			{
			   $contactObj = new CContact();
			   $contactObj->load($contact);
			   $companies = $contactObj->getCompanies();
			   if (count($companies) == 1) //have only one client assigned to this contact, so its okay to delete the contact
			   {
					$sql = "DELETE FROM contacts WHERE contact_id = $contactObj->contact_id";
					//print_r($sql);
					if (!db_exec( $sql )) 
					{
						return db_error();
					}
			   }
			}
			//exit;

			//linked contacts record
			$sql = "DELETE FROM company_contacts WHERE company_contacts_company_id = $this->company_id";
			if (!db_exec( $sql )) 
			{
				return db_error();
			}
		}
		//linked network config
		$table = $net_config[$this->company_type];
		if (isset($table))
		{
			$sql = "DELETE FROM $table WHERE network_company_id = $this->company_id";
			if (!db_exec( $sql )) 
			{
				return db_error();
			}
		}
		//linked terminal equipment
		$table = $terminal_equip_config[$this->company_type];
		if (isset($table))
		{
			$sql = "DELETE FROM $table WHERE term_equip_company_id = $this->company_id";
			if (!db_exec( $sql )) 
			{
				return db_error();
			}
		}
		//linked bs info
		if ($this->checkClientOnBS())
		{
			$sql = "DELETE FROM company_building_solution WHERE company_bs_company_id = $this->company_id";
			if (!db_exec( $sql )) 
			{
				return db_error();
			}
		}
		
		// finally delete the company
		$sql = "DELETE FROM companies WHERE company_id = $this->company_id";
		if (!db_exec( $sql )) 
		{
			return db_error();
		} 
		else
			$this->_action ='deleted';

		 return NULL;
	}
	
	function getUrl($urlType='view', $companyType = NULL)
	{
		if ($companyType == NULL) $companyType = $this->company_type;
		
		
		$modules = dPgetSysVal('CompanyModules');
		$unit = $modules[$companyType];
		$url_array = array(
		"view" => "./index.php?m=companies&u=$unit&a=view&company_id=$this->company_id",
		"add" => "./index.php?m=companies&u=$unit&a=addedit_ks&company_type=$companyType",
		"edit"=> "./index.php?m=companies&u=$unit&a=addedit&company_id=$this->company_id"
		);
		return $url_array[$urlType];
	}

	function getConvertOptions()
	{
	   $modules = dPgetSysVal('CompanyModules');
	   $types = dPgetSysVal('CompanyType');

	   unset($types[$this->company_type]);
	   unset($types[0]);
	   unset ($modules[$this->company_type]);
	   unset ($modules[5]);

       //print_r($modules);
       //print_r($types);
	   //print "<br/>";

	   foreach ($modules as $key=>$value) 
	   {
	       $convertOptions[$value] = "Convert this client to a $types[$key] client";
	   }
	   return $convertOptions;
	}
	function checkClientOnBS()
	{
	
		//check if client is on bs
		$retval = false;

		$q = new DBQuery;
		$q->addTable ("company_building_solution");
		$q->addQuery ("count(*)");
		$q->addWhere("company_bs_company_id = $this->company_id");
		
		$num = intval($q->loadResult());
		if ($num > 0)
			$retval = true;
			
		$q->clear();		
		
		return $retval;
	}
	
	function getDescription()
	{
		static $types; 
		if (!isset($types)) 
		{
			$types = dPgetSysVal('CompanyType');
		}
		$desc = $types[$this->company_type];
		if ($this->checkClientOnBS())
		{
		   $desc .= ' Building Solution';
		}
		return $desc;
	}

	function getBuildingSolutionClients()
	{
		$q = new DBQuery;
		$q->addTable ("company_building_solution");
		$q->addQuery ("DISTINCT company_bs_company_id");
		$retval = $q->loadColumn();

		$q->clear();		
		return $retval;
	   
	}
	function getNoNetscreenClients()
	{
		$q = new DBQuery;
		$q->addTable ("blue_term");
		$q->addQuery ("DISTINCT term_equip_company_id");
		$q->addWhere("term_equip_netscreen_leased = 0");
		$retval = $q->loadColumn();

		$q->clear();		
		return $retval;
	   
	}
	function getStatus()
	{
	   $q = new DBQuery;
	   $q->addTable ("company_status");
	   $q->addQuery("company_status_desc");
	   $q->addWhere("company_status_id = " . $this->company_status);
	   $retval = $q->loadResult();
	   return $retval;
	}
	function getPriority()
	{
	   $retval = 'Not defined';
	   if (isset($this->company_priority))
	   {
		$q = new DBQuery;
		$q->addTable ("company_priority");
		$q->addQuery("company_priority_desc");
		$q->addWhere("company_priority_id = " . $this->company_priority);
		$retval = $q->loadResult();
	   }
		return $retval;
	  
	}
	function getCount($type = NULL, $options = NULL)
	{
		switch ($type)
		{
			case 1:
			case 2:
			case 3:
			case 4:
			case 7:
			case 6:
			case 8:
				$sql = "SELECT COUNT(*) FROM companies ";
				$where = "WHERE company_type IS NOT NULL AND company_type = $type";
				break;
			case 5:
				$sql = "SELECT COUNT(DISTINCT(company_bs_company_id)) FROM company_building_solution LEFT JOIN companies ON  company_bs_company_id  = company_id ";
				break;
			default:
				$sql = "SELECT COUNT(*)  FROM companies ";
				$where = "WHERE company_type IS NOT NULL";
				

		}
		
		if (!empty($options))
		{
		    foreach ($options as $key => $value) 
			{
				if ( !empty($value)  )
				{
					if ($key == "city_filter")  
					{
						 $where .= (empty($where) ? " WHERE " : " AND ");
						 $where .= " company_city = " . $value;
					}

				}
			}
		}
     $sql .= $where;
	$count = db_loadResult($sql);
	 return $count;
  }
}
?>
