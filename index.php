<?php
/***********************************************
file: index.php
creator: Ian McEachern

This is the default page. It displays the most
relevant data based on the function scrape_tags()
in includes.php.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body


$get_tag_set = false;
$set_tag = "Welcome";
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
		
}
else
  {
	$result = scrape_tags();
	while($post = mysql_fetch_array($result))
	{
		$postings[$post['id']] = $post;
	}
  }

$query = "SELECT * FROM tags WHERE num_ref>0 ORDER BY num_ref DESC, tag LIMIT 50";
$tags_result = query_db($query);



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
  global $postings, $get_tag_set, $set_tag_id, $set_tag, $set_tag_postings_result, $tags_result;
  echo "
	<div id=\"\" class =\"meta-pane\">";
  if($get_tag_set)
	{
		echo "
		<h3>$set_tag</h3>";
	}
  while($present_tag = mysql_fetch_array($tags_result))
	{
	  extract($present_tag);
		echo "
		<a href=\"index?tag=$id\">$tag</a>($num_ref) ";
	}
  echo "
	</div>
	<div id=\"\" class =\"content-pane\">";
	$i = 0;
	if(isset($postings))
	  {
		foreach($postings as $post)
		{
			print_mini_post($post, "-".++$i);		
		}
	  }

  echo "
	</div>";
}
?>