<?php
/***********************************************
file: new_business.php
creator: Ian McEachern

This script creates a new business.
***********************************************/

require('../includes/includes.php');

require('../includes/tags.php');
	
$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();	

$b_id = $_GET['id'];

extract($_POST);
$category = add_tag($_POST['category']);

//need to write geocoding script to get lat/lon
$lat = 0;
$lon = 0;

if($b_id != 0)
  {
	$query = "UPDATE business SET name='$name', category='$category', 
	    	 address='$address', city='$city',
			state='$state', zip='$zip', lat='$lat', lon='$lon',
			url='$url', number='$number', hours='$hours'
			WHERE id='$b_id'";
  }
else
  {
	$query = "INSERT INTO business (name, category, address, city, 
									state, zip, lat, lon, url, 
									number, hours)
									VALUES
								   ('$name', '$category', '$address', '$city',
									'$state', '$zip', '$lat',' $lon', '$url',
									'$number', '$hours')";
	$b_id = get_last_insert_ID();
  }
$result = query_db($query);


if($result)
  {
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
							$query = "UPDATE business SET
					         	       logo='img_$b_id.$ext'
						       	       WHERE id='$b_id'";
					  	    query_db($query);
						}
					}
				}
			}
	header("location:../settings");
  }
else
  {
header("location:../");	
  }

disconnect_from_db($link);
?>