<?php

/***********************************************
file: new-post.php
creator: Ian McEachern

This file displays a form for creating a new
posting
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  echo "
	<div id=\"new-post\" class =\"content-pane\">
		<form name=\"new-post-form\" action=\"scripts/new_post.php\" method=\"post\">
			<table>
				<tr>
					<td>Title</td>
					<td>:</td>
					<td><input type=\"text\" name=\"title\" id=\"title\"></td>
				</tr>
				<tr>
					<td>Description</td>
					<td>:</td>
					<td colspan=4><textarea name=\"description\" cols=70 rows=4>Default Values...</textarea></td>
				</tr>
				<tr>
					<td>Tags</td>
					<td>:</td>
					<td><input type=\"text\" name=\"tag1\" id=\"tag1\"></td>
					<td><input type=\"text\" name=\"tag2\" id=\"tag2\"></td>
					<td><input type=\"text\" name=\"tag3\" id=\"tag3\"></td>
				</tr>
				<tr>
					<td>Publish Date</td>
					<td>:</td>
					<td><input type=\"text\" name=\"publish_date\" id=\"publish_date\"></td>
					<td>Publish Time :</td>
					<td><input type=\"time\" name=\"publish_time\" id=\"publish_time\"></td>
				</tr>
				<tr>
					<td colspan=6><input type=\"submit\" value=\"Submit\"></td>
				</tr>
			</table>
		</form>
	</div>";
}
?>