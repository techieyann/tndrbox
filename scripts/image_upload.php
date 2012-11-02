<?php
        require('../includes/includes.php');


        $link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

		analyze_user();
		verify_logged_in();	

		$p_id = sanitize($_GET['p_id']);        

			if($_FILES['image_upload']['error'] > 0)
            {
              	echo "Error: ".$_FILES['image_upload']['error'];
                header("location:../home");
            }
            else
            {
				extract($_FILES['image_upload']);
				if(strcmp("image", substr($type,0,5)) == 0)
				{
					
					if($size < (240*1024))
					{
					  $ext = substr($type,6);
									
						if(move_uploaded_file($tmp_name, "../images/posts/img_$p_id.$ext"))
						{
							$query = "UPDATE postings SET
			       	 	  	       	       photo='img_$p_id.$ext'
						       	       WHERE id='$p_id'";
					  	       	query_db($query);
						}
					}
				}
			}
		

		HEADER("location:../home");
		disconnect_from_db($link);
?>