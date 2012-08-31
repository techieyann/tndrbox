<?php
/***********************************************
file: edit-posting.php
creator: Ian McEachern

This file displays a dialogue for editing a post.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

//set variables
//body
extract($_GET);

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
	global $title, $blurb, $tag_1, $tag_2, $tag_3, $publish_date, $publish_time, $error;

  echo "
	<div id=\"edit-post\" class =\"content-pane\">";
if($error == 1)
{
	echo "<p>Please make sure your data is formatted correctly</p>";
}	
echo "
		<form name=\"edit-post-form\" action=\"scripts/edit_post.php\" method=\"post\">
			<table>
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
					<td><input type=\"text\" name=\"tag3\" id=\"tag3\ value=\"$tag3\"></td>
				</tr>
				<tr>
					<td>Publish Date</td>
					<td>:</td>
					<td><input type=\"text\" name=\"publish_date\" id=\"publish_date\" value=\"$publish_date\"></td>
					<td>Publish Time</td>
					<td>:</td>
					<td><input type=\"time\" name=\"publish_time\" id=\"publish_time\" value=\"$publish_time\"></td>
				</tr>
				<tr>
					<td colspan=6><input type=\"submit\" value=\"Submit\"></td>
				</tr>
			</table>
		</form>
	</div>";
}
?>