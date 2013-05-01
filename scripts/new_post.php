<?php
/***********************************************
file: scripts/new_post.php
creator: Ian McEachern

This script creates a new posting.
 ***********************************************/

require('../includes/includes.php');

require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

$business_id=$GLOBALS['b_id'];
$author_id=$GLOBALS['m_id'];


if(check_admin())
  {
	$business_id = $_POST['business'];

	$query = "SELECT admin_id FROM business WHERE id=$business_id";
	$result = query_db($query);
	$author_id = $result[0]['admin_id'];
  }



extract($_POST);


if($address == "")
  {
	$query = "SELECT address, city, state, zip, lat, lon FROM business WHERE id=$business_id";
	$result = query_db($query);
	if(isset($result[0]))
	  {
		extract($result[0]);
		$address = "$address $city, $state, $zip";
	  }
	else
	  {
		echo "error=business not found";
		disconnect_from_db();
		return;
	  }
  }
else
  {
	$latlon = addr_to_latlon($address);
	$lat = $latlon['lat'];
	$lon = $latlon['lon'];
  }

$title = add_slashes($title);
$description = add_slashes($description);
$address = add_slashes($address);
$url = add_slashes($url);



$tag1_id = add_tag($tag1);
$tag2_id = add_tag($tag2);
$tag3_id = add_tag($tag3);
		
	
$query = "INSERT INTO postings (title, blurb, tag_1, tag_2, tag_3,
								date, alt_address, lat, lon, url, b_id, a_id, posting_time) 
VALUES ('$title', '$description', $tag1_id, $tag2_id, $tag3_id, 
		'$date', '$address', $lat, $lon, '$url', $business_id, $author_id, CURRENT_TIMESTAMP)";

$result = query_db($query);

if($result)
{

	$post_id = get_last_insert_ID();

	
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
	  }
	else
	  {
		$query = "SELECT photo FROM business WHERE id=$business_id";
		$result = query_db($query);
		if(isset($result[0]))
		  {
			$photo = $result[0]['photo'];
			$query = "UPDATE postings SET
						photo='$photo'
						WHERE id='$post_id'";
			query_db($query);
		  }
	  }
	echo "postId=$post_id";

}
else
{
  echo "error=".$result;
}
disconnect_from_db();

?>