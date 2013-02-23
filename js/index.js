var formattedPostings = [];

var url_location = getCookie('location');
var str_location = decodeURIComponent(url_location);

var json_location = JSON.parse(str_location);

function resizeContainer(){
	var container = document.getElementById('body-container');
	var postings = document.getElementById('postings');
	var postings_container = document.getElementById('postings-container');

	var map_button = document.getElementById('map-button');
	container.removeAttribute('class', 'container');

	container.style.marginLeft = '70px';

	var window_width = $(window).innerWidth();
	var window_height = $(window).innerHeight();

	var header_height = $('#postings-header').height();
	var to_top_height = $('#postings-header').offset().top;

	var box = document.getElementById('box');
	box.removeAttribute('style', 'left');

	var middle_box = document.getElementById('middle-box');
	var width = (window_width-155);

	container.style.width= width+'px';
	middle_box.style.width = (width)+'px';			

	postings_width = width-15;	

	$('#map-canvas').width(width*.38-15);
	$('#map-canvas').height(window_height-header_height-50);

	var map_active_flag = false;
	if(map_button.classList.contains('disabled'))
	{
		map_active_flag = true;
		var newWidth = (width*.62);
		$('#postings').width(newWidth);
		postings_width = (width*.62)-15;
	}
	else
	{
		$('#postings').width(width-15);
	}
	$('#postings-header').width(width);


	
	
	//desktop
	if(1400 < window_width)
	{
		if(map_active_flag)
		{
			$('.front-page-button').width(((postings_width)/5)-10+'px');
		}
		else
		{
			$('.front-page-button').width(((postings_width)/6)-10+'px');
		}
	}
	else if(1200 < window_width)
	{
		if(map_active_flag)
		{
			$('.front-page-button').width(((postings_width)/4)-10+'px');
		}
		else
		{
			$('.front-page-button').width(((postings_width)/5)-10+'px');
		}
	}
	//horizontal tablet
	else if(980 < window_width)
	{
		if(map_active_flag)
		{
			$('.front-page-button').width(((postings_width)/3)-10+'px');
		}
		else
		{
			$('.front-page-button').width(((postings_width)/4)-10+'px');
		}

	}
	//vertical tablet
	else if(768 < window_width)
	{
		if(map_active_flag)
		{
			$('.front-page-button').width(((postings_width)/2)-10+'px');
		}
		else
		{
			$('.front-page-button').width(((postings_width)/3)-10+'px');
		}
	}
	//phones
	else
	{
		container.setAttribute('class', 'container');
		container.style.marginLeft = '0';
		container.removeAttribute('style', 'width');
		$('.front-page-button').css('width','');
	}


}

window.onresize = function(){
	resizeContainer();
	if(document.getElementById('postings').classList.contains('masonry'))
	{
		$('#postings').masonry('reload');
	}
};

function setPosition(position){
	var latitude = position.coords.latitude;
	var longitude = position.coords.longitude;

	var json_location = {"lat": latitude, "lon": longitude, "source": "html5"};
	var str_location = JSON.stringify(json_location);
	
	setCookie("location", str_location, 8);
}


function map_initialize() {
	var myLatLon = new google.maps.LatLng(json_location.lat, json_location.lon);
	var mapOptions = {
		zoom: 13,
		center: myLatLon,
		mapTypeId: google.maps.MapTypeId.HYBRID
	}
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

	var myLocationMarker = new google.maps.Marker({
		position: myLatLon,
		map: map,
		title:'Here I am!'
	});
}

