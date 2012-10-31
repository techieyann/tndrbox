<?php

/***********************************************
file: new-post.php
creator: Ian McEachern

This file displays a form for creating a new
posting
 ***********************************************/
require('includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();

//head
$GLOBALS['header_html_title'] = "tndrbox - ";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "business";


require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  echo "
	<div id='new-post' class ='main-content-pane' style='width:65%; margin:0px auto; padding:10px;'>
		<form name='new-post-form'  enctype='multipart/form-data' action='scripts/new_post.php' method='post'>
			<table>
				<tr>
					<td>Title</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=50 name=\"title\" id=\"title\"></td>
				</tr>
				<tr>
					<td>Description</td>
					<td>:</td>
					<td colspan=4><textarea name=\"description\" cols=60 rows=4 maxlength=255></textarea></td>
				</tr>
				<tr>
					<td>Tags</td>
					<td>:</td>
					<td><input type=\"text\" size=8 name=\"tag1\" id=\"tag1\"></td>
					<td><input type=\"text\" size=8 name=\"tag2\" id=\"tag2\"></td>
					<td><input type=\"text\" size=8 name=\"tag3\" id=\"tag3\"></td>
				</tr>";
  /*				<tr>
					<td>Publish Date</td>
					<td>:</td>
					<td><input type=\"text\" name=\"publish_date\" id=\"publish_date\"></td>
					<td>Publish Time :</td>
					<td><input type=\"time\" name=\"publish_time\" id=\"publish_time\"></td>
				</tr>
  */
				echo "
				<tr>
				<td>Image</td>
				<td>:</td>
				<td colspan=4>
				<input type=\"file\" name=\"image_upload\" id=\"image_upload\">
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan=5 style=\"border-bottom: solid 1px black;\">
				Note: filesize must be less than 240Kb
				</td>
			</tr>
			<tr>
				<td><a href='home' style='text-decoration:none'><input type='button' name='cancel' value='Cancel'></a></td>
				<td></td>
		      		<td colspan=4 align=\"right\"><input type=\"submit\" name=\"submit\" value=\"Submit\"></td>
			</tr>
			</table>
			</form>
	</div>";
}
?>