/***********************************************
file: tndrbox.js
creator: Ian McEachern

This is the javascript responsible for page 
initialization, responsive div placement and 
sizing, and # link handling. And all that other 
stuff I'd rather not talking about.
 ***********************************************/

var initialized = false;
var activePostings = [];
var lastURL = '';
var oms = '';
var ogSearchPlaceholder = '';
var mapBounds = '';

var activePost;
var activePostId = 0;
var activeCat = 0;
var activeTag = 0;
var lastCat = 0;
var lastTag = 0;

var activeTagOp = 'and';
var tagOpChange = false;
var welcomePageExpanded = true;
var firstFormatChange = true;
var tilesDisplayed = true;
var lastBoxState = '';

var lastWindowWidth = 0;
var lastWindowHeight = 0;
var lastInfoWindow = null;

var selfMarker;

function initPage()
{

	mapInitialize(afterMapInitialize);
	getPosts();
	if(Modernizr.geolocation)
	{
		navigator.geolocation.watchPosition(geoSuccess, geoError, {enableHighAccuracy:true});
	}

	//prep the meta tndr buttons
	$('#reset-filters-button, #search-options, #activate-tndr, #header-list').hide();

	$('#tndr-buttons, #welcome-close').show();

	//prep the box links
	if(loggedIn)
	{
		$('#login-link').hide();
	}
	else
	{
		$('#settings-link').hide();
		$('#logout-link').hide();
	}

	$('#hide-box-button').hide();

	$('#box-links').show();

	if($(window).innerWidth() < 768)
	{
		welcomePageExpanded = false;
	}
	if($(window).innerWidth()<360)
	{
		$('#header-list').show();
		toggleViewFormat();
	}

	ogSearchPlaceholder = $('#search').attr('placeholder');


	function afterMapInitialize(){	
		initMarkerSprites();

		oms = new OverlappingMarkerSpiderfier(map);
		oms.addListener('click', function(marker) {
			var id = marker.id;
			if($(window).innerWidth() < 768 && initialized)
			{
				var link = (tilesDisplayed ? $('#tile-'+id):$('#list-'+id));
				var postingIndex = link.attr('index')
				var post = postings[postingIndex];
				if(lastInfoWindow != null)
				{
					lastInfoWindow.close();
				}
				postings[postingIndex]['infoWindow'].open(map,marker);

				lastInfoWindow = postings[postingIndex]['infoWindow'];
			}
			else
			{
				$.bbq.pushState({'p':id}, 0);
			}
		});
	}
}

