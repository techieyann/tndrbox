window.onload = function() {
	var wall = new Masonry(document.getElementById('masonry-container'));
}

$(document).ready(function(){

	$('#tag-search').autocomplete({source:'includes/tag_search.php'});

	$('#footer').css('background', "url('../images/bg-tile.png') #323232");
	$('#footer').css('color', '#eee');

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
