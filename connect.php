<?php 
	$connection = new mysqli('localhost', 'root','','dbparkyu');
	
	if (!$connection){
		die (mysqli_error($mysqli));
	}
		
?> 