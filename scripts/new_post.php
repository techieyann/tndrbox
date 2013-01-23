3<?php
/***********************************************
file: new_post.php
creator: Ian McEachern

This script creates a new posting.
 ***********************************************/

require('../includes/includes.php');

require('../includes/tags.php');

$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();



$business_id=$GLOBALS['b_id'];
$author_id=$GLOBALS['m_id'];

if(isset($_GET['admin']))
  {
	if($business_id != 0)
	  {
		header("location:settings");
		disconnect_from_db($link);
		return;
	  }
	else
	  {
		$business_id = sanitize($_POST['business']);
		$admin_flag = 1;
	  }
  }


push_old_post($business_id);
ectract($_POST);

$tag1_id = add_tag($tag1);
$tag2_id = add_tag($tag2);
$tag3_id = add_tag($tag3);

$date = sanitize($_POST['date']);

//$price = sanitize($_POST['price']);
//$number = sanitize($_POST['quantity']);

//$date = sanitize($_POST['publish_date']);
//$time = sanitize($_POST['publish_time']);

//mysql datetime format: 'YYYY-MM-DD HH:MM:SS'
//$post_datetime = "$date $time";
		
	
$query = "INSERT INTO postings (title, blurb, tag_1, tag_2, tag_3,
								date, alt_address, url, b_id, a_id, posting_time) 
VALUES ('$title', '$description', $tag1_id, $tag2_id, $tag3_id, 
		'$date', '$address', '$url', $business_id, $author_id, CURRENT_TIMESTAMP)";
$result = query_db($query);

if($result)
{
	$post_id = get_last_insert_ID();

	if($_FILES['image_upload']['error'] > 0)
            {
              	echo "Error: ".$_FILES['image_upload']['error'];
                header("location:../settings");
            }
            else
            {
				extract($_FILES['image_upload']);
				if(strcmp("image", substr($type,0,5)) == 0)
				{
					
					if($size < (2*1024*1024))
					{
					  $ext = substr($type,6);
									
						if(move_uploaded_file($tmp_name, "../images/posts/img_$post_id.$ext"))
						{
							$query = "UPDATE postings SET
					         	       photo='img_$post_id.$ext'
						       	       WHERE id='$post_id'";
					  	    query_db($query);
						}
					}
				}
			}
	$query = "UPDATE business SET active_post=1 WHERE id=$business_id";
	query_db($query);

	header("location:../settings");
}
else
{
  header("location:../edit-post.php?error=1&title=$title&date=$date&blurb=$description&tag1=$tag1&tag2=$tag2&tag3=$tag3");
}
disconnect_from_db($link);

?>