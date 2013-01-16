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
require('includes/front_page.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body

$title = "Welcome";
$result = array();
$p_flag = 0;

if(isset($_GET['p']))
  {
	$p_id = $_GET['p'];
	$query = "SELECT * FROM postings WHERE id='$p_id'";
	$p_flag = 1;
	$result = query_db($query);
	$result['p_flag'] = 1;
	array_push($result, default_front_page_posts());
  }
elseif(isset($_GET['b']))
  {
	$b_id = $_GET['b'];
	$query = "SELECT * FROM postings WHERE b_id=$b_id and active=1";
	$p_flag = 1;
	$result = query_db($query);
	$p_id = $result[0]['id'];
	$result['p_flag'] = 1;
	array_push($result, default_front_page_posts());
  }
  
elseif(isset($_GET['tag']))
  {
	$set_tag_id = $_GET['tag'];
	$title = get_tag($set_tag_id);
	if($set_tag_id > 0)
	  {
		$query = "SELECT * FROM postings WHERE (tag_1='$set_tag_id' OR tag_2='$set_tag_id' OR tag_3='$set_tag_id') AND active=1 LIMIT 20";
		$result = query_db($query);
		$i=0;
	  }
	else
	  {
		$query = "SELECT id FROM business WHERE category=$set_tag_id AND active_post=1 LIMIT 20";
		$business_result = query_db($query);
		$result = array();
		foreach($business_result as $business)
		  {
			$id = $business['id'];
			$query = "SELECT * FROM postings WHERE b_id=$id AND active=1";
			$post_result = query_db($query);
			array_push($result, $post_result[0]);
		  }
	  }
  }
else
  {
	$result = default_front_page_posts();
  }




$postings = format_rows($result);


//head
$GLOBALS['header_html_title'] = "tndrbox - $title";

if($p_flag == 1)
  {
		$GLOBALS['header_scripts'] = "
<script type='text/javascript'>
$(document).ready(function(){

$('#post-$p_id-modal').modal('show');

});
</script>";
  }
else
  {
	$GLOBALS['header_scripts'] = "";
  }

$GLOBALS['categories'] = get_active_categories();
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
	
	print_formatted_rows($postings);
  }
?>