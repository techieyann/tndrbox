$(document).ready(function(){
$('#tag1').autocomplete({source:'includes/tag_search.php'});
$('#tag2').autocomplete({source:'includes/tag_search.php'});
$('#tag3').autocomplete({source:'includes/tag_search.php'});
$('#edit-tag1').autocomplete({source:'includes/tag_search.php'});
$('#edit-tag2').autocomplete({source:'includes/tag_search.php'});
$('#edit-tag3').autocomplete({source:'includes/tag_search.php'});

$('#date').datepicker();
$('#edit-date').datepicker();

	$('#admin-accordion').accordion({heightStyle:'content', active:false, collapsible:true});
	$('#admin-posts').accordion({heightStyle:'content', active:false, collapsible:true});
	$('#admin-active-posts').accordion({heightStyle:'content', active:false, collapsible:true});
	$('#admin-old-posts').accordion({heightStyle:'content', active:false, collapsible:true});
});