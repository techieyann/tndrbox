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
<link rel='stylesheet' type='text/css' href='css/styles.css' media='all'>
<script src='js/jquery.js' type='text/javascript'></script>
".$GLOBALS['header_scripts']."


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

function print_edit_business_form($business)
{
extract($business);

$tag_1 = get_tag($tag_1);
$tag_2 = get_tag($tag_2);

echo "
	<div id='edit-business'>
		<table width='100%'>
			<form name=\"$id\" id='edit-business-form' enctype=\"multipart/form-data\" method=\"post\" action=\"scripts/edit_business.php?id=$id\">
			<tr>
				<th><a id='edit-business-cancel' href=''>Cancel</a></th>
			</tr>
			<tr>
				<td>Name</td>
			</tr>
			<tr class='error' id='name_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"name\" id=\"name\" value=\"$name\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>Tag 1</td>
			</tr>
			<tr class='error' id='tag_1_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"tag_1\" id=\"tag_1\" value=\"$tag_1\" maxlength=\"100\"></td>
			</tr>
			<tr>
				<td>Tag 2</td>
			</tr>
			<tr class='error' id='tag_2_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"tag_2\" id=\"tag_2\" value=\"$tag_2\" maxlength=\"100\"></td>
			</tr>
			<tr>
				<td>Address</td>
			</tr>
			<tr class='error' id='address_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"address\" id=\"address\" value=\"$address\" maxlength=\"255\"></td>
			</tr>
		    <tr>
				<td>City</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"city\" id=\"city\" value=\"$city\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>State</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"state\" id=\"state\" value=\"$state\" maxlength=\"2\"></td>
			</tr>
			<tr>
				<td>Zip</td>
			</tr>
			<tr class='error' id='zip_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"zip\" id=\"zip\" value=\"$zip\" maxlength=\"5\"></td>
			</tr>
			<tr>
				<td>Number</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"number\" id=\"number\" value=\"$number\" maxlength=\"12\"></td>
			</tr>
		    <tr>
				<td>URL</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"url\" id=\"url\" value=\"$url\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>Hours</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"hours\" id=\"hours\" value=\"$hours\" maxlength=\"100\"></td>
			</tr>
			<tr>
				<td>Logo</td>
			</tr>
			<tr>
				<td>
				<input type=\"file\" name=\"logo_upload\" id=\"logo_upload\" size=\"10\">
				</td>
			</tr>
			<tr>
				<td style=\"border-bottom: solid 1px black;\">
				Note: filesize must be <60Kb
				</td>
			</tr>
			<tr>
		      	<td align=\"right\"><input type=\"submit\" id='edit-business-submit' name=\"submit\" value=\"Upload\"></td>
			</tr>
			</form>
		</table>
	</div>";
}

