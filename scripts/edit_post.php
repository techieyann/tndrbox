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
	$m_id = $_SESSION['m_id'];
	if(!check_admin())
	  {
		$b_id = $_SESSION['b_id'];
	  }


	extract($_POST);

	$title = add_slashes($title);
	$description = add_slashes($description);
	$address = add_slashes($address);
	$url = add_slashes($url);




if($address == "")
  {
	$query = "SELECT lat, lon FROM business WHERE id=$b_id";
	$result = query_db($query);
	if(isset($result[0]))
	  {
		extract($result[0]);
	  }
	else
	  {
		//fail and report
	  }
  }
else
  {
	$latlon = addr_to_latlon($address.", ".$city.", ".$zip);
	$lat = $latlon['lat'];
	$lon = $latlon['lon'];
  }

	$tag1_id = add_tag($tag1);
	$tag2_id = add_tag($tag2);
	$tag3_id = add_tag($tag3);

	$image_upload_flag = false;

	if(isset($_FILES['image_upload']))
	  {
	if($_FILES['image_upload']['error'] > 0)
      {
       	echo "error=".$_FILES['image_upload']['error'];
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


	$query = "UPDATE postings SET active=0, viewed=0, title='$title', blurb='$description', 
			tag_1='$tag1_id', tag_2='$tag2_id', tag_3='$tag3_id',
			date='$date', start_time='$start_time', end_time='$end_time', address='$address', city='$city', zip='$zip', lat='$lat', lon='$lon', url='$url'"
	  .($image_upload_flag ? ", photo='img_$id.$ext'" : " ")
			."WHERE id='$id'";

	query_db($query);

	echo "postId=".$id;

	disconnect_from_db($link);
  }
?>