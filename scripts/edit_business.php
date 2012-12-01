<?php
/***********************************************
file: edit_business.php
creator: Ian McEachern

This script edits an existing business.
***********************************************/

require('../includes/includes.php');

require('../includes/tags.php');
	
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();	

$b_id = sanitize($_GET['id']);

$logo_upload_flag = false;

if($_FILES['logo_upload']['error'] > 0)
            {
              	echo "Error: ".$_FILES['logo_upload']['error'];
                header("location:../settings");
            }
            else
            {
				extract($_FILES['logo_upload']);
				if(strcmp("image", substr($type,0,5)) == 0)
				{
					
					if($size < (60*1024))
					{
					  $ext = substr($type,6);
									
						if(move_uploaded_file($tmp_name, "../images/logos/logo_$b_id.$ext"))
						{
							$logo_upload_flag = true;
						}
					}
				}
			}

$query = "SELECT tag_1, tag_2 FROM business WHERE id='$b_id'";
$result = query_db($query);
$tag_ids = mysql_fetch_array($result);
$old_tag1_id = $tag_ids['tag_1'];
$old_tag2_id = $tag_ids['tag_2'];

$name = sanitize($_POST['name']);

$new_tag1 = sanitize($_POST['tag_1']);
$new_tag2 = sanitize($_POST['tag_2']);

$old_tag1 = get_tag($old_tag1_id);
if(strcmp($new_tag1, $old_tag1) == 0)
{
	$new_tag1_id = $old_tag1_id;
}
else
{
	$new_tag1_id = add_tag($new_tag1);
	decrement_tag($old_tag1_id);
}

$old_tag2 = get_tag($old_tag2_id);
if(strcmp($new_tag2, $old_tag2) == 0)
{
	$new_tag2_id = $old_tag2_id;
}
else
{
	$new_tag2_id = add_tag($new_tag2);
	decrement_tag($old_tag2_id);
}

	$address = sanitize($_POST['address']);
	$city = sanitize($_POST['city']);
	$state = sanitize($_POST['state']);
	$zip = sanitize($_POST['zip']);
	$hours = sanitize($_POST['hours']);
	$number = sanitize($_POST['number']);
	$url = sanitize($_POST['url']);

	//need to write geocoding script to get lat/lon
	$lat = 0;
	$lon = 0;






$query = "UPDATE business SET name='$name', tag_1='$new_tag1_id', 
       	 tag_2='$new_tag2_id', address='$address', city='$city',
		state='$state', zip='$zip', lat='$lat', lon='$lon',
		url='$url', number='$number', hours='$hours'";

if($logo_upload_flag == true)
{
	$query = $query.", logo='logo_$b_id.$ext'";
}
$query = $query."
		WHERE id='$b_id'";
$result = query_db($query);

if($result)
  {
	header("location:../settings");	
  }
else
  {
	header("location:../edit-business?id=$b_id");
  }

disconnect_from_db($link);
?>