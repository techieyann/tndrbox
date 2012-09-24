<?php
/***********************************************
file: new_post.php
creator: Ian McEachern

This script creates a new posting.
 ***********************************************/

require('../includes/includes.php');
require('../includes/db_interface.php');
require('../includes/tags.php');

$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

$business_id=$GLOBALS['b_id'];
$author_id=$GLOBALS['m_id'];

$title = sanitize($_POST['title']);
$desc = sanitize($_POST['description']);

$tag1 = sanitize($_POST['tag1']);
$tag2 = sanitize($_POST['tag2']);
$tag3 = sanitize($_POST['tag3']);

$tag1_id = add_tag($tag1);
$tag2_id = add_tag($tag2);
$tag3_id = add_tag($tag3);

//$price = sanitize($_POST['price']);
//$number = sanitize($_POST['quantity']);

//$date = sanitize($_POST['publish_date']);
//$time = sanitize($_POST['publish_time']);

//mysql datetime format: 'YYYY-MM-DD HH:MM:SS'
//$post_datetime = "$date $time";
	
$query = "INSERT INTO postings (title, blurb, tag_1, tag_2, tag_3, 
       	  b_id, a_id) VALUES ('$title', '$desc', $tag1_id, 
	 $tag2_id, $tag3_id, $business_id, $author_id)";
$result = query_db($query);
if($result)
{
	header("location:../home");
}
else
{
	header("location:../edit-post.php?error=1&title=$title&blurb=$desc&tag1=$tag1&tag2=$tag2&tag3=$tag3");
}
disconnect_from_db($link);

?>