<?php
/***********************************************
file: edit-posting.php
creator: Ian McEachern

This file displays a dialogue for editing a post.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

//set variables
//body
extract($_GET);

$tag1 = get_tag($tag_1);
$tag2 = get_tag($tag_2);
$tag3 = get_tag($tag_3);

//head
$GLOBALS['header_html_title'] = "tndrbox - Edit Post";
//$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "Edit Post";
//$GLOBALS['header_body_includes'] = "";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
	global $title, $blurb, $tag1, $tag2, $tag3, $error;

  echo "
	<div id=\"edit-post\" class =\"content-pane\">";
if($error == 1)
{
	echo "<p>Please make sure your data is formatted correctly</p>";
}	
echo "
		<table>
		<form name=\"edit-post-form\" action=\"scripts/edit_post.php\" method=\"post\">
		  
				<tr>
					<td>Title</td>
					<td>:</td>
					<td><input type=\"text\" name=\"title\" id=\"title\" value=\"$title\"></td>
				</tr>
				<tr>
					<td>Description</td>
					<td>:</td>
					<td colspan=4><textarea name=\"description\" cols=70 rows=4>$blurb</textarea></td>
				</tr>
				<tr>
					<td>Tags</td>
					<td>:</td>
					<td><input type=\"text\" name=\"tag1\" id=\"tag1\" value=\"$tag1\"></td>
					<td><input type=\"text\" name=\"tag2\" id=\"tag2\" value=\"$tag2\"></td>
					<td><input type=\"text\" name=\"tag3\" id=\"tag3\" value=\"$tag3\"></td>
				</tr>";
/*
 				<tr>
					<td>Publish Date</td>
					<td>:</td>
					<td><input type=\"text\" name=\"publish_date\" id=\"publish_date\" value=\"$publish_date\"></td>
					<td>Publish Time</td>
					<td>:</td>
					<td><input type=\"time\" name=\"publish_time\" id=\"publish_time\" value=\"$publish_time\"></td>
				</tr>
*/
echo "
				<tr>
					<td colspan=6><input type=\"submit\" value=\"Submit\"></td>
				</tr>
		</form>
		<form name=\"image_$id\" method=\"post\" enctype=\"multipart/form-data\" action=\"scripts/image_upload.php?p_id=$id\">
			<tr>
				<td>Logo</td>
				<td>:</td>
				<td>
				<input type=\"file\" name=\"image_upload\" id=\"image_upload\">
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
</table>
	</div>";
}
?>