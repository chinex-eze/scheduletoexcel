
<?php
	require_once('IFileProcessor.php'); 

	class TextFileProcessor implements IFileProcessor{	
		
		protected $_File_Array = null; 
		protected $_File_Location = null; 
		
		function __construct($fileLoc){
			$this->loadFile($fileLoc);
		}
		
		public function getFileArray(){
			return $this->_File_Array;
		} 
		
		/**load up the text file and convert it to an array 
			with each element containing a line from the file **/
		public function process(){
			$this->_File_Array = file($this->_File_Location, 
									FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES); 
		} 
		
		public function loadFile($fileLoc){
			$this->_File_Location = $fileLoc; 
		}
	}
?>