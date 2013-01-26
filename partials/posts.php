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

$m_id = $GLOBALS['m_id'];
$query = "SELECT id, title FROM postings WHERE a_id=$m_id AND active=1";
$active_posts = query_db($query);

$query = "SELECT id, title FROM postings WHERE a_id=$m_id AND active=0";
$old_posts = query_db($query);
echo "
			<div id='js-content' class='span12'>
				<div class='row-fluid'>
					<div class='span6'>
						<h3>Active Post:</h3>
					</div>
					<div class='span6'>
						<ul class='inline pull-right'> 
							<li><h3><a href='#edit'>Edit</a></h3></li>
							<li><h3><a href='#deactivate'>Deactivate</a></h3></li>
							<li><h3><a href='#delete'>Delete</a></h3></li>
						</ul>
					</div>
				</div>";

foreach($active_posts as $active_post)
  {
	echo "
			<div class='span12 modal white-bg' style='position:relative; left:auto; right:auto; margin:0; max-width:100%;'>";
	print_modal($active_post['id']);
	echo "
			</div>";
  }
foreach($old_posts as $old_post)
  {
	extract($old_post);
	echo "
			<div class='row-fluid'>
				<div class='span8'>
				<h4>$title</h4>
				</div>
				<div class='span4'>
		   			<ul class='inline pull-right'> 
						<li><h4><a href='#edit'>Edit</a></h4></li>
						<li><h4><a href='#deactivate'>Deactivate</a></h4></li>
						<li><h4><a href='#delete'>Delete</a></h4></li>
					</ul>
				</div>
			</div>";
  }
echo "
			</div>";

disconnect_from_db();
?>