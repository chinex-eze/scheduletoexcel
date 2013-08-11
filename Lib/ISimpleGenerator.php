
<?php

	require_once('IFileProcessor.php'); 
	require_once('IOutputGenerator.php'); 

	interface ISimpleGenerator{
	
		public function setInputFile(IFileProcessor $fileProcessor);  
		public function setOutPutFile(IOutputGenerator $outputGenerator);  
		public function process();  
		public function getOutPut();  
	}
?>