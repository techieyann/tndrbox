<?php

require('includes.php');

analyze_user();

require('prints.php');

print_head();
print_body();
print_foot();

function print_body()
{
	echo "
	<div id=\"login\" class=\"note\">";
	
	if(isset($_GET['error']))
	  {
		echo "
		<h3 class=\"red-text\">Please try again:</h3>";
	  }

	echo "
		<form name=\"\" action=\"scripts/validate_login.php\" method=\"post\">
			<table>
				<tr>
					<td>Email</td>
					<td><input required type=\"text\" name=\"email\" id=\"email\" maxlength=\"50\" 
					pattern=\"\\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\\.[A-Z]{2,4}\\b\"></td>
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