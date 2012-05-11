<?php

/***********************************************
file: new_user.php
creator: Ian McEachern

This script adds new user credentials to the 
database, after confirming the recaptcha was
successful and sanity checking the email format.
 ***********************************************/

require_once('../includes/recaptchalib.php');
  $privatekey = "6LchVNESAAAAADEG1tWkGm4ooU8WwyWaqYXl9q8w";
  $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) 
  {
	header("location/signup?error=captcha");
	exit;	    
  } 
  else 
  {

	require('../includes/includes.php');
	require('../includes/db_interface.php');

	$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	
	$email = sanitize($_POST['email']);

	if(preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email, $matches) == 0)
	{
		header("location:/signup?error=email");
		disconnect_from_db($link);
		exit;
	}
	

$query = "SELECT FROM members WHERE email = '$email'";
$result = query_db($query);
if(mysql_num_rows($result) != 0)
  {
	header("location:/signup?error=dup");
	disconnect_from_db($link);
	exit;	
  }

	$password = sanitize($_POST['pass']);
//password check
if(false)
  {
	header("location:/signup?error=password");
	disconnect_from_db($link);
	exit;
  }	
	

$query = "INSERT INTO members ( 'email', 'password') VALUES ('$email','$password')";
$result = query_db($query);
	if(mysql_num_rows($result) == 1)
	{
		$member = mysql_fetch_array($result);
		$session_id = rand(100,9999);
		$member_id = $member['id'];

		$query = "UPDATE members SET sid='$session_id' WHERE id = '$member_id'";
		$result = query_db($query);
		
		$cookie_val = $username.",".$session_id;

		setcookie("info", $cookie_val, time()+(3600*8), "/");
   		header("location:/home");
	}
	else
	{
 		header("location:/login?error=match");
	}
	disconnect_from_db($link);
  }
?>