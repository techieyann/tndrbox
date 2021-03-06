<?php
/***********************************************
file: scripts/search_business.php
creator: Ian McEachern

This is the php script used to search through 
the database for matching tags when a user 
enters values into a tag field.
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

$search = $_GET['term'];
$return_str = "[";
if(strlen($search) > 0)
  {
	$query = "SELECT name, id FROM business WHERE name LIKE '%$search%' LIMIT 10";
	$result = query_db($query);
	$result_flag = 0;
	foreach($result as $current_tag)
	  {
		extract($current_tag);
		$result_flag = 1;
		$return_str = $return_str." {\"label\": \"$name\", \"value\": \"$id\"},";
	  }
	$return_str_len = strlen($return_str);
	if($result_flag == 1)
	  {
		$return_str = substr($return_str,0,$return_str_len-1);
	  }
	$return_str = $return_str."]";
	echo $return_str;
  }

disconnect_from_db();

?>