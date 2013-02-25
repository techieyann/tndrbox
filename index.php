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

//head
$GLOBALS['header_html_title'] = "tndrbox";
$GLOBALS['header_scripts'] = "
		<script src='js/index.js'></script>
		<script src='js/posting_list.js'></script>
		<script type='text/javascript' src='https://maps.googleapis.com/maps/api/js?key=AIzaSyD0LQT5KDi_tPDcJPP8Rxlj6hOdifAyNO4&sensor=true'></script>
		<script>";

if($post_flag)
  {
		$GLOBALS['header_scripts'] .= "
			$(document).ready(function(){
				loadModal(".$result[0]['id'].");
			});";
  }

$GLOBALS['header_scripts'] .= "

			var postings = $json_postings;
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
			<div id='postings-header' class='rounded-top'>
				<ul class='inline short'>
					<li><p class='white'>View:</p></li>
					<li>
			<div class='btn-group'>
				<button title='Tiles' id='tile' class='format-button btn disabled' href='#'><i class='icon-th-large'></i></button>
				<button title='List' id='list' class='format-button btn' href='#'><i class='icon-list'></i></button>
			</div>
					</li>
					<li><p class='white'>Filter:</p></li>
					<li>
						<form class='form-inline form-inline-margin-fix'>
							<div class='btn-group'>
								<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'>
									".($category_selection != "Category" ? "<img src='images/icons/$category_selection.png' width='35'> &nbsp":"")."$category_selection
									<span class='caret'></span>
								</a>
								<ul class='dropdown-menu'>";
	$count = 0;

	parse_str($_SERVER['QUERY_STRING'], $query_string);

	$categories = get_active_categories();

	foreach($categories as $category)
	  {
		extract($category);
		if($tag != $category_selection)
		  {
			if($count++ > 0)
			  {
				echo "
									<li class='divider'></li>";
			  }

			$query_string['cat'] = $id;
			$href = http_build_query($query_string);
	
			echo "
									<li><a href='?$href'><img src='images/icons/$tag.png' width='35'> &nbsp &nbsp $tag</a></li>";
		  }
	  }
	echo "
								</ul>
							</div><!-- .btn-group -->
			
							<div class='input-prepend'>
								<span class='add-on'><i class='icon-calendar'></i></span>
								<input type='text' id='date-select' name='date-select' class='span1' placeholder='$date'>
							</div><!-- .input-prepend -->

							<div class='input-prepend'>
								<span class='add-on'><i class='icon-tag'></i></span>	
								<input type='text' id='tag-search' name='tag-search' class='span4' placeholder='$tag_example'>
							</div><!-- .input-prepend -->

						</form>
					</li>


			<li class='pull-right'>
			<button title='Map' id='map-button' class='btn' href='#'><i class='icon-globe'></i></button>
			</li>
				</ul>
			</div><!-- #postings-header -->
			<div id='postings-header-filler'>
			</div>

			<div id='postings-container' class='tile'>

			</div><!-- #postings-container -->

			<div id='box' class='hidden-phone'>
				<img src='images/box-L.png'><img id='middle-box' src='images/box-M.png'><img src='images/box-R.png'>
			</div><!-- #box -->

			<div id='post-modal' class='modal hide fade' tabindex='-1' role='dialog' aria-hidden='true'>
				<div id='modal-loading' class='centered'>
					<img src='images/loading.gif'><!--Thanks http://www.loadinfo.net -->
				</div><!-- #modal-loading -->
			</div><!-- #post-modal -->";
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