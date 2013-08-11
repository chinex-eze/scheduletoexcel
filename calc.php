
<?php
	echo '<h3>Welcome!</h3>'; 
	if(isset($_GET['num']) && isset($_GET['lim'])){
		echo "num: {$_GET['num']} lim: {$_GET['lim']} <br />"; 
		echo sumLast($_GET['num'], $_GET['lim']);
	}else 
		echo 'nothing';
	
	/**
		sums all numbers from the last down to the limit
		e.g. sumLast(5,4) would be 5+4+3+2
	*/
	function sumLast($num, $lim=0){
		$total = 0; 
		while($num > $lim){
			$total += $num;
			--$num;
		}
		return $total;
	} 
	
		/*function sumLast($num, $lim=0){
		if($num > $lim){
			return ($num + sumLast($num-1, $lim));
		}else {
			return $num;
		}
	} */
?>