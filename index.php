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
require('includes/front_page.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body

$title = "Welcome";
$result = array();
$p_flag = 0;

$result = get_most_popular_tags(1);
$tag_example = "eg. \"".$result[0]['tag']."\"";

$category_selection = "Categories";

if(isset($_GET['p']))
  {
	$p_id = $_GET['p'];
	$query = "SELECT active, b_id FROM postings WHERE id=$p_id";
	$active_result = query_db($query);
	extract($active_result[0]);
	if($active == 1)
	  {
		$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE id='$p_id'";
	  }
	else
	  {
		$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE b_id=$b_id AND active=1";
	  }
	$p_flag = 1;
	$result = query_db($query);
	$result['p_flag'] = 1;
	array_push($result, default_front_page_posts());
  }
elseif(isset($_GET['b']))
  {
	$b_id = $_GET['b'];
	$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE b_id=$b_id and active=1";
	$p_flag = 1;
	$result = query_db($query);
	$p_id = $result[0]['id'];
	$result['p_flag'] = 1;
	array_push($result, default_front_page_posts());
  }
elseif(isset($_GET['tag']))
  {
	$set_tag_id = $_GET['tag'];
	$title = get_tag($set_tag_id);

	if($set_tag_id > 0)
	  {
		$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE (tag_2='$set_tag_id' OR tag_3='$set_tag_id') AND active=1";
		$result = query_db($query);
		$i=0;
		$tag_example = $title;
	  }
	else
	  {
			$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE tag_1=$set_tag_id AND active=1";
			$result = query_db($query);
		$category_selection = $title;
	  }
  }
else
  {
	$result = default_front_page_posts();
  }




$postings = format_rows($result);


//head
$GLOBALS['header_html_title'] = "tndrbox - $title";
$GLOBALS['header_scripts'] = "
<link rel='stylesheet' type='text/css' href='css/jquery-ui.css' media='all'>
<script src='js/jquery-ui.js'></script>
<script src='js/index.js'></script>";

if($p_flag == 1)
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
	global $postings, $tag_example, $category_selection;
	echo "
		<div id='postings-header' class='row'>
			<div class='btn-group span4' style='padding-left:10px'>
				<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'>
					".($category_selection != "Categories" ? "<img src='images/$category_selection.svg' width='20'> &nbsp":"")."$category_selection
					<span class='caret'></span>
				</a>
				<ul class='dropdown-menu'>";
	$count = 0;
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


			echo "
					<li><a href='?tag=$id'><img src='images/$tag.svg' width='20'> &nbsp &nbsp $tag</a></li>";
		  }
	  }
	echo "
				</ul>
	   		</div>

				

			<div class='span4'>
					<form action='scripts/alpha_to_numeric_tag.php' method='get' class='form form-search form-inline span12'>

							<input type='text' id='tag-search' name='tag-search' class='search-query span4' placeholder='$tag_example'>

					</form>
			</div>
			<div class='span4'>
			<div class='btn-group pull-right' style='padding-right:10px'>
				<button title='Tiles' class='btn disabled' href='#'><i class='icon-th-large'></i></button>
				<button title='List coming soon...' class='btn disabled' href='#'><i class='icon-list'></i></button>";
	/*				<button title='Map coming soon...' class='btn disabled' href='#'><i class='icon-globe'></i></button>*/

	echo "
			</div>
			</div>
		</div>
		<div id='postings-container' class=''>";
	print_formatted_rows($postings);
	echo "
		</div>

<div id='box'>
	<img src='images/box-L.png'><img id='middle-box' src='images/box-M.png'><img src='images/box-R.png'>
</div>

	<div id='post-modal' class='modal hide fade white-bg' tabindex='-1' role='dialog' aria-hidden='true'>
		<div id='modal-loading' class='centered'>
			<img src='images/loading.gif'><!--Thanks http://www.loadinfo.net -->
		</div>
	</div> ";
  }
?>