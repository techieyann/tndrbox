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
  global $id;
  $query = "SELECT name FROM business WHERE id='$id'";
  $result = query_db($query);
  $res = mysql_fetch_array($result);
  $name = $res['name'];

  echo "
	<div id=\"\" class =\"content-pane\">
		<p>Please enter your business' information.</p>
		<form name=\"\" action=\"scripts/edit_business.php?id=$id\" method=\"post\">
			<table>
				<tr>
					<td>Business Name</td>
					<td><input required type=\"text\" name=\"name\" id=\"name\" value=\"$name\" maxlength=\"100\"></td>
				</tr>

	   			<tr>
					<td>Tag 1</td>
					<td><input required type=\"text\" name=\"tag_1\" id=\"tag_1\" maxlength=\"100\"></td>
				</tr>
				<tr>
					<td>Tag 2</td>
					<td><input required type=\"text\" name=\"tag_2\" id=\"tag_2\" maxlength=\"100\"></td>
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
				<td>Number</td>
				<td>:</td>
				<td><input type=\"text\" name=\"number\" id=\"number\"  maxlength=\"12\"></td>
			</tr>
		      	<tr>
				<td>URL</td>
				<td>:</td>
				<td><input type=\"text\" name=\"url\" id=\"url\"  maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>Hours</td>
				<td>:</td>
				<td><input type=\"text\" name=\"hours\" id=\"hours\"  maxlength=\"100\"></td>
			</tr>
				<tr>
					<td></td>
					<td style=\"text-align:right\"><input type=\"submit\" value=\"Submit\"></td>
				</tr>
			</table>
		</form>
	</div>";
}
?>