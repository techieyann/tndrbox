<?php
/***********************************************
file: login.php
creator: Ian McEachern

This file presents a login dialogue and parses 
error messages
 ***********************************************/
require('includes/includes.php');

$GLOBALS['header_html_title'] = "tndrbox - Login";
$GLOBALS['header_selected_page'] = "login";

require('includes/prints.php');

print_head();
print_body();
print_foot();

function print_body()
{
	echo "
	<div class=\"content-pane\" id=\"notice\">";
	
	if(isset($_GET['error']))
	  {
		echo "
		<h3 class=\"red-text\">Please try again:</h3>";
		if(strcmp($_GET['error'],"email")==0)
		  {
			echo "
		<p class=\"red-text\">That email format was not recognized</p>";
		  }
		if(strcmp($_GET['error'],"match")==0)
		  {
			echo "
		<p class=\"red-text\">Incorrect email/password combination</p>";
		  }
	  }

	echo "
		<form name=\"\" action=\"scripts/validate_login.php\" method=\"post\">
			<table>
				<tr>
					<td>Email</td>
					<td><input required type=\"text\" name=\"email\" id=\"email\" maxlength=\"50\"></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input required type=\"password\" name=\"pass\" id=\"pass\" maxlength=\"16\"></td>
				</tr>
				<tr>
					<td></td>
					<td><input type=\"submit\" value=\"Login\"></td>
				</tr>
			</table>
		</form>
	</div>";
}

?>