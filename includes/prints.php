<?php

/***********************************************
file: prints.php
creator: Ian McEachern

This file contains the functions which print
often used sections of the site. For example, 
print_head() prints the site from the start of 
the output file to the start of the body tag.

These functions utilize values set by 
analyze_user() in includes.php. They will only
work properly if called after analyze_user().

This file also needs to be included after the
call to analyze_user() in order for the code to
access the metadata variables.
 ***********************************************/

function print_head()
{
	echo "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\"
\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">

<html>

<head>
<link rel=\"stylesheet\" type=\"text/css\" href=\"styles.css\" media=\"all\">
<title>".$GLOBALS['header_html_title']."</title>
</head>

<body>


<div id=\"fb-root\"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = \"//connect.facebook.net/en_US/all.js#xfbml=1\";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<div id=\"header-fullwidth\">
		     <div id=\"header-wrap\">
                     	  <a href=\"index\" id=\"tndrbox-logo\"></a>
		     
			  <ul id =\"main-nav\"> 
			  <li";
	if($GLOBALS['header_selected_page'] == "business")
	{
		echo " id=\"nav-selected\"";
	}
	echo "><a href=\"business\">Businesses</a></li>
			  <li";
	if($GLOBALS['header_selected_page'] == "about")
	{
		echo " id=\"nav-selected\"";
	}
	echo "><a href=\"about\">About</a></li>
			  <li";
	if($GLOBALS['logged_in'] == false)
	{
		if($GLOBALS['header_selected_page'] == "login")
		{
			echo " id=\"nav-selected\"";
		}
		echo "><a href=\"login\">Login</a></li>";
	}
	else
	{
		if($GLOBALS['header_selected_page'] == "home")
		{
			echo " id=\"nav-selected\"";
		}
		echo "><a href=\"home\">Settings</a></li>
		     <li><a href=\"scripts/logout\">Logout</a></li>";
	}
	echo "	
			  </ul>
                     </div><!-- #header-wrap -->
                </div><!-- #header-fullwidth -->
		<br>
		<div id=\"content-wrap\">";

}

function print_foot()
{
  echo "
</div>
<div id=\"footer\">
	<a href=\"#header-fullwidth\">
	   <img id=\"footer-icon\" src=\"images/footer-logo.png\" alt=\"footer-logo\" width=\"50\" height=\"62\">
	</a>
	<br>
	version ".$GLOBALS['version']."
</div>

<script type='text/javascript' src='http://w.sharethis.com/button/buttons.js'></script>
<script type='text/javascript'>
	stLight.options({
		publisher:'b1a20a12-bf34-4af5-b4f2-5f09117df5e5',
	});
</script>

</body>

</html>";
}

function print_formatted_time($time)
{
//YYYY-MM-DD hh:mm:ss -> hh:mm(am/pm) MM/DD/YY

//Hour formatting
$hours = substr($time,11,2);
$pmam = "am ";
if($hours > 12)
{
	$hours -= 12;
	$pmam = "pm ";
}

//Minute extraction
$minutes = substr($time,14,2);

//Month extraction
$month = substr($time,5,2);

//Day extraction
$day = substr($time,8,2);

//Year extraction
$year = substr($time,2,2);

//Output
echo $hours.":".$minutes.$pmam.$month."/".$day."/".$year;


}

