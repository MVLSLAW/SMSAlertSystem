<?php
//SQL Information
$SQLUsername = 'username';
$SQLPassword = 'password';

//Twilio API Information
$account_sid = '12345'; 
$auth_token = 'abcde'; 


try{
	$conn = new PDO('mysql:host=localhost;dbname=wwwclues_sms', $SQLUsername, $SQLPassword);
} catch (PDOException $e) {
	throw new Exception("Error!: " . $e->getMessage());
}