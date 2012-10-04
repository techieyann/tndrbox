<?php
/***********************************************
file: edit-business.php
creator: Ian McEachern

This file displays a dialogue for editing a 
business.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

//set variables
//body
$id = $GLOBALS['b_id'];
//$nickname = $GLOBALS['nickname'];
//$username = $GLOBALS['username'];
extract($_GET);

//head
$GLOBALS['header_html_title'] = "tndrbox - Edit Business";
//$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "Edit Business";
//$GLOBALS['header_body_includes'] = "";
 
require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  global $id, $nickname, $username, $name, $address, $city, $state, $zip, $number, $url, $hours, $tag_1, $tag_2;
	echo "
		<table class=\"main-content-pane\" id=\"notice\">
			<form name=\"$id\" method=\"post\" action=\"scripts/edit_business.php?id=$id\">
			<tr>
				<td>Name</td>
				<td>:</td>
				<td><input type=\"text\" name=\"name\" id=\"name\" value=\"$name\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>Tag 1</td>
				<td>:</td>
				<td><input type=\"text\" name=\"tag_1\" id=\"tag_1\" value=\"$tag_1\" maxlength=\"100\"></td>
			</tr>
			<tr>
				<td>Tag 2</td>
				<td>:</td>
				<td><input type=\"text\" name=\"tag_2\" id=\"tag_2\" value=\"$tag_2\" maxlength=\"100\"></td>
			</tr>
			<tr>
				<td>Address</td>
				<td>:</td>
				<td><input type=\"text\" name=\"address\" id=\"address\" value=\"$address\" maxlength=\"255\"></td>
			</tr>
		      	<tr>
				<td>City</td>
				<td>:</td>
				<td><input type=\"text\" name=\"city\" id=\"city\" value=\"$city\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>State</td>
				<td>:</td>
				<td><input type=\"text\" name=\"state\" id=\"state\" value=\"$state\" maxlength=\"2\"></td>
			</tr>
			<tr>
				<td>Zip</td>
				<td>:</td>
				<td><input type=\"text\" name=\"zip\" id=\"zip\" value=\"$zip\" maxlength=\"5\"></td>
			</tr>
			<tr>
				<td>Number</td>
				<td>:</td>
				<td><input type=\"text\" name=\"number\" id=\"number\" value=\"$number\" maxlength=\"12\"></td>
			</tr>
		      	<tr>
				<td>URL</td>
				<td>:</td>
				<td><input type=\"text\" name=\"url\" id=\"url\" value=\"$url\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>Hours</td>
				<td>:</td>
				<td><input type=\"text\" name=\"hours\" id=\"hours\" value=\"$hours\" maxlength=\"100\"></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
		      		<td style=\"border-bottom: solid 1px black;\" align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Update\"></td>
			</tr>
			</form>
			<form name=\"logo_$id\" method=\"post\" enctype=\"multipart/form-data\" action=\"scripts/logo_upload.php?id=$id\">
			<tr>
				<td>Logo</td>
				<td>:</td>
				<td>
				<input type=\"file\" name=\"logo_upload\" id=\"logo_upload\" size=\"10\">
				</td>
			</tr>
			<tr>
				<td colspan=\"3\">
				Note: filesize must be <60Kb
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
		      		<td style=\"border-bottom: solid 1px black;\" align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Upload\"></td>
			</tr>
			</form>
		</table>";

}
?>