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




//head
$GLOBALS['header_scripts'] = "

		<script src='js/jquery.form.js' type='text/javascript'></script>
		<script src='js/settings.js' type='text/javascript'></script>";

if(check_admin())
  {
	$GLOBALS['header_html_title'] = "tndrbox - Admin";

  }
else
  {
	$query = "SELECT name FROM business WHERE id=".$GLOBALS['b_id'];
	$result = query_db($query);
	$GLOBALS['header_html_title'] = "tndrbox - ".$result[0]['name'];
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
	$b_id = $GLOBALS['b_id'];
	echo "
	<div class='row-fluid'>
		<div class='span3'>
			<ul id='settings-nav' class='nav nav-pills nav-stacked content'>
				<li id='posts-li'>
					<a class='nav-link' href='posts'>".(	check_admin() ? "Posts"	:"Your Posts")."</a>
				</li>
				<li id='new-post-li'>
					<a class='nav-link' href='new_post'>Add Post</a>
				</li>";
	if(check_admin())
	  {
		echo "
				<li id='new-business-li'>
					<a class='nav-link' href='new_business'>Add Business</a>
				</li>
				<li id='new-user-li'>
					<a class='nav-link' href='new_user'>Add User</a>
				</li>
				<li id='profile-li'>
					<div class='input-prepend'>
						<span class='add-on'><i class='icon-search'></i></span>
						<input id='business-search' type='text' class='span10' placeholder='Businesses'>
					</div>
				</li>";
	  }
	else
	  {
		echo "
				<li id='profile-li'>
					<a class='nav-link' href='edit_profile'>Settings</a>
				</li>";
	  }
	echo "
			</ul>
		</div>
		<div id='settings-content' class='span9 content rounded'>
			<div id='loading' class='centered'>
				<img src='images/loading.gif' alt='Loading...'>
			</div>
		</div>
	</div>";
  }
?>