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

	connect_to_db($mysql_user, $mysql_pass, $mysql_db);
	
	extract($_POST);

	if(preg_match('/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $email, $matches) == 0)
	{
		echo "email format";
		disconnect_from_db();
		return;
	}
	$password = md5($pass);
	$query = "SELECT id FROM members WHERE email = '$email' AND password = '$password'";
	$result = query_db($query);

	if(count($result,1)!=0) // recursively
	{
	    $member = $result[0];
		$session_id = rand(100,9999);
		$member_id = $member['id'];

		$query = "UPDATE members SET s_id='$session_id' WHERE id = '$member_id'";
		$result = query_db($query);
		
		$cookie_val = $email.",".md5($session_id);

		setcookie("login", $cookie_val, time()+(3600*8), "/");
		echo "logged in";
	}
	else
	{
	  echo "email password combo";
	}
	disconnect_from_db();
?>