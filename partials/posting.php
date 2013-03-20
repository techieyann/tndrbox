<?php
//expects $_GET[p] to be set as id of post
if(isset($_GET['p']))
  {
	require('../includes/includes.php');
	require('../includes/tags.php');

	connect_to_db($mysql_user, $mysql_pass, $mysql_db);
	analyze_user();

	$query = "SELECT * FROM postings WHERE id=".$_GET['p'];
	$result = query_db($query);

	if(isset($result[0]))
	  {

	$post = $result[0];

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

	$b_id = $post['b_id'];

	if($post['active'] == 1)
	  {
		$active_flag = true;
	  }
	else
	  { 
		$active_flag = false;
	  }


	extract($post);
	$p_id = $id;

if($id == null)
  {
	echo "		<script>
					
				</script>
				<div class='post-header'>
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
				<div class='post-header'>
					<div class='btn-group pull-right post-mod-buttons'>";
    if($owner_flag)
	  {
		echo "

						<a class='btn btn-mini' title='Edit' href='settings?view=edit_post&id=$id'><i class='icon-pencil'></i></a>
						<a class='btn btn-mini' title='Deactivate'  href='settings?view=deactivate_post&id=$b_id'><i class='icon-ban-circle'></i></a>
						<a class='btn btn-mini' title='Delete' href='settings?view=delete_post&id=$id'><i class='icon-trash'></i></a>";
}

	echo "
						<button class='btn btn-mini' title='Close' onclick='closePost()'><i class='icon-remove'></i></button>
					</div>

					<ul class='inline centered post-title'>
						<li>".($url!="" ? "<a href='http://$url'><b>$title</b></a>":"<b>$title</b>")."</li>".($date != "" ? "<li class='date'> <i>on $date</i></li>" :"")."
					</ul>
				</div>
				<div class='post-body'>
					<div class='row-fluid'>
						<div class='span7 top-left' id='post'>";

	if($photo != "")
	  {
		echo "
							<div id='posting-image' class='row span12'>
								<div class='span12 centered'>




									<img src='images/posts/$photo' alt='photo for $title' class='posting-image'>
								</div>
							</div>";

	  }


	
	
	echo "



   							<div id='posting-blurb' class='posting-blurb content'>
								<strong>$blurb</strong>
							</div>


									<div class='posting-time muted'>
										<p>Posted at <strong>";
	print_formatted_time($posting_time);
	echo "</strong></p>
									</div>


				 		</div>
						<div class='span5'>
							<div class='row'>
							<ul class='unstyled tags'>
								<li class='centered'><a href='index?cat=$tag_1' class='tag'><img src='images/icons/$tags[1].png' width='35'> &nbsp $tags[1]</a></li>
								<li><ul class='inline centered'>
								<li><a href='index?tag=$tag_2' class='tag'>$tags[2]</a></li>
								<li><a href='index?tag=$tag_3' class='tag'>$tags[3]</a></li>
								</ul></li>
							</ul></div>";


	extract($business);
	$category_id = $category;
	$category = get_tag($category_id);
	echo "	

							<div class='business-info business-card'>
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

								Hours:<br>";
	$hours = explode(",", $hours);
	foreach($hours as $line)
	{
		echo "
								$line<br>";
	}
	echo "
";
	  }
	echo "

								<address>
								<a id=\"business-address\" href=\"http://maps.google.com/?q=$address, $city, $state $zip\">
								$address<br>
								$city, $state, $zip<br>
								</a>
								".($number != "" ? "P: $number<br>":"")."
								</address>

						</div>
						</div>";

echo "

<div class='share btn-group'>
	<a class='btn' href=\"mailto:?to=&subject=$title @ $name&body=http://tndrbox.com/?p=$p_id\"><img src='images/icons/em.png' alt='Email'></a>
	<a class='btn' href=\"http://www.facebook.com/sharer.php?t=$title @ $name&u=http://tndrbox.com/?p=$p_id\" target='_blank'><img src='images/icons/fb.png' alt='Facebook'></a>
	<a class='btn' href=\"http://twitter.com/share?url=http://tndrbox.com/?p=$p_id&text=$title @ $name\" target='_blank'><img src='images/icons/tw.png' alt='Twitter'></a>

	<a class='btn' href=\"https://plus.google.com/share?url=http://tndrbox.com/?p=$p_id\" target=_blank'><img src='images/icons/gp.png' alt='Google+'></a>

</div>
   					</div>
				</div>
			</div>";
  }
		disconnect_from_db();
  }
else
  {
		echo "
				<div class='post-header'>
				<button class='btn pull-right' onclick='closePost()'>Close</button>
					<h4 class='error'>Error: no ID specified...</h4>
				</div>";
  }
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
?>