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
		$username = $session[0];

		connect_to_db($mysql_user, $mysql_pass, $mysql_db);
		$query = "SELECT * FROM members 
		WHERE username = '".$username."' AND sid = '".$sid."'";
               	$result = query_db($query);

               	if(mysql_num_rows($result) == 1)
	       	{
			$GLOBALS['logged_in'] = true;
		}
	}

	//check metadata cookie

}

?>