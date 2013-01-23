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

if(isset($_GET['p']))
  {
	$p_id = $_GET['p'];
	$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE id='$p_id'";
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
		$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE (tag_1='$set_tag_id' OR tag_2='$set_tag_id' OR tag_3='$set_tag_id') AND active=1 LIMIT 20";
		$result = query_db($query);
		$i=0;
	  }
	else
	  {
		$query = "SELECT id FROM business WHERE category=$set_tag_id AND active_post=1 LIMIT 20";
		$business_result = query_db($query);
		$result = array();
		foreach($business_result as $business)
		  {
			$id = $business['id'];
			$query = "SELECT id, title, date, photo, tag_1, tag_2, tag_3 FROM postings WHERE b_id=$id AND active=1";
			$post_result = query_db($query);
			array_push($result, $post_result[0]);
		  }
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
<script type='text/javascript'>
$(document).ready(function(){

$('#tag-search').autocomplete({source:'includes/tag_search.php'});


$('.modal-trigger').click(function(e){

	var id = $(this).attr('href');
	var url = 'partials/modal' + id;

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
	history.pushState(null, null, id);
	});
	

	//prevent natural click behavior
	e.preventDefault();
});";


if($p_flag == 1)
  {
		$GLOBALS['header_scripts'] .= "
	var url = 'partials/modal?id=".$result[0]['id']."';

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
	});";
  }
	$GLOBALS['header_scripts'] .= "
});
</script>";

$GLOBALS['categories'] = get_active_categories();
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
	global $postings;
	echo "
	<div id='post-modal' class='modal hide fade content' tabindex='-1' role='dialog' aria-hidden='true'>
		<div id='modal-loading' class='centered'>
			<img src='images/loading.gif'><!--Thanks http://www.loadinfo.net -->
		</div>
	</div>";
	print_formatted_rows($postings);
  }
?>