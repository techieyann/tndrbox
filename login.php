<?php
/***********************************************
file: login.php
creator: Ian McEachern

This file presents a login dialogue and parses 
error messages
 ***********************************************/
require('includes/includes.php');

$GLOBALS['header_html_title'] = "tndrbox - Login";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "login";

require('includes/prints.php');

print_head();
print_body();
print_foot();

function print_body()
{
	echo "
	<div id='signin-form' class='content'>";
	if(isset($_GET['error']))
	  {
		if(strcmp($_GET['error'],"email")==0)
		  {
			echo "
		<h5 class='text-error'>That email format was not recognized</h5>";
		  }
		if(strcmp($_GET['error'],"match")==0)
		  {
			echo "
		<h5 class='text-error'>Incorrect email/password combination</h5>";
		  }
	  }

	echo "
		<form name='signin-form' action='scripts/validate_login.php' method='post' class='form'>
  	  		<input required type='text' name='email' id='email' maxlength=50 placeholder='Email Address...'>
	   		<input required type='password' name='pass' id='pass' maxlength=16 placeholder='Password...'>
			<br>
			<button class='btn btn-medium float-right' type='submit'>Sign in</button>
		</form>
	</div>
	</div>";
}

?>