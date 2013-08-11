
<?php
	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Helsinki');
	
	require_once ('IDocWriter.php'); 
	
	class HTMLTableWriter implements IDocWriter{ 
	
		protected $empty_table_line = array(); 
		protected $_data=null;  
		protected $_table_caption='';
		
		public static function top_writeups() {
				return '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
									"http://www.w3.org/TR/html4/loose.dtd">
									<html>'. 
									'<head>
										<title> NClean Work Schedule Converter</title>
										<link rel="stylesheet" type="text/css" href="../styles/main-style.css" />
									</head>'
									.'<body>
									<div id="main-container">' . 
									file_get_contents('./Parts/top_div.php'); 
		} 

		public static function bottom_writeups(){
				return  '</div>' . file_get_contents('./Parts/footer.php') .
							'</body>
								</html>'; 
		} 
		
		public static $table_open = '<table border="1px" class="schedule-display-table">'; 
		
		public static $table_header = '<table border="1" class="schedule-display-table">';
		
		public function __construct(){
			for($i=0; $i <= 10; $i++){	///there are 11 columns for the schedule table 
				$this->empty_table_line[$i] = '<td>&nbsp;</td>';
			}
		}

		public function addDocProperties(array $docProperties){ 
		
		}  
		public function getDocProperties(){
			return null;
		}
		
		
		public function addCaption($caption){
			$this->_table_caption .= $caption . '<br />' . PHP_EOL;
		}
		
		/**
			the header should be in the format array('<th col="3">header</th>'."\n"); 
		*/
		public function addHeader($headerArr){
			$this->_data[] = '<tr>'.implode($headerArr).'</tr>';
		}
		
		public function writeDataArray(array $inputData){ 
			$temp = clone (object)$this->empty_table_line; 
			$temp = (array) $temp;
			$tempStr = '';
			
			foreach($inputData as $index => $value ){
				$temp[$index] = '<td>' . $value . '</td>';
			} 
			
			$tempStr .= implode($temp);
			
			$this->_data[] = '<tr>'.$tempStr.'</tr>';
		} 
		
		
		
		/**
			creates the html file and writes the data 
			- all exceptions thrown here are to be handled by 
			  the calling function 
		*/
		public function writeToFile($fileLocation=null){ 
			
			if(!empty($this->_data)){
				$htmlFile = fopen($fileLocation, "wb");
				
				///create the html file with the doc declarations and all...
				fwrite($htmlFile, HTMLTableWriter::top_writeups() .PHP_EOL);
				
				//create the table
				fwrite($htmlFile, self::$table_open.PHP_EOL); 
				if(!empty($this->_table_caption)){
						fwrite($htmlFile, '<caption>'.$this->_table_caption.'</caption>'.PHP_EOL); 
				}
				
				//write data to table 
				foreach($this->_data as $table_row){
					fwrite($htmlFile, $table_row.PHP_EOL);
				}
				
				fwrite($htmlFile, '</table>'.PHP_EOL);
				fwrite($htmlFile, HTMLTableWriter::bottom_writeups() . PHP_EOL);
				
				if(is_resource($htmlFile)){ 
						fclose($htmlFile);} 
			}
			else{
				throw new Exception("Empty data to be written!");
			}
		}///end writeToFile 
	} 
?>