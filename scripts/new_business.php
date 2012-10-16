<?php
/***********************************************
file: new_business.php
creator: Ian McEachern

This script creates a new business.
***********************************************/

require('../includes/includes.php');
require('../includes/db_interface.php');
require('../includes/tags.php');
	
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();	

$b_id = sanitize($_GET['id']);

$logo_upload_flag = false;

if($_FILES['logo_upload']['error'] > 0)
            {
              	echo "Error: ".$_FILES['logo_upload']['error'];
                header("location:../home");
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

$tag1 = sanitize($_POST['tag_1']);
$tag2 = sanitize($_POST['tag_2']);

$tag1_id = add_tag($tag1);
$tag2_id = add_tag($tag2);

$name = sanitize($_POST['name']);
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




$query = "UPDATE business SET name='$name', tag_1='$tag1_id', 
       	 tag_2='$tag2_id', address='$address', city='$city',
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
	header("location:../home");
  }
else
  {
header("location:../");	
  }

disconnect_from_db($link);
?>