<?php
global $dPconfig, $AppUI;

require_once($AppUI->getModuleClass('companies'));
require_once($AppUI->getModuleClass('clinics'));
require_once($AppUI->getModuleClass('clients'));
require_once($AppUI->getModuleClass('projects'));
require_once($AppUI->getModuleClass('contacts'));
require_once($AppUI->getModuleClass('tasks'));
if (eregi ('search.class.php', $_SERVER['PHP_SELF'])) {
    die ('This file can not be used on its own.');
}

require_once( $AppUI->getSystemClass( 'searchdata' ) );

class Search
{

   var $_query = '';
   
   var $_topic = '';
   
   var $_dateStart = null;
   
   var $_dateEnd = null;
   
   var $_page = null;
   
   var $_searchgroup = null;

   var $nrows_plugins = null;
   
   var $total_plugins = null;
   
   var $result_plugins = null;
   
   var $searchtime  = null;	
   
   function Search()
   {
		//var_dump($_REQUEST)	;
		$this->_query = @dPFormSafe($_REQUEST["search_string"]);
		$this->_type = @dPFormSafe($_REQUEST['type']);
		$this->_searchgroup = @dPFormSafe($_REQUEST['searchgroup']);
		if (empty ($this->_type)) 
		{
            $this->_type = 'all';
        }
		$this->_page = intval(dPgetParam ($_REQUEST,'page',1));
   }
   function _searchClients()
   {
	  global $dPconfig, $AppUI;
	  
	  $obj = new CClient();

	  $allowedClients = $obj->getAllowedRecords($AppUI->user_id, 'client_id' );
	  
	  //$search_string = urlencode($this->_query);
	  $search_string = $this->_query;
	  
	  if ($dPconfig['max_search_results'] > 0)
	  {
	     $resultLimit = $dPconfig['max_search_limit'];
	  }	  
	  $resultPage = 1;
	  
	  if ($this->_page > 1)
	  {
		$resultPage = $this->_page;
	  }
	  
	  //perform search
	  $q = new DBQuery;
	  $q->addTable('clients', 'c');
	  $q->addQuery('c.client_id, c.client_first_name, c.client_other_name, c.client_last_name, c.client_notes,c.client_adm_no,c.client_status');
	  $q->addOrder('c.client_first_name, c.client_last_name');
      if (count($allowedClients) > 0) { $q->addWhere('c.client_id IN (' . implode(',', array_keys($allowedClients)) . ')'); }
	  
	  if ($search_string != "") 
	  { 
			$q->addWhere("c.client_first_name LIKE '%$search_string%' OR c.client_last_name LIKE '%$search_string%' OR c.client_other_name LIKE '%$search_string%' OR c.client_adm_no LIKE '%$search_string%'");
	  }
	  //$sql = $q->prepare();
	  //var_dump($sql);
	  //exit;
	  $rows = $q->loadList();
	  
	  
	  $numrows = count($rows);
	  $totcount = $obj->getCount();
	  
	  $client_results = new SearchData();
	  $client_results->searchlabel = "Client Search Results";
	 
	  
	  $statusTypes = dPgetSysVal('ClientStatus');
	  $client_results->addSearchHeading("Client Name");
	  $client_results->addSearchHeading("Adm No.");
	  $client_results->addSearchHeading("Client Classification");
	  $client_results->num_searchresults = 0;
      $client_results->num_itemssearched = $totcount;
	  $client_results->searchgroup = 'clients';
	  $client_results->search_string = $this->_query;
    
	  foreach ($rows as $row)
	  {
	      $obj = new CClient();
		  $obj->load($row['client_id']);

		  $search_result =array (array($AppUI->_($obj->getFullName()), $obj->getUrl()) ,$obj->client_adm_no, $statusTypes[$obj->client_status]);	//$obj->getDescription()
		  $client_results->addSearchResult($search_result);
		  $client_results->num_searchresults++;  
	  }
	  
	  return $client_results;
   }
   
