<?php

	$titleBlock = new CTitleBlock( 'Search Results', '', $m, "$m.$a" );
	$titleBlock->addCell ("<form name='searchform' action='?m=search' method='post'>
                        <table>
                         <tr>
                           <td>						   
                              <input class = 'text' type='text' name ='search_string' value='$search_string' />
						   </td>
						   <td>
							  <input type='submit' value='" .$AppUI->_( 'search' )."' class='button' />
						   </td>
						  </tr>
                         </table>
                        </form>");

	$titleBlock->show();

	$searchItem = new Search();
	
	$retval = $searchItem->doSearch();
	if ($searchItem->nrows_plugins > 0) 
	{
		$searchTitle = "Your search for <b> $searchItem->_query </b> returned $searchItem->nrows_plugins total results in the following areas:<br/><br/>";	
	}
	else
	{
		$searchTitle = "Your search for <b> $searchItem->_query </b> returned $searchItem->nrows_plugins total results:<br/><br/>";	
	}
	
?>
<br/><br /><br />	
<?php
	$page = dPgetParam($_GET, 'page', 1);
	$limit = intval($dPconfig['max_limit']);

    $cur_plugin = new SearchData();
	foreach ($searchItem->result_plugins as $cur_plugin)
	{
		
		if ($cur_plugin->num_searchresults == 0) continue;
		$searchTitle .= "<span class=\"search\"><b>$cur_plugin->searchlabel&nbsp;</b>";
		$searchTitle .= "<a href={$cur_plugin->getUrl()}> (" . $cur_plugin->num_searchresults . ")</a></span>&nbsp;";
	}
	echo $searchTitle;  
?>
<br /><br />	
<?php
	//var_dump($retval);
	//count=count of items we want to page
	//$num_pages = ceil ($count / $limit);
	//limit=limit we want to show per page
	//offset=from where we r planning to show
	//$offset = ($page - 1) * $limit;
	//echo printPageNavigation( '?m=search', $page, $num_pages, $offset, $limit, $count, $search );

    echo $retval;
	
?>
