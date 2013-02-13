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
	$p_id = $_GET['p'];
	$query = "SELECT active, b_id FROM postings WHERE id=$p_id";
	$active_result = query_db($query);
	if(isset($active_result[0]))
	  {
		extract($active_result[0]);
		if($active == 1)
		  {
			$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3, lat, lon FROM postings WHERE id='$p_id'";
		  }
		else
		  {
			$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3, lat, lon FROM postings WHERE b_id=$b_id AND active=1";
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
	$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3, lat, lon FROM postings WHERE b_id=$b_id and active=1";

	$result = query_db($query);
	
	if(isset($result[0]))
	  {
		$post_flag = true;
		$p_id = $result[0]['id'];
		$result['post_flag'] = 1;
	  }
  }
array_push($result, default_front_page_posts());
$json_postings = json_encode($result);

//head
$GLOBALS['header_html_title'] = "tndrbox";
$GLOBALS['header_scripts'] = "
		<script src='js/index.js'></script>
		<script>
			var postings = $json_postings;
			$(document).ready(function(){
				$('#postings-container').load('partials/posting_list');
			});
		</script>";

if($post_flag)
  {
		$GLOBALS['header_scripts'] .= "
		<script type='text/javascript'> 

			$(document).ready(function(){
				var url = 'partials/modal?p=".$result[0]['id']."';

				//hide content divs
				$('#modal-header').hide();
				$('#modal-body').hide();
				$('#modal-footer').hide();	

				//show modal
				$('#post-modal').modal('show');

				//display loading div
				$('#modal-loading').show();

				//call load
				$('#post-modal').load(url, function(){
				$('#modal-loading').hide();

				$('.share-button').popover({
					html:true
				});
	
				$('#modal-header').show();
				$('#modal-body').show();
				$('#modal-footer').show();	
				});
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
								<span class='add-on'><i class='icon-search'></i></span>	
								<input type='text' id='tag-search' name='tag-search' class='span4' placeholder='$tag_example'>
							</div><!-- .input-prepend -->

						</form>
					</li>
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

			<div id='postings-container' class=''>";
	print_postings($postings);
	echo "

			</div><!-- $postings-container -->

			<div id='box'>
				<img src='images/box-L.png'><img id='middle-box' src='images/box-M.png'><img src='images/box-R.png'>
			</div><!-- #box -->

			<div id='post-modal' class='modal hide fade white-bg' tabindex='-1' role='dialog' aria-hidden='true'>
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
		$formatted_postings[$i]['post'] = $post;

		$looper = $raw_posts[1];
	  }
	foreach($looper as $post)
	  {
		if($post['id'] != $processed_id)
		  {
			$i++;
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

		$tag_2 = $post['tag_2'];
		$tag_3 = $post['tag_3'];

 
		$tags[2] = get_tag($tag_2); 
		$tags[3] = get_tag($tag_3);

		echo "
								<div class='front-page-button-text'>
									<h4>".$post['title']."</h4>";

		$date = format_date($id);

		if($date != "")
		  {
			echo "
									<p>$date</p>";
		  }
		echo "
				  					<ul class='inline centered'>
	   									<li class='tag'>$tags[2]</li>
										<li class='tag'>$tags[3]</li>
									</ul>
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