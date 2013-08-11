
<?php
	require_once('IOutputGenerator.php'); 
	require_once('IDocWriter.php');
	
	error_reporting(E_ALL); 
	date_default_timezone_set('Europe/Helsinki');
	
	class HTMLTableGenerator implements IOutputGenerator { 
		
		protected $_input_Array;  
		protected $_processed_Array; 
		protected $_docWrriter;
		
		protected $_outPutFilePath='./Downloads/';
		protected $_outPutFileName='Unknown'; 
		
		protected $topCaption='';
		
		protected static $_week_day_arr = array('ma'=>'Monday', 'ti'=>'Tuesday', 'ke'=>'Wednesday', 
												'to'=>'Thursday', 'pe'=>'Friday', 'la'=>'Saturday', 
												'su'=>'Sunday');
		
		protected static $_shift_heading = array(
													'<th>&nbsp;</th>', '<th>&nbsp;</th>',
													'<th colspan="3">Morning</th>', '<th>&nbsp;</th>', 
													'<th colspan="3">Evening</th>', '<th>&nbsp;</th>', '<th>&nbsp;</th>', 
												);
		
		protected static $_heading = array( '<th>Day</th>', '<th>Date</th>', '<th>Code</th>', '<th>Start</th>', '<th>End</th>', 
											'<th>Code</th>', '<th>Start</th>', '<th>End</th>', '<th>&nbsp;</th>', 
											'<th>Hours</th>', '<th>Extra</th>'); 
		
		public function __construct(IDocWriter $writer){
			$this->addWriter($writer); 
		}
		
		public function loadData(array $input_array){ 
			$this->_input_Array = $input_array; 
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
		
		
		
		protected function formatOutputArray(){
			$temp = array(); 
			$strName=''; 
			$str=''; 
			$strTopMsg =''; 
			$TotalHours = 0.0; 
			$CellIndex = 1; $temp1 = array(); 
			
			$colNumStartMn=3; $colNumStartEv=7; $colNumEndMn=4; $colNumEndEv=8; $colNumCode=6; $colNumHours=10;
			
			foreach($this->_processed_Array as $index=>$line){ 
				for($i=0; $i < count($line); $i++){  
					switch($i){	
						case 0:  
								if( array_key_exists(strtolower($line[$i]), self::$_week_day_arr) ){ 
									///perform week days line processing  
									$temp1[$i] = self::$_week_day_arr[strtolower($line[$i])];  
								} elseif($index==0){ //is this the first row?
									$strName .= $line[$i] . ' ';
								}
								else{				//just add all other texts together
									$str .= $line[$i] . ' ';
								} 
							break;
							
						case 1: 
								if(explode('.', $line[$i], -1)){//check for date value in the format dd.mm.yyyy
									//array_merge( (array)$temp[][$CellIndex], array($i =>$line[$i]) ); 
									$temp1[$i] = $line[$i];  
								} elseif($index==0){ 			//is this the first row?
									$strName .= $line[$i] . ' ';
								}
								else{							//just add all other texts together
									$str .= $line[$i] . ' ';
								} 
							break;
						
						case 2: 
								if($index == 0){ 				//is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} else {
									//array_merge( (array)$temp[][$CellIndex], array($i=>$line[$i]) ); 
									$temp1[$i] = $line[$i];  
								} 
							break;
						
						case 3:
								$tmp = explode(':', $line[$i], 2);
								if(strlen($tmp[0])==2){			///THEn it's schedule time 	
									if(intval($tmp[0]) > 12 ){	///then it's evening shift 	
										//array_merge( (array)$temp[][$CellIndex], array($colNumStartEv=>$line[$i]) ); 
										$temp1[$colNumStartEv] = $line[$i];  
									} else{						///then it's morning shift 
										//array_merge( (array)$temp[][$CellIndex], array($colNumStartMn=>$line[$i]) ); 
										$temp1[$colNumStartMn] = $line[$i];  
									}
								}elseif($index==0){ //is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} 
							break;
						
						case 4: 
								if($index==0){ 					///is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} 
							break;
						
						case 5: 
								$tmp = explode(':', $line[$i], 2);
								if(strlen($tmp[0])==2){			///THEn it's schedule time 	
									if(intval($tmp[0]) > 12 ){	///then it's evening shift end	
										//array_merge( (array)$temp[][$CellIndex], array($colNumEndEv=>$line[$i]) ); 
										$temp1[$colNumEndEv] = $line[$i];  
									} else{						///then it's morning shift end 
										//array_merge( (array)$temp[][$CellIndex], array($colNumEndMn=>$line[$i]) ); 
										$temp1[$colNumEndMn] = $line[$i];  
									}
								}elseif($index==0){ 			///is this the first row?  
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} 
							break;
						
						case 6: 
								if($index==0){ 					//is this the first row? 
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} else {
									//array_merge( (array)$temp[][$CellIndex], array($colNumCode=>$line[$i]) ); 
									$temp1[$colNumCode] = $line[$i];  
								} 
							break; 
						
						case 7: 
								$tmp = explode(':', $line[$i], 2); 
								if(strlen($tmp[0])==2){			///THEn it's schedule time 	
									if(intval($tmp[0]) > 12 ){	///then it's evening shift 	
										$temp1[$colNumStartEv] = $line[$i];  
										//array_merge( (array)$temp[][$CellIndex], array($colNumStartEv=>$line[$i]) ); 
									} else{						///then it's morning shift 
										//array_merge( (array)$temp[][$CellIndex], array($colNumStartMn=>$line[$i]) ); 
										$temp1[$colNumStartMn] = $line[$i];  
									}
								}elseif(strlen($tmp[0])==1 || strlen($tmp[0])==4){	///may be it's the num of hours for that day 
									$tmpTotalHoursDay = floatval( str_replace(',', '.', $tmp[0]) ); 
									//array_merge( (array)$temp[][$CellIndex], array($colNumHours=>tmpTotalHoursDay) );  
									$temp1[$colNumHours] = $tmpTotalHoursDay;  
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
										//array_merge( (array)$temp[][$CellIndex], array($colNumEndEv=>$line[$i]) );  
										$temp1[$colNumEndEv] = $line[$i];  
									} else{						///then it's morning shift 
										//array_merge( (array)$temp[][$CellIndex], array($colNumEndMn=>$line[$i]) );  
										$temp1[$colNumEndMn] = $line[$i];  
									}
								}elseif($index==0){ 			//is this the first row?  
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} 
							break; 
						
						case 10:
								if($index==0){ 					//is this the first row?  
									$strTopMsg .= utf8_encode($line[$i] . ' '); 
								} else { 
									$tmpTotalHoursDay = floatval( str_replace(',', '.', $line[$i]) ); 
									//array_merge( (array)$temp[][$CellIndex], array($colNumHours=>$tmpTotalHoursDay) );  
									$temp1[$colNumHours] = $tmpTotalHoursDay;  
									$TotalHours = $TotalHours + $tmpTotalHoursDay;
								} 
							break;
						default:
							//$strTrash .= $line[$i]; 
							break;
						
					}///end switch
					if((count($line)-1)==$i) { $CellIndex++; $temp[$CellIndex] = $temp1; $temp1=array();  }					
				}//end for
			}///end foreach
			 
			$temp1[$colNumHours-1] = 'Total';  $temp1[$colNumHours] = $TotalHours;  
			
			$CellIndex++; $temp[$CellIndex] = $temp1; $temp1=array();
			//array_merge( (array)$temp[][$CellIndex], array(0=>$str) ); ///in the first column 
			$temp1[0] = $str;  
			
			$CellIndex++; $temp[$CellIndex] = $temp1;  $temp1=array();
			$this->topCaption .= $strName . '<br />' . $strTopMsg; //
			///finally set the name of the file to the name of the schedule
			$this->_outPutFileName = (!empty($strName) ? $strName : 'Unknown');
			
			$temp[$CellIndex] = $temp1; unset($temp1);
			$this->_processed_Array = $temp; 
			unset($temp);
		}///end formatoutput
		
		
		
		public function addWriter(IDocWriter $docWrriter){
			$this->_docWrriter = $docWrriter; 
		}
		
		public function getWriter(){
			return $this->_docWrriter; 
		}
		
		
		public function generateOutPut(){ 
			$this->_docWrriter->addCaption($this->topCaption); 
			$this->_docWrriter->addHeader(self::$_shift_heading); 
			$this->_docWrriter->addHeader(self::$_heading); 
			
			foreach($this->_processed_Array as $data){ 
				$this->_docWrriter->writeDataArray($data); 
			} 
		} 
		

		public function getOutPutFile(){ 
			$this->_outPutFilePath .= $this->_outPutFileName . '.php';
			$this->_docWrriter->writeToFile($this->_outPutFilePath); 
			//return $this->_outPutFilePath.'.php'; 
			return $this->_outPutFilePath; 
		}		
	}
?>