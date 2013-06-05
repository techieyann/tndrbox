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
else
  {
	$b_id = $_SESSION['b_id'];
  }

extract($_POST);

$latlon = addr_to_latlon($address.'+'.$city.'+'.$state.'+'.$zip);
$lat = $latlon['lat'];
$lon = $latlon['lon'];

$bus_name = add_slashes($bus_name);
$address = add_slashes($address);
$city = add_slashes($city);
$state = add_slashes($state);
$url = add_slashes($url);
$number = add_slashes($number);
$hours = add_slashes($hours);




	$logo_upload_flag = false;

if(isset($_FILES['logo_upload']))
  {
	if($_FILES['logo_upload']['error'] > 0)
      {
       	echo "Error: ".$_FILES['logo_upload']['error'];
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
  }

	$photo_upload_flag = false;

if(isset($_FILES['photo_upload']))
  {
	if($_FILES['photo_upload']['error'] > 0)
      {
       	echo "Error: ".$_FILES['photo_upload']['error'];
      }
    else
      {
		extract($_FILES['photo_upload']);
		if(strcmp("image", substr($type,0,5)) == 0)
		  {	
		
			if($size < (2*1024*1024))
		      {

				$ext = substr($type,6);
									
				if(move_uploaded_file($tmp_name, "../images/posts/business_$b_id.$ext"))
				  {

					$photo_upload_flag = true;
				  }
		      }
		  }
	  }
  }
$query = "UPDATE business SET name='$bus_name', category='$category', 
        address='$address', city='$city',
		state='$state', zip='$zip', lat='$lat', lon='$lon',
		url='$url', number='$number', hours='$hours'
		".($logo_upload_flag ? ", logo='logo_$b_id.$ext'":"")
		.($photo_upload_flag ? ", photo='business_$b_id.$ext'":"")."
		WHERE id='$b_id'";
$result = query_db($query);

if($result)
  {
	//header("location:../settings");	
  }
else
  {
	//header("location:../edit-business".(check_admin() ? "?id=$b_id":""));
  }

disconnect_from_db($link);
?>