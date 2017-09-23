<?php
class HTMLSpreadSheet
{
	public $data;
	public $meta_data;
	public $meta_data_css; 
	public $initial_list_off_set;
	public $use_text_boxes=true;

	function print_header()
	{
		print "<table align=center border=0 cellspacing=0 cellpadding=0" .
				" style=\"border-collapse:collapse\">\n";
		print "<tr class=\"".$this->meta_data_css["header"]."\">\n";
		print "<td>&nbsp;&nbsp&nbsp&nbsp</td>\n";
		$meta_data_list_size =count($this->meta_data);
		for($i=1;$i<$meta_data_list_size;$i++)
		{
			print "<td align=center>\n&nbsp;&nbsp;<b>";
			if(isset($this->meta_data[$i]["sort_url"]))
			{
				print "<a href=\"".$this->meta_data[$i]["sort_url"]."\">";
			}
			print strtoupper( $this->meta_data[$i]["header"]);
			if(isset($this->meta_data[$i]["sort_url"]))
			{
				print "</a>";
			}			
			print "</b>&nbsp;&nbsp;\n</td>\n";
		}
		print "</tr>\n";
	}
	function print_footer()
	{
		print "</table>\n";
	}
	
	function get_css_name($data_type)
	{
		return $this->meta_data_css[$data_type];
	}
 	function print_data()
	{
		$data_list_size =count($this->data);
		$meta_data_list_size=count($this->meta_data); 
		for($i=0;$i<$data_list_size ;$i++)
		{
			print "<tr>\n";
			print "<td class=\"".$this->meta_data_css["numbering_row"]."\">";
				
				
					$number_element_has_url =false;
					if(isset($this->data[$i][$this->meta_data[0]["id_name"]]) && isset($this->meta_data[0]["popup_url"]))
					{
						$number_element_has_url =true;
					}
					if($number_element_has_url)
					{
							print "<a href=\"";
							print  $this->meta_data[0]["popup_url"]
								."?";
							if(isset($this->meta_data[0]["link_id_name"]))
								print $this->meta_data[0]["link_id_name"];
							print "="								
								.$this->data[$i][$this->meta_data[0]["id_name"]];
						print "\">";
					
					}
				
					print ($i+1+ $this->initial_list_off_set);
				
					if($number_element_has_url)
					{
						print "</a>";
					}
					print ".&nbsp&nbsp";
			
			print "</td>\n";
			for($j=1;$j<$meta_data_list_size;$j++)
			{				
				print "<td align=left valign=top nowrap=\"nowrap\"\n";
				if(!$this->use_text_boxes)
				{
					//CSS
					if(!is_null($this->meta_data[$j]["data_type"]))
								print " class=\"".$this->get_css_name($this->meta_data[$j]["data_type"])."\"";
				}
				print ">\n";
						if (($this->use_text_boxes)  && ($this->meta_data[$j]["data_type"] == "text"))
						{
							print "<input ";
					
							
							//Size
							if(!is_null($this->meta_data[$j]["average_size"]))
								print " size=\"".$this->meta_data[$j]["average_size"]."\"";
							
							//CSS
							if(!is_null($this->meta_data[$j]["data_type"]))
								print " class=\"".$this->get_css_name($this->meta_data[$j]["data_type"])."\"";
							
							//Input type
							print " type=\"text\" ";
							//print " type=\"".$this->meta_data[$j]["data_type"] . "\" ";
						
							
							//Add function or readonly status
							
							if(isset($this->meta_data[$j]["functions"]))
							{	
								
								$this->print_functions($this->meta_data[$j]["functions"],$this->data[$i]);
							}
							else
							{	
								print " readonly ";
							}
						
							//Current Value

							print " value=\"";
							if(isset($this->data[$i][$this->meta_data[$j]["data_index_name"]]))
								print	$this->data[$i][$this->meta_data[$j]["data_index_name"]];

							print "\" ";
							print 	">\n";
						}
						else if ($this->meta_data[$j]["data_type"] == "select")	
						{
							$funcs = $this->get_functions_string($this->meta_data[$j]["functions"],$this->data[$i]);
							//var_dump($this->data[$i][$this->meta_data[$j]["data_index_name"]]);
							$s = arraySelect($this->meta_data[$j]["data_values"], $this->meta_data[$j]["data_index_name"], $funcs,$this->data[$i][$this->meta_data[$j]["data_index_name"]] );
							print ($s);
						}
						else if ($this->meta_data[$j]["data_type"] == "multiselect")	
						{
							$funcs = $this->get_functions_string($this->meta_data[$j]["functions"],$this->data[$i]);
							//$funcs .=  " size=\"" . $this->meta_data[$j]["average_size"] . "\" ";
							//var_dump($this->data[$i][$this->meta_data[$j]["data_index_name"]]);
							//var_dump($this->meta_data[$j]["data_values"]);
							//print "multiselect";
							//var_dump($funcs);
							$ms = arraySelectCheckbox($this->meta_data[$j]["data_values"], $this->meta_data[$j]["data_index_name"]."[]", $funcs,$this->data[$i][$this->meta_data[$j]["data_index_name"]] );
							print ($ms);
						}
				print 	"</td>\n";				
				
			}
			print "</tr>\n";
		}
	}
	
