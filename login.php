<?php 
require_once('config1.php');
function userLogin($conn){
	if(isset($_POST['loginbtn'])){
		$username = $_POST['uname'];
		$pass1 = $_POST['pass'];
		$pass = hash('sha256', $pass1);

		if(empty($username)||empty($pass1)){
			?>  
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-danger">
						<div class="text-center">Provide all the fields.</div>
						
					</div>
				</div>
			</div>

			<?php
		}else{
			$query = "SELECT * FROM tbl_users WHERE username = ? AND password = ?";
			$params = array($username, $pass);

			$stmt = sqlsrv_query($conn, $query, $params);

			if($stmt === false){
				die(print_r(sqlsrv_errors(), true));
			}

			if(sqlsrv_has_rows($stmt)){
				$row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
				session_start();
				$_SESSION['user_id'] = $row['user_id'];
				$_SESSION['role_id'] = $row['role_id'];

				header("Location: index.php");
			}else{
				echo "Invalid username or password";
			}
		}

		sqlsrv_free_stmt($stmt);
		sqlsrv_close($conn);
	}
}

?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>OVS Login</title>
    <style>
        body{display: flex; justify-content: center; flex-direction: column; width: 100vw; height: 100vh;}
    </style>
	<link rel="stylesheet" type="text/css" href="assets/bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="assets/fontawesome/css/all.min.css">
</head>
<body>
<h1 style="text-align: center; color: blue; ">OVS(Oshwal Voting System)</h1>
<h2 style="text-align: center;" class="mt-5">Login to Continue</h1>
<div class="row">
	<div class="col-md-4 offset-4">
		<div class="alert alert-info">
			<?= userLogin($conn)?>
			<form method="POST" action="login.php">
				<label>Username:</label>
				<input type="text" name="uname" class="form-control">
				<label>Password:</label>
				<input type="password" name="pass" class="form-control">

				<button type="submit" name="loginbtn" class="btn btn-primary mt-3">Login <i class="fa fa-right-to-bracket ps-2"></i></button>
			</form>
		</div>
	</div>
</div>

<script src="assets/bootstrap/js/bootstrap.min.js"></script>
<script src="assets/fontawesome/js/all.min.js"></script>
</body>
</html>