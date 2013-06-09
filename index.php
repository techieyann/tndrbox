<?php
/***********************************************
file: index.php
creator: Ian McEachern

This is the default page. It displays the most
relevant data based on the function scrape_tags()
in includes.php.
 ***********************************************/
session_start();
require('includes/includes.php');
require('includes/tags.php');

$post_flag = false;
$event_flag = false;
connect_to_db($mysql_user, $mysql_pass, $mysql_db);
analyze_user();

$tag_example = "Tag";
$result = get_most_popular_tags(1);
if(isset($result[0]))
  {
	$tag_example .= ", eg. \"".$result[0]['tag']."\"";
  }

if(isset($_GET['p']) && is_numeric($_GET['p']))
  {
		$p_id = $_GET['p'];
		$query = "SELECT active, b_id FROM postings WHERE id=$p_id";
		$active_result = query_db($query);
		if(isset($active_result[0]))
		  {
			extract($active_result[0]);
			if($active == 1)
			  {
				$query = "SELECT title, blurb, date, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE postings.id='$p_id'";
			  }
			else
			  {
				$query = "SELECT postings.id, title, blurb, date, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE b_id=$b_id AND active=1";
			  }
	
			$result = query_db($query);

			if(isset($result[0]))
			  {
				$post_flag = true;
				extract($result[0]);
			  }
		  }	  
  }
else if(isset($_GET['e']) && is_numeric($_GET['e']))
  {
		$e_id = $_GET['e'];
		$query = "SELECT active, b_id FROM events WHERE id=$e_id";
		$active_result = query_db($query);
		if(isset($active_result[0]))
		  {
			extract($active_result[0]);
			if($active == 1)
			  {
				$query = "SELECT title, blurb, date, events.photo, tag_1, tag_2, postings.lat, postings.lon, business.name FROM events INNER JOIN business ON events.b_id=business.id WHERE events.id='$e_id'";
				$result = query_db($query);

				if(isset($result[0]))
				  {
					$event_flag = true;
					extract($result[0]);
				  }
			  }
		  }	  
  }
$categories = get_active_categories();
$json_categories = json_encode(get_categories());
disconnect_from_db();

?>

<!DOCTYPE html>

<html>
	<head>
		<title>tndrbox</title>

	<!-- Above the fold CSS -->

		<style>


html{font-size:100%;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;}
a:focus{outline:thin dotted #333;outline:5px auto -webkit-focus-ring-color;outline-offset:-2px;}
a:hover,a:active{outline:0;}

img{max-width:100%;width:auto\9;height:auto;vertical-align:middle;border:0;-ms-interpolation-mode:bicubic;}
input{max-width:100%;}

ul
{
margin:0 !important;
padding:0;
}

html, body
{
height:100%;
margin:0px;
width:100%;
  overflow-x:hidden;
}
body
{

}
#body-container
{
	position:relative;
width:80%;
	min-height:100%;
	margin-top:0px;
	margin-left:70px;
	z-index:10;
}

#left-pane
{
	position:absolute;
	left:0;
	background:#dedede;
	z-index:501;
	min-height:100%;
        -webkit-box-shadow: 3px 0px 2px rgba(0,0,0,.25);
           -moz-box-shadow: 3px 0px 2px rgba(0,0,0,.25);
                box-shadow: 3px 0px 2px rgba(0,0,0,.25);
width:90%;
}

#welcome-page, event
{
background:#d5d5d5;
	max-width: 600px;
	padding:20px 20px 0px 20px;
	overflow:hidden;
	margin: 0px auto;

	position:relative;
        -webkit-box-shadow: 0px 1px 2px rgba(0,0,0,.25);
           -moz-box-shadow: 0px 1px 2px rgba(0,0,0,.25);
                box-shadow: 0px 1px 2px rgba(0,0,0,.25);

}

#welcome-close
{
display:none;
}

#tndr
{
	margin-left:10px;
	margin-top:10px;
  min-heigth:100%;
}
.loading
{
	margin: 20px auto 50px auto;
	text-align:center;
}



#footer{

	margin: 25px auto 105px auto; 
	text-align: center;
	
}

