<?php
/***********************************************
file: edit_old_post.php
creator: Ian McEachern

This script edits an old post and makes it the 
current posting.
 ***********************************************/

require('../includes/includes.php');
require('../includes/db_interface.php');
require('../includes/tags.php');

$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();


$business_id=$GLOBALS['b_id'];
push_old_post($business_id);

$post_id = sanitize($_GET['p_id']);
$query = "SELECT tag_1, tag_2, tag_3 FROM old_postings WHERE id='$post_id'";
$result = query_db($query);

$title = sanitize($_POST['title']);
$desc = sanitize($_POST['description']);

$tag1 = sanitize($_POST['tag1']);
$tag2 = sanitize($_POST['tag2']);
$tag3 = sanitize($_POST['tag3']);

if(strcmp($tag1,$tag_1) != 0)
{ 
	$tag1_id = add_tag($tag1);
	decrement_tag($tag_1);
}
else
{
	$tag1_id = $tag_1;
}
if(strcmp($tag2,$tag_2) != 0)
{ 
	$tag2_id = add_tag($tag2);
	decrement_tag($tag_2);
}
else
{
	$tag2_id = $tag_2;
}
if(strcmp($tag3,$tag_3) != 0)
{ 
	$tag3_id = add_tag($tag3);
	decrement_tag($tag_3);
}
else
{
	$tag3_id = $tag_3;
}

//$price = sanitize($_POST['price']);
//$number = sanitize($_POST['quantity']);

//$date = sanitize($_POST['publish_date']);
//$time = sanitize($_POST['publish_time']);

//mysql datetime format: 'YYYY-MM-DD HH:MM:SS'
//$post_datetime = "$date $time";


$image_upload_flag = false;
			if($_FILES['image_upload']['error'] > 0)
            {
              	echo "Error: ".$_FILES['image_upload']['error'];
                header("location:../home");
            }
            else
            {
				extract($_FILES['image_upload']);
				if(strcmp("image", substr($type,0,5)) == 0)
				{
					
					if($size < (240*1024))
					{
					  $ext = substr($type,6);
									
						if(move_uploaded_file($tmp_name, "../images/posts/img_$post_id.$ext"))
						{
							$image_upload_flag = true;
						}
					}
				}
			}

$query = "INSERT INTO postings (SELECT * FROM old_postings WHERE id=$post_id)";
query_db($query);

$query = "DELETE FROM old_postings WHERE id=$post_id";
query_db($query);

$query = "UPDATE postings set title='$title', blurb='$desc', 
       	 tag_1='$tag1_id', tag_2='$tag2_id', tag_3='$tag3_id', 
       	 posting_time=CURRENT_TIMESTAMP, a_id='$user_id'";

if($image_upload_flag == true)
{
	$query = $query.", photo='img_$post_id.$ext'";
}
$query = $query."
	 WHERE id='$post_id'";
query_db($query);




header("location:../home");
disconnect_from_db($link);

?>