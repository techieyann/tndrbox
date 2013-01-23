<?php
//expects $_GET[id] to be set as id of post
if(isset($_GET['id']))
  {
	require('../includes/includes.php');
	require('../includes/tags.php');
	require('../includes/prints.php');

	connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	$query = "SELECT * FROM postings WHERE id=".$_GET['id'];
	$result = query_db($query);
	$post = $result[0];
	extract($post);

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
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>

						<h3 id='post-modal-label'><strong>".($url!="" ? "<a href='http://$url'>$title</a>":"$title")."</strong>".($date != "" ? " <i>on $date</i>" :"")."</h3>
				</div>
				<div class='modal-body'>
					<div class='row-fluid'>
						<div class='span7 top-left' id='post'>
							<div class='row'>
								<div class='span4 centered'>
									<a href='index?tag=$tag_1'>$tags[1]</a>
								</div>
								<div class='span4 centered'>
									<a href='index?tag=$tag_2'>$tags[2]</a>
								</div>
								<div class='span4 centered'>
									<a href='index?tag=$tag_3'>$tags[3]</a>
								</div>
							</div>
							<div id='posting-border' class='posting-border content'>
								<div id='posting-data' class='posting-data'>";

	if($photo != "")
	  {
		echo "
									<img src='images/posts/$photo' alt='photo for $title' class='posting-image'>";

	  }

	echo "					
									<div id='posting-blurb' class='posting-blurb'>
										<strong>$blurb</strong>
									</div>";
	echo "
								</div>
								<div class='posting-time pull-right muted'>
									<p>Posted on: ";
   	print_formatted_time($posting_time);
	echo "</p>
								</div>
							</div>
				 		</div>
						<div class='span5'>
							<div class='centered content'>
				   			<a href='http://maps.google.com/?q=$alt_address'>
			   				<img src='http://maps.googleapis.com/maps/api/staticmap?center=$alt_address&zoom=16&size=325x250&markers=color:red|$alt_address&sensor=false' class='rounded'>
				   			</a>
							</div>";
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
						<h4 style='text-align:center'>(<a href='?tag=$category_id'>$category</a>)</h4><br>
						</div>
   					</div>
				</div>
		   	</div>
			<div class='modal-footer'>

				<button id='share-button' class='share-button btn btn-info pull-left' title=\"
Select your method:
'<button type='button' class='close' onclick='$(&quot;#share-button&quot;).popover(&quot;hide&quot;);'>&times;</button>\" data-content=\"
<a href='https://twitter.com/share' class='twitter-share-button' data-lang='en'>Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='https://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>
<iframe src='//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ftndrbox.com&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=recommend&amp;height=21' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:450px; height:21px;' allowTransparency='true'></iframe>
\"
				>Share</button>

				<button class='btn pull-right' data-dismiss='modal' aria-hidden='true'>Close</button>
			</div>";
	disconnect_from_db();
  }
?>