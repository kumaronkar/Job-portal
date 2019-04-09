<?php

//To Handle Session Variables on This Page
session_start();

//Including Database Connection From db.php file to avoid rewriting in all files
require_once("db.php");

//If user Actually clicked login button 
if(isset($_POST)) {

	//Escape Special Characters in String
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$password = mysqli_real_escape_string($conn, $_POST['password']);

	//Encrypt Password
	$password = base64_encode(strrev(md5($password)));

	//sql query to check user login
	$sql = "SELECT id_user, firstname, lastname, email, active,type FROM users WHERE email='$email' AND password='$password'";
	$result = $conn->query($sql);

	//if user table has this this login details
	if($result->num_rows > 0) {
		//output data
		while($row = $result->fetch_assoc()) {
			$_SESSION['type']=$row['type'];

			if($row['active'] == '0') {
				$_SESSION['loginActiveError'] = "Your Account Is Not Active. Check Your Email.";
		 		header("Location: login.php");
				exit();
			} else if($row['active'] == '1') { 

				//Set some session variables for easy reference
				$_SESSION['name'] = $row['firstname'] . " " . $row['lastname'];
				$_SESSION['id_user'] = $row['id_user'];

				if(isset($_SESSION['callFrom'])) {
					$location = $_SESSION['callFrom'];
					unset($_SESSION['callFrom']);
					
					header("Location: " . $location);
					exit();
				} else {
					header("Location: user/index.php");
					exit();
				}
			} else if($row['active'] == '2') { 

				$_SESSION['loginActiveError'] = "Your Account Is Deactivated. Contact Admin To Reactivate.";
		 		header("Location: login.php");
				exit();
			}

			//Redirect them to user dashboard once logged in successfully
			
		}
 	}
 	else if('1'=='1'){
		$sql = "SELECT id_company, companyname, email, active FROM company WHERE email='$email' AND password='$password'";
		$result = $conn->query($sql);

		//if company table has this this login details
		if($result->num_rows > 0) {
			//output data
			while($row = $result->fetch_assoc()) {
				$_SESSION['type']=$row['type'];
				if($row['active'] == '2') {
					$_SESSION['companyLoginError'] = "Your Account Is Still Pending Approval.";
					header("Location: login-company.php");
					exit();
				} else if($row['active'] == '0') {
					$_SESSION['companyLoginError'] = "Your Account Is Rejected. Please Contact For More Info.";
					header("Location: login-company.php");
					exit();
				} else if($row['active'] == '1') {
					// active 1 means admin has approved account.
					//Set some session variables for easy reference
					$_SESSION['name'] = $row['companyname'];
					$_SESSION['id_company'] = $row['id_company'];

					//Redirect them to company dashboard once logged in successfully
					header("Location: company/index.php");
					exit();
				} else if($row['active'] == '3') {
					$_SESSION['companyLoginError'] = "Your Account Is Deactivated. Contact Admin For Reactivation.";
					header("Location: login-company.php");
					exit();
				}
			}
 		}
 		else if('1'=='1'){
 			//$username = mysqli_real_escape_string($conn, $_POST['username']);
			$password = mysqli_real_escape_string($conn, $_POST['password']);
			$sql = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
			$result = $conn->query($sql);

			//if user table has this this login details
			if($result->num_rows > 0) {
				//output data
				while($row = $result->fetch_assoc()) {
					$_SESSION['type']=$row['type'];
					//Set some session variables for easy reference
					$_SESSION['id_admin'] = $row['id_admin'];
					header("Location: admin/dashboard.php");
					exit();
				}
 		}
 		$_SESSION['loginError'] = $conn->error;
 		header("Location: login.php");
 	} 
 	else {

 		//if no matching record found in user table then redirect them back to login page
 		$_SESSION['loginError'] = $conn->error;
 		header("Location: login.php");
		exit();
 	}

 	//Close database connection. Not compulsory but good practice.
 	$conn->close();

} 
else {
	//redirect them back to login page if they didn't click login button
	header("Location: login.php");
	exit();
	}
}