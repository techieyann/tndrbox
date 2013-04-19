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
this.active = 0;

function initPage()
{
	var tndrContainer = $('#body-container');
	var box = $('#box');
	var middleBox = $('#middle-box');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');



	mapInitialize(afterMapInitialize);

	//prep the meta tndr buttons
	$('#reset-filters-button').hide();
	$('#search-bar').hide();
	$('#categories-dropdown').hide();
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

	getPosts();


	endLoading('tndr');
	repositionContainers();

	initialized = true;

	function afterMapInitialize(){
		if($(window).innerWidth()<360)
		{
			toggleViewFormat();
		}

		oms = new OverlappingMarkerSpiderfier(map);

		oms.addListener('click', function(marker) {
			$.bbq.pushState('p='+marker.id);
		});

		for(i in formattedPostings)
		{
			oms.addMarker(formattedPostings[i]['marker']);
		}
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
					categoryHtml += ' <span class="caret">'
				}
			});
			$('#categories-dropdown>a').html(categoryHtml);
			filterFlag = true;
		}
		if(tagFlag && (query.t != $.deparam(lastURL, 't')))
		{

			filterFlag = true;
		}
		if(dateFlag && (query.d != $.deparam(lastURL, 'd')))
		{

			filterFlag = true;
		}
		if(filterFlag)
		{
		activePostings = [];
			var j=0;

			for(i in formattedPostings)
			{
				if(tagFlag && (query.t == formattedPostings[i]['content']['tag_2_id'])||(query.t == formattedPostings[i]['content']['tag_3_id']))
				{
					if(categoryFlag)
					{
						if(query.c == formattedPostings[i]['content']['tag_1_id'])
						{
							activePostings[j] = i;
							j++;
						}
					}
					else
					{
						activePostings[j] = i;
						j++;
					}
				}
				else if(categoryFlag && query.c == formattedPostings[i]['content']['tag_1_id'])
				{
					activePostings[j] = i;
					j++;
				}
				else if(dateFlag)
				{
//herp derp
				}
			}
			displayPosts();

		}
		else if(!dateFlag && !tagFlag && !categoryFlag)
		{
		activePostings = [];
			for(i in formattedPostings)
			{

				activePostings[i] = i;
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
	$('#tndr a').fragment(window.location.hash, 1);

	lastUrl = window.location.hash;
	});



	rightPane.click(function(e){
		if(leftPane.hasClass('active'))
		{
			leftPane.removeClass('active');
			rightPane.addClass('active');

		}
	});
	leftPane.click(function(e){
		if(rightPane.hasClass('active'))
		{
			rightPane.removeClass('active');
			leftPane.addClass('active');
		}
	});
	$('.format-button').on('click', function(e){
		toggleViewFormat();
	});

	$('#filters').click(function() {
		toggleFilterView();
	});

	$('#reset-filters-button').click(function(){
		$.bbq.pushState({'c':'', 't':''});
		$('#categories-dropdown>a').html("<i class='icon-folder-open'></i>&nbsp Category <span class='caret'></span>");
		$('#search').attr('placeholder','');
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

		$(window).trigger('hashchange');	
		lastUrl = window.location.hash;
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

		for(i in postings)
			{
				var index = i;
				var post = postings[i];
				activeFlag = false;
				list = document.createElement('div');
				button = document.createElement('div');

				postLink = document.createElement('a');
				id = post['id'];
/*				markers[i] = post['marker'];
				console.log(post['marker']);
				markers[i].setMap();*/
				postLink.innerHTML = post['tile'] + "<div class='post-big'></div>";
				postLink.setAttribute('href','#p='+id);
				postLink.setAttribute('id', id);
				postLink.setAttribute('class', 'post-trigger');
				postLink.setAttribute('index', index);
				button.setAttribute('class', 'posting-list-button tile');

				button.appendChild(postLink);



				postLink.innerHTML = '';
				postLink.innerHTML = post['list'] + "<div class='post-big'></div>";
				list.setAttribute('class', 'posting-list-button list');
				list.appendChild(postLink);

				document.getElementById('tiles').appendChild(button);
				document.getElementById('list').appendChild(list);
				for(j=0; j<=i; j++)
				{
					if(activePostings[j] == index)
					{
						activeFlag = true;
					}
				}
				if(activeFlag)
				{
//					markers[i].setMap(map);
				}
				else
				{
					$('#'+id).hide();
					$('#'+id).parent().hide();
				}
			}
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
	$('.posting-list-button').show().children().show();
		repositionContainers();
}

function startLoading(targetDiv)
{
	$('#'+targetDiv).prepend('<div class="loading"><img src="images/loading.gif"></div>');
}

function endLoading(targetDiv)
{

	$('#'+targetDiv+'>.loading').remove();
}

function repositionContainers()
{

	var tndrContainer = $('#body-container');
	var tndrHeader = $('#tndr-header');
	var headerFiller = $('#tndr-header-filler');
	var leftPane = $('#left-pane');
	var tndr = $('#tndr');
	var rightPane = $('#right-pane');
	
	var box = $('#box.inactive');
	var middleBox = $('#middle-box');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');

	var window_width = $(window).innerWidth();
	var window_height = $(window).innerHeight();

	var header_height = tndrHeader.height();

	//box top position is the same no matter the screen size
	$('#box.active').css('height',window_height);

	rightPane.css('height', window_height - (35 + header_height));
	//reset margins
	tndrContainer.css('margin-left', '');
	box.css('margin-left', '');
	
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
		if(tndr.hasClass('masonry'))
		{
			$('.triggered>.post-big').css('width', (buttonWidth*numColumns)+(10*(numColumns-1)));
			tndr.masonry({columnWidth: buttonWidth});
			tndr.masonry('reload');
		}
		else
		{
			tndr.masonry({
				itemSelector: '.posting-list-button',
				isAnimated: true,
				gutterWidth: 10,
				columnWidth: buttonWidth
			});
			tndr.masonry('reload');
		}
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
	$('#box').switchClass('inactive', 'active', 1000);
	$('body').addClass('inactive');
	$('#hide-box-button').show();
	$('#show-box-button').hide();
	$('#box-js-content').show();
}

function deactivateBox()
{

	$('#box').switchClass('active', 'inactive', 1000);
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
		$('#tile').hide();
		$('#list').show();
	}
	else
	{
		tileFormat.addClass('disabled');
		listFormat.removeClass('disabled');
		postingsFormat = 'tile';
		$('#list').hide();
		$('#tile').show();
	}
	closePost();
	$(window).trigger('hashchange');
}
function toggleFilterView()
{
	if(document.getElementById('filters').classList.contains('disabled'))
	{
		$('#filters').removeClass('disabled');
		$('#categories-dropdown').hide();
		$('#search-bar').hide();
	}
	else
	{
		$('#filters').addClass('disabled');
		$('#categories-dropdown').show();
		$('#search-bar').show();
	}	
}
function highlightPosting(id)
{
	var link = $('#'+id);
	var index = link.attr('index');
	var post = postings[index];
	var marker = formattedPostings[index]['marker'];
	link.parent().addClass('highlight');
	marker.setMap(map);
	marker.setIcon('images/markers/'+post.tag_1+'_a.png');


}

