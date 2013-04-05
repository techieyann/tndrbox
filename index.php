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

disconnect_from_db();



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


<!DOCTYPE html>

<html>

	<head>
	<!-- Meta data -->
		<title>tndrbox</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	   	<meta name="author" content="Ian McEachern">

	<!-- Icons -->
		<link rel="icon" type="image/ico" href="images/favicon.ico">
		<link rel="shortcut icon" href="images/favicon.ico">
		<link rel="apple-touch-icon" href="images/touchicon.png">

	<!-- Javascript -->
		<!-- jquery -->
		<script src="js/jquery.js" type="text/javascript"></script>
		<script src="js/jquery-ui.js"></script>
		<!-- google analytics -->
		<script src="js/jquery.ga.js"></script>
		<!-- bbq hashchange -->
		<script src="js/jquery.ba-hashchange.js"></script>
		<!-- timepicker -->
		<script src="js/jquery.ui.timepicker"></script>
		<!-- Intro tour -->
		<script src="js/intro.js"></script>
		<!-- ajax forms -->
		<script src="js/jquery.form.js" type="text/javascript"></script>
		<!-- Modernizr
		<script src="js/modernizr.js"></script> -->
		<!-- google analytics via jquery.ga -->
		<script>
			$(document).ready(function(){
				$.ga.load("<?php print $GLOBALS['ga_account']?>");
			});
		</script>

	<!-- CSS -->
		<!-- jquery -->
		<link rel="stylesheet" type="text/css" href="css/jquery-ui.css" media="all">
		<!-- Bootstrap -->
		<link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
		<meta name="viewport" content="user-scalable=false, width=device-width, initial-scale=1.0">
		<!-- Intro tour -->
		<link rel="stylesheet" type="text/css" href="css/introjs.css" media="all">
		<!-- Google Maps -->
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD0LQT5KDi_tPDcJPP8Rxlj6hOdifAyNO4&sensor=true"></script>
  
	<!-- homebrewed css/js -->
		<script src="js/tndrbox.js"></script>

		<script src="js/posting_list.js"></script>
		<!-- homebrewed -->
		<link rel="stylesheet" type="text/css" href="css/tndrbox.css" media="all">

		<script>
			var loggedIn = <?php ($GLOBALS['logged_in'] ? print "true" : print "false") ?>;
			var postRequest = <?php ($post_flag ? print "true" : print "false") ?>;
			var postings = <?php print $json_postings ?>;
			var categories = <?php print $categories ?>;
		</script>

	</head>

	<body>

		<div id="welcome-page" class="hidden-phone">
			<ul class="inline">
				<li><h1>Welcome to tndrbox</h1></li>
				<li class="pull-right"><?php print date('n/j/y') ?></li>
			</ul>

			<div id="welcome-page-content">
				<h4>Hello traveler, we need javascript installed/enabled for our site to function properly. Please get back to us when you have done so.</h4>
			</div><!-- #welcome-page-content -->

			<h4>Please get <a href="mailto:tndrbox@gmail.com">in touch</a> with us if you would like to post on the site!</h4>
		</div><!-- #welcome-page -->

		<div id="body-container" class="container">
			<div id="tndr-header" class="rounded-top">

			</div><!-- #tndr-header -->

			<div id="tndr-header-filler">

			</div><!-- #tndr-header-filler -->

			<div id="left-pane" data-step="1" data-intro="This where our posts go. Click one, they don't bite." data-position="right" class="active">
				<div id="tndr">
	
				</div><!-- #tndr -->

				<div id="footer">
					<a href="#">
						<img id="footer-icon" src="images/footer-logo.png" alt="footer-logo" width="50" height="62">
					</a>
					<br>
					<p>version <?php print $GLOBALS['version'] ?></p>
				</div><!-- #footer -->

			</div><!-- #left-pane -->

			<div id="right-pane" data-step="3" data-intro="This is our map. It maps things." data-position="left">
					<div id="map-canvas">

					</div><!-- #map-canvas -->

			</div><!-- #right-pane -->

		</div><!-- #body-container -->

		<div id="box" class="inactive">
			<div id="box-images">
				<img id="box-left" src="images/box-L.png">

				<div id="middle-box">
					<!--<img id="box-back" src="images/box-B.png">-->
					<img id="box-front" src="images/box-M.png"> 
				</div><!-- #middle-box -->

				<img id="box-right" src="images/box-R.png">
			</div><!-- #box-images -->

			<div id="box-content">
				<div id="box-links">
					<ul class="nav nav-tabs gray">
						<li><a href="#b=about">About</a></li>
						<li id="login-link"><a href="#b=login">Members' Login</a></li>
						<li id="settings-link"><a href="#b=members">Settings</a></li>
						<li id="logout-link"><a href="#b=logout">Logout</a></li>
						<li class="pull-right"><button id="show-box-button" onclick="showBox()"><i class="icon-arrow-up"></i></button></li>
						<li class="pull-right"><button id="hide-box-button"  onclick="deactivateBox()"><i class="icon-arrow-down"></i></button></li>
					</ul>
				</div><!-- #box-links -->				

				<div id="box-js-content">

				</div><!-- #box-js-content -->

			</div><!-- #box-content -->

		</div><!-- #box -->
	<!-- minimized javascript -->
		<!--Bootstrap-->
		<script src="js/bootstrap.min.js"></script>
		<!--Masonry-->
		<script src="js/jquery.masonry.min.js"></script>

	</body>

</html>