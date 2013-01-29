function getParameterByName(name)
{
  name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
  var regexS = "[\\?&]" + name + "=([^&#]*)";
  var regex = new RegExp(regexS);
  var results = regex.exec(window.location.search);
  if(results == null)
    return "";
  else
    return decodeURIComponent(results[1].replace(/\+/g, " "));
}//http://stackoverflow.com/questions/901115/how-can-i-get-query-string-values

function smartLoad(url)
{
	//hide content div
	$('#js-content').hide();
	//show loading div
	$('#loading').show();
	//call load
	$('#settings-content').load(url, function(){
		//hide loading div
		$('#loading').hide();

		//show content div
		$('#js-content').show();
	});
}

function smartPushState(view, id)
{
	if(typeof id == 'undefined')
	{
		id = '';
	}

	
	var url = 'settings?view='+view;
	if(id!='')
	{
		url = url + '&id=' + id;
	}

	history.pushState([view, id], null, url);
}

function loadContentByURL(view, id){
	if(typeof id == 'undefined')
	{
		id = '';
	}

	var append_string = '';
	if(id != '')
	{
		append_string = "?id=" + id;
	}
	
	if(view == '')
	{
		view = 'posts';
	}
	
	if(view == 'new_post')
	{
		smartLoad('partials/new_post_form');
	}

	if(view == 'edit_post')
	{
		smartLoad('partials/edit_post_form'+append_string);
	}

	if(view == 'delete_post')
	{	
		$.ajax({
			url:'scripts/delete_post',
			data: {'id': id},
			type: 'get'
		});
		smartLoad('partials/posts');
	}

	if(view == 'deactivate_post')
	{	
		$.ajax({
			url:'scripts/deactivate_post',
			data: {'id': id},
			type: 'get'
		}).done(function(){
			smartLoad('partials/posts');
		});
	}

	if(view == 'posts')
	{
		smartLoad('partials/posts');
	}

	if(view == 'edit_profile')
	{
	
		smartLoad('partials/edit_profile_forms'+append_string);
	}

	if(view == 'new_business')
	{
		smartLoad('partials/new_business_form');
	}

	if(view == 'new_user')
	{
		smartLoad('partials/new_user_form');
	}
	smartPushState(view, id);
}

window.onpopstate = function(e){
	var view = e[0];
	var id = e[1];
	if(id == null)
	{	
		id='';
	}
	if(view == null)
	{
		view='';
	}
	loadContentByURL(view, id);
};

$(document).ready(function(){
	var view = getParameterByName('view');

	var id = getParameterByName('id');
	loadContentByURL(view, id);
	
	$('.nav-link').click(function(e){
		var view = $(this).attr('href');
		loadContentByURL(view);
		e.preventDefault();
	});

	

	$('#edit-tag1').autocomplete({source:'includes/tag_search.php'});
	$('#edit-tag2').autocomplete({source:'includes/tag_search.php'});
	$('#edit-tag3').autocomplete({source:'includes/tag_search.php'});


	$('#edit-date').datepicker();
	$( "#date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );
	$( "#edit-date" ).datepicker( "option", "dateFormat", "yy-mm-dd" );

	$('#post-accordion').accordion({heightStyle:'content', active:false, collapsible:true});
 });