#right-pane
{
	position:fixed;
left:70px;
width:80%;
height:100%;
	z-index:5;
background:#aaa;

}
/************* header*/
#tndr-header
{
	position:fixed;
	top:0px;
	left:70px;

	z-index:600;
       -webkit-box-shadow: 0px 2px 2px rgba(0,0,0,.45);
           -moz-box-shadow: 0px 2px 2px rgba(0,0,0,.45);
                box-shadow: 0px 2px 2px rgba(0,0,0,.45);

	background:#A33539;
	padding-top:2px;
	padding-bottom:2px;
width:80%;
 
}



#tndr-header-filler
{
height:40px;
}

#tndr-buttons
{
	display:none;
	padding:10px;
  padding-bottom:0px;
}

#box
{
width:200%;
	position:fixed;
	left: 5px;
	z-index:200;
}
#box-content
{
	position:relative;

	z-index:300;
background: #7f1214; 

}

#box-links
{
	position:relative;
display:none;
	top: -85px;
	margin: 0 80px 0px 80px;
	font-size:18px;
}
#box-links a
{

	color:#eee;
}

#box-links a:hover
{
	color:#A33539;
}
#box-images
{
display:inline;
}
#box-left
{
position:fixed;
	height:150px;
left:5px;
z-index:1000;

}
#box-right
{
position:fixed;

right:5px;
	height:150px;
z-index:1000;
}


#middle-box
{
width:40%;
  margin-left:65px;
margin-top:0px;

}

#box-front
{
	position:relative;
	height:90px;
	width:100%;
	z-index:200;
}
#box-footer
{
	position:relative;
	top:-85px;
	color:white;
	text-align:center;
}

@media all and (max-width:768px){
	body
	  {
	  background:none;	
	  width:100%;
	  }
	#body-container
	  {
		margin-left:-5px;
	  width:100%;
	  }
	#tndr-header, #right-pane
  {
  left:0px;
  width:100%;
  }
#tndr-header-filler, #tndr-header
  {
  height:25px;
  }
#logo
{
	height:25px;
}
#welcome-page
  {
  max-width:100%;
  height:75px;
  overflow:hidden;
        -webkit-box-shadow: inset 0px -10px 10px rgba(0,0,0,.25);
           -moz-box-shadow: inset 0px -10px 10px rgba(0,0,0,.25);
                box-shadow: inset 0px -10px 10px rgba(0,0,0,.25);
  }
#event
{
	max-width:100%;
        -webkit-box-shadow: inset 0px -10px 10px rgba(0,0,0,.25);
           -moz-box-shadow: inset 0px -10px 10px rgba(0,0,0,.25);
                box-shadow: inset 0px -10px 10px rgba(0,0,0,.25);
}
	#box
	  {
	  left:-65px;
	  }
	#middle-box
	  {
	  width:50%;
	  }
	
}
@media all and (max-width:360px){
	#logo-li
	{
		max-width:45%;
	}
}
//bootstrap.min code


