/***********************************************
file: tndrbox.js
creator: Ian McEachern

This is the javascript responsible for page 
initialization, responsive div placement and 
sizing, and # link handling. And all that other 
stuff I'd rather not talking about.
 ***********************************************/

var initialized = false;
var formattedPostings = [];
var activePostings = [];
var postingsFormat = 'tile';
var lastURL = '';
var oms = '';
var markerSprites = [];
var ogSearchPlaceholder = '';
this.active = 0;

function initPage()
{
	var tndrContainer = $('#body-container');
	var box = $('#box');
	var middleBox = $('#middle-box');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');

	ogSearchPlaceholder = $('#search').attr('placeholder');

	mapInitialize(afterMapInitialize);

	//prep the meta tndr buttons
	$('#reset-filters-button').hide();
	$('#search-options').hide();
	$('#tndr-buttons').show();

	$('#welcome-close').show();

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
	$('#show-box-button').hide();
	$('#box-links').show();
	
	var tileFormat = $('#tile-format');
	var listFormat = $('#list-format');
	var	tndr = $('#tndr');



	function afterMapInitialize(){
		initMarkerSprites();
		oms = new OverlappingMarkerSpiderfier(map);

		oms.addListener('click', function(marker) {
			$.bbq.pushState('p='+marker.id);
		});

		getPosts();


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

		if(!postFlag)
		{
			closePost();
		}
//filter requests
		if(categoryFlag || tagFlag || dateFlag)
		{
 			$('#reset-filters-button').show();	
		}
		else
		{
 			$('#reset-filters-button').hide();	
		}
		if(categoryFlag && (query.c != $.deparam(lastURL, 'c')))
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
			filterFlag = true;
		}
		else
		{
		$('#categories-dropdown>a').html("<i class='icon-folder-open'></i>&nbsp Category <span class='caret'></span>");

		}
		if(tagFlag && (query.t != $.deparam(lastURL, 't')))
		{
			filterFlag = true;
		}
		else
		{
			$('#search').attr('placeholder', ogSearchPlaceholder);
		}
		if(dateFlag && (query.d != $.deparam(lastURL, 'd')))
		{

			filterFlag = true;
		}
		if(filterFlag && initialized)
		{
			activePostings = [];
			var j=0, currentPostActive;
			for(var i=0;i<postings.length; i++)
			{
				currentPostActive = false;
				if(tagFlag && (query.t == postings[i]['tag_2_id'])||(query.t == postings[i]['tag_3_id']))
				{
					if(categoryFlag)
					{
						if(query.c == postings[i]['tag_1_id'])
						{

							activePostings[j] = i;
							j++;
							currentPostActive = true;
						}
					}
					else
					{

						activePostings[j] = i;
						j++;
						currentPostActive = true;
					}
				}
				else if(categoryFlag && query.c == postings[i]['tag_1_id'])
				{
					activePostings[j] = i;
					j++;
					currentPostActive = true;
				}
				else if(dateFlag)
				{
//herp derp
				}
				if(currentPostActive)
				{
					$('.tile>#'+postings[i]['id']).parent().addClass('active-brick');
					postings[i]['marker'].setMap(map);
				}
				else
				{
					$('.tile>#'+postings[i]['id']).parent().removeClass('active-brick');
					postings[i]['marker'].setMap();
				}
			}
			
			displayPosts();

		}
		else if(!dateFlag && !tagFlag && !categoryFlag)
		{
		activePostings = [];

			for(var i=0; i<postings.length; i++)
			{
				activePostings[i] = i;
				$('.tile>#'+postings[i]['id']).parent().addClass('active-brick');
				postings[i]['marker'].setMap(map);
			}
			displayPosts();

		}
//post requests
		if(postFlag)
		{
			loadPost(query.p);
		}
//box requests
		if(boxFlag)
		{
			if(query.b == 'logout')
			{
				$.ajax('scripts/logout');
				$('#logout-link').hide();
				$('#settings-link').hide();
				$('#login-link').show();

				$('#box').switchClass('active', 'inactive', 1000);
				$('#box-js-content').hide();
				$('body').removeClass('inactive');
				$('#hide-box-button').hide();
				$('#show-box-button').hide();
			}
			else
			{
				if(!box.hasClass('active'))
				{
					activateBox();
				}
				if(query.b != 'members' && query.b != lastBoxState())
				{
					$('#box-js-content').load('partials/'+query.b, function(){	
						$('#'+query.b+'-link').addClass('active');
					});
				}
				else if(query.b == 'members')
			    {
					if(lastBoxState() != 'members')
					{
						$('#box-js-content').load('partials/'+query.b, function(){	
							$('#'+query.b+'-link').addClass('active');
							loadBoxContentByURL();
						});
			
					}

						loadBoxContentByURL();

				}
	
			}

		}
		else
		{
			if(box.hasClass('active'))
			{
				deactivateBox();
			}
		}
	lastURL = window.location.hash;
	$('.posting-list-button a').fragment(window.location.hash, 1);


	});



	rightPane.click(function(e){
		if(leftPane.hasClass('active'))
		{
			e.preventDefault();
			leftPane.removeClass('active');
			rightPane.addClass('active');
		}
	});
	leftPane.click(function(e){
		if(rightPane.hasClass('active'))
		{
			e.preventDefault();
			rightPane.removeClass('active');
			leftPane.addClass('active');
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

	$('#search').autocomplete({
		source: 'scripts/search.php?type=t&active=1',
		focus: function(event, ui){
			$('#tag-search').val(ui.item.label);
			return false;
		},
		select: function(event, ui){
			$('#search').attr('placeholder', ui.item.label);
			$('#search').val('')
			$.bbq.pushState(ui.item.value, 0);
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
		if($(this).parent().hasClass('active'))
		{
			e.preventDefault();
			activateBox();
		}
		else
		{
			$(this).parent().parent().children().removeClass('active');
			$(this).parent().addClass('active');
		}
	});


});


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
		writePosts();
	});

}

