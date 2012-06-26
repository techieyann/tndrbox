<?php

/***********************************************
file: new_user.php
creator: Ian McEachern

This script adds new user credentials to the 
database, after confirming the recaptcha was
successful, sanity checking the email format,
and verifying the unique-ness of the email.
 ***********************************************/


//Recaptcha code

require_once('../includes/recaptchalib.php');
$privatekey = "6LchVNESAAAAADEG1tWkGm4ooU8WwyWaqYXl9q8w";
$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

if (!$resp->is_valid) //recaptcha failed
  {
	header("location/signup?error=captcha");
	exit;	    
  } 
else //check the rest of the content
  {
	require('../includes/includes.php');
	require('../includes/db_interface.php');

	//connect to the database
	$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	$email = sanitize($_POST['email']);

	//email format check ~[text]@[text].[txt]
	if(preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email, $matches) == 0)
	{
		header("location:/signup?error=email");
		disconnect_from_db($link);
		exit;
	}
	
	//email unique-ness check
	$query = "SELECT FROM members WHERE email = '$email'";
	$result = query_db($query);
	if(mysql_num_rows($result) != 0)
	  {
		header("location:/signup?error=dup");
		disconnect_from_db($link);
		exit;	
	  }

	$pass1 = sanitize($_POST['pass1']);
	$pass2 = sanitize($_POST['pass2']);	

	if(strcmp($pass1,$pass2) != 0 || $pass1 =="")
	  {
		header("location:/signup?error=password");
		disconnect_from_db($link);
		exit;
	  }	
	
	//hash the password
	$md5_pass = md5($pass1);

	//create new user and log them in
	$query = "INSERT INTO members ( 'email', 'password') VALUES ('$email','$md5_pass')";
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
   		header("location:/settings");
	}
	else
	{
 		header("location:/login?error=db");
	}
	disconnect_from_db($link);
  }
?>