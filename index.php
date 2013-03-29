<?php
/***********************************************
file: index.php
creator: Ian McEachern

This is the default page. It displays the most
relevant data based on the function scrape_tags()
in includes.php.
 ***********************************************/
require('includes/includes.php');
require('includes/tags.php');


connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body


$result = array();
$post_flag = false;

if(isset($_GET['p']))
  {
	if(is_numeric($_GET['p']))
	  {
		$p_id = $_GET['p'];
		$query = "SELECT active, b_id FROM postings WHERE id=$p_id";
		$active_result = query_db($query);
		if(isset($active_result[0]))
		  {
			extract($active_result[0]);
			if($active == 1)
			  {
				$query = "SELECT postings.id, title, date, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE postings.id='$p_id'";
			  }
			else
			  {
				$query = "SELECT postings.id, title, date, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE b_id=$b_id AND active=1";
			  }
	
			$result = query_db($query);
			if(isset($result[0]))
			  {
				$post_flag = true;
				$result['post_flag'] = 1;
			  }
		  }
	  }
  }
elseif(isset($_GET['b']))
  {
	if(is_numeric($_GET['b']))
	  {
		$b_id = $_GET['b'];
		$query = "SELECT postings.id, title, date, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE b_id=$b_id and active=1";

		$result = query_db($query);
	
		if(isset($result[0]))
		  {
			$post_flag = true;
			$p_id = $result[0]['id'];
			$result['post_flag'] = 1;
		  }
	  }
  }
array_push($result, default_front_page_posts());
$processed_postings = process_postings($result);
$json_postings = json_encode($processed_postings);
$categories = json_encode(get_active_categories());
//head

$GLOBALS['header_html_title'] = "tndrbox";
$GLOBALS['header_scripts'] = "
		<script src='js/index.js'></script>
		<script src='js/posting_list.js'></script>
		<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?key=AIzaSyD0LQT5KDi_tPDcJPP8Rxlj6hOdifAyNO4&sensor=true'></script>
		<script>
			var postRequest = ".($post_flag ? "true" : "false").";
			var postings = $json_postings;
			var categories = $categories;
		</script>";


$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "landing";
require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
  {
	global $postings, $date, $tag_example, $category_selection;
	echo "
			<div id='welcome-page' class='hidden-phone'>
				<ul class='inline'>
				<li><h1>Welcome to tndrbox</h1></li>
				<li class='pull-right'>".date('n/j/y')."</li>
				<h4>We are a community events board. We hope you find something that interests you.<br><br>
				For an introduction to our site, click <button id='global-intro-button' class='btn-primary'>here</button><br><br>
				Please get <a href='mailto:tndrbox@gmail.com'>in touch</a> with us if you would like to post on the site!</h4>
				
			</div>
			<div id='postings-header' class='rounded-top'>

			</div><!-- #postings-header -->

			<div id='postings-header-filler'>
			</div>
			<div id='left-pane'>

			<div id='postings-container' class='tile'>

			</div><!-- #postings-container -->


			</div>
			<div id='box' class=''>
				<div id='box-images'>
					<img id='box-left' src='images/box-L.png'>
					<div id='middle-box'>
						<img id='box-back' src='images/box-B.png'> 
						<img id='box-front' src='images/box-M.png'>
					</div>
					<img id='box-right' src='images/box-R.png'>
				</div>
				<div id='box-content'>
					<div id='box-links'>
						<ul class='nav nav-tabs gray'>
							<li><a href='about'>About</a></li>
							<li><a href='login'>Member's Login</a></li>
							<li id='logout'><a href='logout'>Logout</a></li>
							<li class='pull-right'><button class='btn btn-small hide-box-button'><i class='icon-arrow-down'></i></button></li>
						</ul>

					</div>				
					<div id='box-js-content'>
					</div>
				</div>
			</div><!-- #box -->

				<div id='footer-content'>
				<a href='#welcome-page'>
				   <img id='footer-icon' src='images/footer-logo.png' alt='footer-logo' width='50' height='62'>
				</a>
				<br>
				<p>version ".$GLOBALS['version']."</p>
				</div><!-- #footer-content -->

";
  }

function process_postings($raw_posts)
  {
	$looper=$raw_posts[0];
	$processed_posts = array();
	$processed_id = 0;
	$index = 0;
	if(isset($raw_posts['post_flag']))
	  {
		$post = $raw_posts[0];

		extract($post);
		$processed_posts[$index]['id'] = $id;
		$processed_posts[$index]['title'] = $title;
		$processed_posts[$index]['date'] = format_date($id);
		$processed_posts[$index]['photo'] = $photo;
		$processed_posts[$index]['tag_1_id'] = $tag_1;
		$processed_posts[$index]['tag_1'] = get_tag($tag_1);
		$processed_posts[$index]['tag_2_id'] = $tag_2;
		$processed_posts[$index]['tag_2'] = get_tag($tag_2);
		$processed_posts[$index]['tag_3_id'] = $tag_3;
		$processed_posts[$index]['tag_3'] = get_tag($tag_3);
		$processed_posts[$index]['lat'] = $lat;
		$processed_posts[$index]['lon'] = $lon;
		$processed_posts[$index]['business'] = $name;
		//need to calculate speed here
		$processed_posts[$index]['speed'] = 1;

		$processed_id = $id;
		$index++;

		$looper = $raw_posts[1];
	  }

	foreach($looper as $post)
	  {

		if(isset($post['id']) && $post['id'] != $processed_id)
		  {
			extract($post);
		$processed_posts[$index]['id'] = $id;
		$processed_posts[$index]['title'] = $title;
		$processed_posts[$index]['date'] = format_date($id);
		$processed_posts[$index]['photo'] = $photo;
		$processed_posts[$index]['tag_1_id'] = $tag_1;
		$processed_posts[$index]['tag_1'] = get_tag($tag_1);
		$processed_posts[$index]['tag_2_id'] = $tag_2;
		$processed_posts[$index]['tag_2'] = get_tag($tag_2);
		$processed_posts[$index]['tag_3_id'] = $tag_3;
		$processed_posts[$index]['tag_3'] = get_tag($tag_3);
		$processed_posts[$index]['lat'] = $lat;
		$processed_posts[$index]['lon'] = $lon;
		$processed_posts[$index]['business'] = $name;
		//need to calculate speed here
		$processed_posts[$index]['speed'] = 1;
		$index++;

		  }
	  }
	return $processed_posts;
  }

?>