function writePosts()
{
	var markers = [];
	var list, button, tileLink, listLink, id;
	$.each(postings, function(index, post)
			{
				if(!initialized)
				{
					activePostings[index] = index;
				}
				activeFlag = false;
				list = document.createElement('div');
				button = document.createElement('div');

				tileLink = document.createElement('a');
				listLink = document.createElement('a');
				id = post['id'];

				var postLatLon = new google.maps.LatLng(post['lat'], post['lon']);
				markers[index] = new google.maps.Marker({
					position: postLatLon,
					title: post['title'],
					id: post['id'],
					icon: markerSprites[post.tag_1]
				});
				google.maps.event.addListener(markers[index], 'mouseover', function(e){highlightPosting(post.id);});
				google.maps.event.addListener(markers[index], 'mouseout', function(e){lowlightPosting(post.id);});
					markers[index].setMap(map);
				postings[index]['marker']=markers[index];
				oms.addMarker(markers[index]);


				tileLink.innerHTML = post['tile'];
				tileLink.setAttribute('href','#p='+id);
				tileLink.setAttribute('id', id);
				tileLink.setAttribute('class', 'post-trigger');
				tileLink.setAttribute('index', index);

				button.setAttribute('class', 'posting-list-button tile');
				button.appendChild(tileLink);
				button.innerHTML += "<div class='post-big'></div>";

				listLink.innerHTML = post['list'];
				listLink.setAttribute('href','#p='+id);
				listLink.setAttribute('id', id);
				listLink.setAttribute('class', 'post-trigger');
				listLink.setAttribute('index', index);
				listLink.innerHTML = post['list'] + "<div class='post-big'></div>";

				list.setAttribute('class', 'posting-list-button list');
				list.appendChild(listLink);
				list.innerHTML += "<div class='post-big'></div>";


				document.getElementById('list').appendChild(list);
				document.getElementById('tiles').appendChild(button);

				for(j=0; j<=index; j++)
				{
					if(activePostings[j] == index)
					{
						activeFlag = true;
					}
				}
				if(activeFlag)
				{
					markers[index].setMap(map);
				}
				else
				{
					$('#'+id).hide();
					$('#'+id).parent().hide();
				}
			});
		var postScript = document.createElement('script');
		postScript.innerHTML = "$('.posting-list-button').hover(function(e){"
			+"var i = $(this).children('.post-trigger').attr('index');"
			+"highlightPosting($(this).children('.post-trigger').attr('id'));"
			+"}, function(e){"
			+"var i = $(this).children('.post-trigger').attr('index');"
			+"lowlightPosting($(this).children('.post-trigger').attr('id'));"
			+"});";
	var ref = document.getElementsByTagName('script')[0];
	ref.parentNode.insertBefore(postScript, ref);

	displayPosts();

}
function displayPosts()
{
	var post, link, id;

	$('.posting-list-button').hide();
	$('.post-big').hide();

	for(var i=0; i<activePostings.length; i++)
	{
		post = postings[activePostings[i]];
		id = post.id;

		link = $('.tile>#'+id+', .list>#'+id);
		link.show().parent().show();


		if(active!=0 && active!=id)
		{
			post.marker.setMap(null);
			post.marker.setIcon(markerSprites['tndr']);
			post.marker.setMap(map);
		}
		else
		{
			post.marker.setIcon(markerSprites[post.tag_1]);
		}

	}
	repositionContainers();
}

