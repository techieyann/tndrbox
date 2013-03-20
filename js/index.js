var formattedPostings = [];
var postingsFormat = 'tile';

var url_location = getCookie('location');
var str_location = decodeURIComponent(url_location);

var json_location = JSON.parse(str_location);

var to_top_initialized = false;

this.active = 0;

function resizeContainer(){
	var container = document.getElementById('body-container');
	var postings = document.getElementById('postings');
	var postings_container = document.getElementById('postings-container');
	var postings_header = document.getElementById('postings-header');
	

	var map_canvas = document.getElementById('map-canvas');

	var window_width = $(window).innerWidth();
	var window_height = $(window).innerHeight();

	var header_height = $('#postings-header').height();

	var middle_box = document.getElementById('middle-box');

	var box = document.getElementById('box');

	if($('#postings-header').offset().top != 0 && !to_top_initialized)
	{
		this.to_top = $('#postings-header').offset().top;
	}



		if(1400 < window_width)
		{
			this.postColumns = 5;
		}
		else if(1200 < window_width)
		{
			this.postColumns = 4;
		}
		//horizontal tablet
		else if(980 < window_width)
		{
			this.postColumns = 3;
		}
		else if(754 < window_width)
		{
			this.postColumns = 3;
		}
		else if(360 < window_width)
		{
			this.postColumns = 2;
		}
		else
		{
			this.postColumns = 1;
		}

	if(980 < window_width)
	{
		var width = (window_width-155); 
		var postingsWidth = (width*.62);
		var mapWidth = (width-postingsWidth-15);
		var mapHeight = window_height-header_height-45;
		var postings_width = postingsWidth-15;
		$('#map-canvas').addClass('pull-right');

		container.style.marginLeft = '70px';
		box.removeAttribute('style', 'left');
	}
	else if(754 < window_width)
	{
		var width = (window_width-155); 
		var postingsWidth = (width);
		var mapWidth = (postingsWidth-30);
		var mapHeight = (window_height-header_height)*.38;
		var postings_width = postingsWidth-15;

		$('#map-canvas').removeClass('pull-right');
		container.style.marginLeft = '70px';
		box.removeAttribute('style', 'left');
	}
	else
	{
		var width = (window_width-35);
		var postingsWidth = (width);
		var mapWidth = (postingsWidth-30);
		var mapHeight = (window_height-header_height)*.38;
		var postings_width = postingsWidth-15;

		$('#map-canvas').removeClass('pull-right');
		box.setAttribute('style', 'left:-45px');
		container.style.marginLeft = '0';
	}

	var num_columns = this.postColumns;
		if(this.postColumns > 3)
		{
			num_columns = 3;
		}

		var button_width = ((postings_width)/this.postColumns)-12;		


		$('.post-mini.button').width(button_width+'px')
	container.style.width= width+'px';
	box.style.width = width+130+'px';
	$('#postings-header').width(width);
	$('#map-canvas').width(mapWidth);
	$('#map-canvas').height(mapHeight);

	middle_box.style.width = (width)+'px';

	$('#postings').width(postingsWidth);
	$('.post-mini.li').width(postingsWidth-30);
	$('#post').width(postingsWidth-30);
	$('#loading').width(postingsWidth);
	$('#footer-content').width(postingsWidth);

	if(postings.classList.contains('masonry'))
	{
		$('#postings').masonry({columnWidth:button_width });
		$('.triggered>.post-big').width((button_width*num_columns)+(10*(num_columns-1))+'px');
	}

	replacePostingsContainers();
}

