<?php
/***********************************************
file: includes/db_functions.php
creator: Ian McEachern

This file contains global functions which
utilize information from the databases.
 ***********************************************/

function analyze_user()
{
	//check login cookie
	$GLOBALS['logged_in'] = false;
	if(isset($_COOKIE['login']))
	{
		$conc_cookie = $_COOKIE['login'];
		$session = explode(",", $conc_cookie);
		$sid = $session[1];
		$email = $session[0];

		$query = "SELECT id, b_id, s_id FROM members WHERE email = '$email'";
		$result = query_db($query);
		if(isset($result[0]))
		  {

			if(md5($result[0]['s_id']) == $sid)
			  {
				extract($result[0]);

				$GLOBALS['logged_in'] = true;
				$GLOBALS['email'] = $email;
				$GLOBALS['m_id'] = $id;
				$GLOBALS['b_id'] = $b_id;
			  }
		  }
		else
		  {
			//fail and report
		  }
	}

	//check metadata cookies

	//check location
	if(isset($_COOKIE['location']))//location found
	  {
		$loc_json = $_COOKIE['location'];
		$location = json_decode($loc_json, true);

		$GLOBALS['lon'] = $location['lon'];
		$GLOBALS['lat'] = $location['lat'];
		$GLOBALS['latlon_source'] = $location['source'];
	  }
	else//get location from ip address
	  {
		$ip = $_SERVER['REMOTE_ADDR'];
		$latlon = ip_to_latlon($ip);
		$GLOBALS['lat'] = $latlon['lat'];
		$GLOBALS['lon'] = $latlon['lon'];
		//set location
		$cookie_val = $latlon;
		$cookie_val['source'] = 'ip';
		$cookie_json = json_encode($cookie_val, true);
		setcookie("location", $cookie_json, time()+(3600*24), "/");
	  }
}

function verify_logged_in()
{
	if($GLOBALS['logged_in'] == false)
	{
		header("location:#b=login");
		$GLOBALS['DBH'] = null;
		exit;
	}
}

function check_admin()
{
  if($GLOBALS['logged_in'])
	{
	  if($GLOBALS['b_id'] == 0)
		{
		  return true;
		}
	}
  return false;
}

function default_front_page_posts()
{
  /* Distance aware postings code:
	$lat = $GLOBALS['lat'];
	$lon = $GLOBALS['lon'];
	$radius = $GLOBALS['default_latlon_delta']/2;
	$lat_max = $lat+$radius;
	$lat_min = $lat-$radius;
	$lon_max = $lon+$radius;
	$lon_min = $lon-$radius;
	$query = "SELECT postings.id, title, date, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE active=1 AND $lat_min <= postings.lat AND postings.lat <= $lat_max AND ($lon_min) <= postings.lon AND postings.lon <= ($lon_max)";
  */

  $query = "SELECT postings.id, title, date, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE active=1 ORDER BY posting_time DESC";
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
		$query = "UPDATE postings SET active=1 WHERE id=$p_id";
		query_db($query);
		$query = "UPDATE business SET active_post=1, last_touched=CURRENT_TIMESTAMP WHERE id=$b_id";
		echo query_db($query);
		
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