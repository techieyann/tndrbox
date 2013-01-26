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

if(check_admin() && isset($_GET['id']))
  {
	$b_id = $_GET['id'];
  }

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
					
					if($size < (2*1024*1024))
					{
					  $ext = substr($type,6);
									
						if(move_uploaded_file($tmp_name, "../images/logos/logo_$b_id.$ext"))
						{
							$logo_upload_flag = true;
						}
					}
				}
			}

$query = "SELECT category FROM business WHERE id='$b_id'";
$result = query_db($query);
$old_category_id = $result[0];

extract($_POST);

$old_category = get_tag($old_category_id);
if(strcmp($category, $old_category) == 0)
{
	$new_category = $old_category_id;
}
else
{
	$new_category = add_tag($category);
	decrement_tag($old_category_id);
}

	//need to write geocoding script to get lat/lon
	$lat = 0;
	$lon = 0;


$query = "UPDATE business SET name='$name', tag_1='$new_tag1_id', 
       	 tag_2='$new_tag2_id', address='$address', city='$city',
		state='$state', zip='$zip', lat='$lat', lon='$lon',
		url='$url', number='$number', hours='$hours'
		".($logo_upload_flag ? ", logo='logo_$b_id.$ext'":"")."
		WHERE id='$b_id'";
$result = query_db($query);

if($result)
  {
	header("location:../settings");	
  }
else
  {
	header("location:../edit-business".(check_admin() ? "?id=$b_id":""));
  }

disconnect_from_db($link);
?>