	function print_component_event_function($event_name,$function_name,$parameters)
	{
		print " ".$event_name."=\"return ";
		print $function_name."(this,";
		
		//Print all the parameters
		$parameters_list_size =count($parameters);
		for($i=0;$i<$parameters_list_size;$i++)
		{
			if($i!=0)
				print ",";
			print $parameters[$i];
		}
		
		print ");\" ";
	}
	function get_component_event_function_string($event_name,$function_name,$parameters)
	{
		$s = " ".$event_name."=\"return ";
		$s .= $function_name."(this,";
		
		//Print all the parameters
		$parameters_list_size =count($parameters);
		for($i=0;$i<$parameters_list_size;$i++)
		{
			if($i!=0)
				$s .= ",";
			$s .= $parameters[$i];
		}
		
		$s .= ");\" ";
		return $s;
	}
	function print_functions($meta_data_functions,$data_set_row)
	{

		$meta_data_functions_list_size =count($meta_data_functions);
		for($i=0;$i<$meta_data_functions_list_size;$i++)
		{
			$parameter_list = array();
			if(isset($meta_data_functions[$i]["parameters"]))
			{
				$meta_data_function_parameter_list_size =count($meta_data_functions[$i]["parameters"]);
				for($j=0;$j<$meta_data_function_parameter_list_size;$j++)
				{
					$current_value = $data_set_row[$meta_data_functions[$i]["parameters"][$j]];
					if(!is_numeric($current_value))
					{						
				 		array_push($parameter_list,"'".$current_value."'");
					}					 		
				 	else
				 	{
				 		array_push($parameter_list,$current_value);
				 	}
				}
			}
			if(isset($meta_data_functions[$i]["static_parameters"]))
			{
				$meta_data_function_static_parameter_list_size =count($meta_data_functions[$i]["static_parameters"]);
				for($j=0;$j<$meta_data_function_static_parameter_list_size;$j++)
				{
				 array_push($parameter_list,$meta_data_functions[$i]["static_parameters"][$j]);
				}
			}
			$this->print_component_event_function($meta_data_functions[$i]["event"],
												  $meta_data_functions[$i]["name"],
												  $parameter_list									  	
												  );
			
		}			
	}
function get_functions_string($meta_data_functions,$data_set_row)
	{

		$meta_data_functions_list_size =count($meta_data_functions);
		for($i=0;$i<$meta_data_functions_list_size;$i++)
		{
			$parameter_list = array();
			if(isset($meta_data_functions[$i]["parameters"]))
			{
				$meta_data_function_parameter_list_size =count($meta_data_functions[$i]["parameters"]);
				for($j=0;$j<$meta_data_function_parameter_list_size;$j++)
				{
					$current_value = $data_set_row[$meta_data_functions[$i]["parameters"][$j]];
					if(!is_numeric($current_value))
					{						
				 		array_push($parameter_list,"'".$current_value."'");
					}					 		
				 	else
				 	{
				 		array_push($parameter_list,$current_value);
				 	}
				}
			}
			if(isset($meta_data_functions[$i]["static_parameters"]))
			{
				$meta_data_function_static_parameter_list_size =count($meta_data_functions[$i]["static_parameters"]);
				for($j=0;$j<$meta_data_function_static_parameter_list_size;$j++)
				{
				 array_push($parameter_list,$meta_data_functions[$i]["static_parameters"][$j]);
				}
			}
			$funcs .= $this->get_component_event_function_string($meta_data_functions[$i]["event"],
												  $meta_data_functions[$i]["name"],
												  $parameter_list									  	
												  );
			
		}			
		return $funcs;
	}
	public static function generate_meta_data(
											$average_size=null
											,$data_type=null
											,$header_name=null
											,$http_query_string=null
											,$sorting_settings=null
											,$field_index_name=null
											,$functions=null
											)
	{
		if(is_null($http_query_string))
		{
			$sort_string=null;
		}
		else
		{
			$sort_string =$http_query_string
										."&sort_order=" 
										.$sorting_settings[$field_index_name]["order"]
										."&sort_variable=" 
										.$sorting_settings[$field_index_name]["name"];
		}
		return 
				array(
							"average_size"=>$average_size
							,"data_type"=>$data_type
							,"header"=>$header_name
							,"sort_url"=>$sort_string
							,"data_index_name"=>$field_index_name
							,"functions"=>$functions);
	}
}

?>
