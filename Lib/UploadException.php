
<?php
	class UploadException extends Exception{
	
		private static $MESSAGE_ARRAY = array(
		
		0=>"File Upload was successful.",
        1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
        2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form", 
        3=>"The uploaded file was only partially uploaded",
        4=>"No file was uploaded",
        6=>"Temporary folder error" 
										);
	
		public function __construct($code) {
			$message = self::$MESSAGE_ARRAY[$code];
			parent::__construct($message, $code);
		} 
	}
?>