 function _searchClinics()
   {
	  global $dPconfig, $AppUI;
	  
	  $obj = new CClinic();

	  $allowedClinics = $obj->getAllowedRecords($AppUI->user_id, 'clinic_id, clinic_name' );
	  
	  //$search_string = urlencode($this->_query);
	  $search_string = $this->_query;
	  
	  if ($dPconfig['max_search_results'] > 0)
	  {
	     $resultLimit = $dPconfig['max_search_limit'];
	  }	  
	  $resultPage = 1;
	  
	  if ($this->_page > 1)
	  {
		$resultPage = $this->_page;
	  }
	  
	  //perform search
	  $q = new DBQuery;
	  $q->addTable('clinics', 'c');
	  $q->addQuery('c.clinic_id, c.clinic_name, c.clinic_type, c.clinic_description, c.clinic_phone1, c.clinic_email, c.clinic_address1, c.clinic_address2');
	  $q->addOrder('c.clinic_name');
      if (count($allowedClinics) > 0) { $q->addWhere('c.clinic_id IN (' . implode(',', array_keys($allowedClinics)) . ')'); }
	  
	  if ($search_string != "") 
	  { 
			$q->addWhere("c.clinic_name LIKE '%$search_string%'"); 
	  }
	  //$sql = $q->prepare();
	  //var_dump($sql);
	  //exit;
	  $rows = $q->loadList();
	  
	  
	  $numrows = count($rows);
	  $totcount = $obj->getCount();
	  
	  $clinic_results = new SearchData();
	  $clinic_results->searchlabel = "Clinic Search Results";
	  $clinic_results->addSearchHeading("Clinic Name");
	  $clinic_results->addSearchHeading("Clinic Phone");
	  $clinic_results->addSearchHeading("Clinic Email");
	  $clinic_results->addSearchHeading("Clinic Address");
	  $clinic_results->addSearchHeading("Clinic Classification");
      $clinic_results->num_searchresults = 0;
      $clinic_results->num_itemssearched = $totcount;
	  $clinic_results->searchgroup = 'clinics';
	  $clinic_results->search_string = $this->_query;
    
	  foreach ($rows as $row)
	  {
	      $obj = new CClinic();
		  $obj->load($row['clinic_id']);

		  $search_result =	array (array($AppUI->_($obj->clinic_name), $obj->getUrl()), $obj->clinic_phone1, "<a href=mailto:$obj->clinic_email>$obj->clinic_email</a>", "$obj->clinic_address1  $obj->clinic_address2" , $obj->getDescription());	
		  $clinic_results->addSearchResult($search_result);
		  $clinic_results->num_searchresults++;  
	  }
	  
	  return $clinic_results;
   }   
   function _searchCompanies()
   {
	  global $dPconfig, $AppUI;
	  
	  $obj = new CCompany();

	  $allowedCompanies = $obj->getAllowedRecords($AppUI->user_id, 'company_id, company_name' );
	  
	  //$search_string = urlencode($this->_query);
	  $search_string = $this->_query;
	  
	  if ($dPconfig['max_search_results'] > 0)
	  {
	     $resultLimit = $dPconfig['max_search_limit'];
	  }	  
	  $resultPage = 1;
	  
	  if ($this->_page > 1)
	  {
		$resultPage = $this->_page;
	  }
	  
	  //perform search
	  $q = new DBQuery;
	  $q->addTable('companies', 'c');
	  $q->addQuery('c.company_id, c.company_name, c.company_type, c.company_description, c.company_phone1, c.company_email, c.company_address1, c.company_address2');
	  $q->addOrder('c.company_name');
      if (count($allowedCompanies) > 0) { $q->addWhere('c.company_id IN (' . implode(',', array_keys($allowedCompanies)) . ')'); }
	  
	  if ($search_string != "") 
	  { 
			$q->addWhere("c.company_name LIKE '%$search_string%'"); 
	  }
	  //$sql = $q->prepare();
	  //var_dump($sql);
	  //exit;
	  $rows = $q->loadList();
	  
	  
	  $numrows = count($rows);
	  $totcount = $obj->getCount();
	  
	  $company_results = new SearchData();
	  $company_results->searchlabel = "Client Search Results";
	  $company_results->addSearchHeading("Client Name");
	  $company_results->addSearchHeading("Client Phone");
	  $company_results->addSearchHeading("Client Email");
	  $company_results->addSearchHeading("Client Address");
	  $company_results->addSearchHeading("Client Classification");
      $company_results->num_searchresults = 0;
      $company_results->num_itemssearched = $totcount;
	  $company_results->searchgroup = 'companies';
	  $company_results->search_string = $this->_query;
    
	  foreach ($rows as $row)
	  {
	      $obj = new CCompany();
		  $obj->load($row['company_id']);

		  $search_result =	array (array($AppUI->_($obj->company_name), $obj->getUrl()), $obj->company_phone1, "<a href=mailto:$obj->company_email>$obj->company_email</a>", "$obj->company_address1  $obj->company_address2" , $obj->getDescription());	
		  $company_results->addSearchResult($search_result);
		  $company_results->num_searchresults++;  
	  }
	  
	  return $company_results;
   }
   
