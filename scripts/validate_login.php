<?php
	require('../includes.php');
	connect_to_db($mysql_user, $mysql_pass, $mysql_db);
	$email = sanitize($_POST['email']);
	$password = md5(sanitize($_POST['pass']));
	
	$query = "SELECT id FROM members WHERE email = '".$email."' AND password = '".$password."'";
	$result = query_db($query);
	if(mysql_num_rows($result) == 1)
	{
		$member = mysql_fetch_array($result);
		$session_id = rand(100,9999);
		$member_id = $member[id];

		$query = "UPDATE members SET sid='".$session_id."' WHERE id = '".$member_id."'";
		$result = query_db($query);
		
		$cookie_val = $username.",".$session_id;

		setcookie("info", $cookie_val, time()+(3600*8), "/");
		header("location:/home");
	}
	else
	{
		header("location:/login?error=1");
	}
	
?>