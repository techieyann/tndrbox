window.onload = function(){
	var width = 0;
	if($(window).width() > 1200)
	{
		width = 12;
	}
	$('#postings').masonry({
			itemSelector: '.front-page-button',
		isAnimated: true,
		gutterWidth: width
		});

}

window.onresize = function(){
	$('#postings').masonry('reload');
	if($(window).width() < 1200)
	{
		$('#postings').masonry('option', {gutterWidth: 0});
	}
	else
	{
		$('#postings').masonry('option', {gutterWidth: 13});
	}
};

$(document).ready(function(){

	$('#tag-search').autocomplete({source:'includes/tag_search.php'});

	$('#footer').css('background', "#eee");





/*	$('#box').hover(function(){
		$('#box').css('bottom', '0px');
	},function(){
		$('#box').css('bottom', '-80px');
	});
*/

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
	
			var stateObj = id;	
			history.pushState(stateObj, null, id);
		});
	

		//prevent natural click behavior
		e.preventDefault();
	});
	
});

window.onpopstate = function(e){
	var id = e.state;
	if(id == null)
	{	
		$('#post-modal').modal('hide');
	}
	else
	{
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
		});
	}
};
