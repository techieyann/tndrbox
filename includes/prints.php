<?php

/***********************************************
file: prints.php
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
	echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">

<html>

<head>
<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"all\">
<script src=\"http://sharenice.org/code.js\" type=\"text/javascript\"></script>
      <link type=\"text/css\" rel=\"stylesheet\" href=\"http://sharenice.org/main.css\" />   
      <!--[if IE 6]>
        <link type=\"text/css\" rel=\"stylesheet\" href=\"http://sharenice.org/ie6.css\" >  
      <![endif]-->      
<title>".$GLOBALS['header_html_title']."</title>
</head>

<body>


<div id=\"fb-root\"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = \"//connect.facebook.net/en_US/all.js#xfbml=1\";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<div id=\"header-fullwidth\">
		     <div id=\"header-wrap\">
                     	  <a href=\"index\" id=\"tndrbox-logo\"></a>
		     
			  <ul id =\"main-nav\"> 
			  <li";
	if($GLOBALS['header_selected_page'] == "business")
	{
		echo " id=\"nav-selected\"";
	}
	echo "><a href=\"business\">Businesses</a></li>
			  <li";
	if($GLOBALS['header_selected_page'] == "about")
	{
		echo " id=\"nav-selected\"";
	}
	echo "><a href=\"about\">About</a></li>
			  <li";
	if($GLOBALS['logged_in'] == false)
	{
		if($GLOBALS['header_selected_page'] == "login")
		{
			echo " id=\"nav-selected\"";
		}
		echo "><a href=\"login\">Login</a></li>";
	}
	else
	{
		if($GLOBALS['header_selected_page'] == "home")
		{
			echo " id=\"nav-selected\"";
		}
		echo "><a href=\"home\">Settings</a></li>
		     <li><a href=\"scripts/logout\">Logout</a></li>";
	}
	echo "	
			  </ul>
                     </div><!-- #header-wrap -->
                </div><!-- #header-fullwidth -->
		<br>
		<div id=\"content-wrap\">";

}

function print_foot()
{
  echo "
</div>
<div id=\"footer\">
	<a href=\"#header-fullwidth\">
	   <img id=\"footer-icon\" src=\"images/footer-logo.png\" alt=\"footer-logo\" width=\"50\" height=\"62\">
	</a>
	<br>
	version ".$GLOBALS['version']."
</div>

</body>

</html>";
}

?>
