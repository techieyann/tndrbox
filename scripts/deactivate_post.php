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
	$id=$_GET['id'];
	$query = "SELECT b_id FROM postings WHERE id=$id";
	$result = query_db($query);
	if(isset($result[0]))
	{
		if(!$admin_flag && $result[0]['b_id'] != $_SESSION['b_id'])
		  {
			disconnect_from_db();
			return;
		  }
		deactivate_post($id);
	}
	disconnect_from_db();
  }
?>