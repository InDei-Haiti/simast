<?php

if (eregi ('search.class.php', $_SERVER['PHP_SELF'])) {
    die ('This file can not be used on its own.');
}

require_once( $AppUI->getSystemClass( 'searchdata' ) );

class Search
{

   var $_query = '';
   
   var $_topic = '';
   
   var $_query = '';
   
   var $_dateStart = null;
   
   var $_dateEnd = null;
   
   var $_page = null;
   
   function Search()
   {
		if (count($_GET) > 0)
		{
			$input_vars = $_GET;
		}
		else
		{
			$input_vars = $_POST;
		}
		$this->_query = @dPFormSafe($input_vars["query"]);
		$this->_type = @dPFormSafe($input_vars['type']);
		if (empty ($this->_type)) 
		{
            $this->_type = 'all';
        }
		$this->_page = @dPFormSafe ($input_vars['page']);
   }

   function _searchCompanies()
   {
	  global $dPconfig, $AppUI;
	  
	  $where = $AppUI->getState( 'CompIdxWhere' ) ? $AppUI->getState( 'CompIdxWhere' ) : '%';
	  
	  $obj = new CCompany();

	  $allowedCompanies = $obj->getAllowedRecords($AppUI->user_id, 'company_id, company_name' );
	  
	  $search_string = urlencode($this->_query);
	  
	  if ($dPconfig['max_search_results'] > 0)
	  {
	     $resultLimit = $dPconfig['max_search_results'];
	  }	  
	  $resultPage = 1;
	  
	  if ($this->_page > 1)
	  {
		$resultPage = $this->_page;
	  }
	  
	  //perform search
	  $q = new DBQuery;
	  $q->addTable('companies', 'c');
	  $q->addQuery('c.company_id, c.company_name, c.company_type, c.company_description, c.company_phone1');
	  $q->addWhere("c.company_name LIKE '$where%'");
      if (count($allowedCompanies) > 0) { $q->addWhere('c.company_id IN (' . implode(',', array_keys($allowedCompanies)) . ')'); }
	  
	  if ($search_string != "") 
	  { 
	
		$q->addWhere("c.company_name LIKE '%$search_string%'"); 
	  }
	  $rows = $q->loadList;
	  $numrows = count($rows);
	  $totcount = $obj->getCount();
	  
	  $company_results = new SearchData();
	  $company_results->searchlabel = "Company Results";
	  $company_results->addSearchHeading("Company Name");
	  $company_results->addSearchHeading("Company Phone");
	  $company_results->addSearchHeading("Connection Type");
	  $company_results->addSearchHeading("Installation Date");
      $company_results->num_searchresults = 0;
      $company_results->num_itemssearched = $totcount;
    
	  foreach ($rows as $row)
	  {
	  
	  }
	  
	  return $company_results;
   }
   
   function _searchIPs()
   {
       return $ip_results; 
   }
   
   function _searchDomains();
   {
      return $domain_results;
   }
}
?>