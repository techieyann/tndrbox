<?php
/***********************************************
file: signup.php
creator: Ian McEachern

This file presents a signup dialogue with 
recaptcha and parses error messages
 ***********************************************/
require('includes/includes.php');

analyze_user();

require('includes/prints.php');


print_head();
print_body();
print_foot();

function print_body()
{
  	echo "
	<div id=\"signup\" class=\"content-pane\">";
	
	if(isset($_GET['error']))
	  {
		echo "
		<h3 class=\"red-text\">Please try again:</h3>";
		if(strcmp($_GET['error'],"captcha")==0)
		  {
			echo "
		<p class=\"red-text\">Please try the captcha again, robot.Xo</p>";
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
		elseif(strcmp($_GET['error'],"password")==0)
		  {
			echo "
		<p class=\"red-text\">Please use a password with TBD</p>";
		  }
	  }

	echo "
		<form name=\"\" action=\"scripts/new_user.php\" method=\"post\">
			<table>
				<tr>
					<td>Email</td>
					<td><input required type=\"text\" name=\"email\" id=\"email\" maxlength=\"50\"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input required type=\"password\" name=\"pass\" id=\"pass\" maxlength=\"16\"></td>
				</tr>
				<tr>";

  require_once('includes/recaptchalib.php');
  $publickey = "6LchVNESAAAAAMenf3lTWgj00YzeyK-hRKS_bozg";
  echo recaptcha_get_html($publickey);

	echo "
				</tr>
				<tr>
					<td></td>
					<td><input type=\"submit\" value=\"Login\"></td>
				</tr>
			</table>
		</form>";

}
?>