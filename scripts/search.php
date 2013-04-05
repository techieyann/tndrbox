<?php
/***********************************************
file: scripts/search.php
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

if(isset($_GET['term']))
  {
	$search = $_GET['term'];

	$tag_flag = false;
	$business_flag = false;
	$both_flag = false;
	$return_str = "[";
   	if(strlen($search) > 0)
	  {
		if(isset($_GET['type']))
		  {
			$type = $_GET['type'];
			if($type == "b")
			  {
				$query = "SELECT name, id FROM business WHERE name LIKE '%$search%' LIMIT 10";
				$result = query_db($query);
				$business_flag = true;
			  }
			if($type == "t")
			  {
				$query = "SELECT tag, id, num_ref FROM tags WHERE tag LIKE '%$search%' AND id>0 ".($active_flag? "AND num_ref>0 ":"")."ORDER BY num_ref DESC LIMIT 10";
				$result = query_db($query);
				$tag_flag = true;
			  }
		  }
		else
		  {
			$query = "SELECT tag, id, num_ref FROM tags WHERE tag LIKE '%$search%' AND id>0 ".($active_flag? "AND num_ref>0 ":"")."ORDER BY num_ref DESC LIMIT 7";
			$result = query_db($query);
		  	$query = "SELECT name, id FROM business WHERE name LIKE '%$search%' LIMIT 3";
			$business_result = query_db($query);
			foreach($business_result as $business)
			  {
				array_push($result, $business);
			  }

			$both_flag = true;
		  }
		$result_flag = 0;
		foreach($result as $current_result)
		  {

			if($both_flag)
			  {
				if(isset($current_result['tag']))
				  {
					$tag_flag = true;
				  }
				else if(isset($current_result['name']))
				  {
					$business_flag = true;
				  }
			  }
			extract($current_result);
			if($tag_flag)
			  {
				$return_str = $return_str." {\"label\": \"$tag ($num_ref)\", \"value\": \"t=$id\"},";
			$result_flag = 1;
			  }
			if($business_flag)
			  {
				$return_str = $return_str." {\"label\": \"$name\", \"value\": \"b=$id\"},";
			$result_flag = 1;
			  }
			if($both_flag)
			  {
				$tag_flag = false;
				$business_flag = false;
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
  }

disconnect_from_db();

?>