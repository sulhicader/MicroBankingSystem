<?php  session_start();
require_once('inc/connection.php'); ?>

<?php 

	if(isset($_POST['sub'])){
		$x = "1";
		$query = "SELECT account_id from account where transaction_type = '{$x}' ";

		$result_set = mysqli_query($connection , $query);		
			if ($result_set){
				if (mysqli_num_rows($result_set) > 0) {
    // output data of each row
					$count = 1;
					$reduce = array();
					while($row = mysqli_fetch_assoc($result_set)) {
						$acc = $row["account_id"];
						$reduce[$count]=$acc;
						$count+=1;
						
					}
					mysqli_free_result($result_set);
					mysqli_next_result($connection);
					
					for($i=1 ;$i<$count;$i++){
						$acc1 = $reduce[$i];
						
						$query1 = "CALL reduce_quick_transaction('{$acc1}',@status)";
        				$result_set1 =mysqli_query($connection , $query1);
        				if ($result_set1){
        					

        					while($row1 = mysqli_fetch_assoc($result_set1)){
        						
        					}
        					mysqli_free_result($result_set1);
        					mysqli_next_result($connection);
        					
        				}else{
        					echo "not correct";
        					echo mysqli_error($connection);
        				}


					}

        				
    				
    			}else{
    				echo "may be account not exisits";
    			}
			}else{
				echo "Incorrect Query";
			}
	}

	if(isset($_POST['sub1'])){
		$x = "1";
		$query2 = "SELECT account_id,last_modified,interest_per,balance from account NATURAL JOIN ACCOUNT_TYPE" ;

		$result_set2 = mysqli_query($connection , $query2);		
			if ($result_set2){
				if (mysqli_num_rows($result_set2) > 0) {
    // output data of each row
					$count2 = 1;
					$res_ar2 = array();
					while($row = mysqli_fetch_assoc($result_set2)) {
						$res_ar2[$count2]= array(
        				'acc' => $row["account_id"],
        				'l_date' => $row["last_modified"],
        				'interest_per' => $row["interest_per"],
        				'balance' => $row["balance"]);
        				$count2+=1;
						
					}
					mysqli_free_result($result_set2);
					mysqli_next_result($connection);
					
					for($i=1 ;$i<$count2;$i++){
						
						$query3 = "CALL update_balance('{$res_ar2[$i]['acc']}','{$res_ar2[$i]['l_date']}','{$res_ar2[$i]['interest_per']}','{$res_ar2[$i]['balance']}',@status)";
        				$result_set3 = mysqli_query($connection , $query3);
        				if ($result_set3){
        					echo "Done";
        					while($row1 = mysqli_fetch_assoc($result_set3)){
        						
        					}
        					mysqli_free_result($result_set3);
        					mysqli_next_result($connection);	

        				}else{
        					echo "not correct";
        					echo mysqli_error($connection);
        				}
        			}


        				
        				
    			}else{
    				echo "may be account not exisits";
    			}
			}else{
				echo "Incorrect Query";
			}
	}

	if(isset($_POST['sub2'])){
		
		$query5 =
		 // "SELECT account_id,last_modified,interest_rate,amount,deposite_ID from Fixed_deposite natural join fd_type where closing_date > current_date ";
		"SELECT acc,fl,interest_rate,fa,fd from fd_type natural join (select f.fd_ID as fd_ID , f.last_modified as fl,f.amount as fa,f.deposite_ID as fd , a.balance as ab , f.account_id as acc from fixed_deposite as f inner join account as a on (a.account_id=f.account_id) where f.closing_date > current_date ) as m";

		$result_set5 = mysqli_query($connection , $query5);		
			if ($result_set5){
				if (mysqli_num_rows($result_set5) > 0) {
    // output data of each row
					$count1 = 1;
					$res_ar = array();
					while($row2 = mysqli_fetch_assoc($result_set5)) {
        				$res_ar[$count1] = array(
        				'acc' => $row2["acc"],
        				'last' => $row2["fl"],
        				'rate' => $row2["interest_rate"],
        				'amount' => $row2["fa"],
        				'dep' => $row2["fd"]
        			
        				);
        				$count1+=1;
						
					}
					mysqli_free_result($result_set5);
					mysqli_next_result($connection);
					
					for($i=1 ;$i<$count1;$i++){
						
						$query6 = "CALL update_fd_balance('{$res_ar[$i]['acc']}','{$res_ar[$i]['last']}','{$res_ar[$i]["rate"]}','{$res_ar[$i]["amount"]}','{$res_ar[$i]["dep"]}',@status)";
        				$result_set6 =mysqli_query($connection , $query6);
        				if ($result_set6){
        					while($row1 = mysqli_fetch_assoc($result_set6)){
        						
        					}
        					mysqli_free_result($result_set6);
        					mysqli_next_result($connection);	

        				}else{
        					echo "not correct";
        					echo mysqli_error($connection);
        				}
        			}
        		}else{
    				echo "may be account not exisits";
    			}
			}else{
				echo "Incorrect Query";
				echo mysqli_error($connection);
			}
	}

	
?>


<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
		<form action="calc.php" method="post">
			<p>
			<input type="submit"  name="sub" value="Charge for Quick Transaction">
		</p>
		<hr>
		<p>
			<input type="submit"  name="sub1" value="Add Interest for Serving Account">
</p>
<hr>
			<p>
				<input type="submit"  name="sub2" value="Add Interest for Fixed Deposit Account">

			</p>

			
		</form>
</body>
</html>