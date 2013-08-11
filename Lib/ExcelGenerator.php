 
<?php
	require_once('IOutputGenerator.php'); 
	require_once('IDocWriter.php');
	
	error_reporting(E_ALL); 
	date_default_timezone_set('Europe/Helsinki');
	
	class ExcelGenerator implements IOutputGenerator {
		
		protected $_input_Array;  
		protected $_processed_Array;  
		protected $outputFileHandle; 
		protected $_docWrriter; 
		
		protected $_outPutFilePath='./Downloads/';
		protected $_outPutFileName='Unknown'; 
		
		protected $sheetNum = 0; ///default sheet index 
		
		protected static $_week_day_arr = array('ma'=>'Monday', 'ti'=>'Tuesday', 'ke'=>'Wednesday', 
												'to'=>'Thursday', 'pe'=>'Friday', 'la'=>'Saturday', 
												'su'=>'Sunday');
		
		protected static $_shift_heading = array('D4'=>'Morning', 'H4'=>'Evening'); 
		protected static $_heading = array('B5'=>'Day', 'C5'=>'Date', 'D5'=>'Code', 'E5'=>'Start', 'F5'=>'End', 
											'H5'=>'Code', 'I5'=>'Start', 'J5'=>'End', 'L5'=>'Hours', 'M5'=>'Extra'); 
		
		
		public function __construct(IDocWriter $writer){
			$this->addWriter($writer); 
		}
		
		
		public function generateOutPut(){ 
			$propsArr = array(
							'setCreator' => 'Eze', 
							'setTitle' => 'Work Schedule', 
							'setSubject' => 'NClean Work Schedule', 
							'setDescription' => 'This is an NClean Work Schedule created by "link"', 
							'setModified' => ''.microtime(true)
						); 
						
			$sheetPropsArr = array(
								'setTitle'=>'Work Schedule', 
								'setMergeCells' => array('D2:J2', 'D3:J3', 'D4:F4', 'H4:J4')
							);  

			$styleArr = array(
							'fill' => array( 
									'type' => PHPExcel_Style_Fill::FILL_SOLID,
									'color' => array('rgb'=>'6AAC2B')
									),
							'font' => array('bold' => true,)
						);
			
			$alignArr = array(
							'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
							'vertical'   => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
							'rotation'   => 0
						);

			
			
			$this->_docWrriter->addDocProperties($propsArr);
			
			$this->_docWrriter->setSheetProperties( 0, $sheetPropsArr ); 
			
			///the headers
			$this->_docWrriter->writeDataArray(self::$_shift_heading); 
			$this->_docWrriter->writeDataArray(self::$_heading);
			
			///apply header styles before writing data to file 
			$this->_docWrriter->applySheetStyle(array('D2:H2', 'D3:H3', 'D4:F4', 'H4:J4'), $styleArr); 
			$this->_docWrriter->applySheetStyle(array('B5:J5', 'L5:M5'), $styleArr); 
			
			//align the header fonts 
			$this->_docWrriter->applyCellFontAlignment(array('D2:H2', 'D3:H3', 'C4:F4', 'H4:K4'), $alignArr); 
			$this->_docWrriter->applyCellFontAlignment(array('B5:M5'), $alignArr);
			
			foreach($this->_processed_Array as $data){ 
				$this->_docWrriter->writeDataArray($data); 
			} 
		} 		
		
		
		public function process(){  			
			foreach ($this->_input_Array as $line) {  
				//each line is trimmed, the spaces are removed and split into an array 
				$temp_arr = preg_split("/ /", trim($line), null, PREG_SPLIT_NO_EMPTY); 
				//print_r($temp_arr); echo '<br />';
				$this->_processed_Array[] = array_values($temp_arr); 
			} 
			$this->formatOutputArray();
		} 

		/**
			yeah, I know this is a very long method, but that's 
			because it arranges the data in a format 
			that can be [easily] written by the excel writer; hope it works :D
		*/
		protected function formatOutputArray(){    
			$temp = array(); $CellIndex ='6'; ///start entering lines from row 6 
			$strName=''; 		///hold the name at the top of document
			$str=''; 			///hold other messages e.g. at the bottom 
			$strTopMsg ='';		///hold the notice written at the top of document 
			$strTrash ='';		///unidentified strings are kept here (trash) 
			$TotalHours = 0.0;
			
			foreach($this->_processed_Array as $index=>$line){ 
				for($i=0; $i < count($line); $i++){  
					switch($i){	//each case, just like each counter value represent a column in the array
						case 0:  
								if( array_key_exists(strtolower($line[$i]), self::$_week_day_arr) ){ 
									///perform week days line processing 
									$temp[] = array('B'.$CellIndex =>self::$_week_day_arr[strtolower($line[$i])]);  
								} elseif($index==0){ //is this the first row?
									$strName .= $line[$i] . ' ';
								}
								else{				//just add all other texts together
									$str .= $line[$i] . ' ';
								} 
							break;
							
						case 1: 
								if(explode('.', $line[$i], -1)){//check for date value in the format dd.mm.yyyy
									$temp[] = array('C'.$CellIndex =>$line[$i]); 
								} elseif($index==0){ //is this the first row?
									$strName .= $line[$i] . ' ';
								}
								else{				//just add all other texts together
									$str .= $line[$i] . ' ';
								} 
							break;
						case 2: 
								if($index == 0){ //is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} else {
									$temp[] = array('D'.$CellIndex =>$line[$i]); 
								} 
							break;
						case 3:
								$tmp = explode(':', $line[$i], 2);
								if(strlen($tmp[0])==2){			///THEn it's schedule time 	
									if(intval($tmp[0]) > 12 ){	///then it's evening shift 	
										$temp[] = array('I'.$CellIndex =>$line[$i]); 
									} else{						///then it's morning shift 
										$temp[] = array('E'.$CellIndex =>$line[$i]); 
									}
								}elseif($index==0){ //is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} 
							break;
						case 4: 
								if($index==0){ //is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} 
							break; 
						case 5: 
								$tmp = explode(':', $line[$i], 2);
								if(strlen($tmp[0])==2){			///THEn it's schedule time 	
									if(intval($tmp[0]) > 12 ){	///then it's evening shift 	
										$temp[] = array('J'.$CellIndex =>$line[$i]); 
									} else{						///then it's morning shift 
										$temp[] = array('F'.$CellIndex =>$line[$i]); 
									}
								}elseif($index==0){ //is this the first row?  
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} 
							break;
						case 6: 
								if($index==0){ //is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} else {
									$temp[] = array('H'.$CellIndex =>$line[$i]); 
								} 
							break; 
						case 7: 
								$tmp = explode(':', $line[$i], 2); 
								if(strlen($tmp[0])==2){			///THEn it's schedule time 	
									if(intval($tmp[0]) > 12 ){	///then it's evening shift 	
										$temp[] = array('I'.$CellIndex =>$line[$i]); 
									} else{						///then it's morning shift 
										$temp[] = array('E'.$CellIndex =>$line[$i]); 
									}
								}elseif(strlen($tmp[0])==1 || strlen($tmp[0])==4){	///may be it's the num of hours for that day 
									$tmpTotalHoursDay = floatval( str_replace(',', '.', $tmp[0]) ); 
									$temp[] = array('L'.$CellIndex =>$tmpTotalHoursDay);
									$TotalHours = $TotalHours + $tmpTotalHoursDay;
								}
								elseif($index==0){ //is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								}  
							break; 
						case 8: 
								if($index==0){ //is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} 
							break; 
						case 9: 
								$tmp = explode(':', $line[$i], 2);
								if(strlen($tmp[0])==2){			///THEn it's schedule time 	
									if(intval($tmp[0]) > 12 ){	///then it's evening shift 	
										$temp[] = array('J'.$CellIndex =>$line[$i]); 
									} else{						///then it's morning shift 
										$temp[] = array('F'.$CellIndex =>$line[$i]); 
									}
								}elseif($index==0){ //is this the first row?  
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} 
							break; 
						case 10:
								//$tmpTotalHoursDay = floatval( str_replace(',', '.', $line[$i]) ); 
								if($index==0){ //is this the first row?  
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} else { 
									$tmpTotalHoursDay = floatval( str_replace(',', '.', $line[$i]) ); 
									$temp[] = array('L'.$CellIndex =>$tmpTotalHoursDay);
									$TotalHours = $TotalHours + $tmpTotalHoursDay;
								} 
							break;
						default:
							$strTrash .= $line[$i]; 
							break;
					}///end switch
					if((count($line)-1)==$i) { $CellIndex++; }
				}
			}///end foreach
			//$CellIndex++; 
			$temp[] = array( 'K'.$CellIndex => 'Total', 'L'.$CellIndex => $TotalHours ); 
			
			$CellIndex++; 
			$temp[] = array( 'B'.$CellIndex => $str ); 
			$CellIndex = $CellIndex + 3; 	//unidentified strings (trash) are displayed at the bottom 
			//$temp[] = array( 'B'.$CellIndex => 'CHECK: '.$strTrash ); 
			$temp[] = array( 'D2'=>$strName ); //
			$temp[] = array( 'D3'=>$strTopMsg ); 
			//$temp[] = array('M7'=>'=SUM(L6:L18)'); 
			
			///finally set the name of the file to the name of the schedule
			$this->_outPutFileName = (!empty($strName) ? $strName : 'Unknown');
			
			$this->_processed_Array = $temp; 
			unset($temp);  
		}///end formatoutput 
		
		
		
		public function addWriter(IDocWriter $docWrriter){
			$this->_docWrriter = $docWrriter; 
		}
		
		public function getWriter(){
			return $this->_docWrriter; 
		}
		
		public function loadData(array $input_array){ 
			$this->_input_Array = $input_array; 
		} 
		
		
		public function getOutPutFile(){ 
			$this->_outPutFilePath .= $this->_outPutFileName;
			$this->_docWrriter->writeToFile($this->_outPutFilePath); 
			return $this->_outPutFilePath.'.xls'; 
		} 
	}
?>