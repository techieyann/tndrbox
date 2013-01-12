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

function scrape_posts()
{
	$query = "SELECT * FROM postings WHERE active=1 ORDER BY posting_time LIMIT 20";
	return query_db($query);
}

function push_old_post($b_id)
{
  $query = "SELECT tag_1, tag_2, tag_3 FROM postings WHERE b_id=$b_id AND active=1";
  $result = query_db($query);
  $tags = mysql_fetch_array($result);
  extract($tags);
  decrement_tag($tag_1);
  decrement_tag($tag_2);
  decrement_tag($tag_3);	
  $query = "UPDATE postings SET active=0 WHERE b_id=$b_id AND active=1";
  query_db($query);
}

function format_date($id)
{
	$query = "SELECT DATE_FORMAT(date, '%D %M, %Y') FROM postings WHERE id=$id";
	$result = query_db($query);
	$formatted_date = mysql_fetch_array($result);
	return $formatted_date[0];
}

?>