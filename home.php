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
			$tag1 = get_tag($business['tag_1']);
            $tag2 = get_tag($business['tag_2']);
		}
$query = "SELECT * FROM postings WHERE b_id=$b_id";
$result = query_db($query);
$post_flag = 0;

if($posting = mysql_fetch_array($result))
{
	if( mysql_num_rows($result) != 0)
	{
		$post_flag = 1;
	}
}

$query = "SELECT * FROM old_postings WHERE b_id=$b_id";
$result = query_db($query);
$old_postings_flag = false;
$i = 0;
while($old_post = mysql_fetch_array($result))
{
	$old_postings[$i++] = $old_post;
}
if($i>0)
{
	$old_postings_flag = true;
}
//head
$GLOBALS['header_html_title'] = "tndrbox - ".$posting['title'];

$GLOBALS['header_scripts'] = " <script src='includes/jquery.form.js' type='text/javascript'></script>
<script src='includes/home.js' type='text/javascript'></script>";

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
  global $b_id, $business_flag, $business, $tag1, $tag2, $posting, $post_flag, $old_postings_flag, $old_postings;
	extract($business);
echo "
			<div  id='add-post-header' class=\"content-pane\" style=\"text-align:center; filter:alpha(opacity=50);opacity:.5;\"><h3>Add a <a id='add-posting' href=''>new posting</a></h3></div>
		<div class='meta-pane list'>";
		if($business_flag == 1)
		  {
                echo "
			<div id='business-info'>
			<table width='100%'>
			<tr><th><a id='edit-business-link' href=''>Edit Profile</a></th></tr>

			<tr><td>
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
			<br><h3><a id=\"business-address\" href=\"http://maps.google.com/?q=$address $city $state $zip\">
			$address<br>
			$city, $state, $zip
			</a></h3><br><br>";
		echo "<div id=\"static-map\">";
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
		echo "</div>
		</td></tr></table></div>";
	print_edit_business_form($business);
	echo "</div>";
}

	echo "
                 <div id='postings-old-and-new' class='content-pane list'>";
	print_add_post_form();
	echo "
				<table>";

	if($post_flag == 1)
	{
		extract($posting);
        echo "       
				<tr><th> <div id='edit-delete-current-post'>
Current Posting: <a id='edit-posting' href=''>Edit</a>
		|
		<a href=\"scripts/delete_post.php?p_id=$id\">Delete</a>
		</div></th></tr>
		<tr><td>";
		print_formatted_post($posting);
		print_edit_post_form($posting);
	}
        echo "</td></tr>";
	if($old_postings_flag)
	{
		echo "
		<tr><th>Previous Postings:</th></tr>";
		$i=0;
		foreach($old_postings as $old_post)
		{
			echo "<tr";
			if($i%2==0)
			{
				  echo " class=\"alt\"";
			}
			echo "><td>";
			print_old_post($old_post, "-".++$i);
			echo "</td></tr>";	
		}
	}
	echo "
	</table>
	</div>";

}
?>