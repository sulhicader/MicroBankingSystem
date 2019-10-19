
<?php
	if(isset($_POST['sub'])){
		session_start();

		$_SESSION['user'] =$_POST['user'];
		$_SESSION['pass'] = $_POST['pass'];
		$_SESSION['host'] = 'localhost';
		$_SESSION['database'] = 'centralbank';
		 


		require_once("inc/connection.php") ;
		
		header('Location: title.php');
 	}


 ?>

<!DOCTYPE html>
	<html>
	<head>
		<title></title>
	</head>
	<body>
		<form action='index.php' method="post">
			<p>
	     <label>Enter the DB User Name : </label><input type="text" name="user">
	     	</p>
	     	<p>
	     		<label>Enter The PassWord : </label><input type="text" name="pass">
	     	</p>
	     	<p>
	     		<input type="submit" name="sub" value="Log In">
	     	</p>
	    </form>
	</body>
	</html>	


