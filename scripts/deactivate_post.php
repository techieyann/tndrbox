<?php
/***********************************************
file: deactivate_post.php
creator: Ian McEachern

This script deactivates the active posting of
the supplied business.
 ***********************************************/
if(isset($_GET['id']))
  {
	require('../includes/includes.php');

	require('../includes/tags.php');

	$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	analyze_user();
	verify_logged_in();
	check_admin() ? $admin_flag = true : $admin_flag = false;
	$b_id=$_GET['id'];

	if(!$admin_flag && $b_id != $GLOBALS['b_id'])
	  {
		disconnect_from_db();
		return;
	  }

	push_old_post($b_id);
	
	disconnect_from_db();
  }
?>