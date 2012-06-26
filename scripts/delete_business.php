<?php
/***********************************************
file: delete_business.php
creator: Ian McEachern

This script deletes a business and maintains the
validity of the tags database.
 ***********************************************/
require('../includes/includes.php');
require('../includes/db_interface.php');
require('../includes/tags.php');
	
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);
	
$business_id = sanitize($_POST['b_id']);

$query = "SELECT tag1, tag2 FROM business WHERE id='$business_id'";
$result = query_db($query);
	
if(mysql_num_rows($result) == 1)
{

	extract($result);
	decrement_tag($tag1);
	decrement_tag($tag2);
		
	$query = "DELETE FROM business WHERE id='$business_id'";
	query_db($query);
}
else
{
	//report error
}

disconnect_from_db($link);
?>