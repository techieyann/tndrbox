<?php
/***********************************************
file: delete_post.php
creator: Ian McEachern

This script deletes a posting and maintains the
validity of the tags database.
 ***********************************************/
if(isset($_GET['id']))
  {
	require('../includes/includes.php');

	require('../includes/tags.php');
	
	$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	analyze_user();
	verify_logged_in();


	$id = $_GET['id'];

	$query = "SELECT b_id, photo FROM postings WHERE id=$id";
	$result = query_db($query);
	if(isset($result[0]))
	  {
		extract($result[0]);

		if(!check_admin())
		  {	
			if($GLOBALS['b_id'] != $b_id)
			  {
				disconnect_from_db();
				return;
			  }
		  }
		push_old_post($b_id);
		$query = "DELETE FROM postings WHERE id=$id";
		$result = query_db($query);
		if($photo != "")
		  {
			unlink('../images/posts/'.$photo);
		  }
	  }
	disconnect_from_db($link);
  }
?>