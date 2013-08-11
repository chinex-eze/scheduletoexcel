
<?php
	include_once('TextFileProcessor.php'); 
	include_once('ExcelGenerator.php'); 
	include_once('HTMLTableGenerator.php'); 
	include_once('WorkSchedule.php'); 
	include_once('TextInputProcessor.php'); 
	
	
	class ScheduleApp{
		
		public static function process(array $params_arr){ 
			
			if($params_arr['OUTPUTFILEPROCESSOR'] == 'ExcelGenerator') 
					$writer = 'Excel5Writer'; 
			else	
					$writer = 'HTMLTableWriter'; 
			
			$wrkSched = new WorkSchedule();  
			$wrkSched->setInputFile(new $params_arr['PROCESSOR']($params_arr['PARAM'])); 
			$wrkSched->setOutPutFile(new $params_arr['OUTPUTFILEPROCESSOR'](new $writer)); 
			$wrkSched->process(); 
			$fileLoc = $wrkSched->getOutPut(); 
			echo '<p>Download <a href="'.$fileLoc.'" target="_blank">file</a>. <br />Departure in about 5 mins...
			Ooops! sorry, I mean: Link expires in about 5 mins.</p>'; 
		}
	}
?>