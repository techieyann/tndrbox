<?php
/***********************************************
file: delete_post.php
creator: Ian McEachern

This script deletes a posting and maintains the
validity of the tags database.
 ***********************************************/
require('../includes/includes.php');
require('../includes/db_interface.php');
require('../includes/tags.php');
	
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);
	
$post_id = sanitize($_POST['p_id']);

$query = "SELECT tag1, tag2, tag3 FROM postings  WHERE id='$post_id'";
$result = query_db($query);
	
if(mysql_num_rows($result) == 1)
{

	extract($result);
	decrement_tag($tag1);
	decrement_tag($tag2);
	decrement_tag($tag3);
	
	$query = "DELETE FROM postings WHERE id='$post_id'";
	query_db($query);
}
else
{
	//report error
}

disconnect_from_db($link);
?>