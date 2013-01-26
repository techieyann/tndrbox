<?php
/***********************************************
file: partials/edit_profiles_form.php
creator: Ian McEachern

This partial displays the edit forms for the 
indicated business and user
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

require('prints.php');

$id = "";

echo "
		<script>
			$(function(){
				$('.accordion').accordion({
					collapsible:true,
					active:false,
					heightStyle:'content'
				});
			});
		</script>
		<div id='js-content' class='accordion'>
			<h3>Edit Business Information</h3>
			<div>";

print_edit_business_form($id);
echo "
			</div>
			<h3>Edit User Information</h3>
			<div>";

print_edit_user_form($id);

echo "
			</div>
		</div>";

disconnect_from_db();
?>