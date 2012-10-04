<?php
/***********************************************
file: home.php
creator: Ian McEachern

This file is the default page for logged in
users. It displays the user's business info and
the five most current postings. 
Redirects to index.php if user is not
logged in.
 ***********************************************/


require('includes/includes.php');
require('includes/db_interface.php');
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

$business_flag = 0;
$b_id = $GLOBALS['b_id'];
$query = "SELECT * FROM business where id=$b_id";
        $result = query_db($query);
        if(mysql_num_rows($result)==1)
        {
		  $business_flag = 1;
          $business = mysql_fetch_array($result);
          extract($business);
			$tag1 = get_tag($tag_1);
                $tag2 = get_tag($tag_2);
		}
$query = "SELECT * FROM postings WHERE b_id=$b_id";
$result = query_db($query);
$post_flag = 0;

if($posting = mysql_fetch_array($result))
  {
	$post_flag = 1;
  }

//head
$GLOBALS['header_html_title'] = "tndrbox - ".$posting['title'];
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "home";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  global $b_id, $business_flag, $id, $name, $logo, $lat, $lon, $photo, $url, $tag1, $tag2, $address, $city, $state, $zip, $number, $url, $hours, $posting, $post_flag;

  extract($posting);
        
		echo "
				
		<h3 class=\"content-pane\"  style=\"text-align:center; filter:alpha(opacity=50);opacity:.5;\">
		<a href=\"edit-posting.php?p_id=$id&title=$title&blurb=$blurb&photo=$photo&tag_1=$tag_1&tag_2=$tag_2&tag_3=$tag_3\">Edit</a>
		|
		<a href=\"scripts/delete_post.php?p_id=$id\">Delete</a>
		</h3>
			<h3 class=\"meta-pane\"  style=\"text-align:center; filter:alpha(opacity=50);opacity:.5;\"> <a href=\"edit-business.php?name=$name&tag_1=$tag1&tag_2=$tag2&address=$address&city=$city&state=$state&zip=$zip&number=$number&url=$url&hours=$hours\">Edit</a></h3>";
       
        if($post_flag == 1)
          {
                echo "
                        <div id=\"post\" class=\"content-pane\">						
    <a href=\"https://twitter.com/share\" class=\"twitter-share-button\" data-dnt=\"true\" data-count=\"none\" data-url=\"http://tndrbox.com/?p=$id\" data-lang=\"en\">Tweet</a>

    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"https://platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>
	<div class=\"fb-like\" data-href=\"http://tndrbox.com/?p=$id\" data-send=\"false\" data-show-faces=\"false\" data-layout=\"button-count\" data-action=\"recommend\"></div>	";
				print_formatted_post($posting);

                 echo "
                        </div>";
          }


 


        echo "
        <div id=\"\" class =\"meta-pane\">";
		if($business_flag == 1)
		  {
                echo "
                <div id=\"business_info\">
			
			<h2>";
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
			$number</div><br>";
		$hours = explode(",", $hours);
		foreach($hours as $line)
		{
			echo "
			$line<br>";
		}
		echo "
			<br><h3><a id=\"business-address\" href=\"http://maps.google.com/?q=$address $city $state $zip\">
			$address<br>
			$city, $state, $zip
			</a></h3><br><br>";
		echo "<div id=\"static-map\">";
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
			</div>
		</div>";
        }
echo "
			<h3 class=\"content-pane\" style=\"text-align:center; filter:alpha(opacity=50);opacity:.5;\">Add a <a href=\"new-post\">new posting</a></h3>";
}
?>