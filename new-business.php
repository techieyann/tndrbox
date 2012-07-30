<?php
/***********************************************
file: new-business.php
creator: Ian McEachern

This file displays the form for inputting a new
user's business information.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

//set variables
//body
$id = $GLOBALS['b_id'];

//head
$GLOBALS['header_html_title'] = "tndrbox - ";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  echo "
	<div id=\"\" class =\"content-pane\">
		<p>Please enter your business' information.</p>
		<form name=\"\" action=\"scripts/new_business.php\" method=\"post\">
			<table>
				<tr>
					<td>Name</td>
					<td><input required type=\"text\" name=\"name\" id=\"name\" maxlength=\"50\"></td>
				</tr>
				<tr>
					<td>Address</td>
					<td><input required type=\"text\" name=\"address\" id=\"address\" maxlength=\"100\"></td>
				</tr>
				<tr>
					<td>City</td>
					<td><input required type=\"text\" name=\"city\" id=\"city\" maxlength=\"32\" value=\"Oakland\"></td>
				</tr>
				<tr>
					<td>State</td>
					<td><input required type=\"text\" name=\"state\" id=\"state\" maxlength=\"2\" value=\"Ca\"></td>
				</tr>
				<tr>
					<td>Zip</td>
					<td><input required type=\"text\" name=\"zip\" id=\"zip\" maxlength=\"5\"></td>
				</tr>
				<tr>
					<td>Tag 1</td>
					<td><input required type=\"text\" name=\"tag1\" id=\"tag1\" maxlength=\"50\"></td>
				</tr>
				<tr>
					<td>Tag 2</td>
					<td><input required type=\"text\" name=\"tag2\" id=\"tag2\" maxlength=\"50\"></td>
				</tr>
				<tr>
					<td></td>
					<td style=\"text-align:right\"><input type=\"submit\" value=\"Login\"></td>
				</tr>
			</table>
		</form>
	</div>";
}
?>