function loadModal(id){
	var url = 'partials/modal?p='+id;
		
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


$(document).ready(function(){
	$('#map-canvas').hide();
	$('#postings-container').load('partials/posting_list');

	$('.format-button').on('click', function(e){
		var this_id = $(this).attr('id');
		if(!document.getElementById(this_id).classList.contains('disabled'))
		{
			$('.format-button').removeClass('disabled');
			$('.format-button').each(function(i, obj){
				$('#postings-container').removeClass($(obj).attr('id'));
			});
			$(this).addClass('disabled');

			$('#postings-container').addClass(this_id);
			reformatPosts(this_id);
			displayPosts(this_id);
		}
	});

	$('#map-button').click(function(e){
		var map_button = document.getElementById('map-button');
		if(map_button.classList.contains('disabled'))
		{
			$('#map-canvas').hide('slow');
			$('#map-button').removeClass('disabled');
			resizeContainer();
			if(document.getElementById('postings').classList.contains('masonry'))
			{
				$('#postings').masonry('reload');
			}

		}
		else
		{
			$('#map-button').addClass('disabled');
			resizeContainer();
			if(document.getElementById('postings').classList.contains('masonry'))
			{
				$('#postings').masonry('reload');
			}
			$('#map-canvas').show('slow', function(){	
				map_initialize();
			});
		}
	});

	if(Modernizr.geolocation && json_location.source !='user')
	{
		navigator.geolocation.getCurrentPosition(setPosition);//, {enableHighAccuracy: true, maximumAge:120000});
	}



	$('#tag-search').focus();

	$('#tag-search').autocomplete({
		source: 'scripts/search_tag?active=1',
		focus: function(event, ui){
			$('#tag-search').val(ui.item.label);
			return false;
		},
		select: function(event, ui){
			var search = window.location.search;
			var uri = addParameter(search, 'tag', ui.item.value);
			window.location = (uri);
			return false;
		}
	});

	$('#date-select').datepicker({
		dateFormat: 'yy-mm-dd',
		minDate: 0,
		maxDate: '+28D',
		onSelect: function(dateText, ui){
			var search = window.location.search;
			var uri = addParameter(search, 'date', dateText);
			window.location = (uri);
			return false;
		}
	});

	$('#tag-search').keypress(function(e){
		if(e.keyCode == 13) //enter key
		{
			e.preventDefault();
			return false;
		}
	});

	var theWindow = $(window);
	var postings_header = $('#postings-header');
	var postings_header_filler = 	$('#postings-header-filler')
	var to_top = postings_header.offset().top;
	var header_height = postings_header.height();
	postings_header_filler.hide();
	postings_header_filler.height(header_height);

	theWindow.scroll(function(){
		if(theWindow.scrollTop()>to_top)
		{
			postings_header.addClass('sticky');
			postings_header.removeClass('rounded-top');
			postings_header_filler.show();
//			$('#map-canvas').addClass('sticky');
			$('#map-canvas').css({'position' :' fixed', 'top': header_height + 12 + 'px', 'left' : .62*postings_header.width()+85+'px'});
			
		}
		else
		{
			postings_header.addClass('rounded-top');
			postings_header.removeClass('sticky');
			postings_header_filler.hide();
//			$('#map-canvas').removeClass('sticky');
			$('#map-canvas').css({'position': 'relative', 'top': '', 'left':''});
			
		}

	});

	window.onpopstate = function(e){
		var id = e.state;
		if(id == null)
		{	
			$('#post-modal').modal('hide');
		}
		else
		{
			loadModal(id);
		}
	}

	var footer = document.getElementById('footer');
	footer.style.background = '#F4F2E6';
	$('#footer').children('p').removeClass('white');
});



function getCookie(name){
	var search_str = name + "=";
	var cookies = document.cookie.split(';');
	
	for(var i=0; i<cookies.length; i++)
	{
		var cookie = cookies[i];
		while(cookie.charAt(0) == ' ')
		{
			cookie = cookie.substring(1, cookie.length);
		}
		if(cookie.indexOf(search_str) == 0)
		{
			return cookie.substring(search_str.length, cookie.length);
		}
	}
	return null;
}

function setCookie(name, value, hours)
{
	var date = new Date();
	date.setTime(date.getTime()+(hours*3600000));
	var expire = date.toGMTString();
	var new_cookie = name+ "=";
	new_cookie += value + "; expires="; 
	new_cookie += expire + "; path=/";

	//set cookie
	document.cookie = new_cookie;
}
