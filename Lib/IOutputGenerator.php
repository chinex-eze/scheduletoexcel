
<?php
	interface IOutputGenerator{
	  
		public function loadData(array $input_array); 
		public function process(); 
		public function addWriter(IDocWriter $docRriter); 
		public function generateOutPut();  
		public function getOutPutFile(); 
	}
?>