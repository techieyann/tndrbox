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

extract($_POST);
$category = add_tag($_POST['category']);

//need to write geocoding script to get lat/lon
$lat = 0;
$lon = 0;


	$query = "INSERT INTO business (name, category, address, city, 
									state, zip, lat, lon, url, 
									number, hours)
									VALUES
								   ('$name', '$category', '$address', '$city',
									'$state', '$zip', '$lat',' $lon', '$url',
									'$number', '$hours')";


$result = query_db($query);




if($result)
  {
	$b_id = get_last_insert_ID();
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
							$query = "UPDATE business SET
					         	       logo='logo_$b_id.$ext'
						       	       WHERE id='$b_id'";
					  	    query_db($query);
						}
					}
				}
			}
	header("location:../settings?view=new_post");
  }
else
  {
	header("location:../settings?view=new_business");	
  }

disconnect_from_db($link);
?>