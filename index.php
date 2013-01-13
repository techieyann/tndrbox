<?php
/***********************************************
file: index.php
creator: Ian McEachern

This is the default page. It displays the most
relevant data based on the function scrape_tags()
in includes.php.
 ***********************************************/
require('includes/includes.php');
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body

$set_tag = "Welcome";

/*if(isset($_GET['p']))
  {
	$p_id = sanitize($_GET['p']);
	$query = "SELECT b_id FROM postings WHERE id=$p_id";
	$result = query_db($query);
	$business = mysql_fetch_array($result);
	$b_id = $business['b_id'];
	header("location:/business?b_id=$b_id");
	exit;
  }

$get_tag_set = false;

if(isset($_GET['tag']))
{
	$get_tag_set = true;
	$set_tag_id = sanitize($_GET['tag']);
	$set_tag = get_tag($set_tag_id);
	$query = "SELECT * FROM postings WHERE tag_1='$set_tag_id' OR tag_2='$set_tag_id' OR tag_3='$set_tag_id' LIMIT 20";
	$result = query_db($query);
	$i=0;
	while($post = mysql_fetch_array($result))
	{
		$postings[$i++] = $post;
	}
	$query = "SELECT id FROM business WHERE tag_1='$set_tag_id' OR tag_2='$set_tag_id' LIMIT 20";
   	$result = query_db($query);
   	while($business = mysql_fetch_array($result))
   	  {
   		$query = "SELECT * FROM postings WHERE b_id=".$business['id'];
		  $post_result = query_db($query);  
		$postings[$i++] = mysql_fetch_array($post_result);
   	  }
		
	  }*/

	$result = scrape_posts();
	while($post = mysql_fetch_array($result))
	{
		$postings[$post['id']] = $post;
	}




//head
$GLOBALS['header_html_title'] = "tndrbox - $set_tag";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "landing";
require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
  {
	global $postings;

	$count = 0;	

	foreach($postings as $id=>$post)
	  {
		$post_row[$id] = $post;
		if($count++ == 3)
		  {
			$count = 0;
			print_post_row($post_row);
			$post_row = "";
		  }
	  }
	if($count != 0)
	  {
		print_post_row($post_row);
	  }
  
  }
?>