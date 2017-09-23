<?php /* CAREGIVER $Id: caregivers.class.php,v 1.11.4.1 2005/09/14 18:03:24 pedroix Exp $ */

/**
* Caregiver Class
*/
class CRelative extends CDpObject {

	var  $relative_id = NULL;
	var  $relative_module = NULL;
	var  $relative_gender = NULL;
	var  $relative_contact = NULL;
	var  $relative_entry_date = NULL;
	var  $relative_status  = NULL;
	var  $relative_notes = NULL;
	var  $relative_custom = NULL;
  
  

	function CRelative() 
	{
		$this->CDpObject( 'relatives', 'relative_id' );
	}

	function check() {
		if ($this->relative_id === NULL) {
			return 'relative id id is NULL';
		}
		// TODO MORE
		return NULL; // object is ok
	}

	function store() 
	{
		$msg = $this->check();
		if( $msg ) 
		{
			return get_class( $this )."::store-check failed";
		}
		$q  = new DBQuery;
		if( $this->caregiver_id ) 
		{
		
			$ret = db_updateObject( 'relatives', $this, 'relative_id', false );
		} 
		else 
		{
			$ret = db_insertObject( 'relatives', $this, 'relative_id' );
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

	function delete() {

		
		return $result;
 	}
	
	function getRoles($client_id)
	{
			$q = new DBQuery();
			$q->addTable('relative_client');
			$q->addQuery('relative_client_client_id');
			$q->addQuery('relative_client_relative_type');
			$q->addWhere('relative_client_client_id = ' . $client_id);
			return $q->loadHashList();
		
	}
	
	function getDependents()
	{
			$q = new DBQuery();
			$q->addTable('relative_client');
			$q->addQuery('relative_client_client_id');
			$q->addQuery('relative_client_relative_type');
			return $q->loadHashList();
	}
	
	function getDependentCount()
	{
	   return count($this->getDependents());
	}
}

?>