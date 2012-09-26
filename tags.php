<?php
/***********************************************
file: tags.php
creator: Ian McEachern

This file displays either the most popular tags,
or the postings of a specific tag.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body
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
  global $get_tag_set, $tag_id;
	if($get_tag_set)
	{
			$db_query = "SELECT name, id, logo  FROM business WHERE tag_1='$tag_id' OR tag_2='$tag_id' ORDER BY name";
		$result = query_db($db_query);
		echo "
		 <br><div id=\"\" class =\"content-pane\">
		     <table width=\"100%\">";
		
		$count=0;
		
		while($business = mysql_fetch_array($result))
		{
				if($count == 0)
				{
					echo "
			<tr>";
				}
				extract($business);
				echo "
				<td align=\"center\">
				<br>
		     		<a href=\"business?b_id=".$id."\" title=\"".$name."\">";
				if($logo == "")
				{
					echo "
	     	     		<div class=\"bus_button\">
				 <span>".$name."</span>
		     		</div>";
				}
		     	  	else
				{
					echo "
				<img src=\"images/logos/".$logo."\" alt=\"".$name."\" border=\"0\" width=\"200\">";
				}
				echo "</a><br>
				</td>";

				if(++$count == 4)
				{
					$count = 0;
					echo "
			</tr>";
				} 	
		}
		if($count != 0)
		{
			echo "
			</tr>";
		}
	  
		echo "
		     </table>";

	  $query = "SELECT * FROM postings WHERE tag_1='$tag_id' OR tag_2='$tag_id' OR tag_3='$tag_id' LIMIT 20";
	  $result = query_db($query);
	  $i=0;
	  while($post = mysql_fetch_array($result))
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
			<br><div id=\"posting_border_".$i++."\" class=\"content-pane\">
				<div class=\"posting-$i-title\">$title from <a href=\"business?b_id=$b_id\">$name</a></div>";
		//				<div class=\"posting-$i-time\">$posting_time</div>";
		if(isset($GLOBALS['m_id']))
		{
			if($a_id == $GLOBALS['m_id'])
			{
			echo "		
		<div class=\"posting-$i-edit\">
					<a href=\"edit-posting.php?p_id=$id&title=$title&blurb=$blurb&photo=$photo&tag_1=$tag_1&tag_2=$tag_2&tag_3=$tag_3&posting_time=$posting_time\">Edit</a>
				</div>";
			}
		}
		echo "
				<div class=\"posting-$i-data\" >
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
			</div>
		</div>";		
		}
	}
	else
	{
		$query = "SELECT * FROM tags WHERE num_ref>0 ORDER BY num_ref LIMIT 50";
		$result = query_db($query);
		echo "
		<table class=\"content-pane\" id=\"tag-list\" cellspacing=\"0\">
			<tr>
			<th>Tags</th>
			</tr>";
		while($present_tag = mysql_fetch_array($result))
		{
			extract($present_tag);
			echo "
			<tr>
			<td><a href=\"tags?tag=$id\">
			$tag
			</a></td>
			</tr>";
		}
		echo "</table>";	
	}
}
?>