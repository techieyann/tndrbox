<?php
/***********************************************
file: home.php
creator: Ian McEachern

This file is the default page for logged in
users. It displays the user's business info and
the five most current postings. 
Redirects to index.php if user is not
logged in.
 ***********************************************/

require('includes/includes.php');
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

//Body
//Attempt to get data from the user's (1) business and the postings, (2) old and (3) new. Indicate with boolean flag.

//(1) Retrieve business data
$business_flag = 0;
$b_id = $GLOBALS['b_id'];

$query = "SELECT * FROM business where id=$b_id";
$result = query_db($query);

//Check if the business was found
if(mysql_num_rows($result)==1)
  {
	$business_flag = 1;

    $business = mysql_fetch_array($result);
	$tag1 = get_tag($business['tag_1']);
    $tag2 = get_tag($business['tag_2']);
  }


//(2) Retrieve active posting data
$query = "SELECT * FROM postings WHERE b_id=$b_id";
$result = query_db($query);
$post_flag = 0;

//If there wasn't a posting (or business), mysql_fetch_array() would fail
if($posting = mysql_fetch_array($result))
  {
	if( mysql_num_rows($result) != 0)
	  {
		$post_flag = 1;
	  }
  }

//(3) Retrieve old postings data
$query = "SELECT * FROM old_postings WHERE b_id=$b_id ORDER BY posting_time DESC";
$result = query_db($query);
$old_postings_flag = false;
$i = 0;

//Sort found old postings into $old_postings[0-(n-1)]
while($old_post = mysql_fetch_array($result))
  {
	$old_postings[$i++] = $old_post;
  }
//Set flag if any old post was found
if($i>0)
  {
	$old_postings_flag = true;
  }

//head
$GLOBALS['header_html_title'] = "tndrbox - ".$posting['title'];

//include jquery form application (jquery.form.js) and specialized javascript for this page (home.js)
$GLOBALS['header_scripts'] = "
<link rel='stylesheet' type='text/css' href='css/jquery-ui.css' media='all'>
<script src='js/jquery.form.js' type='text/javascript'></script>
<script src='js/jquery-ui.js'></script>
<script src='js/home.js' type='text/javascript'></script>";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "home";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  //Define the variables as set above
  global $b_id, $business_flag, $business, $tag1, $tag2, $posting, $post_flag, $old_postings_flag, $old_postings;
  extract($business);
  echo "
	<div class='meta-pane list'>";
  if($business_flag == 1)
	{
	  echo "
	<div id='business-info'>
		<table width='100%'>
		<tr>
			<th><a id='edit-business-link' href=''>Edit Profile</a></th>
		</tr>
		<tr>
			<td><h2>";
		$ending_string = "";
		if($url != "")
		{
			echo "<a href=\"http://$url\">";
			$ending_string = "</a>";
		}
		if($logo != "")
	    {
	 		echo "<img src=\"images/logos/$logo\" width=\"265\" title=\"$name\" alt=\"$name\">";
	   	}
	   	else
	   	{
	   		echo $name;
	   	}
	   	echo $ending_string;
		echo "</h2>
			<br>
			$number<br>";
		$hours = explode(",", $hours);
		foreach($hours as $line)
		{
			echo "
			$line<br>";
		}
		echo "
			<br><h3><a id=\"business-address\" href=\"http://maps.google.com/?q=$address $city $state $zip\">
			$address<br>
			$city, $state, $zip
			</a></h3><br><br>";
		echo "<div id=\"static-map\">";
		if($lat == "" ||$lat==0 || $lon == "" || $lon==0)
		{
			echo "
			<img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$address $city $state $zip&zoom=16&size=265x400&markers=color:red|$address $city $state $zip&sensor=false\">";
		}
		else
		{
			echo "
			<img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$lat,$lon&zoom=16&size=265x400&markers=color:red|$lat,$lon&sensor=false\">";    
		}
		echo "</div>
		</td></tr></table></div>";
	print_edit_business_form($business);
	echo "</div>";
}

	echo "
                 <div id='post-accordion' class='content-pane list'>
					<h3>Add New Post</h3>";
	print_add_post_form();
	if($post_flag == 1)
	{
		echo "
					<h3>Current Posting</h3>";
		extract($posting);
		print_formatted_post($posting);
		echo "
					<h3>Edit Current Post</h3>";
		print_edit_post_form($posting);	
	}
	if($old_postings_flag)
	{
		$i=0;
		foreach($old_postings as $old_post)
		{
			print_old_post($old_post, "-".++$i);
		}
	}
	echo "
	</div>
	</div>";

}
?>