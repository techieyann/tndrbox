<?php

require('includes.php');

print_head();
print_body();
print_foot();

function print_body()
{
  echo "
	<div id=\"new-post\" class =\"content-pane\">
		<form name=\"new-post-form\" action=\"/scripts/new-post-script.php\" method=\"post\">
			<table>
				<tr>
					<td>Title</td>
					<td>:</td>
					<td><input type=\"text\" name=\"title\" id=\"title\"></td>

					<td>Tagline</td>
					<td>:</td>
					<td><input type=\"text\" name=\"tagline\" id=\"tagline\"></td>
				</tr>
				<tr>
					<td rowspan=2>Date</td>
					<td rowspan=2>:</td>
					<td rowspan=2><input type=\"date\" name=\"date\" id=\"date\"></td>
			 		<td>Clock-in</td>
		   			<td>:</td>
	   				<td><input type=\"time\" name=\"time-start\" id=\"time-start\"></td>
   				</tr>
		  		<tr>
					<td>Clock-out</td>
					<td>:</td>
					<td><input type=\"time\" name=\"time-fin\" id=\"time-fin\"></td>
				</tr>
				<tr>
					<td>Description</td>
					<td>:</td>
					<td colspan=4><textarea name=\"tagline\" cols=70 rows=4>Default Values...</textarea></td>
				</tr>
				<tr>
					<td>Tags</td>
					<td>:</td>
					<td><input type=\"text\" name=\"tags\" id=\"tags\"></td>
				</tr>
				<tr>
					<td>Publish Date</td>
					<td>:</td>
					<td><input type=\"text\" name=\"publish-date\" id=\"publish-date\"></td>
					<td>Publish Time</td>
					<td>:</td>
					<td><input type=\"time\" name=\"publish-time\" id=\"publish-time\"></td>
				</tr>
				<tr>
					<td colspan=6><input type=\"submit\" value=\"Submit\"></td>
				</tr>
			</table>
		</form>
	</div>";
}
?>