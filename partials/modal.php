<?php
//expects $_GET[p] to be set as id of post
if(isset($_GET['p']))
  {
	require('../includes/includes.php');
	require('../includes/tags.php');

	connect_to_db($mysql_user, $mysql_pass, $mysql_db);
	analyze_user();

	
	require('prints.php');

	$query = "SELECT * FROM postings WHERE id=".$_GET['p'];
	$result = query_db($query);
	$post = $result[0];

	$b_id = $post['b_id'];

	if($post['active'] == 1)
	  {
		$active_flag = true;
	  }
	else
	  { 
		$active_flag = false;
	  }
	$owner_flag = false;
	if(isset($GLOBALS['m_id']))
	  {
		$m_id = $GLOBALS['m_id'];

		if($m_id == $post['a_id'])
		  {
			$owner_flag = true;
		  }
	  }
	if(check_admin())
	  {
		$owner_flag = true;
	  }

	extract($post);
	$p_id = $id;

if($id == null)
  {
	echo "		<script>
					
				</script>
				<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='error'>Error: couldn't find post...</h4>
				</div>";
  }

else
  {
	$query = "UPDATE postings SET viewed = viewed + 1 WHERE id=$id";
	query_db($query);
	$query = "SELECT * FROM business WHERE id=$b_id";
	$result = query_db($query);
	$business = $result[0];

   	$tags[1] = get_tag($tag_1); 
   	$tags[2] = get_tag($tag_2); 
   	$tags[3] = get_tag($tag_3);

	if($post['alt_address'] == "")
      {
		$alt_address = $business['address']." ".$business['city'].", ".$business['state'].", ".$business['zip'];
	  }
	$date = format_date($id);
	
	echo "
			
				<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>";
    if($owner_flag)
	  {
		echo "
					<ul class='inline pull-right'>
						<li><a title='Edit' href='settings?view=edit_post&id=$id'><i class='icon-pencil'></i></a></li>
						<li><a title='Deactivate'  href='settings?view=deactivate_post&id=$id'><i class='icon-ban-circle'></i></a></li>
						<li><a title='Delete' href='settings?view=delete_post&id=$id'><i class='icon-trash'></i></a></li>						
					</ul>";
	  }

	echo "
						<h3 id='post-modal-label' class='centered'><strong>".($url!="" ? "<a href='http://$url'>$title</a>":"$title")."</strong>".($date != "" ? " <i>on $date</i>" :"")."</h3>
				</div>
				<div class='modal-body'>
					<div class='row-fluid'>
						<div class='span7 top-left' id='post'>
							<div class='row span12'>
								<div class='span12 centered'>";

	$map_used_flag = false;

	if($photo != "")
	  {
		echo "
									<img src='images/posts/$photo' alt='photo for $title' class='posting-image'>";

	  }
	else
	  {
		echo "
						   			<a href='http://maps.google.com/?q=$alt_address'>
						   				<img src='http://maps.googleapis.com/maps/api/staticmap?center=$alt_address&zoom=16&size=325x250&markers=color:red|$alt_address&sensor=false' class='rounded'>
						   			</a>";
		$map_used_flag = true;
	  }

	
	
	echo "
								</div>
							</div>
							<ul class='inline centered'>
								<li><a href='index?tag=$tag_1' class='tag'><img src='images/$tags[1].svg' width='20'> &nbsp $tags[1]</a></li>
								<li><a href='index?tag=$tag_2' class='tag'>$tags[2]</a></li>
								<li><a href='index?tag=$tag_3' class='tag'>$tags[3]</a></li>
							</ul>

   							<div id='posting-blurb' class='posting-blurb content'>
								<strong>$blurb</strong>
							</div>

								<div class='row span11'>								
									<div class='posting-time pull-right muted'>
										<p>Posted at <strong>";
	print_formatted_time($posting_time);
	echo "</strong></p>
									</div>
								</div>

				 		</div>
						<div class='span5'>";

	if(!$map_used_flag)
	  {
		echo "
							<div class='centered content'>
				   			<a href='http://maps.google.com/?q=$alt_address'>
			   				<img src='http://maps.googleapis.com/maps/api/staticmap?center=$alt_address&zoom=16&size=325x250&markers=color:red|$alt_address&sensor=false' class='rounded'>
				   			</a>
							</div>";
	  }
	extract($business);
	$category_id = $category;
	$category = get_tag($category_id);
	echo "
							<div class='business-info bottom-right'>
								<h3 style='text-align:center'>";
	$close_link = "";
	if($url != "")
	{
		echo "<a href=\"http://$url\">";
		$close_link = "</a>";
	}
	if($logo != "")
    {
 		echo "<img src='images/logos/$logo' title='$name' alt='$name'>";
   	}
   	else
   	{
   		echo $name;
   	}
	echo $close_link."</h3><br>
							<div class='row'>
							<div class='span6'>
								Hours:<br>";
	$hours = explode(",", $hours);
	foreach($hours as $line)
	{
		echo "
								$line<br>";
	}
	echo "
							</div>
							<div class='span6'>
								<address>
								<a id=\"business-address\" href=\"http://maps.google.com/?q=$address, $city, $state $zip\">
								$address<br>
								$city, $state, $zip<br>
								</a>
								P: $number<br>
								</address>
							</div>
						</div>
						</div>
   					</div>
				</div>
		   	</div>
			<div class='modal-footer'>

				<button id='share-button' class='share-button btn btn-info pull-left' title=\"
Select your method:
'<button type='button' class='close' onclick='$(&quot;#share-button&quot;).popover(&quot;hide&quot;);'>&times;</button>\" data-content=\"

<a href='https://twitter.com/share' class='twitter-share-button' data-url='http://tndrbox.com/?p=$p_id' data-text='$title at $name' data-count='none' data-dnt='true'>Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>


<iframe src='//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ftndrbox.com%2F%3Fp%3D$p_id&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=recommend&amp;height=21' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:450px; height:21px;' allowTransparency='true'></iframe>
\"
				>Share</button>

				<button class='btn pull-right' data-dismiss='modal' aria-hidden='true'>Close</button>
			</div>";
  }
		disconnect_from_db();
  }
else
  {
		echo "
				<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='error'>Error: no ID specified...</h4>
				</div>";
  }


?>