li{line-height:20px;}
ul.inline,ol.inline{margin-left:0;list-style:none;}ul.inline>li,ol.inline>li{display:inline-block;*display:inline;*zoom:1;padding-left:5px;padding-right:5px;}
		</style>

	<!-- Global js variables -->


	<!-- Meta data -->
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	   	<meta name="author" content="Ian McEachern">
		<meta name="viewport" content="user-scalable=false, width=device-width, initial-scale=1.0">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<!-- Open Graph Data -->
		<meta property="og:title" content="<?php print ($post_flag|$event_flag ? "$title at $name":"A posting board for Oakland") ?>">
	    <meta property="og:description" content="<?php print ($post_flag|$event_flag ? substr($blurb, 0, 100)."...":"tndrbox is a community events board in Oakland. Come see what is happening around you") ?>">
		<meta property="og:site_name" content="tndrbox">
		<meta property="og:type" content="website">
		<meta property="og:url" content="tndrbox.com/<?php print ($post_flag|$event_flag ? "?p=$p_id":"") ?>">
	    <meta property="og:image" content="<?php print ($post_flag|$event_flag ? "images/posts/$photo": "images/logo.png") ?>">

	<!-- Icons -->
		<link rel="icon" type="image/ico" href="images/favicon.ico">
		<link rel="shortcut icon" href="images/favicon.ico">
		<link rel="apple-touch-icon" href="images/touchicon.png">

		<script>
			var loggedIn = <?php ($_SESSION['logged_in'] ? print "true" : print "false") ?>;
			var postRequest = <?php ($post_flag ? print "true" : print "false") ?>;
			var eventRequest = <?php ($event_flag ? print "true" : print "false") ?>;
			var categories = <?php print $json_categories ?>;
			var postings;
			var markerSprites = [];
		</script>
	</head>

	<body onload='loadTheRest()'>
			<div id="posting" class="inactive">
					<div id="posting-content">
					</div><!-- #posting-content -->
			</div><!-- #posting -->

			<div id="tndr-header">
				<ul class="inline centered">

				<li class="pull-left"><button class="btn btn-mini" id="activate-tndr"><i id="header-list" class="icon-list"></i><i id="header-tiles" class="icon-th-large"></i></button></li>
				<li id="logo-li"><a href="index"><img id="logo" src="slir/h40/images/logo.png" alt="tndrbox"></a></li>
				<li class="pull-right"><button class="btn btn-mini" id="locate-button" onclick="getLocation();"><i class="icon-screenshot"></i></button></li>
				<li class="pull-right"><button class="btn btn-mini" id="activate-map" onclick="toggleActivePane();"><i class="icon-globe"></i></button></li>

				</ul>
			</div><!-- #tndr-header -->

		<div id="body-container" class="">


			<div id="tndr-header-filler">

			</div><!-- #tndr-header-filler -->

			<div id="left-pane" data-step="1" data-intro="This where our posts go. Click one, they don't bite." data-position="right" class="active">

			<div id="welcome-page">
						<div id="welcome-page-content">
							<h4>Hello traveller, we need javascript installed/enabled for our site to function properly. Please get back to us when you have done so.</h4>
						</div><!-- #welcome-page-content -->
			  <p class="pull-right"><?php print date('F jS, Y')?></p>
			  <button class="btn btn-mini pull-left" id="welcome-close" onclick="$('#welcome-page').hide('fast'); "><i class="icon-remove"></i></button>
			</div><!-- #welcome-page -->
			<div id="event">
			</div><!-- #event -->

				<div id="tndr-buttons">
						<ul class="inline">
							<li>

									<button title="Filter" id="filters" class="btn btn-small" ><i class="icon-search"></i></button>

							</li>
							<li>
									<button title="Clear Filters" id="reset-filters-button" class="btn btn-small" ><i class="icon-remove-circle"></i></button>
							</li>
							<li class='pull-right'>
								<div class="btn-group">
									<button title="Tiles" id="tile-format" class="format-button btn btn-small disabled"><i class="icon-th-large"></i></button>
									<button title="List" id="list-format" class="format-button btn btn-small"><i class="icon-list"></i></button>
								</div>
							</li>

						</ul>
						<ul class="inline" id="search-options">
							<li class="btn-group" id="categories-dropdown">
								<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
								<i class="icon-folder-open"></i>&nbsp Category
								<span class="caret"></span>
								</a>
								<ul id="categories-ul" class="dropdown-menu">
									<?php
										
			  foreach($categories as $index =>$category)
			{
			  extract($category);
			  if($index != 0)
				{
				  print "<li class='divider'></li>";
				}
			  print "<li  class='category-link'><a href='#c=$id'><div class='pull-left $tag'></div>$tag</a></li>";
			}
									?>	
								</ul>
							</li>
							<li>
								<button class="btn" id="tag-op">and</button>
							</li>
							<li>
								<form id='search-bar' class='form-inline'>
									<div class='input-prepend'>
										<span class='add-on'><i class='icon-tags'></i></span>
										<input type='text' id='search' name='search' class='span3' placeholder='<?php print $tag_example ?>'>
									</div><!-- .input-prepend -->
								</form>
							</li>
						</ul>
				</div><!-- tndr-buttons -->
				<div id="tndr">


					<div id="tiles">

					</div><!-- #tiles -->					
					<div id="list">

					</div><!-- #list -->
				</div><!-- #tndr -->

				<div id="footer">
					<a href="#">
						<img id="footer-icon" src="images/footer-logo.png" alt="footer-logo" width="50" height="62">
					</a>
					<br>
					<p>version <?php print $GLOBALS['version'] ?></p>
				</div><!-- #footer -->


			</div><!-- #left-pane -->



		</div><!-- #body-container -->

			<div id="right-pane" data-step="2" data-intro="This is our map. It maps things." data-position="left">
					<div id="map-canvas">

					</div><!-- #map-canvas -->

			</div><!-- #right-pane -->

		<div id="box" class="inactive">
			<div id="box-content">
				<div id="box-links">
					<ul class="nav nav-tabs gray">
						<li><a href="#b=about">About</a></li>
						<li id="login-link"><a href="#b=login">Members' Login</a></li>
						<li id="settings-link"><a href="#b=members">Settings</a></li>
						<li id="logout-link"><a href="#b=logout">Logout</a></li>
						<li class="pull-right"><button id="hide-box-button"  onclick="deactivateBox()"><i class="icon-remove"></i></button></li>
					</ul>
				</div><!-- #box-links -->				

				<div id="box-js-content">

				</div><!-- #box-js-content -->
				<div id='box-footer'>tndrbox &copy; 2013</div>
			</div><!-- #box-content -->

		</div><!-- #box -->
	<script>
		function loadTheRest()
		{
	document.getElementById('welcome-page-content').innerHTML = "<strong class='centered'><p>Welcome to tndrbox, Oakland's Community Events Board</strong></p>Tap into local events and happenings posted by our neighborhood for our neighborhood";

			var boxImage = document.createElement('div');
			boxImage.setAttribute('id', 'box-images');
			boxImage.innerHTML = '<div id="middle-box"><!--<img id="box-back" src="images/box-B.png">--><img id="box-front" src="images/box-M.png"></div>';

			var box = document.getElementById('box');
			box.insertBefore(boxImage, box.firstChild);

			boxImage = document.createElement('img');
				boxImage.setAttribute('id', 'box-left');
				boxImage.setAttribute('class', 'inactive');
				boxImage.setAttribute('src', 'images/box-L.png');

				box.parentNode.insertBefore(boxImage, box);

				boxImage = document.createElement('img');
				boxImage.setAttribute('id', 'box-right');
				boxImage.setAttribute('class', 'inactive');
				boxImage.setAttribute('src', 'images/box-R.png');

				box.parentNode.insertBefore(boxImage, box.nextSibling);


			var tndr = document.getElementById('tndr');
			var loadingDiv = document.createElement('div');
			loadingDiv.innerHTML = '<img src="images/loading.gif"><br>Loading...';
			loadingDiv.setAttribute('class', 'loading');
			tndr.insertBefore(loadingDiv, tndr.firstChild);

			var homebrewCSS = document.createElement('link');
			homebrewCSS.type = 'text/css';
			homebrewCSS.rel = 'stylesheet';
			homebrewCSS.href = 'css/tndrbox.css';

			var modularCSS = document.createElement('link');
			modularCSS.type = 'text/css';
			modularCSS.rel = 'stylesheet';
			modularCSS.href = 'css/modules.css';

			var googleMaps = document.createElement('script');
			googleMaps.type = 'text/javascript';
			googleMaps.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyD0LQT5KDi_tPDcJPP8Rxlj6hOdifAyNO4&sensor=true&callback=afterMapsLoad';

			var ref = document.getElementsByTagName('script')[0];
			ref.parentNode.insertBefore(modularCSS, ref);
			ref.parentNode.insertBefore(homebrewCSS, ref);
			ref.parentNode.insertBefore(googleMaps, ref);
		}


		function afterMapsLoad()
		{

			var modularJs = document.createElement('script');
			modularJs.type = 'text/javascript';
			modularJs.src = 'js/modules.js';
			modularJs.setAttribute('onload', 'afterModulesLoad()');

			var ref = document.getElementsByTagName('script')[0];
			ref.parentNode.insertBefore(modularJs, ref);
		}
		function afterModulesLoad()
		{
//			var bootstrapJs = document.createElement('script');
//			bootstrapJs.type = 'text/javascript';
//			bootstrapJs.src = 'js/bootstrap.min.js';



			$.ga.load("<?php print $GLOBALS['ga_account']?>");

			var homebrewJs = document.createElement('script');
			homebrewJs.type = 'text/javascript';
			homebrewJs.src = 'js/tndrbox.js';

			var ref = document.getElementsByTagName('script')[0];
//			ref.parentNode.insertBefore(bootstrapJs, ref);
			ref.parentNode.insertBefore(homebrewJs, ref);
		}

	</script>

	<!-- minimized javascript -->

	</body>

</html>