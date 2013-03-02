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

$title = "";
$result = array();
$post_flag = false;

$result = get_most_popular_tags(1);

$tag_example = "Tag";
$category_selection = "Category";
$date = "Date";

if(isset($result[0]))
  {
	$tag_example .= ", eg. \"".$result[0]['tag']."\"";
  }

if(isset($_GET['p']))
  {
	$p_id = $_GET['p'];
	$query = "SELECT active, b_id FROM postings WHERE id=$p_id";
	$active_result = query_db($query);
	if(isset($active_result[0]))
	  {
		extract($active_result[0]);
		if($active == 1)
		  {
			$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE id='$p_id'";
		  }
		else
		  {
			$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE b_id=$b_id AND active=1";
		  }
	
		$result = query_db($query);
		if(isset($result[0]))
		  {
			$post_flag = true;
			$result['post_flag'] = 1;
		  }
	  }
  }
elseif(isset($_GET['b']))
  {
	$b_id = $_GET['b'];
	$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE b_id=$b_id and active=1";

	$result = query_db($query);
	
	if(isset($result[0]))
	  {
		$post_flag = true;
		$p_id = $result[0]['id'];
		$result['post_flag'] = 1;
	  }
  }



	$tag_flag = false;
	$cat_flag = false;
	$date_flag = false;
	$title = "";

	if(isset($_GET['date']))
	  {
		$date = $_GET['date'];
		$title = $date;
		$date_flag = true;
	  }
	if(isset($_GET['cat']))
	  {
		$set_cat_id = $_GET['cat'];
		$category_selection = get_tag($set_cat_id);
		$title .= ($date_flag ? " & " : "" ).$category_selection;
		
		$cat_flag = true;
	  }
	if(isset($_GET['tag']))
	  {
		$set_tag_id = $_GET['tag'];
		$tag_example = get_tag($set_tag_id);
		$title .= ($cat_flag || $date_flag ? " & " : "").$tag_example;

		$tag_flag = true;
	  }

	if($tag_flag || $cat_flag || $date_flag)
	  {

		$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE"
		  .($cat_flag ? " tag_1=$set_cat_id" : "" )
		  .($cat_flag && ($tag_flag || $date_flag) ? " AND" : "" )
		  .($tag_flag ? " (tag_2='$set_tag_id' OR tag_3='$set_tag_id')" : "" )
		  .($tag_flag && $date_flag ? " AND" : "" )
		  .($date_flag ? " date=$date" : "" )
		  ." AND active=1 ORDER BY posting_time DESC";
		$filtered_results = query_db($query);
	  }
	else
	  {
		$filtered_results = default_front_page_posts();
	  }

if($post_flag)
  {
	array_push($result, $filtered_results);
  }
else
  {
	$result = $filtered_results;
  }

$postings = format_posts($result);

//head
$GLOBALS['header_html_title'] = "tndrbox - ".($title != "" ? "$title":"Information on Tap");
$GLOBALS['header_scripts'] = "
		<script src='js/index.js'></script>";