function lowlightPosting(id)
{
	var link = $('#'+id);
	var index = link.attr('index');
	var post = postings[index];
	var marker = formattedPostings[index]['marker'];
	link.parent().removeClass('highlight');


	if(active!=id &&  active !=0)
	{
		marker.setMap();
	}

	marker.setIcon('images/markers/'+post.tag_1+'.png');

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
	var link = $('#'+id);
	var index = link.attr('index');
	var post = postings[index];
	var marker = formattedPostings[index]['marker'];

	var rightPane = $('#right-pane');
	var leftPane = $('#left-pane');

	var postMini = link.children('.post-mini');
	var button = link.parent();
	var partial = button.children('.post-big');
	var url = 'partials/posting?p='+id;
	var docPostings = document.getElementById('tndr');
	var loadingDiv = document.createElement('div');

	resetMarkers();

	loadingDiv.innerHTML = "<div class='loading'><img src='images/loading.gif'></div>";
	//hide content divs
	partial.hide('fast');
	postMini.hide('fast', function(){
		button.addClass('triggered');
		button.prepend(loadingDiv);
			//call load

			partial.load(url, function(){
				$('.tags a').fragment(window.location.hash, 1);
				partial.show('fast', function(){		
					repositionContainers();
					if($('#tndr').hasClass('masonry'))
					{
						$('#tndr').masonry('reload');
					}
					scrollTo(id);

					this.lastPosition = map.getCenter();
					var postLatLon = new google.maps.LatLng(post['lat'], post['lon']);
					map.panTo(postLatLon);
					marker.setMap(map);
					marker.setAnimation(google.maps.Animation.BOUNCE);
					$('.loading').remove();
					if(rightPane.hasClass('active'))
					{
						rightPane.removeClass('active');
						leftPane.addClass('active');
					}
				});
					active = id;

			});

	});

}

function closePost(){
	var id = active
	var lastPosition = this.lastPosition;

	if(id != 0)
	{
		var link = $('#'+id);
		var index = link.attr('index');

		var post = postings[index];
		var marker = formattedPostings[index]['marker'];
		var postMini = link.children('.post-mini');
		var button = link.parent();
		var partial = button.children('.post-big');


		partial.hide("fast", function(){
			button.removeClass('triggered');
		});	

		postMini.show("fast", function(){
			if(document.getElementById('tndr').classList.contains('masonry'))
			{
				$('#tndr').masonry('reload');
			}
			repositionContainers();
			marker.setAnimation(null);

		});
		displayActiveMarkers();
		map.setCenter(lastPosition);
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