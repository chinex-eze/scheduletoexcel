
<?php
	
	/**
		This interface represents all objects that can receive inputs (e.g. text file) 
		and convert them to arrays that can be served into output processor to 
		generate the output file (e.g. excel spreadsheet file)
	*/
	interface IFileProcessor{
	
		/**
		  *Gets the array that contains the data from the file 
		   and supplies it to the functiont that formats and 
		   writes it to output
		*/
		public function getFileArray(); 
		public function process(); 
		public function loadFile($fileLoc); 
	}

?>