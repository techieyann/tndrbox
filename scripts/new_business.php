<?php
/***********************************************
file: new_business.php
creator: Ian McEachern

This script creates a new business.
***********************************************/

require('../includes/includes.php');
require('../includes/db_interface.php');
require('../includes/tags.php');
	
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();	

$name = sanitize($_POST['name']);

$tag1 = sanitize($_POST['tag1']);
$tag2 = sanitize($_POST['tag2']);

$tag1_id = add_tag($tag1);
$tag2_id = add_tag($tag2);

$address = sanitize($_POST['address']);
$city = sanitize($_POST['city']);
$state = sanitize($_POST['state']);
$zip = sanitize($_POST['zip']);

//need to write geocoding script to get lat/lon
$lat = 0;
$lon = 0;

$query = "INSERT INTO business (name, tag1, tag2, address, city, state,
       	 zip, lat, lon) VALUES ('$name', '$tag1_id', '$tag2_id',
	 '$address', '$city', '$state', '$zip', '$lat', '$lon')";
query_db($query);

disconnect_from_db($link);
?>