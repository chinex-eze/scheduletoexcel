
<?php 
	require_once('ISimpleGenerator.php'); 
	require_once('TextFileProcessor.php'); 
	require_once('Excel5Writer.php'); 
	require_once('HTMLTableWriter.php'); 
	require_once('ExcelGenerator.php'); 

	class WorkSchedule implements ISimpleGenerator{
		
		protected $_input_file_processor = null; 
		protected $_output_file_processor = null; 
		
		public function setInputFile(IFileProcessor $fileProcessor){
			$this->_input_file_processor = $fileProcessor; 
		}  
		
		public function setOutPutFile(IOutputGenerator $outputGenerator){
			$this->_output_file_processor = $outputGenerator; 
		}  		
		
		public function process(){
			$this->_input_file_processor->process();  
			$this->_output_file_processor->loadData($this->_input_file_processor->getFileArray()); 
			$this->_output_file_processor->process();  
			//$this->_output_file_processor->addWriter(new Excel5Writer());  
			$this->_output_file_processor->generateOutPut(); 
		}  
		
		public function getOutPut(){  
			return $this->_output_file_processor->getOutPutFile(); 
		}
	}
?>