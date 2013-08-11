
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html>
	<?php include_once('./Parts/header.php');?>
	<body>
		<div id="main-container">
		
			<?php include_once('./Parts/top_div.php'); ?>
			
			 <?php include_once('./Parts/side_menu.php'); ?> 
			
			<div id="file-upload-div"> 
				<form action="" method="post" accept="text/txt" 
					enctype="multipart/form-data"> 
					<input type="hidden" name="MAX_FILE_SIZE" value="3000" /><!--not more than 3 KB-->
					<label for="file">Filename:</label>
					<input type="file" name="file" id="file" />

					<input type="submit" name="submit" value="Submit" /> <br />
					Output: <input type="radio" name="output" value="excel" checked="checked"> Excel
							<input type="radio" name="output" value="html"> HTML
				</form>
			</div>
			
			<div id="text-area-div"> 
				<form name="text_input" action="" method="post"> 
					<input type="hidden" name="text_input" value="text" /> 
					<textarea rows="15" cols="55" name="text_area" id="text-schedule"></textarea> 
					
					Output: <input type="radio" name="output" value="excel" checked="checked"> Excel
							<input type="radio" name="output" value="html"> HTML <br />
					<input type="reset" name="submit" value="Clear" /> 
					<input type="submit" name="submit" value="Submit" /> 
				</form>
			</div>
			
			<div id="download-div">
			
		<?php
				include_once('./Lib/ScheduleApp.php'); 
				include_once('./Lib/UploadException.php'); 
				
				$arr=null; $output='ExcelGenerator'; ///excel file is outputted by default
				
				try{
					if(isset($_POST['output']) && $_POST['output'] == 'html'){ $output = 'HTMLTableGenerator'; } 
					
					if(isset($_FILES["file"])){ 
						if ($_FILES["file"]["error"] > 0)
						  {
							throw new UploadException($_FILES["file"]["error"]);
						  }
						else
						{	
							move_uploaded_file($_FILES["file"]["tmp_name"],
								"./TestUploads/" . $_FILES["file"]["name"]); 
							
							$arr = array('PROCESSOR'=>'TextFileProcessor', 
										'PARAM'=>"TestUploads/" . $_FILES["file"]["name"], 
										'OUTPUTFILEPROCESSOR' => $output); 
						} 
					}elseif(isset($_POST['text_input']) 
							&& isset($_POST['text_area'])
							&& !empty($_POST['text_area'])){
							
							$arr = array('PROCESSOR'=>'TextInputProcessor', 
										'PARAM'=>$_POST['text_area'],
										'OUTPUTFILEPROCESSOR' => $output); 
					}
					
					if(isset($arr)){
						echo ScheduleApp::process($arr); 
					}
				}
				catch (Exception $e) { 
					echo 'Sorry, there was error! <br /> '. 
							$e->getMessage(); 
				} 
			?> 
		</div>	
		
		</div>
		<?php include_once('./Parts/footer.php'); ?>			
		
	</body>
</html> 

