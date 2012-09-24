<?php
/***********************************************
file: edit-user.php
creator: Ian McEachern

This file displays a dialogue for editing a 
user.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

//set variables
//body
$id = $GLOBALS['b_id'];
$nickname = $GLOBALS['nickname'];
$username = $GLOBALS['username'];
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
  global $id, $nickname, $username;
	echo "
		<table class=\"content-pane\" id=\"notice\">
			<form name=\"\" method=\"post\" action=\"scripts/edit_user.php?id=$id\">
			<tr>
				<td>e-mail</td>
				<td>:</td>
				<td><input type=\"text\" name=\"email\" id=\"email\" value=\"$email\" maxlength=\"60\"></td>
			</tr>
			<tr>
				<td>Nickname</td>
				<td>:</td>
				<td><input type=\"text\" name=\"nickname\" id=\"username\" value=\"$nickname\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>Password</td>
				<td>:</td>
				<td><input type=\"password\" name=\"pass1\" id=\"pass1\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>Confirm</td>
				<td>:</td>
				<td><input type=\"password\" name=\"pass2\" id=\"pass2\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
		      		<td align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Update\"></td>
			</tr>
			</form>
		</table>";

}
?>