function startLoading(targetDiv)
{
	targetDiv.prepend('<div class="loading"><img src="images/loading.gif"></div>');
}

function endLoading(targetDiv)
{

	targetDiv.children('.loading').remove();
}

function repositionContainers()
{

	var tndrContainer = $('#body-container');
	var tndrHeader = $('#tndr-header');
	var headerFiller = $('#tndr-header-filler');
	var leftPane = $('#left-pane');
	var tndr = $('#tndr');
	var tiles = $('#tiles');
	var rightPane = $('#right-pane');
	
	var box = $('#box.inactive');
	var middleBox = $('#middle-box');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');

	var window_width = $(window).innerWidth();
	var window_height = $(window).innerHeight();

	var header_height = tndrHeader.height();

	//box top position is the same no matter the screen size
//	$('#box.active').css('height',window_height);

	rightPane.css('height', window_height - (35 + header_height));
	//reset margins
	tndrContainer.css('margin-left', '');
//	box.css('margin-left', '');
	
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
	else if(760 < window_width)
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
	tndrContainer.css('width', tndrContainerWidth);
	tndrHeader.css('width', tndrContainerWidth);
	leftPane.css('width', leftPaneWidth);
	var buttonWidth = (leftPaneWidth-10)/postColumns - 10
	$('.post-mini.button').css('width', buttonWidth);
	rightPane.css('width', rightPaneWidth);
	rightPane.css('left', rightPaneLeft);
	if(rightPane.children().hasClass('initialized'))
	{
		google.maps.event.trigger(map, 'resize');
	}
	middleBox.css('width', tndrContainerWidth);

	box.css('width', tndrContainerWidth+160);
	$('#box-content').css('width', tndrContainerWidth+130);
	if(postingsFormat == 'tile')
	{
		if(tiles.hasClass('masonry'))
		{
			$('.tile>.post-big').css('width', (buttonWidth*numColumns)+(10*(numColumns-1)));
			tiles.masonry({columnWidth: buttonWidth});
			tiles.masonry('reload');
		}
		else
		{
			tiles.masonry({
				itemSelector: '.active-brick',
				isAnimated: true,
				gutterWidth: 10,
				columnWidth: buttonWidth
			});
			tiles.masonry('reload');
		}
	}
	if(!initialized)
	{
		endLoading($('#tndr'));
		toggleViewFormat();
		if($(window).innerWidth()>360)
		{
			toggleViewFormat();
		}
		initialized = true;
		lastURL = window.location.hash;
		$(window).trigger('hashchange');	

	}

}

