<?php
/***********************************************
file: tags.php
creator: Ian McEachern

This file displays either the most popular tags,
or the postings of a specific tag.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body
$id = $GLOBALS['b_id'];
$get_tag_set = false;
if(isset($_GET['tag']))
{
	$get_tag_set = true;
	$tag_id = sanitize($_GET['tag']);
}

//head
$GLOBALS['header_html_title'] = "tndrbox - ";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "tags";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  echo "
	<div id=\"\" class =\"content-pane\">";
	if($get_tag_set)
	{
	  $query = "SELECT * FROM postings WHERE tag1='$tag_id' OR tag2='$tag_id' OR tag3='$tag_id' LIMIT 20";
	  $result = query_db($query);
	  while($post = mysql_fetch_array($result))
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
	}
	else
	{
		$query = "SELECT * FROM tags ORDER BY tag LIMIT 50";
		$result = query_db($query);
		while($present_tag = mysql_fetch_array($result))
		{
			extract($present_tag);
			echo "
			<a href=\"tags?tag=$id\">
			$tag
			</a><br>";
		}	
	}	
  echo "
	</div>";
}
?>