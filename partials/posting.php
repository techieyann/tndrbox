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
		$alt_address = $business['address'].", ".$business['city'].", ".$business['state'].", ".$business['zip'];
	  }
	$date = format_date($id);
	
	echo "
				<script>
					$(document).ready(function(){
					$('a.tag').click(function(e){
						if(!$('#filters').hasClass('disabled'))
						{
							toggleFilterView();
						}
						if(!$(this).hasClass('category'))
						{
							$('#search').attr('placeholder', $(this).html());
						}
					});
					});
				</script>
				<div class='post-header'>
					<div class='btn-group pull-right post-mod-buttons'>";
    if($owner_flag)
	  {
		echo "

						<a class='btn btn-mini' title='Edit' href='#b=members&view=edit_post&id=$id'><i class='icon-pencil'></i></a>
						<a class='btn btn-mini' title='Deactivate'  href='#b=members&view=deactivate_post&id=$b_id'><i class='icon-ban-circle'></i></a>
						<a class='btn btn-mini' title='Delete' href='#b=members&view=delete_post&id=$id'><i class='icon-trash'></i></a>";
}

	echo "
						<button class='btn btn-mini' title='Close' onclick='closePostButton()'><i class='icon-remove'></i></button>
					</div>

<p class='post-title centered'><b>$title</b><p>

				</div>
				<div class='post-body'  onload=\"$('.tiles').masonry('reload');\">
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
								<strong>$blurb</strong>";
	if($url!="")
	  {
		echo "
								<br><br>
								<div style='text-align:right'>
								Would you like to know more?<br>
								Click <a href='http://$url' target='_blank'>here.</a>
								</div>";
	  }

	echo "
							</div>


									<div class='posting-time muted'>
										<p>Posted at <strong>";
	print_formatted_time($posting_time);
	echo "</strong></p>
									</div>


				 		</div>
						<div class='span5'>
							<div class='post-spacetime'>
								<a href='http://maps.google.com/?q=".urlencode($alt_address)."' target='_blank'>
								<div id='post-address'>
									<div class='target pull-left'></div>
									<address>$alt_address</address>
								</div></a>";
	if($date != "")
	  {
		echo "
								<div id='post-time'>
									<div class='date pull-left'></div>
									$date								
								</div>";
	  }

	echo "
							</div>
							<div class='row'>
							<ul class='inline tags centered'>
								<li><a href='#t=$tag_2' class='tag'>$tags[2]</a></li>
								<li><a href='#t=$tag_3' class='tag'>$tags[3]</a></li>
								<li><a href='#c=$tag_1' class='category tag'><div class='$tags[1] pull-left'></div> $tags[1]</a></li>
								</ul></div>";


	extract($business);
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
							<div class='business-information'>";
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
								".($number != "" ? "P: $number<br>":"")."
								</address>

						</div>
						</div>";
	$tndrbox_share = urlencode('http://tndrbox.com/?p='.$p_id);
echo "
<div class='share btn-group pull-right'>
	<a class='btn' href=\"mailto:?to=&subject=$title @ $name&body=http://tndrbox.com/?p=$p_id\" target='_blank'><div class='email'></div></a>
	<a class='btn' href=\"http://www.facebook.com/sharer.php?t=$title @ $name&u=$tndrbox_share\" target='_blank'><div class='facebook'></div></a>
	<a class='btn' href=\"http://twitter.com/share?url=$tndrbox_share&text=$title @ $name\" target='_blank'><div class='twitter'></div></a>
	<a class='btn' href=\"https://plus.google.com/share?url=$tndrbox_share\" target=_blank'><div class='google_plus'></div></a>

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