<?php
/***********************************************
file: validate_login.php
creator: Ian McEachern

This script checks if the email is formatted 
before accessing the database, and then checks 
the username password combination. If correct,
it sets a cookie on the user with validated 
login via a session id number.
 ***********************************************/

	require('../includes/includes.php');


	$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	
	$email = sanitize($_POST['email']);

	if(preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email, $matches) == 0)
	{
		print($matches);
		header("location:../login?error=email");
	}

	$password = md5(sanitize($_POST['pass']));

	
	$query = "SELECT id FROM members WHERE email = '$email' AND password = '$password'";
	$result = query_db($query);
	if(mysql_num_rows($result) == 1)
	{
		$member = mysql_fetch_array($result);
		$session_id = rand(100,9999);
		$member_id = $member['id'];

		$query = "UPDATE members SET s_id='$session_id' WHERE id = '$member_id'";
		$result = query_db($query);
		
		$cookie_val = $email.",".$session_id;

		setcookie("login", $cookie_val, time()+(3600*8), "/");
   		header("location:../settings");
	}
	else
	{
 		header("location:../login?error=match");
	}
	disconnect_from_db($link);
?>