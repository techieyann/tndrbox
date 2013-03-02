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

	var windowHeight = $(window).innerHeight();

	$('.modal-body').css('height', windowHeight - 200);

};

function removeParameter(url, param, value) {
    // Using a positive lookahead (?=\=) to find the
    // given parameter, preceded by a ? or &, and followed
    // by a = with a value after than (using a non-greedy selector)
    // and then followed by a & or the end of the string
    var val = new RegExp('(\\?|\\&)' + param + '=.*?(?=(&|$))'),
        qstring = /\?.+$/;

    // Check if the parameter exists
    if (val.test(url))
    {
        // if it does, replace it, using the captured group
        // to determine & or ? at the beginning
        return url.replace(val, '');
    }
    else if (qstring.test(url))
    {
        // otherwise, if there is a query string at all
        // add the param to the end of it
        return url;
    }
    else
    {
        // if there's no query string, add one
        return '';
    }
}

function addParameter(url, param, value) {
    // Using a positive lookahead (?=\=) to find the
    // given parameter, preceded by a ? or &, and followed
    // by a = with a value after than (using a non-greedy selector)
    // and then followed by a & or the end of the string
    var val = new RegExp('(\\?|\\&)' + param + '=.*?(?=(&|$))'),
        qstring = /\?.+$/;

    // Check if the parameter exists
    if (val.test(url))
    {
        // if it does, replace it, using the captured group
        // to determine & or ? at the beginning
        return url.replace(val, '$1' + param + '=' + value);
    }
    else if (qstring.test(url))
    {
        // otherwise, if there is a query string at all
        // add the param to the end of it
        return url + '&' + param + '=' + value;
    }
    else
    {
        // if there's no query string, add one
        return url + '?' + param + '=' + value;
    }
}//http://stackoverflow.com/questions/7640270/adding-modify-query-string-get-variables-in-a-url-with-javascript
function resetFilters()
{
		window.location = 'index'
}

$(document).ready(function(){
	var windowHeight = $(window).innerHeight();
	$('.modal-body').css('height', windowHeight - 200);

	$('#tag-search').autocomplete({
		source: 'scripts/search_tag?active=1',
		focus: function(event, ui){
			$('#tag-search').val(ui.item.label);
			return false;
		},
		select: function(event, ui){
			var search = window.location.search;
			var uri = removeParameter(search, 'p');
			uri = addParameter(uri, 'tag', ui.item.value);

			window.location = (uri);
			return false;
		}
	});

/*	$('#date-select').datepicker({
		dateFormat: 'yy-mm-dd',
		minDate: 0,
		maxDate: '+28D',
		onSelect: function(dateText, ui){
			var search = window.location.search;
			var uri = addParameter(search, 'date', dateText);
			window.location = (uri);
			return false;
		}
	});*/

	$('#tag-search').keypress(function(e){
		if(e.keyCode == 13) //enter key
		{
			return false;
		}
	});


	$('#footer').css('background', "#F4F2E6");
	$('#footer > p').removeClass('white');





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
