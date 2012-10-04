<?php
/***********************************************
file: includes.php
creator: Ian McEachern

This creatively titled file is necessary for 
every page. It sets metadata found about the
user and sets the metadata about the website via
defines.php.
 ***********************************************/
require('defines.php');

ini_set('error_reporting', E_ALL|E_STRICT);
ini_set('display_errors', 1);


function analyze_user()
{
	//check login cookie
	$GLOBALS['logged_in'] = false;
	if(isset($_COOKIE["login"]))
	{
		$conc_cookie = $_COOKIE["login"];
		$session = explode(",", $conc_cookie);
		$sid = $session[1];
		$email = $session[0];

		
		$query = "SELECT id, b_id, nickname FROM members 
		WHERE email = '".$email."' AND s_id = '".$sid."'";
               	$result = query_db($query);

        if(mysql_num_rows($result) == 1)
		{
		  $member_metadata = mysql_fetch_array($result);
		  extract($member_metadata);
			$GLOBALS['logged_in'] = true;
			$GLOBALS['email'] = $email;
			$GLOBALS['nickname'] = $nickname;
			$GLOBALS['m_id'] = $id;
			$GLOBALS['b_id'] = $b_id;
		}
	}

	//check metadata cookie

}

function verify_logged_in()
{
	if($GLOBALS['logged_in'] == false)
	{
		header("location:/");
		disconnect_from_db($GLOBALS['link']);
		exit;
	}

}

function scrape_tags()
{
	$query = "SELECT * FROM postings ORDER BY posting_time LIMIT 20";
	return query_db($query);
}

function push_old_post($b_id)
{
  $query = "INSERT INTO old_postings (SELECT * FROM postings WHERE b_id=$b_id)";
  query_db($query);
  $query = "SELECT tag_1, tag_2, tag_3 FROM postings WHERE b_id=$b_id";
  $result = query_db($query);
  $tags = mysql_fetch_array($result);
  extract($tags);
  decrement_tag($tag_1);
  decrement_tag($tag_2);
  decrement_tag($tag_3);	
  $query = "DELETE FROM postings WHERE b_id=$b_id";
  query_db($query);
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
				<div class=\"posting-time$div_id\">$posting_time</div>";
		echo "
				<div id=\"posting-data$div_id\" class=\"posting-data\">
					<img src=\"images/posts/$photo\" alt=\"photo for $title\" class=\"posting-image\">
					<div id=\"posting-blurb$div_id\" class=\"posting-blurb\">
						$blurb
					</div>
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
				<div id=\"posting-title$div_id\" class=\"posting-title\">
					<a href=\"business?b_id=$b_id\">$title from $name</a>
				</div>
				<div class=\"posting-time$div_id\">$posting_time</div>";
	if(isset($GLOBALS['m_id']))
	{
		if($a_id == $GLOBALS['m_id'])
		{
			echo "		
		<div id=\"posting-edit$div_id\" class=\"posting-edit\">
		<a href=\"edit-posting.php?p_id=$id&title=$title&blurb=$blurb&photo=$photo&tag_1=$tag_1&tag_2=$tag_2&tag_3=$tag_3&posting_time=$posting_time\">Edit</a>
		</div>
		<div id=\"posting-delete$div_id\" class=\"posting-delete\">
		<a href=\"scripts/delete_post.php?p_id=$id\">Delete</a>
		</div>";
		}
	}
		echo "
				<div id=\"posting-data$div_id\" class=\"posting-data\">
					<div id=\"posting-blurb$div_id\" class=\"posting-blurb\">";
		echo substr($blurb, 0, 100);
		echo "...			
					</div>
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

?>