<?php
/***********************************************
file: partials/posts.php
creator: Ian McEachern

This partial displays the posts authored by the
user
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');


connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

require('prints.php');

echo "
			<div id='js-content' class='span12'>
			<script>
				$(document).ready(function(){
					$('.post-link').click(function(e){
						e.preventDefault();

						var view = $(this).attr('href');
						var id = $(this).attr('id');

						loadContentByURL(view, id);
				   	});
					$('.accordion').accordion({
						collapsible:true,
						active:false,
						heightStyle:'content'
					});
				});

			</script>";

if(check_admin())
  {
	$m_id = $GLOBALS['m_id'];
	$query = "SELECT DISTINCT b_id FROM postings WHERE a_id=$m_id";
	$businesses = query_db($query);
	echo "
			<div class='accordion'>";
	foreach($businesses as $business)
	  {
		$b_id = $business['b_id'];
		$query = "SELECT name FROM business WHERE id=$b_id";
		$result = query_db($query);
		$name = $result[0]['name'];
		echo "
				<h3>$name</h3>
				<div class='posts'>";
		print_business_posts($b_id);
		echo "
				</div>";
	  }	
	echo "
			</div>";
  }
else
  {
	$b_id = $GLOBALS['b_id'];
	print_business_posts($b_id);
  }

echo "
			</div>";

disconnect_from_db();
?>