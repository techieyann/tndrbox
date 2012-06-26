<?php
/***********************************************
file: edit_business.php
creator: Ian McEachern

This script edits an existing business.
***********************************************/

require('../includes/includes.php');
require('../includes/db_interface.php');
require('../includes/tags.php');
	
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();	

$post_id = sanitize($_GET['p_id']);
$query = "SELECT tag1, tag2, tag3 FROM postings WHERE id='$post_id'";
$result = query_db($query);

$name = sanitize($_POST['name']);

$tag1 = sanitize($_POST['tag1']);
$tag2 = sanitize($_POST['tag2']);
if(strcmp($tag1,$tag_1) != 0)
{ 
	$tag1_id = add_tag($tag_1);
	decrement_tag($tag1);
}
else
{
	$tag1_id = $tag1;
}
if(strcmp($tag2,$tag_2) != 0)
{ 
	$tag2_id = add_tag($tag_2);
	decrement_tag($tag2);
}
else
{
	$tag2_id = $tag2;
}

if(isset($_GET['loc']))
{
	$address = sanitize($_POST['address']);
	$city = sanitize($_POST['city']);
	$state = sanitize($_POST['state']);
	$zip = sanitize($_POST['zip']);

	//need to write geocoding script to get lat/lon
	$lat = 0;
	$lon = 0;
}

$query = "UPDATE business SET name='$name', tag1='$tag1_id', 
       	 tag2='$tag2_id'";
if(isset($_GET['loc'])
{
	$query .= ", address='$address', city='$city',
	 state='$state', zip='$zip', lat='$lat', lon='$lon'";
}
$query .= " WHERE id='$post_id'";

query_db($query);

disconnect_from_db($link);
?>