$(document).ready(function(){
	initPage();

	var tndr = $('#body-container');
	var box = $('#box');
	var middleBox = $('#middle-box');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');
	var rightPane = $('#right-pane');
	var leftPane = $('#left-pane');



	window.onresize = function(){
		repositionContainers();

	};

	$(window).bind('hashchange', function(){
		

		var query = $.deparam.fragment(true);
		var postFlag = (query.p != null && query.p != '');
		var categoryFlag = (query.c != null && query.c != '');
		var tagFlag = (query.t != null && query.t != '');
		var dateFlag = (query.d != null && query.d != '');
		var boxFlag = (query.b != null && query.b != '');
		var filterFlag = false;
		var filterChange = false;

		if(!postFlag)
		{
			closePost();
		}
//post requests
		if(postFlag && query.p != activePostId)
		{
			loadPost(query.p);
		}

//filter requests
		if(categoryFlag || tagFlag || dateFlag || postFlag)
		{


			$('#welcome-page').hide('fast');
			if(!postFlag)
			{
	 			$('#reset-filters-button').show();	
			}
		}
		else
		{
 			$('#reset-filters-button').hide();	
		}
		if(categoryFlag && query.c!=lastCat)
		{

			var dropdownList = $('#categories-dropdown>ul').children();
			var categoryHtml = $('#categories-dropdown>a').html()
			$.each(dropdownList.children(), function(index)
			{
				if($.deparam($(this).attr('href').substring(1)).c == query.c)
				{
					categoryHtml = $(this).html();
					category = categoryHtml.substr(categoryHtml.lastIndexOf('>')+1)
			
					categoryHtml = "<div class='pull-left "+category+"_sm'></div> "+category+" <span class='caret'>";
				}
			});
			$('#categories-dropdown>a').html(categoryHtml);
				activeCat = query.c;
				$('.tags a').fragment({'c':''+activeCat}, 0);
				filterFlag = true;
		}
		else
		{
			activeCat = 0;
			$('.tags a').fragment({'c':''}, 0);
		}

		if(tagFlag)
		{
			if(query.t != activeTag)
			{

				activeTag = query.t;
				filterFlag = true;
			}
			$('#categories-ul a').fragment({'t':''+activeTag}, 0);
		}
		else
		{
			activeTag = 0;
			$('#categories-ul a').fragment({'t':''}, 0);
		}
		
/*		if(dateFlag && (query.d != activeDate))
		{

			filterFlag = true;
		}*/

		if(!filterFlag)
		{
			if(tagOpChange)
			{
				filterFlag = true;
				tagOpChange = false;
			}
		}

		if(filterFlag && initialized)
		{
			activePostings = [];
			var j=0, currentPostActive;
			for(var i=0;i<postings.length; i++)
			{
				currentPostActive = false;
				if(categoryFlag && query.c == postings[i]['tag_1_id'])
				{
					if(activeTagOp == 'and' && tagFlag)
					{
						if((query.t == postings[i]['tag_2_id'])||(query.t == postings[i]['tag_3_id']))
						{

							currentPostActive = true;
						}
					}
					else
					{

						currentPostActive = true;
					}
				}

				if(activeTagOp == 'or' || !categoryFlag)
				{

				if(tagFlag && (query.t == postings[i]['tag_2_id'])||(query.t == postings[i]['tag_3_id']))
				{

					currentPostActive = true;
				}
				}


/*				else if(dateFlag)
				{

				}*/
				if(currentPostActive)
				{
					activePostings[j] = i;
					j++;
					postId = postings[i]['id'];
					$('#tile-'+postId+', #list-'+postId).fragment({'c':query.c, 't': query.t});
				}
			}
			displayPosts();
			resetMarkers();
			displayActiveMarkers();
		}
		else if(!dateFlag && !tagFlag && !categoryFlag)
		{
			activePostings = [];

			for(var i=0; i<postings.length; i++)
			{
				postId = postings[i]['id'];
				activePostings[i] = i;
				$('#tile-'+postId+', #list-'+postId).fragment({'c':'', 't':''});
			}
			activeCat = 0;
			activeTag = 0;
			if(activeCat != lastCat || activeTag != lastTag)
			{
				displayPosts();
				if(initialized)
				{
				resetMarkers();
				displayActiveMarkers();
				}
			}
		}

//box requests
		if(boxFlag)
		{
			if(query.b == 'logout')
			{
				deactivateBox();
				$.ajax('scripts/logout');
				$('#logout-link').hide();
				$('#settings-link').hide();
				$('#login-link').show();

			}
			else
			{
				if(query.b != 'members')
				{
					if(query.b != lastBoxState)
					{

					$('#box-js-content').load('partials/'+query.b, function(){	
						$('#'+query.b+'-link').addClass('active');

							activateBox();

					});
					lastBoxState = query.b;
					}
					else
					{
						activateBox();
					}
				}
				else if(query.b == 'members')
			    {
					if(query.b != lastBoxState)
					{
						$('#box-js-content').load('partials/'+query.b, function(){	
							$('#'+query.b+'-link').addClass('active');
							loadBoxContentByURL();
						});
					lastBoxState = query.b;			
					


					}
					else
					{

							loadBoxContentByURL();


					}
					activateBox();
				}
	
			}

		}
		else
		{
				deactivateBox();
		}
		if(activeCat == 0)
		{
			$('#categories-dropdown>a').html("<i class='icon-folder-open'></i>&nbsp Category <span class='caret'></span>");
		}
		if(activeTag == 0)
		{
			$('#search').attr('placeholder', ogSearchPlaceholder);
		}
		if(categoryFlag == 0 && tagFlag == 0)
		{
			$('#tag-op').hide();
		}
		else
		{
			$('#tag-op').show();
		}
		lastCat = activeCat;
		lastTag = activeTag;
		lastURL = window.location.hash;



	});


	$('#activate-tndr').click(function(e){
		if($('#left-pane').hasClass('active'))
		{
			if($('#posting').hasClass('active'))
			{
				closePostButton();
			}
		}
		else
		{
			toggleActivePane();
		}
	});

		

	$('.ui-accordion').click(function(e){
		repositionContainers();
	});
	$('#posting-content').hover(function(e){
		$(this).addClass('highlight');
	}, function(e){
		$(this).removeClass('highlight');
	});
	$('#welcome-page').hover(function(e){
		var welcome = $('#welcome-page');

		if(!welcomePageExpanded)
		{
			var welcomeNow = welcome.height();
			welcome.css('height', 'auto');
			var welcomeHeight = welcome.height();

			welcome.css('height', welcomeNow);


			welcome.animate({
				height: welcomeHeight
			}, 500);
			
			welcomePageExpanded = true;
		}
	});

	rightPane.click(function(e){
		if(leftPane.hasClass('active') && ($(window).innerWidth() < 768))
		{
			e.preventDefault();
			toggleActivePane();
		}

	});
	
	leftPane.click(function(e){
		if(rightPane.hasClass('active')  && ($(window).innerWidth() < 768))
		{
			e.preventDefault();
			toggleActivePane();
		}

		
	});
	$('#posting').click(function(e){
		if(rightPane.hasClass('active')  && ($(window).innerWidth() < 768))
		{
			e.preventDefault();
			toggleActivePane();
		}
		
	});

	$('.format-button').on('click', function(e){
		if(!$(this).hasClass('disabled'))
		{
			toggleViewFormat();
		}
	});

	$('#filters').click(function() {
		toggleFilterView();
	});

	$('#reset-filters-button').click(function(){
		$.bbq.pushState({'c':'', 't':''});
	});

	$('#tag-op').click(function(e){
		if(activeTagOp == 'and')
		{
			activeTagOp = 'or';
		}
		else if(activeTagOp == 'or')
		{
			activeTagOp = 'and';
		}
		$('#tag-op').html(activeTagOp);
		tagOpChange = true;
		$(window).trigger('hashchange');	
	});

	$('#search').autocomplete({
		source: 'scripts/search.php?type=t&active=1',
		focus: function(event, ui){
			$('#tag-search').val(ui.item.label);
			return false;
		},
		select: function(event, ui){
			$('#search').attr('placeholder', ui.item.label);
			$('#search').val('');

			var tagLink = $.deparam(ui.item.value);
			tagLink.p = ''
			$.bbq.pushState(tagLink, 0);
			return false;
		}
	});
	
	$('#search').keypress(function(e){
		if(e.keyCode == 13) //enter key
		{
			e.preventDefault();
			return false;
		}
	});

	$('#box-links a').click(function(e){
		if(!$(this).parent().hasClass('active'))
		{
			$(this).parent().parent().children().removeClass('active');
			$(this).parent().addClass('active');
		}
	});
	$('#body-container, #right-pane, #tndr-header, #posting').click(function(e){
		if($('body').hasClass('inactive'))
		   {
			   e.preventDefault();
			   deactivateBox();
		   }
	});

});

