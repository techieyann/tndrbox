<?php
/***********************************************
file: index.php
creator: Ian McEachern

This is the default page. It displays the most
relevant data based on the function scrape_tags()
in includes.php.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body
$id = $GLOBALS['id'];
$result = scrape_tags();
while($post = mysql_fetch_array($result))
{
	$postings[$post['id']] = $post;
}

//head
$GLOBALS['header_html_title'] = "tndrbox - ";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "landing";
require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  global $postings;
  echo "
	<div id=\"\" class =\"content-pane\">";
	$i = 0;
	foreach($postings as $post)
	{
		extract($post);
		$query = "SELECT tag FROM tags WHERE id='$tag_1' AND id='$tag_2' AND id='$tag_3'";
		$tags_result = query_db($query);
		$j = 0;
		while($tag = mysql_fetch_array($tags_result))
		  {
			if(++$j == 1)
			  {
				$tags[$tag_1] = $tag['tag']; 
			  }
			else if($j == 2)
			  {
				$tags[$tag_2] = $tag['tag'];
			  }
			else if($j == 3)
			  {
				$tags[$tag_3] = $tag['tag'];
			  }
		  }
		echo "
			<div id=\"posting_border_".$i++."\">
				<div class=\"posting-$i-title\">$title</div>
				<div class=\"posting-$i-time\">$posting_time</div>
				<div class=\"posting-$i-edit\">
					<a href=\"/edit-posting.php?p_id=$id&title=$title&blurb=$blurb&photo=$photo&tag_1=$tag_1&tag_2=$tag_2&tag_3=$tag_3&posting_time=$posting_time\">Edit</a>
				</div>
				<div class=\"posting-$i-data\">
					<img src=\"$photo\" alt=\"photo for $title\" class=\"posting-image\">
					<div class=\"posting-$i-blurb\">
						$blurb
					</div>
					<ul>
						<li><a href=\"tags?id=$tag_1\">$tags[$tag_1]</a></li>
						<li><a href=\"tags?id=$tag_2\">$tags[$tag_2]</a></li>
						<li><a href=\"tags?id=$tag_3\">$tags[$tag_3]</a></li>
					</ul>
				</div>
			</div>";		
	}

  echo "
	</div>";
}
?>