   function _searchPhoneNos()
   {
	  global $dPconfig, $AppUI;
	  
	  $obj = new CCompany();

	  $allowedCompanies = $obj->getAllowedRecords($AppUI->user_id, 'company_id, company_name' );
	  
	  //$search_string = urlencode($this->_query);
	  $search_string = $this->_query;
	  
	  if ($dPconfig['max_search_results'] > 0)
	  {
	     $resultLimit = $dPconfig['max_search_limit'];
	  }	  
	  $resultPage = 1;
	  
	  if ($this->_page > 1)
	  {
		$resultPage = $this->_page;
	  }
	  
	  //perform search
	  $q = new DBQuery;
	  $q->addTable('companies', 'c');
	  $q->addQuery('c.company_id, c.company_name, c.company_type, c.company_description, c.company_phone1, c.company_phone2, c.company_email, c.company_address1');
	  $q->addOrder('c.company_name');
      if (count($allowedCompanies) > 0) { $q->addWhere('c.company_id IN (' . implode(',', array_keys($allowedCompanies)) . ')'); }
	  
	  if ($search_string != "") 
	  { 
			$q->addWhere("c.company_phone1 LIKE '%$search_string%' OR c.company_phone2 LIKE '%$search_string%'" ); 
	  }
	  //$sql = $q->prepare();
	  //var_dump($sql);
	  //exit;
	  $rows = $q->loadList();
	  //var_dump($rows);
	  
	  $numrows = count($rows);
	  $totcount = $obj->getCount();
	  
	  $phonesrch_results = new SearchData();
	  $phonesrch_results->searchlabel = "Telephone No. Search Results";
	  $phonesrch_results->addSearchHeading("Client Name");
	  $phonesrch_results->addSearchHeading("Client Phone(1)");
	  $phonesrch_results->addSearchHeading("Client Phone(2)");
	  $phonesrch_results->addSearchHeading("Client Email");
	  $phonesrch_results->addSearchHeading("Client Address");
	  $phonesrch_results->addSearchHeading("Client Classification");
      $phonesrch_results->num_searchresults = 0;
      $phonesrch_results->num_itemssearched = $totcount;
	  $phonesrch_results->searchgroup = 'telephone';
	  $phonesrch_results->search_string = $this->_query;
    
	  foreach ($rows as $row)
	  {
		  $obj->load($row['company_id']);
		  $search_result =	array (array($AppUI->_($obj->company_name), $obj->getUrl()."company_id=$obj->company_id"), $obj->company_phone1,$obj->company_phone2, "<a href=mailto:$obj->company_email>$obj->company_email</a>", $obj->company_address1, $obj->getDescription());	
		  $phonesrch_results->addSearchResult($search_result);
		  $phonesrch_results->num_searchresults++;  
	  }
	  
	  return $phonesrch_results;
   }
   
   function _searchProgrammes()
   {
   	  global $dPconfig, $AppUI;
	  
	  $where = $AppUI->getState( 'CompIdxWhere' ) ? $AppUI->getState( 'CompIdxWhere' ) : '%';
	  
	  $obj = new CProject();
	  $companyObj = new CCompany();

	  $allowedCompanies = $companyObj->getAllowedRecords($AppUI->user_id, 'company_id, company_name' );
	  
	  //$search_string = urlencode($this->_query);
	  $search_string = $this->_query;
	  
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
	  $q->addTable('projects', 'a');
	  $q->addQuery('a.project_name, a.project_short_name, a.project_start_date,a.project_end_date');
	  $q->addOrder('a.project_name');
	  if (count($allowedCompanies) > 0) 
	  { $q->addWhere('a.project_company IN (' . implode(',', array_keys($allowedCompanies)) . ')'); }	  
	  
	  if ($search_string != "") 
	  { 
	
		$q->addWhere("a.project_name LIKE '%$search_string%'");
	  }
	  //$sql = $q->prepare()
	  $rows = $q->loadList();
	  
	  //var_dump($rows);
	  $numrows = count($rows);
	  $totcount = $obj->getCount();
	  
	  $project_results = new SearchData();
	  $project_results->searchlabel = "Project Search Results";
	  $project_results->addSearchHeading("Project Name");
	  $project_results->addSearchHeading("Project Short Name");
	  $project_results->addSearchHeading("Project Company");
	  $project_results->addSearchHeading("Start Date");
	  $project_results->addSearchHeading("End Expiry Date");
	  $project_results->searchgroup='programmes';
      $project_results->num_searchresults = 0;
      $project_results->num_itemssearched = $totcount;
	  $project_results->search_string = $this->_query;
    
	  foreach ($rows as $row)
	  {
		  $obj->load($row['project_id']);
		  $companyObj->load($row['project_company']);
		  $search_result =	array 
		  (array($AppUI->_($obj->project_name),$obj->project_url()), 
		  array($row["company_name"],$companyObj->getUrl()),
		  $obj->project_start_date, $obj->project_end_date);	
		  $project_results->addSearchResult($search_result);
		  $project_results->num_searchresults++;  
	  }

      return $project_results;
   }
   