function print_formatted_post($post, $div_id="")
{
	extract($post);
	$query = "SELECT name, tag_1, tag_2 FROM business WHERE id='$b_id'";
   	$result = query_db($query);
   	$business_result = mysql_fetch_array($result);

	$date = format_date($id);

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
					$title";
	if($date != "")
	  {
	    echo " on $date";
	  }
	if($alt_address != "")
	  {
	    echo " at $alt_address";
	  }
	echo "
				</div>
				<div class=\"posting-time$div_id\">";
		print_formatted_time($posting_time);
		echo "</div>
				<div id=\"posting-data$div_id\" class=\"posting-data\">
					<img src=\"images/posts/$photo\" alt=\"photo for $title\" class=\"posting-image\" width='300px'>
					
					<div id=\"posting-blurb$div_id\" class=\"posting-blurb\">
						$blurb
					
					</div>";
		if($url != "")
		  {
		    echo "
<div id='posting-purchase$div_id' class='posting-purchase'>
<a href='http://$url'><img src='images/purchase.png'></a>
</div>";
		  }
echo "
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

function print_add_post_form()
{
echo "
	<div id='add-posting-form'>
		<form name='new-post-form'  enctype='multipart/form-data' action='scripts/new_post.php' method='post'>
			<table>
				<tr>
					<td>Title</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=50 name=\"title\" id=\"title\"></td>
				</tr>
<tr>
					<td>Date</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=50 name=\"date\" id=\"date\"></td>
				</tr>
<tr>
					<td>Address</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=250 name=\"address\" id=\"address\"></td>
<tr>
					<td>Purchase URL</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=100 name=\"url\" id=\"url\"></td>
				</tr>
				</tr>
				<tr class='error' id='title_error'>
				<td></td><td></td>
				<td>This field is required.</td>  
				</tr>
				<tr>
					<td>Description</td>
					<td>:</td>
					<td colspan=4><textarea name=\"description\" cols=50 rows=5 maxlength=255></textarea></td>
				</tr>
				<tr class='error' id='desc_error'>
				<td></td><td></td>
				<td>This field is required.</td>  
				</tr>
				<tr>
					<td>Tags</td>
					<td>:</td>
					<td><input type=\"text\" size=8 name=\"tag1\" id=\"tag1\"></td>
					<td><input type=\"text\" size=8 name=\"tag2\" id=\"tag2\"></td>
					<td><input type=\"text\" size=8 name=\"tag3\" id=\"tag3\"></td>
				</tr>
				<tr class='error' id='tag_error'>
				<td></td><td></td>
				<td>This field is required.</td>  
				</tr>";
  /*				<tr>
					<td>Publish Date</td>
					<td>:</td>
					<td><input type=\"text\" name=\"publish_date\" id=\"publish_date\"></td>
					<td>Publish Time :</td>
					<td><input type=\"time\" name=\"publish_time\" id=\"publish_time\"></td>
				</tr>
  */
				echo "
				<tr>
				<td>Image</td>
				<td>:</td>
				<td colspan=4>
				<input type=\"file\" name=\"image_upload\" id=\"image_upload\">
				</td>
			</tr>
			<tr>
				<td></td>
				<td colspan=5 style=\"border-bottom: solid 1px black;\">
				Note: filesize must be less than 240Kb
				</td>
			</tr>
			<tr>
				<td><input type='button' id='add-cancel-button' name='cancel' value='Cancel'></td>
				<td></td>
		      		<td colspan=4 align=\"right\"><input type=\"submit\" id='add-submit' name=\"submit\" value=\"Submit\"></td>
			</tr>
			</table>
			</form>
	</div>";
}

function print_edit_post_form($post, $div_id="")
{
extract($post);
$tag1 = get_tag($tag_1);
$tag2 = get_tag($tag_2);
$tag3 = get_tag($tag_3);

echo "
	<div id=\"edit-posting-form\">	

		<form id='edit-post-form' name='edit-post-form' enctype='multipart/form-data' action='scripts/edit_post.php?p_id=$id' method='post'>
		<table>	  
				<tr>
					<td>Title</td>
					<td>:</td>
					<td colspan=4><input type=\"text\"  size=40 maxlength=50 name=\"title\" id=\"edit-title\" value=\"$title\"></td>
				</tr>
				<tr class='error' id='title_error'>
				<td></td><td></td>
				<td>This field is required.</td>  
				</tr>
<tr>
					<td>Date</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=50 name=\"date\" id=\"edit-date\" value=\"$date\"></td>
				</tr>
<tr>
					<td>Address</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=250 name=\"address\" id=\"edit-address\" value=\"$alt_address\"></td>
<tr>
					<td>Purchase URL</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=100 name=\"url\" id=\"edit-url\" value=\"$url\"></td>
				</tr>

				<tr>
					<td>Description</td>
					<td>:</td>
					<td colspan=4><textarea name=\"description\" id='edit-description' cols=50 rows=5>$blurb</textarea></td>
				</tr>
				<tr class='error' id='desc_error'>
				<td></td><td></td>
				<td>This field is required.</td>  
				</tr>
				<tr>
					<td>Tags</td>
					<td>:</td>
					<td><input type=\"text\" size=8 name=\"tag1\" id=\"edit-tag1\" value=\"$tag1\"></td>
					<td><input type=\"text\" size=8 name=\"tag2\" id=\"edit-tag2\" value=\"$tag2\"></td>
					<td><input type=\"text\" size=8 name=\"tag3\" id=\"edit-tag3\" value=\"$tag3\"></td>
				</tr>
				<tr class='error' id='tag_error'>
				<td></td><td></td>
				<td>This field is required.</td>  
				</tr>";
/*
 				<tr>
					<td>Publish Date</td>
					<td>:</td>
					<td><input type=\"text\" name=\"publish_date\" id=\"publish_date\" value=\"$publish_date\"></td>
					<td>Publish Time</td>
					<td>:</td>
					<td><input type=\"time\" name=\"publish_time\" id=\"publish_time\" value=\"$publish_time\"></td>
				</tr>
*/
echo "
			<tr>
				<td>Image</td>
				<td>:</td>
				<td colspan=4>
				<input type=\"file\" name=\"image_upload\" id=\"image_upload\">
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td colspan=4 style=\"border-bottom: solid 1px black;\">
				Note: filesize must be less than 240Kb
				</td>
			</tr>
			<tr>
				<td><input type='button' id='edit-cancel-button' name='cancel' value='Cancel'></td>
				<td></td>
		      		<td colspan=4 align=\"right\"><input type=\"submit\" id='edit-submit' name=\"submit\" value=\"Submit\"></td>
			</tr>
			</table>
			</form>
	</div>";
}

function print_mini_post($post, $div_id="")
{
	extract($post);
	$query = "SELECT name, tag_1, tag_2 FROM business WHERE id='$b_id'";
   	$result = query_db($query);
   	$business_result = mysql_fetch_array($result);

	$date = format_date($id);

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
					<a href=\"business?b_id=$b_id\">$title from $name";
	if($date != "")
	  {
	    echo " on $date";
	  }
	echo "</a>
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

	$date = format_date($id);

   	echo "
			
			<div id=\"posting-border$div_id\" class=\"posting-border\">
				<div id=\"posting-content$div_id\" class=\"posting-content\">
				<div id=\"posting-title$div_id\" class=\"posting-title\">
					$title";
	if($date != "")
	  {
	    echo " on $date";
	  }
	echo "
				</div>";
	
		echo substr($blurb, 0, 100);
		echo "...
				</div>
				<div id=\"posting-meta$div_id\" class=\"posting-meta\">
				<div id=\"posting-time$div_id\" class=\"posting-time\">";
		print_formatted_time($posting_time);
		echo "</div>
				<a id='make-current$div_id' href=''>Make current post</a>
				</div>
			</div>";
}

?>
