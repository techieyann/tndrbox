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
	$db_query = "SELECT name, id, logo  FROM business ORDER BY name";
	$result = query_db($db_query);
	$i=0;
	while($business = mysql_fetch_array($result))
	  {
  		$query = "SELECT id FROM postings WHERE b_id=".$business['id'];
		$post_result = query_db($query);
		if(mysql_num_rows($post_result) != 0)
		{
			$businesses_result[$i++] = $business;
		}
	  }
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
  global $b_flag, $b_id, $active_post_flag, $posting, $business_flag, $business, $businesses_result;
	
	if($b_flag == true)
	{
  	//print active business posting
		if($active_post_flag == true)
		{
			echo "
	<div class='row-fluid'>
		<div class='span9 top-left' id='post'>";
			if($posting['alt_address'] == "")
			  {
				$posting['alt_address'] = $business['address']." ".$business['city'].", ".$business['state'].", ".$business['zip'];
			  }
		print_formatted_post($posting);
				echo "
			<div class='fb-comments' data-href='http://tndrbox.com/?p=".$posting['id']."' data-num-posts='4' data-width='500px' data-colorscheme='light'></div>";
		echo "
		</div>
		<div class='span3 content'>
			Related posts...
		</div>
	</div>";
	}
 echo "
	<br>
	<div class='row-fluid'>
		<div class='span4 content'>
			Posting Archives
		</div>";
	
	//print business info
 if($business_flag = true)
   {
		extract($business);
		
		$tag1 = get_tag($tag_1);
		$tag2 = get_tag($tag_2);

		echo "
		<div id='business_info' class='span8 bottom-right'>
			
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
		echo "
		</div>";
	  }
	}
	  if($b_flag == false)
	  {
		$count=0;
		
		foreach($businesses_result as $business)
		{
			extract($business);	
			if($count == 0)
			{
				echo "
	<div class='row-fluid'>";
			}
				
				echo "
		<div class='span2'>
			<a href='?b_id=$id' title='$name'>";
			if($logo == "")
			{
				echo "
				<div class='bus_button'>
					<span>".$name."</span>
		     	</div>";
			}
		    else
			{
				echo "
				<img src='images/logos/$logo' alt='$name' border='0' max-width='100%'>";
			}
			echo "
			</a>
		</div>";
			if(++$count == 4)
			{
		  		$count = 0;
	   			echo "
	 </div>";
			} 	
		}
		if($count != 0)
		{
			echo "
	 </div>";
		}
	} 	
	echo "	
	</div>";
}
?>