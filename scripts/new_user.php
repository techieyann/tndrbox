<?php

/***********************************************
file: new_user.php
creator: Ian McEachern

This script adds new user credentials to the 
database, after confirming the recaptcha was
successful, sanity checking the email format,
and verifying the unique-ness of the email.
 ***********************************************/
require('../includes/includes.php');

//connect to the database
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

$admin_flag = false;

if(isset($_GET['admin']))
  {
	analyze_user();
	verify_logged_in();

	$b_id=$GLOBALS['b_id'];
	if(check_admin())
	  {
		$b_id = $_POST['business'];
		$admin_flag = true;
	  }
	else
	  {
		header("location:../settings");
		disconnect_from_db($link);
		return;
	  }
  }
else
  {
	//Recaptcha code

	require_once('../includes/recaptchalib.php');
	$privatekey = "6LchVNESAAAAADEG1tWkGm4ooU8WwyWaqYXl9q8w";
	$resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

	if (!$resp->is_valid) //recaptcha failed
	  {
		header("location:../signup?error=captcha");	
		exit;	    
	  } 
  }

extract($_POST);
$redirect_url = ($admin_flag ? "settings?view=new_user&":"signup?");
	//email format check ~[text]@[text].[txt]
if(preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email, $matches) == 0)
  {
	header("location:../".$redirect_url."error=email");
	disconnect_from_db($link);
	exit;
  }
	
//email unique-ness check
$query = "SELECT id FROM members WHERE email = '$email'";
$result = query_db($query);
if($result[0]['id']!="")
  {
	header("location:../".$redirect_url."error=dup");
	disconnect_from_db($link);
	exit;	
  }

if(strcmp($pass1,$pass2) != 0 || $pass1 =="")
  {
	header("location:../".$redirect_url."error=password");
	disconnect_from_db($link);
	exit;
  }	
	
//hash the password
$md5_pass = md5($pass1);
	
//create new user and log them in
$query = "INSERT INTO members (email, password, b_id) VALUES ('$email', '$md5_pass', $b_id)";
$result = query_db($query);
if(!$result)
  {
	header("location:../".$redirect_url."error=db");
  }
elseif(!$admin_flag)
  {
	  $query = "SELECT id FROM members WHERE email='$email'";
	  $result = query_db($query);
		$member = mysql_fetch_array($result);
		$session_id = rand(100,9999);
		$member_id = $member['id'];
		$query = "UPDATE business SET admin_id='$member_id' WHERE id = '$b_id'";
		$result = query_db($query);

		$query = "UPDATE members SET s_id='$session_id' WHERE id = '$member_id'";
		$result = query_db($query);
		
		$cookie_val = $email.",".$session_id;

		setcookie("login", $cookie_val, time()+(3600*8), "/");
		header("location:../new-business");
  }
else
  {
	header("location:../settings");
  }
disconnect_from_db($link);
?>