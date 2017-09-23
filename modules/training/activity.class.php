<?php
require_once( $AppUI->getSystemClass('dp'));
require_once( $AppUI->getModuleClass('contacts'));

class CActivity extends CDpObject
{
  var $activity_id = NULL;
  var $activity_date = NULL;
  var $activity_end_date = NULL;
  var $activity_curriculum = NULL;
  var $activity_curriculum_desc = NULL;
  var $activity_description = NULL;
  var $activity_entry_date = NULL;
  var $activity_clinic = NULL;
  var $activity_male_count = NULL;
  var $activity_female_count = NULL;
  var $activity_notes = NULL;
  var $activity_hpd = null;
  var $activity_visiters_total = null;
  var $activity_cadres = null;

  function CActivity()
  {
    $this->CDpObject('activity', 'activity_id');
  }
  function IsDeceased()
  {
	$clientStatus = dPgetSysVal("ClientStatus");
	
	$q = new DBQuery();
	$q->addTable("mortality_info");
	$q->addQuery("count(*)");
	$q->addWhere("mortality_client_id = ".  $this->client_id);
	$count = $q->loadResult();
	
	return ( ($clientStatus[$this->client_status] == "Deceased") || ($count > 0));	
  }
  function reset()
  {
  
	  $this->activity_id = NULL;
	  $this->activity_date = NULL;
	  $this->activity_end_date = NULL;
	  $this->activity_description = NULL;
	  $this->activity_curriculum = NULL;
	  $this->activity_curriculum_desc = NULL;
	  $this->activity_male_count = NULL;
	  $this->activity_female_count = NULL;
	  $this->activity_notes = NULL;
	  $this->activity_hpd = null;
	  $this->activity_visiters_total = null;
	  $this->activity_cadres = null;
  
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
  function getClinicName()
  {
		
	 $clinic = NULL;	
     if (isset($this->activity_clinic))
	 {
		$q = new DBQuery();	
		$q->addTable('clinics');
		$q->addQuery('clinic_name');
		$q->addWhere('clinic_id = '.$this->activity_clinic );
		$clinic = $q->loadResult();
	 }
	 return $clinic;
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
		
		if( ($this->activity_id) && ($this->activity_id > 0)) 
		{
			
			addHistory('activity', $this->activity_id, 'update', $this->activity_name);
			$this->_action = 'updated';

			$ret = db_updateObject( 'activity', $this, 'activity_id', true );
		} 
		else 
		{
		    
			$this->_action = 'added';
			$ret = db_insertObject( 'activity', $this, 'activity_id' );
			addHistory('activity', $this->activity_id, 'add', $this->activity_name);
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
	function getClients($type = NULL)
	{
		$clients = NULL;
		$q = new DBQuery;
		
		if (isset($this->company_id))
		{
			$q->addTable('activity_clients');
			$q->addQuery('activity_clients_client_id');
			$q->addWhere("activity_clients_activity_id = $this->activity_id");
			   
			$clients = $q->loadColumn();
		}
		//if (count($contacts)==1)
		   //$contacts = $contacts[0];
		   
		return $clients;

	}
	function delete() 
	{

		$sql = "DELETE FROM activity_clients WHERE activity_clients_activity_id = $this->activity_id";
		if (!db_exec( $sql )) 
		{
				return db_error();
		}
		// finally delete the company
		$sql = "DELETE FROM activity WHERE activity_id = $this->activity_id";
		if (!db_exec( $sql )) 
		{
			return db_error();
		} 
		else
			$this->_action ='deleted';

		 return NULL;
	}
	
	function getUrl($urlType='view')
	{

		$url_array = array(
		"view" => "./index.php?m=activity&a=view&activity_id=$this->activity_id",
		"add" => "./index.php?m=activity&a=addedit",
		"edit"=> "./index.php?m=activity&a=addedit&activity_id=$this->activity_id"
		);
		return $url_array[$urlType];
	}

	
	function getStatus()
	{
	   /*$q = new DBQuery;
	   $q->addTable ("company_status");
	   $q->addQuery("company_status_desc");
	   $q->addWhere("company_status_id = " . $this->company_status);
	   $retval = $q->loadResult();*/
	   return $retval;
	}

	function getCount($activity_curriculum = NULL)
	{
		global $AppUI;
		
		if ($user_type == NULL)
			$user_type = $AppUI->user_type;
		if ($user_id == NULL)
			$user_id = $AppUI->user_id;
		
		$sql = "SELECT COUNT(*) FROM activity a ";
		

		if ( ($activity_curriculum > 0) && ($activity_curriculum < 99) )
		{
			$where = "WHERE ";
		    $where .= " a.activity_curriculum = $activity_curriculum ";
		}
		/*if ($center_id === NULL)
		{
			//$where .= " AND  (ci.counselling_clinic = 0 OR ci.counselling_clinic IS NULL ) ";
			$where .= " AND  (a.activity_clinic NOT IN (SELECT CONCAT_WS(',', clinic_id) FROM clinics)) OR a.activity_clinic IS NULL ";
		}*/		

		if ($user_type <> 1) //not admin user type
		{
			//get allowed clinics if user is not an admin
			$q = new DBQuery();
			$q->addTable("users");
			$q->addQuery("users.user_clinics");
			$q->addWhere("users.user_id = " . $user_id);
			$allowedClinics = $q->loadHashList();

		}
		
		if ((count($allowedClinics) > 0) && ($allowedClinics[0] <> NULL)) { $where .= ' AND a.activity_clinic IN (' . implode(',', array_keys($allowedClinics)) . ')'; }	
		$sql .= $where;
		//print($sql);
		$count = db_loadResult($sql);
		return $count;
  }

}
?>