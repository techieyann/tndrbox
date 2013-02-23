<?php

/***********************************************
file: includes/prints.php
creator: Ian McEachern

This file contains the functions which print
often used sections of the site. For example, 
print_head() prints the site from the start of 
the output file to the start of the body tag.

These functions utilize values set by 
analyze_user() in includes.php. They will only
work properly if called after analyze_user().

This file also needs to be included after the
call to analyze_user() in order for the code to
access the metadata variables.
 ***********************************************/

function print_head()
{
	echo "<!DOCTYPE html>

<html>

	<head>
	<!-- Meta data -->
		<title>".$GLOBALS['header_html_title']."</title>
		<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>
	   	<meta name='author' content='Ian McEachern'>

	<!-- Icons -->
		<link rel='icon' type='image/ico' href='images/favicon.ico'>
		<link rel='shortcut icon' href='images/favicon.ico'>
		<link rel='apple-touch-icon' href='images/touchicon.png'>

	<!-- Javascript -->
		<!-- jquery -->
		<script src='js/jquery.js' type='text/javascript'></script>
		<script src='js/jquery-ui.js'></script>
		<script src='js/jquery.ga.js'></script>
		<!-- Bootstrap -->
		<link href='css/bootstrap.min.css' rel='stylesheet' media='screen'>
		<meta name='viewport' content='width=device-width, initial-scale=1.0'>
		<!-- Modernizr -->
		<script src='js/modernizr.js'></script>
		<!-- google analytics via jquery.ga -->
		<script>
			$(document).ready(function(){\$.ga.load('".$GLOBALS['ga_account']."');});
		</script>

	<!-- CSS -->
		<!-- homebrewed -->
		<link rel='stylesheet' type='text/css' href='css/styles.css' media='all'>
		<!-- jquery -->
		<link rel='stylesheet' type='text/css' href='css/jquery-ui.css' media='all'>

	<!-- page specific css/js -->".$GLOBALS['header_scripts']."

	</head>

	<body>

		<header>
			<div id='top' class='navbar navbar-inverse navbar-static-top'>
				<div class='navbar-inner'>
					<div class='container'>
						<a class='btn btn-navbar' data-toggle='collapse' data-target='.nav-collapse'>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
							<span class='icon-bar'></span>
						</a>
						<a href='index' id='tndrbox-logo' class='brand'></a>
						<div class='nav-collapse collapse'>
							<ul class='nav main-nav pull-right'>
								<li";
	if($GLOBALS['header_selected_page'] == "about")
	{
		echo " class='active'";
	}
	echo "><a href='about'>About</a></li>
								<li";
	if($GLOBALS['logged_in'] == false)
	{
		if($GLOBALS['header_selected_page'] == "login")
		{
			echo " class='active'";
		}
		echo "><a href='login'>Login</a></li>";
	}
	else
	{
		if($GLOBALS['header_selected_page'] == "settings")
		{
			echo " class='active'";
		}
		echo "><a href='settings'>Settings</a></li>
								<li><a href='scripts/logout'>Logout</a></li>";
	}
	echo "	
							</ul>
						</div><!-- .nav-collapse .collapse -->
					</div><!-- .container -->
				</div><!-- .navbar-inner -->
			</div><!-- #top -->
		</header>

		<div id='body-container' class='container'>";

}

function print_foot()
{
  echo "

			<div id='footer'>
				<a href='#top'>
				   <img id='footer-icon' src='images/footer-logo.png' alt='footer-logo' width='50' height='62'>
				</a>
				<br>
				<p class='white'>version ".$GLOBALS['version']."</p>
			</div><!-- #footer -->

		</div><!-- #body-container -->

	<!-- minimized javascript -->
		<!--Bootstrap-->
		<script src='js/bootstrap.min.js'></script>
		<!--Masonry-->
		<script src='js/jquery.masonry.min.js'></script>
	</body>

</html>";
}

?>
