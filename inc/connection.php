<?php 
	

	$connection  = mysqli_connect($_SESSION['host'],$_SESSION['user'],$_SESSION['pass'],$_SESSION['database']);

	
	if (!$connection){
		echo $_SESSION['user'];
		echo $_SESSION['pass'];
		die('Database Connection failed' . mysqli_error($connection) );

	}
?>