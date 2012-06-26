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
<!--DOCTYPE HTML-->

<html>

<head>
<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"all\">
</head>

<body>
<div id=\"header-fullwidth\">
		     <div id=\"header-wrap\">
                     	  <a href=\"/\" id=\"tndrbox-logo\"></a>
		     
			  <ul id =\"main-nav\"> 
			  </ul>
                     </div><!-- #header-wrap -->
                </div><!-- #header-fullwidth -->
		<div id=\"content-wrap\">";

}

function print_foot()
{
  global $version;
  echo "

<footer>version $version</footer>

</body>

</html>";
}

?>
