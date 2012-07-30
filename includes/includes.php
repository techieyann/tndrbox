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

?>