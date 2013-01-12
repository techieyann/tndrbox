<?php
/***********************************************
file: signup.php
creator: Ian McEachern

This file presents a signup dialogue with 
recaptcha and parses error messages
 ***********************************************/
require('includes/includes.php');

require('includes/prints.php');

//head
$GLOBALS['header_html_title'] = "tndrbox - Signup";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "";


print_head();
print_body();
print_foot();

function print_body()
{
  	echo "
	<div id='new-user' class='column'>";
	
	if(isset($_GET['error']))
	  {
		echo "
		<h3 class=\"red-text\">Please try again:</h3>";
		if(strcmp($_GET['error'],"captcha")==0)
		  {
			echo "
		<p class=\"red-text\">The captcha tricked you again, robot.</p>";
		  }
		elseif(strcmp($_GET['error'],"email")==0)
		  {
			echo "
		<p class=\"red-text\">That email format was not recognized</p>";
		  }
		elseif(strcmp($_GET['error'],"dup")==0)
		  {
			echo "
		<p class=\"red-text\">That email is already in use</p>";
		  }
		elseif(strcmp($_GET['error'],"bus_dup")==0)
		  {
			echo "
		<p class=\"red-text\">That business name is already taken</p>";
		  }
		elseif(strcmp($_GET['error'],"password")==0)
		  {
			echo "
		<p class=\"red-text\">Passwords do not match</p>";
		  }
		elseif(strcmp($_GET['error'],"db")==0)
		  {
			echo "
		<p class=\"red-text\">Sorry, there was a database error</p>";
		  }
	  }
	print_new_user_form();

}
?>