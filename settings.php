<?php
/***********************************************
file: settings.php
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
$m_id = $GLOBALS['m_id'];

if($b_id != 0)
  {
	$query = "SELECT * FROM business where id=$b_id";
	$result = query_db($query);

	//Check if the business was found
	if(mysql_num_rows($result)==1)
	  {
		$business_flag = 1;
	
		$business = mysql_fetch_array($result);
		$cantegory = get_tag($business['category']);

		
		$query = "SELECT * FROM postings WHERE a_id=$m_id";
		$result = query_db($query);
		
		$active_post_flag = false;
		$old_posting_flag = false;
		$i = 0;
		while($post = mysql_fetch_array($result))
		{
			if($post['active'])
			{
				$post_flag = true;
				$posting = $post;
			}
			else
			{
				$old_postings[$i++] = $post;
			}
		}
		if($i>0)
		  {
			$old_posting_flag = true;
		  }
	}
  }
else
  {
	$query = "SELECT * FROM postings WHERE a_id=".$GLOBALS['m_id']." AND active=1";
	$result = query_db($query);
	$i = 0;
	while($post = mysql_fetch_array($result))
	  {
		$admin_active_posts[$i++] = $post;
	  }

	$query = "SELECT * FROM postings WHERE a_id=".$GLOBALS['m_id']." AND active=0";
	$result = query_db($query);
	$i = 0;
	while($post = mysql_fetch_array($result))
	  {
		$admin_old_posts[$i++] = $post;
	  }

	$query = "SELECT id, name FROM business";
	$result = query_db($query);
	while($business = mysql_fetch_array($result))
	  {
		extract($business);
		$businesses[$id] = $name;
	  }
  }


//head
$GLOBALS['header_scripts'] = "
<link rel='stylesheet' type='text/css' href='css/jquery-ui.css' media='all'>
<script src='js/jquery.form.js' type='text/javascript'></script>
<script src='js/jquery-ui.js'></script>";

if($b_id == 0)
  {
	$GLOBALS['header_html_title'] = "tndrbox - Admin";
	$GLOBALS['header_scripts'] = $GLOBALS['header_scripts']."
<script src='js/settings_admin.js' type='text/javascript'></script>";
  }
else
  {
	$GLOBALS['header_html_title'] = "tndrbox - ".$posting['title'];
	$GLOBALS['header_scripts'] = $GLOBALS['header_scripts']."
<script src='js/settings.js' type='text/javascript'></script>";
  }

//include jquery form application (jquery.form.js) and specialized javascript for this page (home.js)


$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "settings";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
  {
	global $b_id;
	if($b_id != 0)
	  {
		//Define the standard user variables as set above
		global $business_flag, $business, $category, $posting, $post_flag, $old_postings_flag, $old_postings;
		
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
			if($posting['alt_address'] == "")
			  {
				$posting['alt_address'] = $business['address']." ".$business['city'].", ".$business['state'].", ".$business['zip'];
			  }
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

	//Admin page
	else
	  {
		//Define the admin variables as set above
		global $admin_active_posts, $admin_old_posts, $businesses;
		echo "
	<div id='admin-accordion'>
		<h3>Posts</h3>
		<div id='admin-posts'>";
		echo "	
			<h3>Add New Post</h3>";
		print_add_post_form($businesses);
		echo "
			 <h3>Active Posts</h3>
			 <div id='admin-active-posts'>";
		foreach($admin_active_posts as $post)
		  {
			echo "
				<h3>".$post['title']." | <a href='#'>Delete</a></h3>";
			print_formatted_post($post);
		  }
		echo "
			</div>
			<h3>Old Posts</h3>
			<div id='admin-old-posts'>";
		foreach($admin_old_posts as $post)
		  {
			echo "
				<h3>".$post['title']." | <a href='#'>Activate</a> | <a href='#'>Delete</a></h3>";
			print_formatted_post($post);
		  }

		echo "
			</div>			 
		</div>
		<h3>Add New Business</h3>
		<div id='admin-new-business'>";
		print_new_business_form();
		echo "
		</div>
		<h3>Add New User</h3>
		<div id='admin-new-users'>";
		print_new_user_form($businesses);
		echo "
		</div>
	</div>";
	  }
  }
?>