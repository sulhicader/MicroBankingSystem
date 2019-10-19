<?php  session_start();
require_once('inc/connection.php'); ?>

<?php 

	$errors = array();
	if(isset($_POST['sub'])){
		//if(isset($_POST['acc_num']) )
		$type = mysqli_real_escape_string($connection , $_POST['type']);
		$cu1 = mysqli_real_escape_string($connection , $_POST['cu1']);
		$cu2 = mysqli_real_escape_string($connection , $_POST['cu2']);
		$transaction_type = mysqli_real_escape_string($connection , $_POST['tra_type']);
		$agent_id = mysqli_real_escape_string($connection , $_POST['agent_id']);
		$password = mysqli_real_escape_string($connection , $_POST['pass']);
		$amount = mysqli_real_escape_string($connection , $_POST['amount']);

		
		if ($cu2==NULL){
			$cu2 = 0 ;
		}
		
		$query = "CALL account_create( '{$cu1}', '{$cu2}','{$type}','{$transaction_type}','{$password}','{$amount}','{$agent_id}',@STATUS )";

		
		$result_set = mysqli_query($connection , $query);
		
		if($result_set){
			if(mysqli_num_rows($result_set)!=0){
				
				echo "it is ok";
				header('Location: title.php');
			}
			else{
				echo "no";
				$errors = 'Inalid Username or Pass';

			}

		}
		else{
			echo "no1";
			$errors = 'Database query failed';
			echo mysqli_error($connection);
		}
		
	}
	else{
		echo "not set";

	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>
		
	</title>
</head>
<body>
	<form method="post" action="create_acc.php">
		<p>
			<label> Account Type </label><select name='type' required>
				<option value = 1>Children</option>
				<option value = 2>Teen</option>
				<option value = 3>Adult</option>
				<option value = 4>Senior</option>
				<option value = 5>Joint</option>

			</select>
		</p>
		<p>
			<label>Customer Id</label></p><p>
			<label>Customer 1</label><input type="number" name="cu1">
			<label>Customer 2</label><input type="number" name="cu2">
		</p>

			<p>
			<label> Transection Type</label><select name='tra_type'>
				<option value = 1>Quick</option>
				<option value = 0>Slow</option>
			</select></p>
		<label>Agent Id</label><input type="number" name="agent_id">
			<p>
			<label> Password </label><input type="password" name="pass" required / ></p>
			<p>
			<label> Depositing Amount </label><input type="text" name="amount" required / ></p>
			
			<input type="submit" name="sub" ></p>
		
	</form>
</body>
</html>