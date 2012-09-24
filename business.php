<?php
/***********************************************
file: business.php
creator: Ian McEachern

This file outputs the active posting for a given
business and the relevant business data. Or if
no business is selected shows a list of 
businesses.
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body
$b_flag = false;
if(isset($_GET['b_id']))
{
	$b_id = sanitize($_GET['b_id']);
	$b_flag = true;
}

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
  global $b_flag;

 
	if($b_flag == true)
	{

	$b_id = $GLOBALS['b_id'];
  	//print active business posting
	$query = "SELECT * FROM postings WHERE b_id=$b_id";
	$result = query_db($query);
	if(mysql_num_rows($result) != 0)
	{
		$posting = mysql_fetch_array($result);
		extract($posting);
		$query = "SELECT tag FROM tags WHERE id='$tag_1' OR id='$tag_2' OR id='$tag_3'";
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
			<br><div id=\"posting_border_1\" class=\"content-pane\">
				<div class=\"posting-1-title\">$title</div>
				<div class=\"posting-1-data\">
					<img src=\"images/posts/$photo\" alt=\"photo for $title\" class=\"posting-image\">
					<div class=\"posting-1-blurb\">
						$blurb
					</div>
					<ul>
						<li><a href=\"tags?tag=$tag_1\">$tags[$tag_1]</a></li>
						<li><a href=\"tags?tag=$tag_2\">$tags[$tag_2]</a></li>
						<li><a href=\"tags?tag=$tag_3\">$tags[$tag_3]</a></li>
					</ul>
				</div>
			</div>";
	}
 echo "
	<div id=\"\" class =\"content-pane\">";
	
	//print business info
 	$query = "SELECT * FROM business where id='$b_id'";
 	$result = query_db($query);
  	if(mysql_num_rows($result)==1)
  	{
	  $business = mysql_fetch_array($result);
		extract($business);
		
		$tag1 = get_tag($tag_1);
		$tag2 = get_tag($tag_2);

		echo "
		<div id=\"bar_info\">
			<div id=\"shareNice\" data-services=\"facebook.com,digg.com,email,delicious.com,reddit.com,twitter.com,plus.google.com\"
data-color-scheme=\"black\" 
data-share-label=\"\"></div>
		      <table width=\"95%\"><tr>
		      	     <td><h2>";
		$ending_string = "";
		if($url != "")
		{
			echo "<a href=\"http://$url\">";
			$ending_string = "</a>";
		}
		if($logo != "")
	    {
	 		echo "<img src=\"images/logos/$logo\" width=\"300\" title=\"$name\" alt=\"$name\">";
	   	}
	   	else
	   	{
	   		echo $name;
	   	}
	   	echo $ending_string;
		echo "</h2>
			<br>
		     <a href=\"tags?tag=$tag_1\">$tag1</a>
		     <a href=\"tags?tag=$tag_2\">$tag2</a>
			<br><h3>$address<br>";
		echo "
			$city, $state, $zip<br><br>";
		echo "
			$number</h3><br>";
		$hours = explode(",", $hours);
		foreach($hours as $line)
		{
			echo "
			$line<br>";
		}
		echo "
			</td>
			<td style=\"text-align:right\">";
		if($lat == "" ||$lat==0 || $lon == "" || $lon==0)
		{
			echo "
			<img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$address $city $state $zip&zoom=16&size=300x400&markers=color:red|$address $city $state $zip&sensor=false\">";
		}
		else
		{
			echo "
			<img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$lat,$lon&zoom=16&size=300x400&markers=color:red|$lat,$lon&sensor=false\">";    
		}
		echo "
			</td>
			</tr></table>
			</div>
		</div>";
	  }
	}
	  if($b_flag == false)
	  {
	       	$db_query = "SELECT name, id, logo  FROM business ORDER BY name";
		$result = query_db($db_query);
		echo "
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
		     		<a href=\"?b_id=".$id."\" title=\"".$name."\">";
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
	} 	
	echo "	
	</div>";
}
?>