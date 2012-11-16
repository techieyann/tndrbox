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
	$query = "SELECT * FROM postings WHERE b_id=$b_id";
	$result = query_db($query);
	$active_post_flag = false;
	if(mysql_num_rows($result)==1)
	{
		$active_post_flag = true;
		$posting = mysql_fetch_array($result);
	}
	$query = "SELECT * FROM business where id='$b_id'";
 	$result = query_db($query);
	$business_flag = false;
  	if(mysql_num_rows($result)==1)
  	{
		$business_flag = true;
		$business = mysql_fetch_array($result);
	}
}
else
{
	
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
  global $b_flag, $b_id, $active_post_flag, $posting, $business_flag, $business;
	
	if($b_flag == true)
	{
  	//print active business posting
		if($active_post_flag == true)
		{
			echo "
	<div class='row'>
		<div class='span8 content' id='post'>";
		print_formatted_post($posting);
		echo "
			<div class=\"fb-comments\" data-href=\"http://tndrbox.com/?p=".$posting['id']."\" data-num-posts=\"4\"  data-width=\"620\" data-colorscheme=\"light\"></div>";
		echo "
		</div>
		<div class='span3 offset1 content'>
			Related posts...
		</div>
	</div>";
	}
 echo "
	<div class='row'>
		<div class='span4 content'>
			Event Information
		</div>";
	
	//print business info
 if($business_flag = true)
   {
		extract($business);
		
		$tag1 = get_tag($tag_1);
		$tag2 = get_tag($tag_2);

		echo "
		<div id='business_info' class='span7 offset1 content'>
			
			<h2>";
		$ending_string = "";
		if($url != "")
		{
			echo "<a href=\"http://$url\">";
			$ending_string = "</a>";
		}
		if($logo != "")
	    {
	 		echo "<img src=\"images/logos/$logo\" width=\"275\" title=\"$name\" alt=\"$name\">";
	   	}
	   	else
	   	{
	   		echo $name;
	   	}
	   	echo $ending_string;
		echo "</h2>
			<br>
			$number<br>";
		$hours = explode(",", $hours);
		foreach($hours as $line)
		{
			echo "
			$line<br>";
		}
		echo "
			<br><h3><a id=\"business-address\" href=\"http://maps.google.com/?q=$address, $city, $state $zip\">
			$address<br>
			$city, $state, $zip
			</a></h3><br><br>";
		echo "<div id=\"static-map\">
				<a href=\"http://maps.google.com/?q=$address, $city, $state $zip\">";
		if($lat == "" ||$lat==0 || $lon == "" || $lon==0)
		{
			echo "
			<img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$address $city $state $zip&zoom=16&size=275x400&markers=color:red|$address $city $state $zip&sensor=false\">";
		}
		else
		{
			echo "
			<img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$lat,$lon&zoom=16&size=275x400&markers=color:red|$lat,$lon&sensor=false\">";    
		}
		
		echo "</a>
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
			extract($business);
			$query = "SELECT id FROM postings WHERE b_id=$id";
			$post_result = query_db($query);
			if(mysql_num_rows($post_result) == 0)
			{
				continue;
			}	
			if($count == 0)
				{
					echo "
			<tr>";
				}
				
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