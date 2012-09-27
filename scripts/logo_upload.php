<?php
        require('../includes/includes.php');
        require('../includes/db_interface.php');

        $link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

		analyze_user();
		verify_logged_in();	

		$b_id = sanitize($_GET['id']);        

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
							$query = "UPDATE business SET
			       	 	  	       	       logo='logo_$b_id.$ext'
						       	       WHERE id='$b_id'";
					  	       	query_db($query);
						}
					}
				}
			}
		

		header("location:../home");
		disconnect_from_db($link);
?>