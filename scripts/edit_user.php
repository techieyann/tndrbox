<?php

/***********************************************
file: edit_user.php
creator: Ian McEachern

This script edits user credentials.
 ***********************************************/

require('../includes/includes.php');


//connect to the database
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();

if(check_admin())
  {
	$id = $_GET['id'];
  }
else
  {
	$id = $GLOBALS['m_id'];
  }

extract($_POST);

if(strcmp($pass1,$pass2) != 0)
{
	header("location:../settings?view=edit_profile");
	disconnect_from_db($link);
	exit;
}	
	
//hash the password
$md5_pass = md5($pass1);
$pass_flag = false;

if($pass1 != "")
  {
	$pass_flag = true;
  }

//update user information
$query = "UPDATE members SET ".
  (check_admin() ? "email='$email'":"").
  ($pass_flag ? ", password='$md5_pass'": "").
  " WHERE id=$id";

query_db($query);

header("location:../settings?view=edit_profile");	

disconnect_from_db($link);

?>