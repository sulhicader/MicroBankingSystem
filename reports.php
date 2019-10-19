<?php  
session_start();
require_once('inc/connection.php'); ?>

<?php 
	if(isset($_POST['sub1'])){
		//if(isset($_POST['acc_num']) )
		$acc_num = mysqli_real_escape_string($connection , $_POST['acc_num']);
		$s_date1 = mysqli_real_escape_string($connection , $_POST['s_date1']);
		$e_date1 = mysqli_real_escape_string($connection , $_POST['e_date1']);

		$query1 = "SELECT datecomparision('{$s_date1}','{$e_date1}')";
		$result_set1 = mysqli_query($connection , $query1);

		if ($result_set1){
			$vae = mysqli_fetch_assoc($result_set1);
			$ret = ($vae ["datecomparision('{$s_date1}','{$e_date1}')"]);
			if( $ret == 1) {
				$query = "SELECT transaction_ID,time,date,amount,trans_type FROM transaction WHERE account_id='{$acc_num}' AND ( date >= '{$s_date1}' AND date < '{$e_date1}')";

				$result_set = mysqli_query($connection , $query);		
				if ($result_set){
					if (mysqli_num_rows($result_set) > 0) {
    // output data of each row
						print_r ("<p>id        time        date        amount        trans_type </p>");
    					while($row = mysqli_fetch_assoc($result_set)) {
        					echo $row["transaction_ID"]. "    " .$row["time"]. "    " . $row["date"]."    " .$row["amount"]."    " . $row["trans_type"]. "<br>";
    					}
    					
    				}else{
    					echo "may be account not exisits";
    				}
				}else{
					echo "Incorrect Query";
				}
			}else{
				echo "Date given is not correct";
			}

		}
		

	}

	if(isset($_POST['sub2'])){
		//if(isset($_POST['acc_num']) )
		$agent = mysqli_real_escape_string($connection , $_POST['agent']);
		$s_date2 = mysqli_real_escape_string($connection , $_POST['s_date2']);
		$e_date2 = mysqli_real_escape_string($connection , $_POST['e_date2']);

		$query2 = "SELECT datecomparision('{$s_date2}','{$e_date2}')";
		$result_set2 = mysqli_query($connection , $query2);

		if ($result_set2){
			$vae = mysqli_fetch_assoc($result_set2);
			$ret = ($vae ["datecomparision('{$s_date2}','{$e_date2}')"]);
			
			if( $ret == 1) {
				$query3 = "SELECT transaction_ID,account_id,time,date,amount,trans_type FROM transaction WHERE agent_id='{$agent}' AND ( date >= '{$s_date2}' AND date < '{$e_date2}')";

				$result_set3 = mysqli_query($connection , $query3);		
				if ($result_set3){
					if (mysqli_num_rows($result_set3) > 0) {
    // output data of each row
						print_r ("<p>id        account_id        time        date        amount        trans_type </p>");  
    					while($row = mysqli_fetch_assoc($result_set3)) {

    						
        					print_r($row["transaction_ID"]."		" . $row["account_id"].  "		" . $row["time"]. "		" . $row["date"]. "		".$row["amount"]."		". $row["trans_type"]. "<br>");
    					}
    					
    				}else{
    					echo "may be account not exisits";
    				}
				}else{
					echo "Incorrect Query";
				}
			}else{
				echo "Date given is not correct";
			}

		}
		

	}


?>
	
	<!DOCTYPE html>
	<html>
	<head>
		<title></title>
	</head>
	<body>
		<form action="reports.php" method="post">
			<p>
			<label>Enter the Account Num</label><input type="number" name="acc_num" required / ></p><p>
			<label>Staring Date</label><input type="date" name="s_date1" required>
			</p><p>
			<label>End Date</label><input type="date" name="e_date1" required>
		</p><p>
			<input type="submit" name="sub1"></p>
		</form>

		<form action="reports.php" method="post">
			<p>
			<label>Enter the Agent id</label><input type="number" name="agent" required / ></p><p>
			<label>Staring Date</label><input type="date" name="s_date2" required>
			</p><p>
			<label>End Date</label><input type="date" name="e_date2" required></p><p>
			<input type="submit" name="sub2"></p>
		</form>

	</body>
	</html>
<?php mysqli_close($connection);  ?>