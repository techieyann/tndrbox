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

        if(count($result, 1) != 0) //count recursively
		{
			extract($result[0]);
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
		$GLOBALS['DBH'] = null;
		exit;
	}

}

function default_front_page_posts()
{
	$query = "SELECT * FROM postings WHERE active=1 ORDER BY posting_time LIMIT 20";
	return query_db($query);
}

function push_old_post($b_id)
{
  $query = "SELECT tag_1, tag_2, tag_3 FROM postings WHERE b_id=$b_id AND active=1";
  $result = query_db($query);
  $tags = $result[0];
  extract($tags);
  decrement_tag($tag_1);
  decrement_tag($tag_2);
  decrement_tag($tag_3);	
  $query = "UPDATE postings SET active=0 WHERE b_id=$b_id AND active=1";
  query_db($query);
}

function format_date($id)
{
	$query = "SELECT DATE_FORMAT(date, '%M %D, %Y') FROM postings WHERE id=$id";
	$formatted_date = query_db($query);
	$date = $formatted_date[0][0];
	return $date;
}

function get_categories()
{
	$query = "SELECT id, tag FROM tags where id<0 ORDER BY tag ASC";
	return query_db($query);
}
?>