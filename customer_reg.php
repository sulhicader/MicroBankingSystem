<?php session_start();
require_once('inc/connection.php');




	$errors = array();
	if(isset($_POST['sub'])){
		//if(isset($_POST['acc_num']) )
		$first = mysqli_real_escape_string($connection , $_POST['first']);
		$last = mysqli_real_escape_string($connection , $_POST['last']);
		// $w_phone = mysqli_real_escape_string($connection ,$_POST['w_phone']);
		$phone = mysqli_real_escape_string($connection ,$_POST['phone']);
		$nic =  mysqli_real_escape_string($connection ,$_POST['nic']);
		$dob =  mysqli_real_escape_string($connection ,$_POST['dob']);
		$address =  mysqli_real_escape_string($connection ,$_POST['pass']);
		
		
		
		$query = "INSERT INTO `customer` (`customer_id`, `first_name`, `last_name`, `telephone_mobile`,  `NIC`, `dateofbirth`, `address`) VALUES(NULL, '{$first}','{$last}', '{$phone}','{$nic}', '{$dob}','{$address}');";

		$result_set  = mysqli_query ( $connection , $query );
		
		
		if($result_set){
			header('Location: title.php');
			

		}
		else{
			$errors = 'Database query failed';
			echo  mysqli_error($connection);
		}
		
	}
	
?>


<!DOCTYPE html>
<html>
<head>
	<title>
		
	</title>
</head>
<body>
	<form method="post" action="customer_reg.php">
		<p>
			<label> First Name </label><input type="text" name="first" required / ></p>
			<p>
			<label> Last Name </label><input type="text" name="last" required / ></p>
			<p>
			<!-- <label> Phone Number(Work Place) </label><input type="number" name="w_phone" required / ></p> -->
			<p>
			<label> Phone Number(Mobile) </label><input type="text" name="phone" required / ></p>
			<p>
			<label> NIC </label><input type="text" name="nic" required / ></p>
			<p>
			<label> DoB </label><input type="date" name="dob" required / ></p>
			<p>
			<label> Address </label><input type="text" name="pass" required></p>
			<p>
			<input type="submit" name="sub" ></p>
		
	</form>
</body>
</html>