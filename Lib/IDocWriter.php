
<?php
	interface IDocWriter{
		public function addDocProperties(array $docProperties); 
		public function getDocProperties(); ///returns document properties in array form 
		public function writeDataArray(array $inputData);
		public function writeToFile($fileLocation=null); 
	}
?>