if($post_flag)
  {
		$GLOBALS['header_scripts'] .= "
		<script type='text/javascript'> 
			$(document).ready(function(){
				loadModal('?p=".$result[0]['id']."');
			});
		</script>";
		}

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
			<div id='tndrbox-welcome'>
				<h3 class='tagline'>Welcome to tndrbox, Temescal's Community Events Board</h3>
				<p>Tap into local events and happenings posted by our neighborhood for our neighborhood.</p>
			</div>
			<div id='postings-header' class='row'>
				<ul class='inline'>
					<li><p class='white'>Filter by:</p></li>
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
			$query_string['p'] = null;
			$href = http_build_query($query_string);
	
			echo "
									<li><a href='?$href'><img src='images/icons/$tag.png' width='35'> &nbsp &nbsp $tag</a></li>";
		  }
	  }
	echo "
								</ul>
							</div><!-- .btn-group -->
			


							<div class='input-prepend'>
								<span class='add-on'><i class='icon-tag'></i></span>	
								<input type='text' id='tag-search' name='tag-search' class='span4' placeholder='$tag_example'>
							</div><!-- .input-prepend -->
						</form>
					</li>
					<li>							<button class='btn' title='Reset Filters' onclick='resetFilters()'><i class='icon-remove-sign'></i></button></li>
				</ul>";

	/*			<div class='span4'>
			<div class='btn-group pull-right' style='padding-right:10px'>
				<button title='Tiles' class='btn disabled' href='#'><i class='icon-th-large'></i></button>
				<button title='List coming soon...' class='btn disabled' href='#'><i class='icon-list'></i></button>
				<button title='Map coming soon...' class='btn disabled' href='#'><i class='icon-globe'></i></button>


			</div>
			</div>*/
	echo "
			</div><!-- #postings-header -->

			<div id='postings-container' class=''>
				<div id='postings'>";
	print_postings($postings);
	echo "
				</div><!-- #postings -->
			</div><!-- $postings-container -->

			<div id='box'>
				<img src='images/box-L.png'><img id='middle-box' src='images/box-M.png'><img src='images/box-R.png'>
			</div><!-- #box -->

			<div id='post-modal' class='modal hide fade' tabindex='-1' role='dialog' aria-hidden='true'>
				<div id='modal-loading' class='centered'>
					<img src='images/loading.gif'><!--Thanks http://www.loadinfo.net -->
				</div><!-- #modal-loading -->
			</div><!-- #post-modal -->";
  }

function format_posts($raw_posts)
  {
	$i=-1;
	$looper = $raw_posts;
	$processed_id = 0;
	$formatted_postings = "";
	if(isset($raw_posts['post_flag']))
	  {
		$i++;
		$post = $raw_posts[0];
		$processed_id = $post['id'];
		$query = "SELECT name FROM postings INNER JOIN business ON business.id = postings.b_id WHERE postings.id=$processed_id";
		$result = query_db($query);
		$post['business'] = $result[0]['name'];
		$formatted_postings[$i]['post'] = $post;

		$looper = $raw_posts[1];
	  }
	foreach($looper as $post)
	  {
		$p_id = $post['id'];
		if($p_id != $processed_id)
		  {
			$i++;
			$query = "SELECT name FROM postings INNER JOIN business ON business.id = postings.b_id WHERE postings.id=$p_id";
			$result = query_db($query);
			$post['business'] = $result[0]['name'];
			$formatted_postings[$i]['post'] = $post;			
		  }
	  }

	return $formatted_postings;
  }


function print_postings($posts)
{
  if(isset($posts[0]))
	{
	foreach($posts as $post_data)
	  {
		$post = $post_data['post'];


		if($post != "filler")
		  {
		$id = $post['id'];

		$tag_1 = $post['tag_1'];
		$tags[1] = get_tag($tag_1);

		echo "
					<a href='?p=$id' class='modal-trigger'>
						<div class='span3 front-page-button'>
							<div class='front-page-button-header'>
								$tags[1]
							</div><!-- .front-page-button-header -->

							<div class='front-page-button-body'>";

		if($post['photo'] != "")
		  {
			$img_src = "images/posts/".$post['photo'];
			echo "
					   			<img src='$img_src' alt='photo for ".$post['title']."'>";

		  }



		echo "
								<div class='front-page-button-text'>
									<h4>".$post['title']."</h4>
									<p class='muted'><b>".$post['business']."</b></p>";

		$date = format_date($id);

		if($date != "")
		  {
			echo "
									<p>$date</p>";
		  }
		echo "
								</div><!-- .font-page-button-text -->
							</div><!-- front-page-button-body -->
						</div><!-- front-page-button -->
					</a><!-- .modal-trigger -->
";
		  }
		else
		  {
			echo "
			<div class='$span'>
			</div>";
		  }
	  }
	}
}
?>