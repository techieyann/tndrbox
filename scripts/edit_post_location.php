<?php
/***********************************************
file: edit_post_location.php
creator: Ian McEachern

This script edits the location of an exisiting posting.
 ***********************************************/
if(isset($_GET['id']) && isset($_GET['lat']) && isset($_GET['lon']))
  {
	require('../includes/includes.php');

	$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	analyze_user();
	verify_logged_in();

	extract($_GET);
	
	$query = "UPDATE postings SET lat=$lat, lon=$lon WHERE id=$id";
	$result = query_db($query);

	disconnect_from_db();
}
?>