   function _formatResults($nrows_plugins, $total_plugins, $result_plugins, $searchtime)
   { 	
		global $dPconfig;
		$retval = '';
		
		reset($result_plugins);
		$resultLimit = intval($dPconfig['max_search_limit']);
			
		$cur_plugin = new SearchData();
		$retval .= '<table width="100% class= "search" align="center">';
		$retval .= '<td width="99%" align="center">';
		for ($i = 0; $i < count($result_plugins); $i++)
		{
			    $cur_plugin = current($result_plugins);
				 //var_dump($cur_plugin);
					 if (($cur_plugin->num_searchresults > 0) || $dPconfig['showemptysearchresults'] )
					 {
						$retval .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="basic">'.
						 '<tr>' .
						  '<td>'.
							'<table border="0" cellpadding="0" cellspacing="0" width="100%" class="search">'.
								'<tr><td height="22" colspan="3" align="left"><strong>'.$cur_plugin->searchlabel.'</strong></td>'.
									'</tr><tr>';
						 //$retval .= '<table class=search align="center" width="80%">';
						 //$retval .= '<td align="left"><h2>' . $cur_plugin->searchlabel . '</h2></td>';
						 //$retval .= '<tr class=heading>';
						 
						 
						 //show heading
						 if ($resultLimit > $cur_plugin->num_searchresults) $resultLimit = $cur_plugin->num_searchresults;			
						 $retval .= '<tr>';
						 $retval .= $this->_showHeader($resultLimit, $cur_plugin->searchgroup, $cur_plugin->searchlabel, $cur_plugin->num_searchresults);	
						 $retval .= '</tr>';
						 $retval .= '<tr>';
						 for ($j = 1; $j <= $cur_plugin->num_searchheadings; $j++)
						 {
							$retval .= '<th align="left">' . $cur_plugin->searchheading[$j] . '</th>';
						 }
						 
						 $retval .= '</tr>';
						 
						 reset($cur_plugin->searchresults);
						 //intval($dPconfig['max_search_limit'])
						 //$cur_plugin->num_searchresults;
						 for ($j = 0; $j < $resultLimit; $j++ )
						 {
							$retval .= '<tr align="center">';
							$column = current($cur_plugin->searchresults);
							//var_dump($cur_plugin->searchresults);		
							//var_dump($column);
							$c = count($column);
							
							for ($x = 0; $x < count($column); $x++)
							{
								if (is_array($column[$x]))
								{
									$retval .= '<td align="left" nowrap><a href='.$column[$x][1].'>' . $column[$x][0] . '</a></td>';
								}
								else
								{
									$retval .= '<td align="left" nowrap>' . $column[$x] . '</td>';
								}
								next($column);	
							}
							$retval .= '</tr>';
							
							next($cur_plugin->searchresults);
							$resultNumber++;
						 }
						 
						 if ($cur_plugin->num_searchresults == 0)
						 {
							$retval .= "No results found";
						 }
						 $retval .= '</td></tr></table></td></tr></table><br>';
						 //$retval .= '<table cellspacing="0" cellpadding="0" align="center" width="100%" class="block-divider"><tr><td><img src="./images/speck.gif" width="1" height="2" alt=""></td></tr></table>';


					 }			 

				 next($result_plugins);
		}
		reset($result_plugins);
		
		$total_found = 0;
		
		foreach($result_plugins as $key)
		{
		   $total_found .= $key->num_searchresults;
		}
		$retval .= '</table></td></table>';
		return $retval;
   }
   function _formatSearchGroup($nrows_plugins, $total_plugins, $result_plugins, $searchtime, $searchgroup)
   {
		global $dPconfig;
		$retval = '';
		//var_dump($nrows_plugins);
		//var_dump($total_plugins);
		//var_dump($result_plugins);
		reset($result_plugins);
		$resultLimit = intval($dPconfig['max_searchgroup_limit']);
		
		if ($this->_page == 0) $this->_page = 1;
		
		$cur_plugin = new SearchData();
		$retval .= '<table width="100% class="search" align="center">';
		$retval .= '<td width="99%" align="center">';
		
		for ($i = 0; $i < count($result_plugins); $i++)
		{
		     $cur_plugin = current($result_plugins);
			 //var_dump($cur_plugin);
			 //var_dump($searchgroup);
			 $num_pages = ceil ($cur_plugin->num_searchresults / $resultLimit);
			 if (strcmp($cur_plugin->searchgroup,  $searchgroup) == 0)
			 {    
				 
				 if (($cur_plugin->num_searchresults > 0) || $dpConfig['showemptysearchresults'] )
				 {
					$retval .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="search">'.
					 '<tr>' .
					  '<td>'.
						'<table border="0" cellpadding="0" cellspacing="0" width="100%">'.
							'<tr><td height="22" colspan="3" align="left"><strong>'.$cur_plugin->searchlabel.'</strong></td>'.
								'</tr><tr>';
					 //$retval .= '<table class=search align="center" width="80%">';
					 //$retval .= '<td align="left"><h2>' . $cur_plugin->searchlabel . '</h2></td>';
					 //$retval .= '<tr class=heading>';
					 
					 
					 //show heading
					 if ($resultLimit > $cur_plugin->num_searchresults) $resultLimit = $cur_plugin->num_searchresults;			
					 $offset = ($this->_page - 1) * $resultLimit;
					 if ($offset < 0) $offset = 0;
					 $limit = $this->_page * $resultLimit;
					 if ($limit < 0) $limit = $cur_plugin->num_searchresults;
					 //$retval .= '<tr>';
					 //$retval .= $this->_showHeader($resultLimit, $cur_plugin->searchgroup, $cur_plugin->searchlabel, $cur_plugin->num_searchresults);	
					 $retval .= printPageNavigation( "?m=search&searchgroup=$searchgroup&search_string=$this->_query", $this->_page, $num_pages, $offset, $resultLimit, $cur_plugin->num_searchresults, $cur_plugin->searchlabel);
					 //$this->_showHeader($resultLimit, $cur_plugin->searchgroup, $cur_plugin->searchlabel,$cur_plugin->num_searchresults);	
					 $retval .= '</tr>';
					 $retval .= '<tr>';
					 for ($j = 1; $j <= $cur_plugin->num_searchheadings; $j++)
					 {
						$retval .= '<th align="left">' . $cur_plugin->searchheading[$j] . '</th>';
					 }
					 
					 $retval .= '</tr>';
					 
					 reset($cur_plugin->searchresults);
					 //intval($dPconfig['max_search_limit'])
					 //$cur_plugin->num_searchresults;
					 
					 
					 for ($j = $offset; $j < $limit; $j++ )
					 {
						$retval .= '<tr align="center">';
						$column = $cur_plugin->searchresults[$j];
						//var_dump($cur_plugin->searchresults);		
						//var_dump($column);
						$c = count($column);
						
						for ($x = 0; $x < count($column); $x++)
						{
							if (is_array($column[$x]))
							{
								$retval .= '<td align="left" nowrap><a href='.$column[$x][1].'>' . $column[$x][0] . '</a></td>';
							}
							else
							{
								$retval .= '<td align="left" nowrap>'. $column[$x] . '</td>';
							}
							next($column);	
						}
						$retval .= '</tr>';
						
						next($cur_plugin->searchresults);
						$resultNumber++;
					 }
					 
					 if ($cur_plugin->num_searchresults == 0)
					 {
						$retval .= "No results found";
					 }
					 //printPageNavigation( $base_url, $curpage, $num_pages, $offset, $limit, $count, $searching = false )
					
					 //var_dump($offset);
					 //var_dump($this->_page);
					 //var_dump($limit);
					 
					 $pager = printPageNavigation( "?m=search&searchgroup=$searchgroup&search_string=$this->_query", $this->_page, $num_pages, $offset, $resultLimit, $cur_plugin->num_searchresults);
					 $retval .= '</td></tr></table></td></tr></table><br>';
					 $retval .= $pager;
					 
					 $retval .= '<table cellspacing="0" cellpadding="0" align="center" width="100%" class="block-divider"><tr><td><img src="./images/speck.gif" width="1" height="2" alt=""></td></tr></table>';
					}
				 }			 

			 next($result_plugins);
		}
		
		
		reset($result_plugins);
		
		$total_found = 0;
		
		foreach($result_plugins as $key)
		{
		   $total_found .= $key->num_searchresults;
		}
		$retval .= '</table></td></table>';
		return $retval;
   
   }
   function _searchContacts()
   {
   	  global $dPconfig, $AppUI;
	  
	  $obj = new CContact();

	  //$search_string = urlencode($this->_query);
	  $search_string = $this->_query;
	  
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
	  $q->addTable('contacts', 'a');
	  $q->addQuery('a.contact_id, a.contact_first_name, a.contact_other_name,a.contact_last_name, a.contact_mobile, a.contact_email, a.contact_email2, a.contact_phone, a.contact_phone2, a.contact_address1, a.contact_address2');
	  $q->addOrder('a.contact_first_name, a.contact_last_name');
	  if ($search_string != "") 
	  { 
		$q->addWhere("a.contact_first_name LIKE '%$search_string%' OR a.contact_last_name LIKE '%$search_string%' OR a.contact_other_name LIKE '%$search_string%'");
	  }
	  //$sql = $q->prepare()
	  $rows = $q->loadList();
	  
	  //var_dump($rows);
	  $numrows = count($rows);
	  $totcount = $obj->getCount();
	  
	  $contact_results = new SearchData();
	  $contact_results->searchlabel = "Contact Search Results";
	  $contact_results->addSearchHeading("Contact Name");
	  $contact_results->addSearchHeading("Mobile Phone No.");
	  $contact_results->addSearchHeading("Phone No.");
	  $contact_results->addSearchHeading("Phone No.(2)");
	  $contact_results->addSearchHeading("Email");
	  $contact_results->addSearchHeading("Email(2)");
	  $contact_results->searchgroup = 'contacts';
          $contact_results->num_searchresults = 0;
	  $contact_results->num_itemssearched = $totcount;
	  $contact_results->search_string = $this->_query;
	  
	  foreach ($rows as $row)
	  {
		  $obj->load($row['contact_id']);
		  
		  $search_result =	array (array($AppUI->_($obj->getFullname()), $obj->getUrl()), $obj->contact_mobile, $obj->contact_phone, $obj->contact_phone2, "<a href=mailto:$obj->contact_email>$obj->contact_email</a>", "<a href=mailto:$obj->contact_email2>$obj->contact_email2</a>");	
		  $contact_results->addSearchResult($search_result);
		  $contact_results->num_searchresults++;  
	  }

      return $contact_results;
   
   }

