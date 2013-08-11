
<?php
	error_reporting(E_ALL);
	date_default_timezone_set('Europe/Helsinki');
	
	require_once ('IDocWriter.php'); 
	require_once ('./Classes/PHPExcel.php'); 
	
	 
	class Excel5Writer implements IDocWriter{ 
		protected $_docProperties; 
		protected $objPHPExcel; 
								
		
		public function __construct(){
			$this->objPHPExcel = new PHPExcel(); 
		}
		
		public function addDocProperties(array $docPropertiesArr){
			$this->_docProperties = $docPropertiesArr;
			foreach($this->_docProperties as $poperty=>$value){ 
				$this->objPHPExcel->getProperties()->$poperty($value);
				
			} 
		} 
		
		public function getDocProperties(){ 
			return $this->_docProperties; 
		}
		
		
		//you can either set only the active index or set it and name it 
		public function setSheetProperties($sheetIndex, array $sheetPropertiesArr=null){ 
			$this->objPHPExcel->setActiveSheetIndex($sheetIndex);
			if($sheetPropertiesArr){
				foreach($sheetPropertiesArr as $poperty=>$value){ 
					$this->objPHPExcel->getActiveSheet()->$poperty($value);
					
				}
				//each time the excel file is written to, the sheet is set as the current 
				//sheet so that is the default when the document is opened
				$this->objPHPExcel->setActiveSheetIndex($sheetIndex);
			}
		}
		
		public function applySheetStyle(array $targetCellsArr, array $stylesArr){
			foreach($targetCellsArr as $targetCells){ 
				$this->objPHPExcel->getActiveSheet()->getStyle($targetCells)
									->applyFromArray( $stylesArr ); 
			} 
		}
		
		public function applyCellFontAlignment(array $targetCellsArr, array $stylesArr){ 
			foreach($targetCellsArr as $targetCells){ 
				$this->objPHPExcel->getActiveSheet()->getStyle($targetCells)
									->getAlignment()
									->applyFromArray( $stylesArr ); 
			} 
		}
		
		
		public function writeData($cell, $value){ 
			$this->objPHPExcel->getActiveSheet()
						->setCellValue($cell, $value);
		} 
		
		//the arrar index is (in) the sheet cell index format 
		//e.g. array('A1'=>'some data')
		public function writeDataArray(array $inputData){
			foreach($inputData as $cell => $value){
				$this->objPHPExcel->getActiveSheet()
									->setCellValue($cell, $value);
			}
		}
		
		///file location param should include file name without extension 
		public function writeToFile($fileName=null){ 
			$objWriter = PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel5'); 
			if($fileName){ 
				$objWriter->save($fileName. '.xls'); 
			} 
			/*//else{ 
				// Redirect output to a client’s web browser (Excel5)
				//header('Content-Type: application/vnd.ms-excel');
				header('Content-Type: application/vnd.xls');
				header('Content-Disposition: attachment;filename="'.basename($fileName).'.xls"');
				header('Cache-Control: max-age=0'); 
				
				$objWriter->save('php://output'); 
			//}*/ 
		}
		
		
		///calling classes can be able to call this directly and 
		///execute those functionalities that are not offered by 
		///this wrapper class 
		public function getPHPExcelObj(){
			return $this->objPHPExcel; 
		}
	}
?>