function lastBoxState()
{
	var url = $.deparam(lastURL);
	return url.b;
}

function showBox()
{
	$.bbq.pushState('b='+lastBoxState());
}

function activateBox()
{
	$('#box').switchClass('inactive', 'active');
	$('body').addClass('inactive');
	$('#hide-box-button').show();
	$('#show-box-button').hide();
	$('#box-js-content').show();
}

function deactivateBox()
{

	$('#box').switchClass('active', 'inactive');
	$('#box-js-content').hide();
	$('body').removeClass('inactive');

	$('#hide-box-button').hide();

	$('#show-box-button').show();
	$.bbq.removeState(['b', 'view', 'id']);
}

function toggleViewFormat()
{
	var tileFormat = $('#tile-format');
	var listFormat = $('#list-format');
	var	tndr = $('#tndr');
	if(tileFormat.hasClass('disabled'))
    {
		listFormat.addClass('disabled');
		tileFormat.removeClass('disabled');
		postingsFormat = 'list';
		$('#tiles').hide();
		$('#list').show();
	}
	else
	{
		tileFormat.addClass('disabled');
		listFormat.removeClass('disabled');
		postingsFormat = 'tile';
		$('#list').hide();
		$('#tiles').show();
		if(initialized)
		{
			repositionContainers();
		}
	}
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
	var link = $('.tile>#'+id+', .list>#'+id);
	var index = link.attr('index');
	var post = postings[index];
	var marker = postings[index]['marker'];
	link.parent().addClass('highlight');
	marker.setMap(map);
	marker.setIcon(markerSprites[post.tag_1+'_a']);


}

function lowlightPosting(id)
{
	var link = $('.tile>#'+id+', .list>#'+id);
	var index = link.attr('index');
	var post = postings[index];
	var marker = postings[index]['marker'];
	link.parent().removeClass('highlight');


	if(active!=id && active !=0)
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
	for(i in formattedPostings)
	{
		formattedPostings[i]['marker'].setMap();
	}
}

function displayActiveMarkers()
{
	for(i in formattedPostings)
	{
		for(j=0; j<=i; j++)
		{
			if(activePostings[j] == i)
			{
				formattedPostings[i]['marker'].setMap(map);
			}
		}
	}
}

function loadPost(id){
	closePost();

	var link = $('.tile>#'+id+', .list>#'+id);

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


//	resetMarkers();


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
		if(fullPost.hasClass('loaded'))
		{
			active = id;
			fullPost.show('fast', function(){repositionContainers();});
		}
		else
		{
		startLoading(button);
			//call load
		$.get(url, function(data){
			fullPost.html(data);
				$('.tags a').fragment(window.location.hash, 1);
				if(rightPane.hasClass('active'))
				{
					rightPane.removeClass('active');
					leftPane.addClass('active');
				}
					endLoading(button);
				fullPost.show('fast', function(){		

					repositionContainers();
					scrollTo(id);

//					this.lastPosition = map.getCenter();
					var postLatLon = new google.maps.LatLng(post['lat'], post['lon']);
					map.panTo(postLatLon);
//					marker.setMap(map);
//					marker.setAnimation(google.maps.Animation.BOUNCE);


				});
			fullPost.addClass('loaded');
				active = id;	
			});
		}

/*					if($('#tiles').hasClass('masonry'))
					{
						$('#tiles').masonry('reload');
					}*/

}

function closePost(){
	var id = active
	var lastPosition = this.lastPosition;

	if(id != 0)
	{
		for(var i=0; i<postings.length; i++)
		{
			var post = postings[i];
			if(post.id != id)
			{
				post.marker.setIcon(markerSprites[post['tag_1']]);
			}
		}
		var link = $('.tile>#'+id+', .list>#'+id);
		var index = link.attr('index');

		var post = postings[index];
//		var marker = formattedPostings[index]['marker'];
		var postMini = link.children('.post-mini');
		var button = link.parent();
		var fullPost = button.children('.post-big');





		fullPost.hide('fast');//, function(){
			postMini.show('fast', function(){
				repositionContainers();
			});
//		}); 
		button.removeClass('triggered');




//			marker.setAnimation(null);


//		displayActiveMarkers();
//		map.setCenter(lastPosition);
		active = 0;
	}

}