function print_formatted_post($post, $div_id="")
{
	extract($post);
	$query = "SELECT name, tag_1, tag_2 FROM business WHERE id='$b_id'";
   	$result = query_db($query);
   	$business_result = mysql_fetch_array($result);
   	$name = $business_result['name'];
	$tag_4 = $business_result['tag_1'];
	$tag_5 = $business_result['tag_2'];

   	$tags[1] = get_tag($tag_1); 
   	$tags[2] = get_tag($tag_2); 
   	$tags[3] = get_tag($tag_3);
	$tags[4] = get_tag($tag_4);
	$tags[5] = get_tag($tag_5);

   	echo "
			<div id=\"posting-border$div_id\" class=\"posting-border\">
				<div id=\"posting-title$div_id\" class=\"posting-title\">
					$title
				</div>
				<div class=\"posting-time$div_id\">";
		print_formatted_time($posting_time);
		echo "</div>
				<div id=\"posting-data$div_id\" class=\"posting-data\">
					<img src=\"images/posts/$photo\" alt=\"photo for $title\" class=\"posting-image\">
					
					<div id=\"posting-blurb$div_id\" class=\"posting-blurb\">
						$blurb
					
					</div>
				</div>
					<div id=\"share-buttons\">
			<a href=\"https://twitter.com/share\" class=\"twitter-share-button\" data-dnt=\"true\" data-count=\"none\" data-url=\"http://tndrbox.com/?p=$id\" data-lang=\"en\">Tweet</a>

    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=\"https://platform.twitter.com/widgets.js\";fjs.parentNode.insertBefore(js,fjs);}}(document,\"script\",\"twitter-wjs\");</script>
	<div class=\"fb-like\" data-href=\"http://tndrbox.com/?p=$id\" data-send=\"false\" data-show-faces=\"false\" data-layout=\"button\" data-action=\"recommend\"></div>	
			</div>
				<div class=\"posting-tags\">
				<ul>
						<li><a href=\"index?tag=$tag_1\">$tags[1]</a></li>
						<li><a href=\"index?tag=$tag_2\">$tags[2]</a></li>
						<li><a href=\"index?tag=$tag_3\">$tags[3]</a></li>
						<li><a href=\"index?tag=$tag_4\">$tags[4]</a></li>
						<li><a href=\"index?tag=$tag_5\">$tags[5]</a></li>
					</ul>
				</div>
			</div>";
}

function print_mini_post($post, $div_id="")
{
	extract($post);
	$query = "SELECT name, tag_1, tag_2 FROM business WHERE id='$b_id'";
   	$result = query_db($query);
   	$business_result = mysql_fetch_array($result);
   	$name = $business_result['name'];
	$tag_4 = $business_result['tag_1'];
	$tag_5 = $business_result['tag_2'];

   	$tags[1] = get_tag($tag_1); 
   	$tags[2] = get_tag($tag_2); 
   	$tags[3] = get_tag($tag_3);
	$tags[4] = get_tag($tag_4);
	$tags[5] = get_tag($tag_5);

   	echo "
			
			<div id=\"posting-border$div_id\" class=\"posting-border\">
				<div id=\"posting-content$div_id\" class=\"posting-content\">
				<div id=\"posting-title$div_id\" class=\"posting-title\">
					<a href=\"business?b_id=$b_id\">$title from $name</a>
				</div>";
	
		echo substr($blurb, 0, 100);
		echo "...
				</div>
				<div id=\"posting-meta$div_id\" class=\"posting-meta\">
				<div id=\"posting-time$div_id\" class=\"posting-time\">";
		print_formatted_time($posting_time);
		echo "</div>
					<ul>
						<li><a href=\"index?tag=$tag_1\">$tags[1]</a></li>
						<li><a href=\"index?tag=$tag_2\">$tags[2]</a></li>
						<li><a href=\"index?tag=$tag_3\">$tags[3]</a></li>
						<li><a href=\"index?tag=$tag_4\">$tags[4]</a></li>
						<li><a href=\"index?tag=$tag_5\">$tags[5]</a></li>
					</ul>";
	if(isset($GLOBALS['m_id']))
	{
		if($a_id == $GLOBALS['m_id'])
		{
			echo "		
		<div id=\"posting-edit$div_id\" class=\"posting-edit\">
		<a href=\"edit-posting.php?p_id=$id&title=$title&blurb=$blurb&photo=$photo&tag_1=$tag_1&tag_2=$tag_2&tag_3=$tag_3\">Edit</a>
		</div>
		<div id=\"posting-delete$div_id\" class=\"posting-delete\">
		<a href=\"scripts/delete_post.php?p_id=$id\">Delete</a>
		</div>";
		}
	}
	echo "
				</div>
				</div>
			</div>";
}

function print_old_post($post, $div_id="")
{
	extract($post);
   	echo "
			
			<div id=\"posting-border$div_id\" class=\"posting-border\">
				<div id=\"posting-content$div_id\" class=\"posting-content\">
				<div id=\"posting-title$div_id\" class=\"posting-title\">
					$title
				</div>";
	
		echo substr($blurb, 0, 100);
		echo "...
				</div>
				<div id=\"posting-meta$div_id\" class=\"posting-meta\">
				<div id=\"posting-time$div_id\" class=\"posting-time\">";
		print_formatted_time($posting_time);
		echo "</div>
				<a href='edit-old-posting.php?p_id=$id&title=$title&blurb=$blurb&photo=$photo&tag_1=$tag_1&tag_2=$tag_2&tag_3=$tag_3'>Make current post</a>
				</div>
			</div>";
}

?>
