<?php
/***********************************************
file: edit_post.php
creator: Ian McEachern

This script edits an existing posting.
 ***********************************************/

require('../includes/includes.php');
require('../includes/db_interface.php');
require('../includes/tags.php');

$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();

$post_id = sanitize($_GET['p_id']);
$query = "SELECT tag1, tag2, tag3 FROM postings WHERE id='$post_id'";
$result = query_db($query);

$title = sanitize($_POST['title']);
$desc = sanitize($_POST['description']);

$tag_1 = sanitize($_POST['tag1']);
$tag_2 = sanitize($_POST['tag2']);
$tag_3 = sanitize($_POST['tag3']);

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
if(strcmp($tag3,$tag_3) != 0)
{ 
	$tag3_id = add_tag($tag_3);
	decrement_tag($tag3);
}
else
{
	$tag3_id = $tag3;
}

//$price = sanitize($_POST['price']);
//$number = sanitize($_POST['quantity']);

$date = sanitize($_POST['publish_date']);
$time = sanitize($_POST['publish_time']);

//mysql datetime format: 'YYYY-MM-DD HH:MM:SS'
$post_datetime = "$date $time";
	
$query = "UPDATE postings set title='$title', blurb='$blurb', 
       	 tag1='$tag1_id', tag2='$tag2_id', tag3='$tag3_id', 
       	 posting_time='$post_datetime', a_id='$user_id'
	 WHERE id='$post_id'";
query_db($query);

disconnect_from_db($link);

?>