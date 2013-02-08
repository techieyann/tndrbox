<?php
/***********************************************
file: partials/posts.php
creator: Ian McEachern

This partial displays the posts authored by the
user
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

echo "
			<div id='js-content' class='span12'>
			<script>
				$(document).ready(function(){
					$('.post-link').click(function(e){
						e.preventDefault();

						var view = $(this).attr('href');
						var id = $(this).attr('id');

						loadContentByURL(view, id);
				   	});
					$('.accordion').accordion({
						collapsible:true,
						active:false,
						heightStyle:'content'
					});
  					$('.post-accordion').accordion({
						collapsible:true,
						active:0,
						heightStyle:'content'
					});
				});

			</script>";

if(check_admin())
  {
	$m_id = $GLOBALS['m_id'];
	$query = "SELECT DISTINCT b_id FROM postings WHERE a_id=$m_id";
	$buss = query_db($query);
	echo "
			<div class='accordion'>";
	foreach($buss as $bus)
	  {
		$b_id = $bus['b_id'];
		$query = "SELECT name FROM business WHERE id=$b_id";
		$result = query_db($query);
		$name = $result[0]['name'];
		echo "
				<h3>$name</h3>
				<div class='posts'>";
		print_business_posts($b_id);
		echo "
				</div>";
	  }	
	echo "
			</div>";
  }
else
  {
	$b_id = $GLOBALS['b_id'];
	print_business_posts($b_id);
  }

echo "
			</div>";

disconnect_from_db();

function print_business_posts($b_id)
{
$m_id = $GLOBALS['m_id'];
$query = "SELECT id, active, title FROM postings WHERE a_id=$m_id AND b_id=$b_id ORDER BY active DESC";
$posts = query_db($query);
$count = 0;
echo "
   	<div class='post-accordion'>";

foreach($posts as $post)
  {
	if($post['active'] == 1)
	  {
		echo "
	   	<h3>Active Post</h3>
		<div>";
		$id = $post['id'];
		echo "
			<div class='span12 modal white-bg' style='position:relative; left:auto; right:auto; margin:0; max-width:100%;'>";
		print_modal($id);
		echo "
			</div>
		</div>";
	  }
	if($count++ == 0)
	  {
		echo "
		<h3>Archived Posts</h3>
		<table class='table table-hover'>
			<tbody>";
	  }
	if($post['active'] == 0)
	  {
		extract($post);
		echo "
			<tr>
				<td>
				<h4>$title</h4>
				</td>
				<td>
		   			<ul class='inline pull-right'> 
						<li><a class='post-link' title='Activate' href='edit_post' id='$id'><i class='icon-fire large'></i></a></li>
						<li><a class='post-link'title='Delete' href='delete_post' id='$id'><i class='icon-trash'></i></a></li>
					</ul>
				</td>
			</tr>";
		}
  }
echo "
			</tbody>
		</table>
	</div>";


}

function print_formatted_time($time)
{
//YYYY-MM-DD hh:mm:ss -> hh:mm(am/pm) MM/DD/YY

//Hour formatting
$hours = substr($time,11,2);
$pmam = "am ";
if($hours > 12)
{
	$hours -= 12;
	$pmam = "pm ";
}

//Minute extraction
$minutes = substr($time,14,2);

//Month extraction
$month = substr($time,5,2);

//Day extraction
$day = substr($time,8,2);

//Year extraction
$year = substr($time,2,2);

//Output
echo $hours.":".$minutes.$pmam.$month."/".$day."/".$year;


}


function print_modal($id)
  {
	$query = "SELECT * FROM postings WHERE id=$id";
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

		if($m_id == $post['a_id'] || check_admin())
		  {
			$owner_flag = true;
		  }
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
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<ul class='inline pull-right'>
						<li><a class='post-link' title='Edit' href='edit_post' id='$id'><i class='icon-pencil'></i></a></li>
						<li><a class='post-link' title='Deactivate' href='deactivate_post' id=$b_id><i class='icon-ban-circle'></i></a></li>
						<li><a class='post-link' title='Delete' href='delete_post' id='$id'><i class='icon-trash'></i></a></li>						
					</ul>
					<ul class='inline centered'>
						<li>
						<li id='post-modal-label'><h3>".($url!="" ? "<a href='http://$url'>$title</a>":"$title")."</h3></li>".($date != "" ? "<li><h4> <i>on $date</i></h4></li>" :"")."
					</ul>
				</div>
				<div class='modal-body'>
					<div class='row-fluid'>
						<div class='span7 top-left' id='post'>
							<div id='posting-image' class='row span12'>
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
							<div class='row'>";
	if($hours != "")
	  {
	echo "
							<div class='span6'>
								Hours:<br>";
	$hours = explode(",", $hours);
	foreach($hours as $line)
	{
		echo "
								$line<br>";
	}
	echo "
							</div>";
	  }
	echo "
							<div class='span6'>
								<address>
								<a id=\"business-address\" href=\"http://maps.google.com/?q=$address, $city, $state $zip\">
								$address<br>
								$city, $state, $zip<br>
								</a>
								".($number != "" ? "P: $number<br>":"")."
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
<button type='button' class='close' onclick='$(&quot;#share-button&quot;).popover(&quot;hide&quot;);'>&times;</button>\" data-content=\"
<ul class='inline'>

<a href='mailto:?to=&subject=$title&body=http://tndrbox.com/?p=$p_id'><i class='icon-envelope'></i> E-mail</a><br>

<a href='https://twitter.com/share' class='twitter-share-button' data-url='http://tndrbox.com/?p=$p_id' data-text='$title at $name' data-count='none' data-dnt='true'>Tweet</a>

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>

<iframe src='//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ftndrbox.com%2F%3Fp%3D$p_id&amp;&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=recommend&amp;height=21' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:450px; height:21px;' allowTransparency='true'></iframe>

\"
				>Share</button>

				<button class='btn pull-right' data-dismiss='modal' aria-hidden='true'>Close</button>
			</div>";
  }

  }

?>