function geoSuccess(position) 
{
	if(selfMarker!=undefined)
	{
		selfMarker.setMap();
	}
	setPosition(position);
	var selfPos = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

	selfMarker = new google.maps.Marker({
		position: selfPos,
		animation: google.maps.Animation.DROP,
		title: 'You are here',
		zIndex: 9999
	});

	selfMarker.setMap(map);
	if(initialized)
	{
		var mapBound = map.getBounds();
		if(!mapBound.contains(selfPos))
		{
			mapBound.extend(selfPos);
			map.fitBounds(mapBound);
		}
	}

	sortPostings();
}

function geoError(err){
	switch(err.code)
	{
	case 1:
		//PERMISSION_DENIED
		console.log('error 1');
		break;
	case 2:
		//POSITION_UNAVAILABLE
		console.log('error 2');
		break;
	case 3:
		//TIMEOUT
		console.log('error 3');
		break;
	}
	
}

function setPosition(position)
{
        var latitude = position.coords.latitude;
        var longitude = position.coords.longitude;

        var json_location = {"lat": latitude, "lon": longitude, "source": "html5"};
        var str_location = JSON.stringify(json_location);
        
        setCookie("location", str_location, 8);
}

function setCookie(name, value, hours)
{
        var date = new Date();
        date.setTime(date.getTime()+(hours*3600000));
        var expire = date.toGMTString();
        var new_cookie = name+ "=";
        new_cookie = new_cookie + value + "; expires="; 
        new_cookie = new_cookie + expire + "; path=/";


        document.cookie = new_cookie;
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

function getPosts()
{
	var url = 'partials/posting_list.php';
	var query = $.deparam.querystring();
	if(query.hasOwnProperty('p'))
	{		
		url += '?p='+query.p;
	}
	$.getJSON(url, function(data){
		postings = data;
		for(var i=0; i<postings.length; i++)
		{
			activePostings[i] = i;
		}
		writePosts();
	});
	
}

function sortPostings()
{
	if(initialized)
	{
		var postingsDiv = (tilesDisplayed ? $('#tiles'):$('#list'));

	}
	return;
}

function writePosts()
{

	var post, marker, infoWindow, list, button, tileLink, listLink, p_id, postLatLon;
	var postingsIndex = postings.length;
	var postingsLength = postings.length;

	for(var i=0; i<postingsLength; i++)
	{
		post = postings[i];

		p_id = post['id'];

		if(tilesDisplayed)
		{
			button = document.createElement('div');
			tileLink = document.createElement('a');

			tileLink.innerHTML = post['tile'];
			tileLink.setAttribute('href','#p='+p_id);
			tileLink.setAttribute('id', 'tile-'+p_id);
			tileLink.setAttribute('class', 'post-trigger');
			tileLink.setAttribute('index', i);

			button.setAttribute('class', 'posting-list-button tile');
			button.appendChild(tileLink);
			button.innerHTML += "<div class='post-big'></div>";

			document.getElementById('tiles').appendChild(button);
			}

		//lists displayed
		else
		{
		list = document.createElement('div');
		listLink = document.createElement('a');

		listLink.innerHTML = post['list'];
		listLink.setAttribute('href','#p='+p_id);
		listLink.setAttribute('id', 'list-'+p_id);
		listLink.setAttribute('class', 'post-trigger');
		listLink.setAttribute('index', i);
		listLink.innerHTML = post['list'] + "<div class='post-big'></div>";

		list.setAttribute('class', 'posting-list-button list');
		list.appendChild(listLink);
		list.innerHTML += "<div class='post-big'></div>";

		document.getElementById('list').appendChild(list);
		}

		if(!initialized)
		{
		postLatLon = new google.maps.LatLng(post['lat'], post['lon']);

		postings[i]['marker'] = new google.maps.Marker({
			position: postLatLon,
			title: post['title'],
			id: p_id,
			index: i,
			icon: markerSprites[post.tag_1]
		});
		postInfo = getPostInfo(post);
		postings[i]['infoWindow'] = new google.maps.InfoWindow({
			content: postInfo
		});
		infoWindow = postings[i]

		$('.iw-link').click(function(e){
				$(this).parent().close();
			});
		marker=postings[i]['marker'];
			google.maps.event.addListener(postings[i]['marker'], 'mouseover', function(e){
				var id = $(this).attr('id')

				highlightPosting(id);

			});
			google.maps.event.addListener(postings[i]['marker'], 'mouseout', function(e){
				var id = $(this).attr('id')
				lowlightPosting(id);
			});

		oms.addMarker(postings[i]['marker']);
		}

		postingsIndex++;
/*	var postScript = document.createElement('script');
	postScript.innerHTML = "$('.posting-list-button').hover(function(e){"
		+"var i = $(this).children('.post-trigger').attr('index');"
		+"highlightPosting("+postings[i]['id']+");"
		+"}, function(e){"
		+"var i = $(this).children('.post-trigger').attr('index');"
		+"lowlightPosting(postings[i]['id']);"
		+"});";
	var ref = document.getElementsByTagName('script')[0];
	ref.parentNode.insertBefore(postScript, ref);*/
	}
$('.posting-list-button').hover(function(e){
		var i = $(this).children('.post-trigger').attr('index');
		highlightPosting(postings[i]['id']);
		}, function(e){
		var i = $(this).children('.post-trigger').attr('index');
		lowlightPosting(postings[i]['id']);
		});
		


	displayPosts();
	if(initialized)
	{
		resetMarkers();
		displayActiveMarkers();
	}
}
function displayPosts()
{
	var post, link, id, activePost;

	$('.posting-list-button').hide().removeClass('active-brick');

	for(var i=0; i<activePostings.length; i++)
	{
		post = postings[activePostings[i]];

		id = post['id'];
		link = (tilesDisplayed ? $('#tile-'+id):$('#list-'+id));
		if(tilesDisplayed)
		{
			link.parent().addClass('active-brick');
		}
		link.show().parent().show();
	}


	if(!initialized && tilesDisplayed)
	{
		$('#tiles').masonry();
	}

	repositionContainers();
}

function startLoading(targetDiv)
{
	targetDiv.prepend('<div class="loading"><img src="images/loading.gif"><br>loading...</div>');
}

function endLoading(targetDiv)
{

	targetDiv.children('.loading').remove();
}

function repositionContainers()
{
	var window_width = $(window).innerWidth();
	var window_height = $(window).innerHeight();

	var rightPane = $('#right-pane');
	var tndrHeader = $('#tndr-header');
	var header_height = tndrHeader.height();

	var tiles = $('#tiles');
	
	var box = $('#box');
	var boxWings = $('#box-left, #box-right');
	var activeBox = $('#box.active');
	var activeBoxWings = $('#box-left.active, #box-right.active');

		var posting = $('#posting');

	if(window_width != lastWindowWidth)
	{

	var tndrContainer = $('#body-container');

	var headerFiller = $('#tndr-header-filler');
	var leftPane = $('#left-pane');
	var tndr = $('#tndr');





	var middleBox = $('#middle-box');
	var leftBox = $('#box-left');
	var rightBox = $('#box-right');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');




	//reset margins

		//set for large screens where both panes are fully displayed
	var tndrContainerWidth = window_width - 140;
	var leftPaneWidth = tndrContainerWidth*.62;
	var rightPaneWidth = tndrContainerWidth - leftPaneWidth;
	var rightPaneLeft = leftPaneWidth + 70
	var postColumns;

	if(1400 < window_width)
	{
		postColumns = 5;
	}
	else if(1200 < window_width)
	{
		postColumns = 4;
	}
	else if(980 < window_width)
	{
		postColumns = 3
	}
	else if(768 < window_width)
	{
		postColumns = 2
	}
	//either pane will be active and covering the other
	//also, margins are removed
	else
	{
		tndrContainerWidth = window_width;
		leftPaneWidth = tndrContainerWidth*.91;
		rightPaneWidth = leftPaneWidth;


		rightPaneLeft = window_width-rightPaneWidth;
		postColumns = 3;
		if(window_width < 500)
		{	
			postColumns = 2;
		}	
		if(window_width	< 360)
		{	
			postColumns = 1;
		}	

	}
	var numColumns = postColumns;
	if(numColumns > 4)
	{
		numColumns = 4;
	}
	var buttonWidth = (leftPaneWidth-10)/postColumns - 10;

	middleBox.css('width', tndrContainerWidth);

//	box.css('width', tndrContainerWidth+160);
	$('#box-content').css('width', tndrContainerWidth+130);
	box.css('top',window_height-65);
	boxWings.css('top',window_height-126);

	if(window_width > 768)
	{
		posting.css({'width':leftPaneWidth, 'top':header_height+5});
		rightPane.css({'top':header_height+5,'height': window_height - (75 + header_height)});
		boxWings.show();
	}
	else
	{
		posting.css({'width':leftPaneWidth, 'top':header_height+5});

		rightPane.css({'top':header_height+5,'height': window_height - (header_height+10)});
		box.css('top',window_height);
		boxWings.hide();
	}

	tndrContainer.css('width', leftPaneWidth);
	tndrHeader.css('width', tndrContainerWidth);
	leftPane.css('width', leftPaneWidth);

	$('.post-mini.button').css('width', buttonWidth);
	rightPane.css('width', rightPaneWidth);
	rightPane.css('left', rightPaneLeft);
	if(rightPane.children().hasClass('initialized') && !$('body').hasClass('inactive'))
	{
		google.maps.event.trigger(map, 'resize');
//		displayActiveMarkers();
	}


			$('#posting-content').css('width', (buttonWidth*numColumns)+(10*(numColumns-1))-10);
		if(initialized && tilesDisplayed)
		{
			tiles.masonry('option', {
				columnWidth: buttonWidth
			});

		}
	

	}//window_width check

	box.css('top',window_height-65);
	boxWings.css('top',window_height-126);
	var boxHeight = $('#box-content').height();
	var boxTop = 66;
	var boxWingsTop = 5;
	if(boxHeight < window_height-65)
	{
		boxTop = window_height - boxHeight -7;
		boxWingsTop = boxTop-61;
	}
	activeBox.css('top',boxTop);
	activeBox.css('min-height', window_height-boxHeight);
	activeBoxWings.css('top',boxWingsTop);		

	if(window_width > 768)
	{
		rightPane.css('height', window_height - (70 + header_height));
		posting.css('height',window_height-header_height-70);
		boxWings.show();
	}
	else
	{
		rightPane.css('height', window_height - (header_height+5));
		posting.css('height',window_height-header_height-5);
		box.css('top',window_height);
		boxWings.hide();
	}

	if(box.hasClass('active'))
	{
		var boxHeight = window_height - boxTop;
		box.css('height', boxHeight);
	}




	if(!initialized)
	{
		endLoading($('#tndr'));

		initialized = true;

		lastURL = window.location.hash;
		if(postRequest)
		{
			var postId = $.deparam.querystring().p;
			$.bbq.pushState('p='+postId);
		}
		else
		{
			$(window).trigger('hashchange');	
		}
			tiles.masonry({
	 			itemSelector: '.active-brick',
				isAnimated: true,
				gutterWidth: 10
			});		
	}
	else
	{
		if(tilesDisplayed)
		{
			tiles.masonry('reload');
		}		
	}

	lastWindowWidth = window_width;


}



function activatePosting()
{
	var posting = $('#posting');

	if(!posting.hasClass('active'))
	{
		posting.switchClass('inactive', 'active', function(){
			posting.show();
			if(lastWindowWidth<768)
			{
				$('#activate-tndr').show();
			}
		});

	}
}

function deactivatePosting()
{
	var posting = $('#posting');

	if(posting.hasClass('active'))
	{
		posting.switchClass('active', 'inactive', function(){
			posting.hide();
			if($(window).innerWidth()<768)
			{
				$('#activate-tndr').hide();
			}
	});


	}
}

function activateBox()
{
	var box = $('#box');
	if(!box.hasClass('active'))
	{
		$('#box-js-content').hide();
		$('#box-left, #box-right, #box').switchClass('inactive', 'active', function(){


		$('body').addClass('inactive');
		$('#hide-box-button').show();

		$('#box-js-content').show('slow', function(){
		
		repositionContainers();
		});
});

		$('#box-content').css('height', 'auto');

	}
	else
	{
		repositionContainers();
	}

}

function deactivateBox()
{
	var box = $('#box');
	if(box.hasClass('active'))
	{
		$('#box-content').css('height', '');
		$('#hide-box-button').hide();


		$('#box').switchClass('active', 'inactive', function(){

			$('#box-left, #box-right').switchClass('active', 'inactive', function(){
			$('#box-js-content').hide('fast');
			$('#box').css('height','');
			repositionContainers();
			});
		});



	$('#box-links a').parent().removeClass('active');

	$('body').removeClass('inactive');

	$.bbq.removeState(['b', 'view', 'id']);

	}
}

function toggleActivePane()
{
	var leftPane = $('#left-pane');
	var rightPane = $('#right-pane');


	if(leftPane.hasClass('active'))
	{
		leftPane.removeClass('active');
		rightPane.addClass('active');

		$('#activate-tndr').show();		
		$('#activate-map').hide();
	}
	else
	{
		rightPane.removeClass('active');
		leftPane.addClass('active');
		if(lastWindowWidth < 768 && $('#posting').hasClass('inactive'))
		{
			$('#activate-tndr').hide();		
		}

		$('#activate-map').show();
	}
}

function toggleViewFormat()
{
	var tileFormat = $('#tile-format');
	var listFormat = $('#list-format');

	startLoading($('#tndr'));	
	if(tilesDisplayed)
    {
		$('#tiles, #header-tiles').hide();	


		listFormat.addClass('disabled');
		tileFormat.removeClass('disabled');
		tilesDisplayed = false;
		$(window).trigger('hashchange');	
		if(initialized)
		{
			if(firstFormatChange)
			{
				writePosts();
				firstFormatChange = false;
			}
			displayPosts();
		}

		$('#list, #header-list').show();
	}
	else
	{
		$('#list, #header-list').hide();


		tileFormat.addClass('disabled');
		listFormat.removeClass('disabled');
		tilesDisplayed = true;
		$(window).trigger('hashchange');	
		if(initialized)
		{
			if(firstFormatChange)
			{
				writePosts();
			}
			displayPosts();
			if(firstFormatChange)
			{
				lastWindowWidth = 0;
				repositionContainers();
				firstFormatChange = false;
			}

		}


		$('#tiles, #header-tiles').show();


		if(initialized)
		{
			$('#tiles').masonry('reload');

		}
	}
	endLoading($('#tndr'));	
}
function toggleFilterView()
{
	if(document.getElementById('filters').classList.contains('disabled'))
	{
		$('#filters').removeClass('disabled');
		$('#search-options').hide();
	}
	else
	{
		$('#filters').addClass('disabled');
		$('#search-options').show();
	}	
}
function highlightPosting(id)
{
	var link = (tilesDisplayed ? $('#tile-'+id):$('#list-'+id));

	var index = link.attr('index');

	var post = postings[index];
	var marker = post['marker'];
	link.parent().addClass('highlight');

	marker.setIcon(markerSprites[post.tag_1+'_a']);


}

function lowlightPosting(id)
{
	var link = (tilesDisplayed ? $('#tile-'+id):$('#list-'+id));

	var index = link.attr('index');

	var post = postings[index];
	var marker = post['marker'];
	link.parent().removeClass('highlight');


	if(activePostId!=id && activePostId !=0)
	{
		marker.setIcon(markerSprites['tndr']);
	}
	else
	{
		marker.setIcon(markerSprites[post.tag_1]);
	}
}


function isPostVisible(index)
{
	for(i in activePostings)
	{
		if(activePostings[i] == index)
		{
			return true;
		}
	}
	return false;
}
function resetMarkers()
{
	for(var i=0; i<postings.length; i++)
	{
		postings[i]['marker'].setMap();
	}
}

function displayActiveMarkers()
{
	if(activePostings.length > 0)
	{
		var currentMarker = '';
		mapBounds = new google.maps.LatLngBounds();
		for(var i=0; i<activePostings.length; i++)
		{
			currentMarker =	postings[activePostings[i]]['marker'];
			currentMarker.setMap(map);

			mapBounds.extend(currentMarker.position);
		}
		if(selfMarker != undefined)
		{
			if(!mapBounds.contains(selfMarker.position))
			{
				mapBounds.extend(selfMarker.position);
			}
		}
		map.fitBounds(mapBounds);


		//from http://stackoverflow.com/a/4709017
		zoomChangeBoundsListener = 
		    google.maps.event.addListenerOnce(map, 'bounds_changed', function(event) {
		        if (this.getZoom()>16)
				{
		            this.setZoom(16);
			    }
		});//from http://stackoverflow.com/a/4709017
	}
}
function getPostInfo(post){
	var id = post['id'];
	var link = (tilesDisplayed ? $('#tile-'+id):$('#list-'+id));
	var postInfo = '<div class="post-info-window" id="piw-'+id+'">'+
		'<h4><a href="'+link.attr('href')+'" class="iw-link">'+post['title']+'</a></h4>'+
		'<a href="http://maps.google.com/?q='+post['lat']+','+post['lon']+'" target="_blank">('+post['lat']+', '+post['lon']+')</a>'+
		'</div>';
	return postInfo;
}
function loadPost(id){

/*	if(tilesDisplayed)
	{
	var link = $('#tile-'+id);
	}
	else
	{
	var link = $('#list-'+id);
	}


	var index = link.attr('index');
	var post = postings[index];
//	var marker = postings[index]['marker'];

	var rightPane = $('#right-pane');
	var leftPane = $('#left-pane');

	var postMini = link.children('.post-mini');
	var button = link.parent();

	var fullPost = link.next();
	var url = 'partials/posting?p='+id;
	var docPostings = document.getElementById('tndr');
	var tiles = $('#tiles');



	//hide content divs
	$('.post-big').hide();
	postMini.hide('fast');
		button.addClass('triggered');

					for(var i=0; i<postings.length; i++)
					{
						var currentPost = postings[i];

						if(currentPost.id != id)
						{
							currentPost.marker.setIcon(markerSprites['tndr']);
						}

					}
				if(rightPane.hasClass('active'))
				{
					toggleActivePane();
				}
		if(fullPost.hasClass('loaded'))
		{
			fullPost.addClass('active');
			activePostId = id;
			fullPost.show('fast', function(){
			repositionContainers();
		if(tilesDisplayed)
		{
			tiles.masonry('reload', function(){
				window.setTimeout(scrollToActive, 500);
			});
		}
		else
		{
			scrollToActive();
		}
			});

			var postLatLon = new google.maps.LatLng(post['lat'], post['lon']);
			map.panTo(postLatLon);



		}
		else
		{
		startLoading(button);
			//call load
		$.get(url, function(data){
			fullPost.html(data);



					endLoading(button);
				fullPost.show('fast', function(){		



					var postLatLon = new google.maps.LatLng(post['lat'], post['lon']);

					map.panTo(postLatLon);
					fullPost.find('.share').hide();

			fullPost.addClass('loaded');
	
			fullPost.addClass('active');
			activePostId = id;

					repositionContainers();
		if(tilesDisplayed)
		{
			tiles.masonry('reload', function(){
				window.setTimeout(scrollToActive, 500);
			});
		}
		else
		{
			scrollToActive();
		}
				});

			});
		}
*/

		if(tilesDisplayed)
		{
		var link = $('#tile-'+id);
		}
		else
		{
		var link = $('#list-'+id);
		}

		var index = link.attr('index');
	var post = postings[index];


	var url = 'partials/posting?p='+id;

	if(activePostId!=0)
	{
		if(tilesDisplayed)
		{
		var activeLink = $('#tile-'+activePostId);
		}
		else
		{
		var activeLink = $('#list-'+activePostId);
		}

		var activeIndex = activeLink.attr('index');
		postings[activeIndex]['loaded'] = $('#posting-content').html();
		$('#posting-content').empty();
	}
	if(lastInfoWindow != null)
	{
		lastInfoWindow.close();
		lastInfoWindow = null;
	}
	for(var i=0; i<postings.length; i++)
	{
		var currentPost = postings[i];
		if(currentPost.id != id)
		{
			currentPost.marker.setIcon(markerSprites['tndr']);
		}
	}

	if($('#right-pane').hasClass('active'))
	{
		toggleActivePane();
	}
	activatePosting();

	if(post['loaded'] != undefined)
	{
		$('#posting-content').html(post['loaded']);

		$('#posting-content').show('fast');

	}
	else
	{

		startLoading($('#posting'));
		$.get(url, function(data){
			$('#posting-content').html(data);

			endLoading($('#posting'));

			$('#posting-content').show('fast');
		});
	}
	activePostId = id;
	var postLatLon = new google.maps.LatLng(post['lat'], post['lon']);
	map.panTo(postLatLon);

}

function closePost(){
	var id = activePostId;

	if(id != 0)
	{	
		activePostId = 0;
		activePost = null;
		if(tilesDisplayed)
		{
		var link = $('#tile-'+id);
		}
		else
		{
		var link = $('#list-'+id);
		}

		var index = link.attr('index');
		postings[index]['loaded'] = $('#posting-content').html();

		$('#posting-content').empty();
		if(tilesDisplayed)
		{
			var link = $('#tile-'+id);
		}
		else
		{
			var link = $('#list-'+id);
		}
		for(var i=0; i<postings.length; i++)
		{	
			var post = postings[i];
			if(post.id != id)
			{
				post.marker.setIcon(markerSprites[post['tag_1']]);
			}
		}

		var index = link.attr('index');

		$('#posting-content').hide('fast', function(){
			deactivatePosting();
		});
	}

}

function closePostButton(){
	$.bbq.pushState('p=');
}

function sparkPost(id)
{

	var sparkButtons = $('.tile,.list').find('#spark-'+id);
	if(!sparkButtons.hasClass('disabled'))
	{
		sparkButtons.addClass('disabled');
		sparkButtons.children().switchClass('unlit','lighting', function(){
			sparkButtons.parent().next().children().slideDown('fast', function(){
				if(tilesDisplayed)	
				{
					$('#tiles').masonry('reload');	
				}
			});	
		});
		var url = 'scripts/spark_post?p='+id;
		$.getJSON(url, function(data){

			sparkButtons.children().switchClass('lighting','lit');
		});
	}
}



function initMarkerSprites()
{
	var path = 'images/tndr-sprites.png';
	var size = new google.maps.Size(23, 15, 'px', 'px');

	var origin = new google.maps.Point(298, 170);
	markerSprites['tndr'] = new google.maps.MarkerImage(path, size, origin, null, null);
	
	size = new google.maps.Size(40, 50, 'px', 'px');
	var xPos = -40;
	var yPos;

	$.each(categories, function(index, value) {
		yPos = 52;
		xPos = xPos + 40;
		var markerIndex = value.tag;			

		origin = new google.maps.Point(xPos, yPos); // 0, 53
		markerSprites[markerIndex] = new google.maps.MarkerImage(path, size, origin, null, null);

		yPos = 104;
		markerIndex += '_a';

		origin = new google.maps.Point(xPos, yPos); // 0, 53
		markerSprites[markerIndex] = new google.maps.MarkerImage(path, size, origin, null, null);
	});
}

function mapInitialize(callback) {
//location functionality
//var myLatLon = new google.maps.LatLng(json_location.lat, json_location.lon);
//	var temescalLatLon = new google.maps.LatLng(37.833222, -122.264222);
	var defaultLatLon = new google.maps.LatLng(37.817311, -122.260923);
	var style= [
	{
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
		zoom: 15,
		maxZoom: 18,
		center: defaultLatLon,//temescalLatLon,//myLatLon,
//		disableDefaultUI: true,
		mapTypeId: google.maps.MapTypeId.ROADMAP,
		styles: style
	}

	this.map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
	$('#map-canvas').addClass('initialized');
	
	callback();
}


function loadBoxContentByURL()
{

					var id = $.bbq.getState('id');
	var b_id = $.bbq.getState('b_id');
					var view = $.bbq.getState('view');
					if(typeof id == 'undefined')
					{
						id = '';
					}

					$('#settings-nav > li').removeClass('active');

					var append_string = '';
					if(id != '')
					{
						append_string = "?id=" + id;
					}
	
					if(view == '' || typeof view == 'undefined')
					{
						view = 'new-post';
					}

					if(view == 'new-post')
					{
						smartLoad('partials/new_post_form');
						$('#new-post-li').addClass('active');
					}
					if(view == 'new-event')
					{
						smartLoad('partials/new_event_form');
						$('#new-event-li').addClass('active');
					}
					if(view == 'edit-post')
					{
						smartLoad('partials/edit_post_form'+append_string);
						$('#posts-li').addClass('active');
					}

					if(view == 'preview-post')
					{
						smartLoad('partials/preview'+append_string);
						$('#posts-li').addClass('active');
					}

					if(view == 'delete-post')
					{	
						$.ajax({
							url:'scripts/delete_post',
							data: {'id': id},
							type: 'get'
						}).done(function(){
							$.bbq.pushState({'b':'members','view':'posts'});
							$('#posts-li').addClass('active');
						});
					}

					if(view == 'activate-post')
					{	
						$('#js-content').hide();
						$.ajax({
							url:'scripts/activate_post',
							data: {'id': id},
							type: 'get'
						}).done(function(){
							$.bbq.pushState({'b':'members','view':'posts'});
							$('#posts-li').addClass('active');
						});
					}
				
					if(view == 'deactivate-post')
					{	
						$('#js-content').hide();
						$.ajax({
							url:'scripts/deactivate_post',
							data: {'id': id},
							type: 'get'
						}).done(function(){
							$.bbq.pushState({'b':'members','view':'posts'});
							$('#posts-li').addClass('active');
						});
					}
				
					if(view == 'posts')
					{
						if(b_id != null)
						{
							append_string = '?id='+b_id;
						}
						else
						{
							append_string = '';
						}
						smartLoad('partials/posts'+append_string);
						$('#posts-li').addClass('active');
					}
				
					if(view == 'edit-profile')
					{	
						smartLoad('partials/edit_profile_forms'+append_string);
						$('#profile-li').addClass('active');
					}
				
					if(view == 'new-business')
					{
						smartLoad('partials/new_business_form');
						$('#new-business-li').addClass('active');
					}
				
					if(view == 'new-user')
					{
						smartLoad('partials/new_user_form');
					    $('#new-user-li').addClass('active');
					}
}

function smartLoad(url)
{
	//hide content div
	$('#js-content').hide('slow');
	//show loading div
	$('#loading').show('fast');
	//call load
	$('#settings-content').load(url, function(){
		//hide loading div
		$('#loading').hide('fast');

		//show content div
		$('#js-content').show('fast', function(){activateBox();});
	});
}