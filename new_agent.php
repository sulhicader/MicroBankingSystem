<?php  session_start();
require_once('inc/connection.php'); ?>

<?php
$errors = array();
	if(isset($_POST['sub'])){
		//if(isset($_POST['acc_num']) )
		$first = mysqli_real_escape_string($connection , $_POST['first']);
		$last = mysqli_real_escape_string($connection , $_POST['last']);
		$phone = mysqli_real_escape_string($connection ,$_POST['phone']);
		$nic =  mysqli_real_escape_string($connection ,$_POST['nic']);
		$dob =  mysqli_real_escape_string($connection ,$_POST['dob']);
		$address =  mysqli_real_escape_string($connection ,$_POST['adds']);
		
		
		
		$query = "INSERT INTO `agent` (`agent_id`, `first_name`, `last_name`, `telephone_mobile`,  `NIC`, `dateofbirth`, `address`) VALUES(NULL, '{$first}','{$last}', '{$phone}','{$nic}', '{$dob}','{$address}');";

		$result_set  = mysqli_query ( $connection , $query );
		
		
		if($result_set){
			header('Location: title.php');
			

		}
		else{
			$errors = 'Database query failed';
			echo  mysqli_error($connection);
		}
		
	}
	else{
		echo "not set";

	}

 ?>
<!DOCTYPE html>
	<html>
	<head>
		<title></title>
	</head>
	<body>
		
		<form action="new_agent.php" method="post">
			<p>
			<label> First Name </label><input type="text" name="first" required / ></p>
			<p>
			<label> Last Name </label><input type="text" name="last" required / ></p>
			<label> Phone Number </label><input type="text" name="phone" required / >
			<p>
			<label> NIC </label><input type="text" name="nic" required / ></p>
			<p>
			<label> DoB </label><input type="date" name="dob" required / ></p>
			<p>
			<label> Address </label><input type="text" name="adds" required></p>
			<p>
			<input type="submit" name="sub" ></p>
		</form>
	</body>
	</html>