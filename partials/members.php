<?php

require('../includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

disconnect_from_db();
?>


	<script>
		$(document).ready(function(){
			$('#login-link').removeClass('active').hide();
			$('#settings-link').addClass('active').show();
			$('#logout-link').show();	
			$('#box-js-content').show();
	$('#settings-nav>li a').click(function(e){
		var view = $(this).attr('href');
		$.bbq.removeState('id');
		$.bbq.pushState('view='+view);
		e.preventDefault();
	});


	$('#business-search').autocomplete({
		source:'scripts/search_business.php?',
		focus: function(event, ui){
			$('#business-search').val(ui.item.label);
			return false;
		},
		select: function(event, ui){
			$.bbq.pushState('view=posts&b_id='+ui.item.value);
			$('#business-search').val('');
			return false;
		}
	});


	});
	</script>
	<div id='members' class='row-fluid'>
		<div class='span3'>
			<ul id='settings-nav' class='nav nav-pills nav-stacked content'>
				<li id='new-post-li'>
					<a class='nav-link' href='new-post'><i class='icon-plus'></i> New Post</a>
				</li>
	<?php 
/*			if($_SESSION['type'] != 1)
			{
				echo "
				<li id='new-event-li'>
					<a class='nav-link' href='new-event'><i class='icon-plus'></i> New Parent Event</a>
				</li>";
			}*/
			if(check_admin())
			{
				echo "
				<li id='new-business-li'>
					<a class='nav-link' href='new-business'><i class='icon-plus'></i> New Business</a>
				</li>
				<li id='new-user-li'>
					<a class='nav-link' href='new-user'><i class='icon-plus'></i> New User</a>
				</li>
				<li id='profile-li'>
					<div class='input-prepend'>
						<span class='add-on'><i class='icon-search'></i></span>
						<input id='business-search' type='text' class='span10' placeholder='Businesses'>
					</div>
				</li>";
			}
			else
			{
				echo "
				<li id='posts-li'>
					<a class='nav-link' href='posts'><i class='icon-folder-open'></i> Posts</a>
				</li>
				<li id='profile-li'>
					<a class='nav-link' href='edit-profile'><i class='icon-cog'></i> Member Info</a>
				</li>";
			} ?>
			</ul>
		</div>
		<div id='settings-content' class='span9 content rounded'>
			<div id='loading' class='centered'>
				<img src='images/loading.gif' alt='Loading...'>
			</div>
		</div>
	</div>
