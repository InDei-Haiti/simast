<?php
include_once("XmlFileWriter.class.php");

	class ChartData extends XmlFileWriter
	{
		
		private $data;
		public $xml;
		
		public function format_data($data,$type="column",$title="")
		{
				switch ($type) 
				{
					case "column":
						$this->xml = new XmlFileWriter();
						$this->data = $data;
						$this->xml->push('chart');	
							$this->prepare_series_data();
							$this->prepare_graph_data();
							$this->prepare_graph_tile($title);
						$this->xml->pop();
						return $this->xml->getXml();
						break;
					case "line":
						$this->xml = new XmlFileWriter();
						$this->data = $data;
						$this->xml->push('chart');	
							$this->prepare_series_data();
							$this->prepare_graph_data();
							$this->prepare_graph_tile($title);
						$this->xml->pop();
						return $this->xml->getXml();
						break;
					case "pie":
						$this->xml = new XmlFileWriter();
						$this->data = $data;
							$this->xml->push('pie');	
							$this->prepare_pie_data();
                            $this->prepare_graph_tile($title);
						$this->xml->pop();
						return $this->xml->getXml();
					break;
				}

		}
		
		
		
		private function prepare_series_data()
		{
			$this->xml->push('series');
			for($i=0; $i < count($this->data["series"]);$i++) 
			{
    			$this->xml->element('value',$this->data["series"][$i],array("xid"=>$i));
			}
			$this->xml->pop();
		}
		
		private function prepare_pie_data()
		{
			for($i=0; $i < count($this->data["slices"]);$i++) 
			{
    			$this->xml->element('slice',$this->data["slices"][$i],array("title"=>$this->data["titles"][$i], "pull_out"=>true));
			}
		}
		
		private function prepare_graph_data()
		{
			$this->xml->push('graphs');
			for($i=0; $i < count($this->data["graphs"]); $i++) 
			{
    			$this->xml->push('graph',array(
										  "gid"=>$i
										 //,"type"=>$this->data["graphs"][$i]["type"]
										 ,"title"=>$this->data["graphs"][$i]["title"]
										 ,"width"=>$this->data["graphs"][$i]["width"]
										 ,"color"=>$this->data["graphs"][$i]["color"]
										 ,"gradient_fill_colors"=>$this->data["graphs"][$i]["gradient_fill_colors"]
										 )
						   );
				for($j=0;$j < count($this->data["graphs"][$i]["data_values"]);$j++) 
				{
    				$this->xml->element('value',$this->data["graphs"][$i]["data_values"][$j],array(
																 "xid"=>$j
															 	,"description"=>""
															 	,"url"=>""
															 	)
								 );
				}
			
				$this->xml->pop();
			}
			$this->xml->pop();
		}
		
		private function prepare_graph_tile($title)
		{
			$this->xml->push('labels');
				$this->xml->push('label',array("lid"=>0));
					$this->xml->element("x","130");
					$this->xml->element("y","10");
					$this->xml->element("rotate","");
					$this->xml->element("width","");
					$this->xml->element("align","center");
					$this->xml->element("text_color","");
                    $this->xml->element("text_size","12");
					$this->xml->element("text","<b>".$title."</b>");
				$this->xml->pop();
              $this->xml->pop();
		}
	}
?>
