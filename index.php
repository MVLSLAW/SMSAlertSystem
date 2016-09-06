<?php
session_start();
include('sql.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="utf-8"> 
<title>SMS - MVLS</title>
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-lg-3"></div>
		<div class="col-lg-6">
		<h1 class="text-center text-primary">MVLS SMS Alert System</h1>
		</div>
		<div class="col-lg-3"></div>
	</div>
</div>
<div class="container">
	<div class="row">
		<div class="col-lg-6 col-lg-offset-3 ">
			<div id='notification'>
				
			</div>
		</div>
	</div>
</div>
<?php
if(!isset($_SESSION['Username']) && isset($_POST['username']) && isset($_POST['password'])){
	//username and password have been submitted. Need to check.
	if(checkLogin($_POST['username'],$_POST['password']) == 1){
		//Correct login
		$_SESSION['Username']=$_POST['username'];
		include('main.inc.html');
	}else{
		//Incorrect login
		//Load sorry data
		setNotificationMessage('alert alert-danger','Wrong Username/Password');
		include('login.inc.html');
	}
}else if(!isset($_SESSION['Username'])){
	//Means they haven't yet entered a username or password
	//Display login box
	include('login.inc.html');
}else if(isset($_SESSION['Username'])){
	//They are logged in. Display text message info.
	include('main.inc.html');
}
else{
	//Some unknown error
	setNotificationMessage('alert alert-danger','Unknown Error');
}

?>
</body>
</html>
<?php
function checkLogin($username,$password){
	global $conn;
	$stmt = $conn->prepare("SELECT * FROM credentials WHERE username = :username AND password = :password");
	$stmt->bindParam(':username',$username);
	$stmt->bindParam(':password',$password);
	$stmt->execute();
	$number_of_rows = $stmt->fetchColumn(); 
	return $number_of_rows;
}
function setNotificationMessage($class,$message){
	echo "<script>";
	echo "console.log('Notification Message Function');";
	echo '$("#notification").html("<div class=\"' . $class . '\">' . $message .'</div>");';
	echo "</script>";
}