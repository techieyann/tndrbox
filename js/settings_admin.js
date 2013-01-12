$(document).ready(function(){
$('#tag1').autocomplete({source:'includes/tag_search.php'});
$('#tag2').autocomplete({source:'includes/tag_search.php'});
$('#tag3').autocomplete({source:'includes/tag_search.php'});
$('#edit-tag1').autocomplete({source:'includes/tag_search.php'});
$('#edit-tag2').autocomplete({source:'includes/tag_search.php'});
$('#edit-tag3').autocomplete({source:'includes/tag_search.php'});

	$('#admin-accordion').accordion({heightStyle:'content'});
	$('#admin-posts').accordion({heightStyle:'content'});
	$('#admin-active-posts').accordion({heightStyle:'content'});
	$('#admin-old-posts').accordion({heightStyle:'content'});
});