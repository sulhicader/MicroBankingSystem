<?php  session_start();
require_once('inc/connection.php'); ?>

<?php 

	$errors = array();
	if(isset($_POST['sub'])){
		//if(isset($_POST['acc_num']) )
		$acc_num = mysqli_real_escape_string($connection , $_POST['id']);
		$type = mysqli_real_escape_string($connection , $_POST['type']);
		$amount = mysqli_real_escape_string($connection , $_POST['amount']);
		
		if ($type==1){
			$dur = 6;

		}elseif ($type==2) {
			$dur = 12;
		}elseif ($type==3) {
			# code...
			$dur = 36;
		}

		$query = "INSERT INTO fixed_deposite values (NULL,'{$acc_num}','{$type}','{$amount}', curdate() ,curdate(), DATE_ADD(curdate(),INTERVAL {$dur} MONTH))";

		$result_set = mysqli_query($connection , $query);
		
		if($result_set){
			
				echo "it is ok";
				header('Location: title.php');
			
		}
		else{
			echo "Your Filled Data maybe not correct. May be your account not exists<br>";
			$errors = 'Database query failed';
			// echo  mysqli_error($connection);
		}
		
	}
	
?>

<!DOCTYPE html>
	<html>
	<head>
		<title></title>
	</head>
	<body>
		
		<form action="fd.php" method="post">
			<p>
			<label> Serving Account Id </label><input type="text" name="id" required / ></p>
			<p>
			<label>FD type</label><select name="type" required>
			<option value=1>6 Month</option>
			<option value=2>1 Year</option>
			<option value=3>3 Year</option>
			</select></p>
			<p>
			<label> Amount </label><input type="text" name="amount" required / ></p>
			<p>
			<input type="submit" name="sub" ></p>
		</form>
	</body>
	</html>