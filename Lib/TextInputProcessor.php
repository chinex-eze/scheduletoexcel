
<?php
	require_once('IFileProcessor.php'); 
	
	class TextInputProcessor implements IFileProcessor{
		
		protected $_File_Array = null; 
		protected $_Input_String = null; 
		
		function __construct($strInput){ 
			$this->loadFile($strInput);
		}
		
		public function getFileArray(){ 
			return $this->_File_Array;
		}
		
		public function process(){ 
			$this->_File_Array = preg_split( '/\r\n|\r|\n/', $this->_Input_String, 
									null, PREG_SPLIT_NO_EMPTY ); 
		} 
		
		public function loadFile($strInput){ 
			$this->_Input_String=$strInput;
		} 
	}
?>