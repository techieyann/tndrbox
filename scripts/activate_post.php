<?php
/***********************************************
file: scripts/activate_post.php
creator: Ian McEachern

This script activates the indicated post
 ***********************************************/
echo "hello world";
if(isset($_GET['id']))
  {
	require('../includes/includes.php');
	require('../includes/tags.php');

	connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	analyze_user();
	verify_logged_in();
	check_admin() ? $admin_flag = true : $admin_flag = false;
	$p_id=$_GET['id'];

	$query = "SELECT b_id FROM postings WHERE id=$p_id";
	$result = query_db($query);

	if(isset($result[0]))
	  {
		extract($result[0]);
		if(!$admin_flag && $b_id != $GLOBALS['b_id'])
		  {
			disconnect_from_db();
			return;
		  }

		push_old_post($b_id);
		activate_post($p_id);

		disconnect_from_db();
	  }
  }
?>