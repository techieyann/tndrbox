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
	$admin = ($b_id==0 ? true : false);
	echo "
	<div class='row-fluid'>
		<div class='span3'>
			<ul class='nav nav-tabs nav-stacked content'>
				<li>
					<h4><a href='#'>Add Post</a></h4></li>
				<li class='active'>
					<h4><a href='#'>".(	$admin ? "Posts"	:"Your Posts")."</a></h4>
				</li>";
	if($admin)
	  {
		echo "
				<li>
					<h4><a href='#'>Add Business</a></h4>
				</li>
				<li>
					<h4><a href'=#'>Add User</a></h4>
				</li>
				<li><form class='navbar-search'>
					<input type='text' class='search-query span11' placeholder='Edit business'>
					<div class='icon-search'></div>
				</form></li>
				<li><form class='navbar-search'>
					<input type='text' class='search-query span11' placeholder='Edit user'>
					<div class='icon-search'></div>
				</form></li>";
	  }
	else
	  {
		echo "
				<li>
					<h4><a href='#'>Settings</a></h4>
				</li>";
	  }
	echo "
		</div>
		<div class='span8 content rounded'>
			testing
		</div><br>
	</div>";
  }
?>