<?php
require('../includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();
$b_id = $GLOBALS['b_id'];

	echo "
	<script src='js/settings.js' type='text/javascript'></script>
	<br><br>
	<div id='members' class='row-fluid'>
		<div class='span3'>
			<ul id='settings-nav' class='nav nav-pills nav-stacked content'>
				<li id='new-post-li'>
					<a class='nav-link' href='new_post'><i class='icon-plus'></i> New Post</a>
				</li>";
	if(check_admin())
	  {
		echo "
				<li id='new-business-li'>
					<a class='nav-link' href='new_business'><i class='icon-plus'></i> New Business</a>
				</li>
				<li id='new-user-li'>
					<a class='nav-link' href='new_user'><i class='icon-plus'></i> New User</a>
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
				<li id='posts-li'>
					<a class='nav-link' href='posts'><i class='icon-folder-open'></i> Posts</a>
				</li>
				<li id='profile-li'>
					<a class='nav-link' href='edit_profile'><i class='icon-cog'></i> Member Info</a>
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
	</div>
	<br><br>";
?>