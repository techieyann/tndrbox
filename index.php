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
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body
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

		$query = "SELECT name FROM business WHERE id='$b_id'";
		$b_result = query_db($query);
		$b_name = mysql_fetch_array($b_result);
		$name = $b_name['name'];

		$tags[1] = get_tag($tag_1); 
		$tags[2] = get_tag($tag_2); 
		$tags[3] = get_tag($tag_3);

		echo "
			<div id=\"posting_border_".$i++."\">
				<div class=\"posting-$i-title\">$title from <a href=\"business?b_id=$b_id\">$name</a></div>";
		//				<div class=\"posting-$i-time\">$posting_time</div>";
	if(isset($GLOBALS['m_id']))
	{
		if($a_id == $GLOBALS['m_id'])
		{
			echo "		
		<div class=\"posting-$i-edit\">
					<a href=\"edit-posting.php?p_id=$id&title=$title&blurb=$blurb&photo=$photo&tag_1=$tag_1&tag_2=$tag_2&tag_3=$tag_3&posting_time=$posting_time\">Edit</a>
				</div>
		<div class=\"posting-$i-delete\">
					<a href=\"scripts/delete_post.php?p_id=$id\">Delete</a>
				</div>";
		}
	}
		echo "
				<div class=\"posting-$i-data\">
					<img src=\"images/posts/$photo\" alt=\"photo for $title\" class=\"posting-image\">
					<div class=\"posting-$i-blurb\">
						$blurb
					</div>
					<ul>
						<li><a href=\"tags?tag=$tag_1\">$tags[1]</a></li>
						<li><a href=\"tags?tag=$tag_2\">$tags[2]</a></li>
						<li><a href=\"tags?tag=$tag_3\">$tags[3]</a></li>
					</ul>
				</div>
			</div>";		
	}

  echo "
	</div>";
}
?>