function replacePostingsContainers()
{

	var theWindow = $(window);
	var postings_header = $('#postings-header');
	var postings_header_filler = $('#postings-header-filler');
	var map_canvas = $('#map-canvas');
	var header_height = postings_header.height();
	var window_width = theWindow.innerWidth();



	if($(document).scrollTop() < this.to_top)
	{
			postings_header.addClass('rounded-top');
			postings_header.removeClass('sticky');
			postings_header.css({'left' : ''});
			postings_header_filler.css('height', postings_header.height()+'px');
			postings_header_filler.hide();

		if(window_width >= 980)
		{
			map_canvas.css({'position':'relative','top': '', 'bottom':'', 'left':''});

		}
	}

	else	
	{
		postings_header.addClass('sticky');
		postings_header.removeClass('rounded-top');
		postings_header_filler.show();
		if(window_width < 980)
		{
			postings_header.css({'left' : '70px'});
		}
		else
		{
			map_canvas.css({'position' : 'fixed', 'top': header_height + 10 + 'px', 'left' : $('#postings').width()+85+'px', 'margin-top':''});
			postings_header.css({'left' : ''});
			postings_header_filler.css('height', postings_header.height()+'px');
		}
	}
		if(window_width < 980)
		{
//			map_canvas.css({'position':'relative','top': '0', 'left':'-15px'});			
			map_canvas.css({'position' : 'fixed', 'bottom': '40px', 'top':'','left' :'85px', 'margin-top':'', 'z-index':'99'});
			if(window_width < 754)
			{
			map_canvas.css({'left' : '35px'});
			postings_header.css({'left' : '20px'});
			}
		}
}



function setPosition(position){
	var latitude = position.coords.latitude;
	var longitude = position.coords.longitude;

	var json_location = {"lat": latitude, "lon": longitude, "source": "html5"};
	var str_location = JSON.stringify(json_location);
	
	setCookie("location", str_location, 8);
}


function map_initialize(callback) {
	var myLatLon = new google.maps.LatLng(json_location.lat, json_location.lon);
	var temescalLatLon = new google.maps.LatLng(37.833222, -122.264222);
	var style= [
  {
    "featureType": "road",
    "elementType": "geometry",
    "stylers": [
      { "color": "#A33539" }
    ]
  },{
    "featureType": "administrative",
    "elementType": "geometry",
    "stylers": [
      { "visibility": "off" }
    ]
  },{
    "featureType": "poi",
    "stylers": [
      { "visibility": "off" }
    ]
  },{
    "featureType": "water",
    "elementType": "geometry",
    "stylers": [
      { "color": "#A33539" }
    ]
  },{
    "featureType": "landscape.natural",
    "stylers": [
      { "color": "#F4F2E6" }
    ]
  },{
    "featureType": "transit",
    "stylers": [
      { "visibility": "off" }
    ]
  },{
    "featureType": "landscape.man_made",
    "stylers": [
      { "visibility": "off" }
    ]
  }
]
	var mapOptions = {
		zoom: 16,
		center: temescalLatLon,//myLatLon,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		styles: style
	}

	this.map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	$('#map-canvas').addClass('initialized');
	var myLocationMarker = new google.maps.Marker({
		position: myLatLon,
		map: map,
		title:'Here I am!'
	});
	callback();
}



function loadPost(id){
	closePost();
	var postings = $('#postings');

	var link = $('#'+id);
	var postMini = link.children('.post-mini');
	var button = link.parent();
	var partial = button.children('.post-big');
	var url = 'partials/posting?p='+id;
	var docPostings = document.getElementById('postings');
	var loadingDiv = document.createElement('div');
	loadingDiv.innerHTML = "<div class='loading'><img src='images/loading.gif'></div>";
	//hide content divs
	partial.hide('fast');
	postMini.hide('fast', function(){
		button.addClass('triggered');
		button.prepend(loadingDiv);
		//call load

		partial.load(url, function(){
			partial.show('fast', function(){		
				resizeContainer();
				if(docPostings.classList.contains('masonry'))
				{
					postings.masonry('reload');
				}

			});
			$('.loading').remove();

		});
	});
		this.active = id;
}

function closePost(){
	var id = this.active

	if(id != 0)
	{
		var postings = $('#postings');
		var link = $('#'+id);
		var postMini = link.children('.post-mini');
		var button = link.parent();
		var partial = button.children('.post-big');

		partial.hide("fast", function(){
			button.removeClass('triggered');

		});	
		postMini.show("fast", function(){
		partial.empty();
		if(document.getElementById('postings').classList.contains('masonry'))
		{

			postings.masonry('reload');
		}
			resizeContainer();
		});



		this.active = 0;

	}

}

