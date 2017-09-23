<?php

class SearchData
{

  var $searchlabel = '';
  var $view_url = '';
  var $search_string = '';
  var $num_searchresults = '';
  var $searchheading = array();
  var $searchresults = array();
  var $num_itemssearched = 0;
  var $num_searchheadings = 0;
  var $searchgroup = '';
  var $expandedSearchSupport = false;
  
  function SearchData($search_string='')
  {
     $this->reset();
	 $this->search_string = $search_string;
  }
  
  function reset()
  {
        $this->searchlabel = '';
        $this->searchheading = array();
        $this->num_searchresults = 0;
        $this->searchresults = array();
        $this->num_itemssearched = 0;
        $this->num_searchheadings = 0; 
  }
  
  function addSearchHeading($heading)
  {
        $this->num_searchheadings = $this->num_searchheadings + 1;
        $this->searchheading[$this->num_searchheadings] = $heading;
  }
  
  function addSearchResult($result_string)
  {
    $this->searchresults[] = $result_string;
  }

  function setExpandedSearchSupport($switch)
  {
        if (!is_bool($switch)) 
		{
            $switch = false;
        }
        
        $this->_expandedSearchSupport = $switch;
  }
  function supportsExpandedSearch()
  {
       return $this->_expandedSearchSupport;
  }
  function setSearchGroup($searchgroup)
  {
	$this->searchgroup = $searchgroup;
  }
  function getUrl()
  {
	return "index.php?m=search&amp;search_string=$this->search_string&amp;searchgroup=$this->searchgroup";
  }

}
?>