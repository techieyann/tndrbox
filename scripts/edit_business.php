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
verify_logged_in();	

$b_id = sanitize($_GET['id']);
$query = "SELECT tag_1, tag_2 FROM business WHERE id='$b_id'";
$result = query_db($query);
$tag_ids = mysql_fetch_array($result);
$tag_1_id = $tag_ids['tag_1'];
$tag_2_id = $tag_ids['tag_2'];

$name = sanitize($_POST['name']);

$tag1 = sanitize($_POST['tag_1']);
$tag2 = sanitize($_POST['tag_2']);

$tag_1 = get_tag($tag_1_id);
if(strcmp($tag1, $tag_1) == 0)
{
	$tag1_id = $tag_1_id;
}
else
{
	$tag1_id = add_tag($tag1);
	decrement_tag($tag_1_id);
}

$tag_2 = get_tag($tag_2_id);
if(strcmp($tag2, $tag_2) == 0)
{
	$tag2_id = $tag_2_id;
}
else
{
	$tag2_id = add_tag($tag2);
	decrement_tag($tag_2_id);
}

	$address = sanitize($_POST['address']);
	$city = sanitize($_POST['city']);
	$state = sanitize($_POST['state']);
	$zip = sanitize($_POST['zip']);
	$hours = sanitize($_POST['hours']);
	$number = sanitize($_POST['number']);
	$url = sanitize($_POST['url']);

	//need to write geocoding script to get lat/lon
	$lat = 0;
	$lon = 0;



$query = "UPDATE business SET name='$name', tag_1='$tag1_id', 
       	 tag_2='$tag2_id', address='$address', city='$city',
		state='$state', zip='$zip', lat='$lat', lon='$lon',
		url='$url', number='$number', hours='$hours'
		WHERE id='$b_id'";
$result = query_db($query);

if($result)
  {
	header("location:../home");	
  }
else
  {
	header("location:../edit-business?id=$b_id");
  }

disconnect_from_db($link);
?>