function getPostingsHeader() {
	headerString = document.createElement('ul');
	headerString.setAttribute('class', 'inline');
	headerString.innerHTML = "<li>"
		+"<div class='btn-group'>"
			+"<button title='Tiles' id='tile' class='format-button btn disabled'><i class='icon-th-large'></i></button>"
			+"<button title='List' id='list' class='format-button btn'><i class='icon-list'></i></button>"
			+"</div>"
		+"</li>"
		+"<li>";
	categoriesDiv = document.createElement('div');
	categoriesDiv.setAttribute('class', 'btn-group');
	categoriesDiv.innerHTML = "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'>"
		+"Category "							
		+"<span class='caret'></span>"
		+"</a>";
	categoriesString = document.createElement('ul');
	categoriesString.setAttribute('class', 'dropdown-menu');
		+"<ul class='dropdown-menu'>";
		jQuery.each(categories, function(i, val){
			if(i!=0)
			{
				categoriesString.innerHTML += "<li class='divider'></li>";
			}
			categoriesString.innerHTML += "<li><a href='#cat="+val.id+"'><img src='images/icons/"+val.tag+".png'>&nbsp"+val.tag+"</a></li>";
		});
/*


			$query_string['cat'] = $id;
			$query_string['p'] = null;
			$href = http_build_query($query_string);
	
			echo "
									<li><a href='?$href'><img src='images/icons/$tag.png' width='35'> &nbsp &nbsp $tag</a></li>";
		  }
	  }
	echo "*/
	categoriesDiv.appendChild(categoriesString);
	headerString.appendChild(categoriesDiv);
	headerString.innerHTML +="</li>"
		+"<li><button title='Filter' id='filters' class='btn'><i class='icon-search'></i></button></li>"
		+"<li>"
		+"<form class='form-inline'>"// form-inline-margin-fix'>"
		+"<div class='input-prepend'>"
		+"<span class='add-on'><i class='icon-tag'></i></span>"
		+"<input type='text' id='tag-search' name='tag-search' class='span4' placeholder=''>"
		+"</div><!-- .input-prepend -->"
		+"</form>"
		+"</li>"
		+"</ul>";
	return headerString;
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
	if(Modernizr.geolocation && json_location.source !='user')
	{
		navigator.geolocation.getCurrentPosition(setPosition);//, {enableHighAccuracy: true, maximumAge:120000});
	}

	var headerHTML = getPostingsHeader();
	document.getElementById('postings-header').appendChild(headerHTML);

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
			closePost();
			postingsFormat = this_id;
			displayPosts();
		}
	});


	$('#tag-search').focus();

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
	var header_height = postings_header.height();
	postings_header_filler.hide();
	postings_header_filler.height(header_height);
	this.to_top = postings_header.offset().top;





window.onresize = function(){
	resizeContainer();

	if(document.getElementById('postings').classList.contains('masonry'))
	{
		$('#postings').masonry('reload');
	}
};

	theWindow.scroll(function(){replacePostingsContainers();});

	window.onload = function(e){
		replacePostingsContainers();
	};
	window.onpopstate = function(e){
		var id = e.state;
		if(id == null)
		{	
			closePost();
		}
		else
		{
			loadPost(id);
		}
	}

	$('#postings-container').load('partials/posting_list', function(){
		map_initialize(after_initialize);
		resizeContainer();
		this.to_top = $('#postings-header').offset().top;
		to_top_initialized = true;
	});

	function after_initialize(){
		displayPosts();
		if(postRequest)
		{
			loadPost(postings[0].id);
		}
		resizeContainer();
	}

	var footer = document.getElementById('footer');
	footer.style.background = '#F4F2E6';
	$('#footer-content').children('p').removeClass('white');
});

function scrollTo(id)
{	
	var window_width = $(window).innerWidth();

	var post = $('#'+id).parent();

	if(754 < window_width)
	{
		document.documentElement.scrollTop = ((post.offset().top - $('#postings-header').height()) - 10);
	}
	else
	{
		document.documentElement.scrollTop = post.offset().top - $('#postings-header').height() - $('#map-canvas').height() - 10;
	}
}

function highlightPosting(id)
{
	$('#'+id).parent().addClass('highlight');
}

function lowlightPosting(id)
{
	$('#'+id).parent().removeClass('highlight');
}

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

