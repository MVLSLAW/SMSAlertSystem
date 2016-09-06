<?php
//Attempt to limit outside access to this.
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
if(!IS_AJAX) {die('Restricted access');}

$pos = strpos($_SERVER['HTTP_REFERER'],getenv('HTTP_HOST'));
if($pos===false)
  die('Restricted access');

//Must be a real script.
session_start();
header('Content-type: application/json');
include('sql.php');
include('twilio/sdk/Twilio/autoload.php');

/*
Add
$_POST['first_name'] = 'Matthew';
$_POST['last_name'] = "Stubenberg";
$_POST['phone_number'] = "555";
$_POST['method'] = 'addnumber';
*/
/*
Delete
$_POST['Name'] = 'Matthew';
$_POST['ID'] = '20';
$_POST['method'] = 'deletenumber';
*/
/*
Text Message
$_POST['method']='sendtext';
$_POST['Message']='Test';
$_POST['PhoneNumbers'] = array('2404185536');
*/
if(isset($_POST['method']) && $_POST['method'] == 'addnumber'){
	//Add number
	$returnvalue = addNumber($_POST['first_name'],$_POST['last_name'],$_POST['phone_number']);
	if($returnvalue !== true){
		//Means there was an error
		echo json_encode(array('status'=>false,'message'=>$returnvalue));
	}else{
		echo json_encode(array('status'=>true,'message'=>"Succesfully added " . $_POST['first_name'] . " " . $_POST['last_name']. " " . $_POST['phone_number']));
	}
}else if(isset($_POST['method']) && $_POST['method'] == 'deletenumber'){
	//Delete Number
	$returnvalue = deleteNumber($_POST['ID']);
	if($returnvalue !== true){
		//Means there was an error
		echo json_encode(array('status'=>false,'message'=>$returnvalue));
	}else{
		echo json_encode(array('status'=>true,'message'=>"Succesfully deleted " . $_POST['Name'] . ".<br> You might need to refresh the page to see changes"));
	}
}else if(isset($_POST['method']) && $_POST['method'] == 'sendtext'){
	//Send Text Message
	$finalresult = null;
	$phonenumberarray = $_POST['PhoneNumbers'];
	$message = $_POST['Message'];
	if($phonenumberarray[0] == 'on') $phonenumberarray = array_shift($phonenumberarray); //Gets rid of the 'on' from the check all checkbox
	foreach($phonenumberarray as $phonenumber){
		$result = sendText($phonenumber,$_POST['Message']);
		if($result !== true) $finalresult.="--" . $result;
	}
	if($finalresult == null){
		//Texting went well.
		//Saving in message log
		$savemessagereport = saveMessage($_POST['Message'],json_encode($phonenumberarray),$_SESSION['Username']) == true; 
		if($savemessagereport == true) echo json_encode(array('status'=>true,'message'=>"Succesfully sent message to " . count($phonenumberarray) . " people"));
		else echo json_encode(array('status'=>false,'message'=>"Failed Saving Message:" . $savemessagereport));
		
	}
	else echo json_encode(array('status'=>false,'message'=>"Failed Sending Message:" . $finalresult));

}else if(isset($_POST['method']) && $_POST['method'] == 'logout'){
	//Log the person out
	session_destroy();
	echo json_encode(array('status'=>true,'message'=>"Succesfully logged out"));
}else{
	echo json_encode(array('status'=>false,'message'=>"Unknown method: " . json_encode($_POST)));
}
function addNumber($firstname,$lastname,$phonenumber){
	global $conn;
	$stmt = $conn->prepare("INSERT INTO phonenumbers(First_Name,Last_Name,Phone_Number) VALUES (:firstname,:lastname,:phonenumber)");
	$stmt->bindParam(':firstname', $firstname);
	$stmt->bindParam(':lastname', $lastname);
	$stmt->bindParam(':phonenumber', $phonenumber);
	if(!$stmt->execute()){
		return $stmt->errorInfo()[2];
	}else{
		return true;
	}
}
function deleteNumber($ID){
	global $conn;
	$stmt = $conn->prepare("DELETE FROM phonenumbers WHERE ID = :ID");
	$stmt->bindParam(':ID', $ID, PDO::PARAM_INT);
	if(!$stmt->execute()){
		return $stmt->errorInfo()[2];
	}else{
		return true;
	}
}
function sendText($phonenumber,$message){
	global $account_sid; 
	global $auth_token;
	
	$client = new Twilio\Rest\Client($account_sid, $auth_token);
	try{
	  $client->messages->create(
				   $phonenumber,
					[
						"body" => $message,
						"from" => '4432724557'
					]
				);
	}catch(Excetion $e){
		return $e->getMessage();
	}
	//print $client->sid; 
	return true;
}
function saveMessage($message,$phonenumbers,$sender){
		global $conn;
		$sql = "INSERT INTO message_log (Username,Message,Phone_Numbers,Timestamp) 
		Values(:Username,:Message,:Phone_Numbers,:Timestamp)";
		$statement = $conn->prepare($sql);
		//CommunicationType
		$statement->bindValue(':Username',$sender);
		
		//Reciever
		$statement->bindValue(':Message',$message);
		
		//sender
		$statement->bindValue(':Phone_Numbers',$phonenumbers);
			
		//Timestamp
		$date = new DateTime();
		$statement->bindValue(':Timestamp',$date->getTimestamp());
		if($statement->execute() != true) return $statement->errorInfo()[2];
		else return true;
	}