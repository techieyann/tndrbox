<?php
/***********************************************
file: edit_post.php
creator: Ian McEachern

This script edits an existing posting.
 ***********************************************/
if(isset($_GET['id']))
  {
	require('../includes/includes.php');

	require('../includes/tags.php');

	$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	analyze_user();
	verify_logged_in();

	$id = $_GET['id'];
	$m_id = $GLOBALS['m_id'];
	if(!check_admin())
	  {
		$b_id = $GLOBALS['b_id'];
	  }

	$query = "SELECT tag_1, tag_2, tag_3".(check_admin() ? ", b_id":"")." FROM postings WHERE id='$id'";
	$result = query_db($query);

	extract($result[0]);
	extract($_POST);

	$title = add_slashes($title);
	$description = add_slashes($description);
	$address = add_slashes($address);
	$url = add_slashes($url);

	push_old_post($b_id);	


	$tag1_id = add_tag($tag1);
	$tag2_id = add_tag($tag2);
	$tag3_id = add_tag($tag3);

	$image_upload_flag = false;

	if(isset($_FILES['image_upload']))
	  {
	if($_FILES['image_upload']['error'] > 0)
      {
       	echo "Error: ".$_FILES['image_upload']['error'];
      }
    else
      {
		extract($_FILES['image_upload']);
		if(strcmp("image", substr($type,0,5)) == 0)
		  {			
			if($size < (2*1024*1024))
		      {
				$ext = substr($type,6);
									
				if(move_uploaded_file($tmp_name, "../images/posts/img_$id.$ext"))
				  {
					$image_upload_flag = true;
				  }
		      }
		  }
	  }
	  }	


	$query = "UPDATE postings SET active=1, viewed=0, title='$title', blurb='$description', 
			tag_1='$tag1_id', tag_2='$tag2_id', tag_3='$tag3_id',
			date='$date', alt_address='$address', url='$url',
			posting_time=CURRENT_TIMESTAMP"
	  .($image_upload_flag ? ", photo='img_$id.$ext'" : " ")
			."WHERE id='$id'";

	query_db($query);
	$query = "UPDATE business SET active_post=1, last_touched=CURRENT_TIMESTAMP WHERE id=$b_id";
	query_db($query);

	disconnect_from_db($link);
	return true;
  }
?>