   function _searchBuildingSolutions()
   {
   	  global $dPconfig, $AppUI;
	  
	  $obj = new CBuildingSolution();

	  //$search_string = urlencode($this->_query);
	  $search_string = $this->_query;
	  
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
	  $q->addTable('building_solution', 'a');
	  $q->addQuery('a.building_solution_id, a.building_solution_location,a.building_solution_equipment_location');
	  $q->addOrder('a.building_solution_location');
	  
	  if ($search_string != "") 
	  { 
		$q->addWhere("a.building_solution_location LIKE '%$search_string%'");
	  }
	  //$sql = $q->prepare()
	  $rows = $q->loadList();
	  
	  //var_dump($rows);
	  $numrows = count($rows);
	  $totcount = $obj->getCount();
	  
	  $bs_results = new SearchData();
	  $bs_results->searchlabel = "Building Solutions Search Results";
	  $bs_results->addSearchHeading("Building Name");
	  $bs_results->addSearchHeading("Connection Type");
	  $bs_results->addSearchHeading("Equipment Location");
      $bs_results->num_searchresults = 0;
	  $bs_results->searchgroup = 'buildingsolution';
      $bs_results->num_itemssearched = $totcount;
	  $bs_results->search_string = $this->_query;
	  foreach ($rows as $row)
	  {
		  $obj->load($row['building_solution_id']);
		  
		  $search_result =	array (array($AppUI->_($obj->building_solution_location),"index.php?".$obj->getUrl()), $obj->getConnectionType(), $obj->building_solution_equipment_location);	
		  $bs_results->addSearchResult($search_result);
		  $bs_results->num_searchresults++;  
	  }

      return $bs_results;
	
   }   
   function doSearch()
   {
		global $dPconfig, $AppUI;
		
		//$perms = & $AppUI->acl();
		
		//if (!($canSearch = $perms->checkModuleItem($m, "search")))
		//{
		   //$AppUI->redirect("m=public&a=access_denied");
		//}
		
		$this->client_results = $this->_searchClients();
		
		//var_dump($this->company_results);
		$this->contact_results = $this->_searchContacts();
		//$this->ip_results = $this->_searchIPs();
		$this->clinic_results = $this->_searchClinics();
	 	
		$this->nrows_plugins = $this->nrows_plugins + $this->client_results->num_searchresults;
        $this->nrows_plugins = $this->nrows_plugins + $this->contact_results->num_searchresults;
        $this->nrows_plugins = $this->nrows_plugins + $this->clinic_results->num_searchresults;
        
        //$this->nrows_plugins = $this->nrows_plugins + $this->domain_results->num_searchresults;
        //$this->nrows_plugins = $this->nrows_plugins + $this->bs_results->num_searchresults;
        //$this->nrows_plugins = $this->nrows_plugins + $this->ip_results->num_searchresults;
		
        $this->total_plugins = $this->total_plugins + $this->client_results->num_itemssearched;
        $this->total_plugins = $this->total_plugins + $this->contact_results->num_itemssearched;
        $this->total_plugins = $this->total_plugins + $this->clinic_results->num_itemssearched;
        //$this->total_plugins = $this->total_plugins + $this->domain_results->num_itemssearched;
		//$this->total_plugins = $this->total_plugins + $this->bs_results->num_itemssearched;
		//$this->total_plugins = $this->total_plugins + $this->ip_results->num_itemssearched;
		
		$this->result_plugins = array();
		array_unshift($this->result_plugins, $this->client_results, $this->clinic_results, $this->contact_results);
		//$result_plugins = array($this->company_results);
        // Format results
		if (empty($this->_searchgroup))
			$retval = $this->_formatResults($this->nrows_plugins, $this->total_plugins, $this->result_plugins, $this->searchtime);
		else
		    $retval = $this->_formatSearchGroup($this->nrows_plugins, $this->total_plugins, $this->result_plugins, $this->searchtime, $this->_searchgroup);
		//var_dump($retval);
        return $retval;
    
   }
   function _showPager($resultPage, $pages)
   {
		$search_string = @dPformsafe($this->_query);
		$search_group = @dPformsafe($this->_searchgroup);
		$pager = '';
		if (isset($this->_searchgroup))
		{
			if ($pages > 1) 
			{
				if ($resultPage > 1)
				{
				    $previous = $resultPage - 1;
					$pager = "<a href=?m=search&amp;search_string=$search_string&amp;searchgroup=$search_group&amp;page=$previous>Previous</a>";
				}
				if ($pages <= 20)
				{
					$startPage = 1;
					$endPage = $pages;
				}
				else
				{
					$startPage = $resultPage - 10;
					if ($startPage < 1)
					{
						$startPage = 1;
					}
					$endPage = $resultPage + 9;
					if ($endPage > $pages)
					{
						$endPage = $pages;
					}
					
					for ($i = $startPage; $i < $endPage; $i++)
					{
						if ($i == $resultPage)
						{
						   $pager .= "<strong>$i</strong>";
						}
						else
						{
						   $pager .= "<a href=?m=search&amp;search_string=$search_string&amp;searchgroup=$search_group&amp;page=$i>$i</a>";
						}
					}
					if ($resultPage < $pages)
					{
						$next = $resultPage + 1;
						$pager .= "<a href=?m=search&amp;search_string=$search_string&amp;searchgroup=$search_group&amp;page=$next>Next</a>";
					}
				}
			}
		}
	return $pager;	
   }
   
   function _showHeader($limit, $searchgroup, $searchlabel, $count)
   {
		$search_string = @dPformsafe($this->_query);
		$header = '';
		$header = "<td>Showing 1 - $limit of <a href=?m=search&amp;search_string=$search_string&amp;searchgroup=$searchgroup>$count $searchlabel</a></td>";
		return $header;
   }
   
}
?>
