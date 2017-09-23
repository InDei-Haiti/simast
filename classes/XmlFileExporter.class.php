<?php
	class XmlFileExporter
	{
		
		public $data;
		public $file_name;
		private $file_handle;
		public $header_data;
		
		public function get_header()
		{	
			if(!is_null($this->header_data))
			{
				return $this->header_data;
			}
			else
			{
				$this->header_data= NULL;
						
				return $this->header_data;
			}
		}
		
		public function write_to_file()
		{
			
			if(!is_null($this->data))
			{
				//Open File
				$this->open_file();
				
				//Write Header
				!is_null($this->get_header()) ? $this->write_line($this->get_header()): NULL;
				
				//Write Data
				$this->write_line($this->data);
				//Close File
				$this->close_file();
			}
			else
			{
				$this->file_name = "error.xml";
			}
			
		}
		public function open_file()
		{
			$this->file_handle =fopen($this->file_name,"w");
			if(!is_null($this->file_name))
			{
				$this->file_handle =fopen($this->file_name,"w");	
			}
			else
			{
				$this->file_name = "data.xml";
				$this->file_handle =fopen($this->file_name,"w");
			}
		}
		public function write_line($line_data)
		{
			fwrite($this->file_handle,$line_data."\n");
		} 
		public function close_file()
		{
			fclose($this->file_handle);
		}
		public function send_file_over_http()
		{
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="'.$this->file_name.'"');
			readfile($this->file_name);			
		}
	}
?>
