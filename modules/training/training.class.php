<?php
require_once( $AppUI->getSystemClass('dp'));
require_once( $AppUI->getModuleClass('contacts'));

class CTraining extends CDpObject
{
  var $training_id = NULL;
  var $training_date = NULL;
  var $training_entry_date = NULL;
  var $training_name = NULL;
  var $training_clinic = NULL;
  var $training_curriculum = NULL;
  var $training_curriculum_desc = NULL;
  var $training_notes = NULL;

  function CTraining()
  {
    $this->CDpObject('trainings', 'training_id');
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
  
	  $this->training_id = NULL;
	  $this->training_name = NULL;
	  $this->training_date = NULL;
	  $this->training_curriculum = NULL;
	  $this->training_curriculum_desc = NULL;
	  $this->training_notes = NULL;

  
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
     if (isset($this->training_clinic))
	 {
		$q = new DBQuery();	
		$q->addTable('clinics');
		$q->addQuery('clinic_name');
		$q->addWhere('clinic_id = '.$this->training_clinic );
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
		
		if( ($this->training_id) && ($this->training_id > 0)) 
		{
			
			addHistory('training', $this->training_id, 'update', $this->training_name);
			$this->_action = 'updated';

			$ret = db_updateObject( 'trainings', $this, 'training_id', true );
		} 
		else 
		{
		    
			$this->_action = 'added';
			$ret = db_insertObject( 'trainings', $this, 'training_id' );
			addHistory('training', $this->training_id, 'add', $this->training_name);
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
			$q->addTable('training_clients');
			$q->addQuery('training_clients_client_id');
			$q->addWhere("training_clients_training_id = $this->training_id");
			   
			$clients = $q->loadColumn();
		}
		//if (count($contacts)==1)
		   //$contacts = $contacts[0];
		   
		return $clients;

	}
	function delete() 
	{


		// finally delete the company
		$sql = "DELETE FROM trainings WHERE training_id = $this->training_id";
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
		"view" => "./index.php?m=training&a=view&training_id=$this->training_id",
		"add" => "./index.php?m=training&&a=addedit",
		"edit"=> "./index.php?m=training&a=addedit&training_id=$this->training_id"
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

	function getCount($training_curriculum = NULL)
	{
		global $AppUI;
		
		if ($user_type == NULL)
			$user_type = $AppUI->user_type;
		if ($user_id == NULL)
			$user_id = $AppUI->user_id;
		
		$sql = "SELECT COUNT(*) FROM trainings a ";
		

		if ( ($training_curriculum > 0) && ($training_curriculum < 99) )
		{
			$where = "WHERE ";
		    $where .= " a.training_curriculum = $training_curriculum ";
		}
		/*if ($center_id === NULL)
		{
			//$where .= " AND  (ci.counselling_clinic = 0 OR ci.counselling_clinic IS NULL ) ";
			$where .= " AND  (a.training_clinic NOT IN (SELECT CONCAT_WS(',', clinic_id) FROM clinics)) OR a.training_clinic IS NULL ";
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
		
		//if ((count($allowedClinics) > 0) && ($allowedClinics[0] <> NULL)) { $where .= ' AND a.training_clinic IN (' . implode(',', array_keys($allowedClinics)) . ')'; }	
		$sql .= $where;
		//print($sql);
		$count = db_loadResult($sql);
		return $count;
  }

}
?>