function closePostButton(){
	$.bbq.pushState('p=');
}

function scrollTo(id)
{	
	var window_width = $(window).innerWidth();

	var post = $('#'+id);//.parent();

		$('html, body'). animate({
			scrollTop: post.offset().top -100
			}, 500);

}

function initMarkerSprites()
{
	var path = 'images/tndr-sprites.png';
	var size = new google.maps.Size(20, 13, 'px', 'px');

	var origin = new google.maps.Point(294, 169);
	markerSprites['tndr'] = new google.maps.MarkerImage(path, size, origin, null, null);
	
	size = new google.maps.Size(30, 50, 'px', 'px');
	origin = new google.maps.Point(0, 52);
	markerSprites['Advocacy'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(0, 104);
	markerSprites['Advocacy_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(28, 52);
	markerSprites['Art'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(29, 105);
	markerSprites['Art_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(57, 53);
	markerSprites['Community'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(60, 105);
	markerSprites['Community_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(87, 53);
	markerSprites['Drinks'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(89, 104);
	markerSprites['Drinks_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(118, 53);
	markerSprites['Education'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(117, 105);
	markerSprites['Education_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(148, 54);
	markerSprites['Entertainment'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(148, 106);
	markerSprites['Entertainment_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(180, 54);
	markerSprites['Fashion'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(178, 107);
	markerSprites['Fashion_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(209, 53);
	markerSprites['Food'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(207, 105);
	markerSprites['Food_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(237, 53);
	markerSprites['Health'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(237, 105);
	markerSprites['Health_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(266, 53);
	markerSprites['Music'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(267, 104);
	markerSprites['Music_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(294, 54);
	markerSprites['Other'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(296, 104);
	markerSprites['Other_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(325, 55);
	markerSprites['Recreation'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(325, 104);
	markerSprites['Recreation_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(353, 53);
	markerSprites['Spirituality'] = new google.maps.MarkerImage(path, size, origin, null, null);
	origin = new google.maps.Point(353, 104);
	markerSprites['Spirituality_a'] = new google.maps.MarkerImage(path, size, origin, null, null);
}

function mapInitialize(callback) {
//location functionality
//var myLatLon = new google.maps.LatLng(json_location.lat, json_location.lon);
	var temescalLatLon = new google.maps.LatLng(37.833222, -122.264222);
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
		minZoom: 11,
		maxZoom: 18,
		center: temescalLatLon,//myLatLon,
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
						view = 'new_post';
					}

					if(view == 'new_post')
					{
						smartLoad('partials/new_post_form');
						$('#new-post-li').addClass('active');
					}

					if(view == 'edit_post')
					{
						smartLoad('partials/edit_post_form'+append_string);
						$('#posts-li').addClass('active');
					}

					if(view == 'delete_post')
					{	
						$.ajax({
							url:'scripts/delete_post',
							data: {'id': id},
							type: 'get'
						}).done(function(){
							smartLoad('partials/posts');
							$('#posts-li').addClass('active');
						});
					}
				
					if(view == 'deactivate_post')
					{	
						$.ajax({
							url:'scripts/deactivate_post',
							data: {'id': id},
							type: 'get'
						}).done(function(){
							smartLoad('partials/posts');
							$('#posts-li').addClass('active');
						});
					}
				
					if(view == 'posts')
					{
						smartLoad('partials/posts'+append_string);
						$('#posts-li').addClass('active');
					}
				
					if(view == 'edit_profile')
					{	
						smartLoad('partials/edit_profile_forms'+append_string);
						$('#profile-li').addClass('active');
					}
				
					if(view == 'new_business')
					{
						smartLoad('partials/new_business_form');
						$('#new-business-li').addClass('active');
					}
				
					if(view == 'new_user')
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
		$('#js-content').show('fast');
	});
}