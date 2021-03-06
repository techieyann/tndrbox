<?php
/***********************************************
file: scripts/search_tag.php
creator: Ian McEachern

This is the php script used to search through 
the database for matching tags when a user 
enters values into a tag field.
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);
$active_flag = false;
if(isset($_GET['active']))
  {
	$active_flag = true;
  }
$search = $_GET['term'];
$return_str = "[";
if(strlen($search) > 0)
  {
	$query = "SELECT tag, id FROM tags WHERE tag LIKE '%$search%' AND id>0 ".($active_flag? "AND num_ref>0 ":"")."ORDER BY num_ref DESC LIMIT 20";
	$result = query_db($query);
	$result_flag = 0;
	foreach($result as $current_tag)
	  {
		extract($current_tag);
		$result_flag = 1;
		if($active_flag)
		  {
			$return_str = $return_str." {\"label\": \"$tag\", \"value\": \"$id\"},";
		  }
		else
		  {
			$return_str = $return_str." \"$tag\",";
		  }
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