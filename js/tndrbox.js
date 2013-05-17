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
var markerSprites = [];
var ogSearchPlaceholder = '';

var activePost;
var activePostId = 0;
var activeCat = 0;

var activeTag = 0;

var activeTagOp = 'and';
var tagOpChange = false;
var welcomePageExpanded = true;

var lastBoxState = '';


function initPage()
{
	var tndrContainer = $('#body-container');
	var box = $('#box');
	var middleBox = $('#middle-box');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');

	if($(window).innerWidth() < 768)
	{
		welcomePageExpanded = false;
	}

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
		if(categoryFlag)
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
			if(query.c != activeCat)
			{
				activeCat = query.c;
				$('.tags a').fragment({'c':''+activeCat}, 0);
				filterFlag = true;
			}
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
					$('.tile>#'+postId).parent().addClass('active-brick');
					$('.tile>#'+postId+', .list>#'+postId).fragment({'c':query.c, 't': query.t});
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
				postId = postings[i]['id'];
				activePostings[i] = i;
				$('.tile>#'+postId).parent().addClass('active-brick');
				$('.tile>#'+postId+', .list>#'+postId).fragment({'c':'', 't':''});
				postings[i]['marker'].setMap(map);
			}

			displayPosts();
			activeCat = 0;
			activeTag = 0;
		}
//post requests
		if(postFlag && query.p != activePostId)
		{
			loadPost(query.p);
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
	lastURL = window.location.hash;



	});
	$('.ui-accordion').click(function(e){
		repositionContainers();
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
			leftPane.removeClass('active');
			rightPane.addClass('active');
		}

	});
	leftPane.click(function(e){
		if(rightPane.hasClass('active')  && ($(window).innerWidth() < 768))
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
	$('#body-container').click(function(e){
		if($('body').hasClass('inactive'))
		   {
			   e.preventDefault();
			   deactivateBox();
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


		if(activePostId!=0 && activePostId!=id)
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
	
	var box = $('#box');
	var boxWings = $('#box-left, #box-right');
	var activeBoxWings = $('#box-left.active, #box-right.active');
	var activeBox = $('#box.active');
	var middleBox = $('#middle-box');
	var leftBox = $('#box-left');
	var rightBox = $('#box-right');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');

	var window_width = $(window).innerWidth();
	var window_height = $(window).innerHeight();

	var header_height = tndrHeader.height();

	if(window_width > 768)
	{
		rightPane.css('height', window_height - (65 + header_height));
	}
	else
	{
		rightPane.css('height', window_height - (30 + header_height));
	}
	//reset margins
	tndrContainer.css('margin-left', '');

	
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
	middleBox.css('width', tndrContainerWidth);

//	box.css('width', tndrContainerWidth+160);
	$('#box-content').css('width', tndrContainerWidth+130);
	box.css('top',window_height-65);
	boxWings.css('top',window_height-126);

	if(window_width < 769)
	{
			box.css('top',window_height-37);
		boxWings.hide();
	}
	else
	{
		boxWings.show();
	}
		var boxHeight = 	$('#box-content').height();
	var boxTop = 66;
		var boxWingsTop = 5;
		if(boxHeight < window_height-65)
		{
			boxTop = window_height - boxHeight + 2;
			boxWingsTop = boxTop-61;
		}
		activeBox.css('top',boxTop);
		activeBoxWings.css('top',boxWingsTop);		

	if(box.hasClass('active'))
	{

		var boxHeight = window_height - boxTop;

		box.css('height', boxHeight);
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
		if(postRequest)
		{
			var postId = $.deparam.querystring().p;
			$.bbq.pushState('p='+postId);
		}
		else
		{
			$(window).trigger('hashchange');	
		}
	}
	else
	{
	scrollToActive();
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

function toggleViewFormat()
{
	var tileFormat = $('#tile-format');
	var listFormat = $('#list-format');
	var	tndr = $('#tndr');
	if(tileFormat.hasClass('disabled'))
    {
		listFormat.addClass('disabled');
		tileFormat.removeClass('disabled');

		$('#tiles').hide();
		$('#list').show();
	}
	else
	{
		tileFormat.addClass('disabled');
		listFormat.removeClass('disabled');

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

	marker.setIcon(markerSprites[post.tag_1+'_a']);


}

function lowlightPosting(id)
{
	var link = $('.tile>#'+id+', .list>#'+id);
	var index = link.attr('index');
	var post = postings[index];
	var marker = postings[index]['marker'];
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
			activePostId = id;
			fullPost.show('fast', function(){repositionContainers();});
			fullPost.addClass('active');
			var postLatLon = new google.maps.LatLng(post['lat'], post['lon']);
			map.panTo(postLatLon);

		}
		else
		{
		startLoading(button);
			//call load
		$.get(url, function(data){
			fullPost.html(data);

				if(rightPane.hasClass('active'))
				{
					rightPane.removeClass('active');
					leftPane.addClass('active');
				}
					endLoading(button);
				fullPost.show('fast', function(){		

					repositionContainers();

					var postLatLon = new google.maps.LatLng(post['lat'], post['lon']);

					map.panTo(postLatLon);
					fullPost.find('.share').hide();



				});
			fullPost.addClass('loaded');
				activePostId = id;	
			fullPost.addClass('active');
			});
		}



}

function closePost(){
	var id = activePostId;
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






		link.next().hide('fast');//, function(){
		link.next().removeClass('active');
			postMini.show('fast', function(){
				repositionContainers();
			});
//		}); 
		button.removeClass('triggered');




//			marker.setAnimation(null);


//		displayActiveMarkers();
//		map.setCenter(lastPosition);
		activePostId = 0;
		activePost = null;
	}

}

function closePostButton(){
	$.bbq.pushState('p=');
}

function sparkPost(id)
{
	var sparkButtons = $('.tile,.list').find('#spark-'+id);

	sparkButtons.addClass('disabled');
	sparkButtons.children().switchClass('unlit','lighting', function(){
		sparkButtons.parent().next().children().slideDown('fast', function(){
			if($('#tile-format').hasClass('disabled'))	
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

function scrollToActive()
{	
	if(activePostId != 0)
	{
		var top;

		if($('#tile-format').hasClass('disabled'))
		{
			top = $('.tile>.active').offset().top;

		}
		else
		{
			top = $('.list>.active').offset().top;

		}

//		top -= 100;

		$('html, body').animate({
			scrollTop: top
			}, 'fast');
	}
}

function initMarkerSprites()
{
	var path = 'images/tndr-sprites.png';
	var size = new google.maps.Size(20, 13, 'px', 'px');

	var origin = new google.maps.Point(295, 164);
	markerSprites['tndr'] = new google.maps.MarkerImage(path, size, origin, null, null);
	
	size = new google.maps.Size(30, 50, 'px', 'px');
	var xPos = -29;
	var yPos;

	$.each(categories, function(index, value) {
		yPos = 53;
		xPos = xPos + 29;
		var markerIndex = value.tag;			

		origin = new google.maps.Point(xPos, yPos); // 0, 53
		markerSprites[markerIndex] = new google.maps.MarkerImage(path, size, origin, null, null);

		yPos = 105;
		markerIndex += '_a';

		origin = new google.maps.Point(xPos, yPos); // 0, 53
		markerSprites[markerIndex] = new google.maps.MarkerImage(path, size, origin, null, null);
	});
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