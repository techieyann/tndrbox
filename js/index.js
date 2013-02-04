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

	$('#tag-search').focus();

	$('#tag-search').autocomplete({
		source: 'scripts/search_tag?active=1',
		focus: function(event, ui){
			$('#tag-search').val(ui.item.label);
			return false;
		},
		select: function(event, ui){
			window.location = ('?tag='+ui.item.value);
			return false;
		}
	});

	$('#tag-search').keypress(function(e){
		if(e.keyCode == 13) //enter key
		{
			return false;
		}
	});


	$('#footer').css('background', "#eee");





//http://stackoverflow.com/questions/2907367/have-a-div-cling-to-top-of-screen-if-scrolled-down-past-it


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
