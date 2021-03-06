<?php
/***********************************************
file: includes/db_functions.php
creator: Ian McEachern

This file contains global functions which
utilize information from the databases.
 ***********************************************/

function analyze_user()
{
	if(!isset($_SESSION['logged_in']))
{
	$_SESSION['logged_in'] = false;
}
	//check login cookie
	if(!$_SESSION['logged_in'])
{
	if(isset($_COOKIE['login']))
	{
		$conc_cookie = $_COOKIE['login'];
		$session = explode(",", $conc_cookie);
		$sid = $session[1];
		$email = $session[0];

		$query = "SELECT id, b_id, s_id, type FROM members WHERE email = '$email'";
		$result = query_db($query);
		if(isset($result[0]))
		  {

			if(md5($result[0]['s_id']) == $sid)
			  {
				extract($result[0]);

				$_SESSION['logged_in'] = true;
				$_SESSION['email'] = $email;
				$_SESSION['m_id'] = $id;
				$_SESSION['b_id'] = $b_id;
				$_SESSION['type'] = $type;
			  }
		  }
		else
		  {
			//fail and report
		  }
	}
}
	//check metadata cookies

	//check location
	if(isset($_COOKIE['location']))//location found
	  {
		$loc_json = $_COOKIE['location'];
		$location = json_decode($loc_json, true);

		$_SESSION['lon'] = $location['lon'];
		$_SESSION['lat'] = $location['lat'];
		$_SESSION['latlon_source'] = $location['source'];
	  }
	else//get location from ip address
	  {
		$ip = $_SERVER['REMOTE_ADDR'];
		$latlon = ip_to_latlon($ip);
		$_SESSION['lat'] = $latlon['lat'];
		$_SESSION['lon'] = $latlon['lon'];
		//set location
		$cookie_val = $latlon;
		$cookie_val['source'] = 'ip';
		$cookie_json = json_encode($cookie_val, true);
		setcookie("location", $cookie_json, time()+(3600*24), "/");
	  }
}

function verify_logged_in()
{
	if($_SESSION['logged_in'] == false)
	{
		header("location:#b=login");
		$GLOBALS['DBH'] = null;
		exit;
	}
}

function check_admin()
{
  if($_SESSION['logged_in'])
	{
	  if($_SESSION['b_id'] == 0)
		{
		  return true;
		}
	}
  return false;
}

function default_front_page_posts()
{
  /* Distance aware postings code:
	$lat = $_SESSION['lat'];
	$lon = $_SESSION['lon'];
	$radius = $GLOBALS['default_latlon_delta']/2;
	$lat_max = $lat+$radius;
	$lat_min = $lat-$radius;
	$lon_max = $lon+$radius;
	$lon_min = $lon-$radius;
	$query = "SELECT postings.id, title, date, start_time, posting_time,  postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE active=1 AND $lat_min <= postings.lat AND postings.lat <= $lat_max AND ($lon_min) <= postings.lon AND postings.lon <= ($lon_max)";
  */

  $query = "SELECT postings.id, title, date, start_time, posting_time, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE active=1 ORDER BY posting_time DESC";
	return query_db($query);
}

function push_old_post($b_id)
  {
    $query = "SELECT tag_1, tag_2, tag_3 FROM postings WHERE b_id=$b_id AND active=1";
	$result = query_db($query);
	if(isset($result[0]))
	  {
  		$tags = $result[0];
		extract($tags);
		decrement_tag($tag_1);
		decrement_tag($tag_2);
		decrement_tag($tag_3);	
		$query = "UPDATE postings SET active=0 WHERE b_id=$b_id AND active=1";
		query_db($query);
		$query = "UPDATE business SET active_post=0, last_touched=CURRENT_TIMESTAMP WHERE id=$b_id";
		query_db($query);
	  }
  }
function deactivate_post($id)
  {
    $query = "SELECT active, b_id, tag_1, tag_2, tag_3 FROM postings WHERE id=$id";
	$result = query_db($query);
	if(isset($result[0]))
	  {
		extract($result[0]);
		if($active == 1)
		{
			decrement_tag($tag_1);
			decrement_tag($tag_2);
			decrement_tag($tag_3);	
			$query = "UPDATE postings SET active=0 WHERE id=$id";
			query_db($query);
			$query = "SELECT id FROM postings WHERE active=1 AND b_id=$b_id";
			$result = query_db($query);
			if(!isset($result[0]))
			{
				$query = "UPDATE business SET active_post=0, last_touched=CURRENT_TIMESTAMP WHERE id=$b_id";
				query_db($query);
			}
		 }
	  }
  }

function activate_post($p_id)
  {
    $query = "SELECT tag_1, tag_2, tag_3, b_id FROM postings WHERE id=$p_id";
	$result = query_db($query);
	if(isset($result[0]))
	  {
		extract($result[0]);
		increment_tag($tag_1);
		increment_tag($tag_2);
		increment_tag($tag_3);	
		$query = "UPDATE postings SET active=1, viewed=0, posting_time=CURRENT_TIMESTAMP WHERE id=$p_id";
		query_db($query);
		$query = "UPDATE business SET active_post=1, last_touched=CURRENT_TIMESTAMP WHERE id=$b_id";
		query_db($query);
		
	  }
  }
function format_date($id)
{
	$query = "SELECT DATE_FORMAT(date, '%W, %M %D') FROM postings WHERE id=$id";
	$formatted_date = query_db($query);
	$date = $formatted_date[0][0];
	return $date;
}

function get_categories()
{
	$query = "SELECT id, tag FROM tags where id<0 ORDER BY tag ASC";
	return query_db($query);
}

function get_active_categories()
{
		$query = "SELECT id, tag FROM tags WHERE id<0 AND num_ref>0 ORDER BY id DESC";
		return query_db($query);

}


function add_slashes($input)
{
  $output = str_replace('\'', '\\\'', $input);
  return $output;
}

?>