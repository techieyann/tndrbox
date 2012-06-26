<?php

/***********************************************
file: edit_user.php
creator: Ian McEachern

This script edits user credentials.
 ***********************************************/

require('../includes/includes.php');
require('../includes/db_interface.php');

//connect to the database
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

//sanity check the passwords
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

//update user information
$query = "UPDATE members SET password='$md5_pass' WHERE id='$user_id';
query_db($query);

disconnect_from_db($link);

?>