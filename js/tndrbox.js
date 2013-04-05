/***********************************************
file: tndrbox.js
creator: Ian McEachern

This is the javascript responsible for page 
initialization, responsive div placement and 
sizing, and # link handling. 
 ***********************************************/
var initialized = false;
var formattedPostings = [];
var activePostings = [];
var postingsFormat = 'tile';
var lastURL = '';
this.active = 0;

function initPage()
{
	var tndrContainer = $('#body-container');
	var box = $('#box');
	var middleBox = $('#middle-box');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');

	//if js enabled, launch the welcome boat!
	$('#welcome-page-content').html("<h4>We are a community events board. We hope you find something that interests you.");//<br><br> For an introduction to our site, click <button id='global-intro-button' class='btn-primary' onclick='introJs().start()'>here</button></h4><br>");
	//so anticlimactic, I know..

	//write header
	var headerHTML = getTndrHeader();
	document.getElementById('tndr-header').appendChild(headerHTML);
	map_initialize(afterMapInitialize);


	initialized = true;

	function afterMapInitialize(){
/*		var tndrContainer = $('#body-container');
		var box = $('#box');*/




		$('#search-bar').hide();
		$('#categories-dropdown').hide();
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


		displayPosts();
		
		//show content
		box.show();
		tndrContainer.show();




		//initilialize header offset
		this.tndrHeader_toTop = $('#tndr-header').offset().top;



		repositionContainers();

	}

}

$(document).ready(function(){
	var tndr = $('#body-container');
	var box = $('#box');
	var middleBox = $('#middle-box');
	var frontBox = $('#front-of-box');
	var boxLinks = $('#box-links');
	var rightPane = $('#right-pane');
	var leftPane = $('#left-pane');

	formatPosts();

	initPage();

	window.onscroll = function(){
		toggleStickyHeader();
	};

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
//post requests
		if(postFlag)
		{
			loadPost(query.p);
		}
		else
		{
			closePost();
		}
//filter requests

		if(categoryFlag && (query.c != $.deparam(lastURL, 'c')))
		{
			
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
				if(categoryFlag && query.c == formattedPostings[i]['content']['tag_1_id'])
				{
					activePostings[j] = i;
					j++;
				}
				else if(tagFlag && (query.t == formattedPostings[i]['content']['tag_2_id'])||(query.t == formattedPostings[i]['content']['tag_3_id']))
				{
					activePostings[j] = i;
					j++;
				}
				else if(dateFlag )
				{

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

//box requests
		if(boxFlag)
		{
			if(query.b == 'logout')
			{
				$.ajax('../scripts/logout');
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
					$('#box-js-content').load('../partials/'+query.b, function(){	
						$('#'+query.b+'-link').addClass('active');
					});
				}
				else if(query.b == 'members')
			    {
					if(lastBoxState() != 'members')
					{
						$('#box-js-content').load('../partials/'+query.b, function(){	
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
//		$('#body-container a').fragment(window.location.hash, 1);

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
		$(window).trigger('hashchange');
		}
	});

	$('#filters').click(function() {
		toggleFilterView();
	});



	$('#search').autocomplete({
		source: 'scripts/search.php?type=t&active=1',
		focus: function(event, ui){
			$('#tag-search').val(ui.item.label);
			return false;
		},
		select: function(event, ui){
			$.bbq.pushState(ui.item.value, 2);
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
//	$('#box.active').css('top',-62);
//	box.css('top', window_height-105);

	
	rightPane.css('height', window_height - (45 + header_height));
	//reset margins
	tndrContainer.css('margin-left', '');
	box.css('margin-left', '');
	
	//set for large screens where both panes are fully displayed
	var tndrContainerWidth = window_width - 140;
	var leftPaneWidth = tndrContainerWidth*.62;
	var rightPaneWidth = tndrContainerWidth - leftPaneWidth;
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
	else if(754 < window_width)
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
		tndrContainer.css('margin-left',-20);
		box.css('margin-left', -70);
		postColumns = 3;
		if(window_width < 500)
		{	
			postColumns = 2;
		}	
		if(window_width < 360)
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

		}
	}
	if(tndrHeader.hasClass('sticky'))
   {

		rightPane.css('top', tndrHeader.height()+2);
		var marginLeft = parseInt(tndrContainer.css('marginLeft'), 10);
		if(marginLeft == -20)
		{
			marginLeft = 0;
		}
		rightPane.css('left', tndrContainer.width()-rightPane.width()+marginLeft);
	   if(window_width > 754)
	   {
		tndrHeader.css({'left' : ''});
	   }
	   else
	   {
		tndrHeader.css({'left' : '0'});
	   }
	tndrHeader_toTop = headerFiller.offset().top;
   }
	else
	{
		rightPane.css('top', '');
		rightPane.css('left', '');
		tndrHeader.css({'left' : ''});
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
function toggleStickyHeader(){
	var tndrContainer = $('#body-container');
	var header = $('#tndr-header');
	var headerFiller = $('#tndr-header-filler');
	var rightPane = $('#right-pane');
	var leftPane = $('#left-pane');
	var window_width = $(window).innerWidth();
	//if stickty and shoudln't be
	if(header.hasClass('sticky'))
	{
		tndrHeader_toTop = headerFiller.offset().top;
		if($(document).scrollTop() < tndrHeader_toTop)
		{
			header.addClass('rounded-top');
			header.removeClass('sticky');
			header.css({'left' : ''});
			headerFiller.hide();
			rightPane.removeClass('sticky');
			rightPane.css('top', '');
			rightPane.css('left', '');
		}
	}
	else if($(document).scrollTop() > this.tndrHeader_toTop)
	{
		header.addClass('sticky');
		rightPane.addClass('sticky');
		rightPane.css('top', header.height()+2);
		var marginLeft = parseInt(tndrContainer.css('marginLeft'), 10);
		if(marginLeft == -20)
		{
			marginLeft = 0;
		}
		rightPane.css('left', tndrContainer.width()-rightPane.width()+marginLeft);
		header.removeClass('rounded-top');
		headerFiller.css('height', header.height());
		headerFiller.show();

		if(window_width < 754)
		{
			header.css('left', 0);
		}
		
	}
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
	$('#'+id).parent().addClass('highlight');
}

function lowlightPosting(id)
{
	$('#'+id).parent().removeClass('highlight');
}
function loadPost(id){
	closePost();
	var link = $('#'+id);
	var index = link.attr('index');
	var post = postings[index];
	var marker = formattedPostings[index]['marker'];


	var postMini = link.children('.post-mini');
	var button = link.parent();
	var partial = button.children('.post-big');
	var url = 'partials/posting?p='+id;
	var docPostings = document.getElementById('tndr');
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
					repositionContainers();
					if($('#tndr').hasClass('masonry'))
					{
						$('#tndr').masonry('reload');
					}
					scrollTo(id);

					this.lastPosition = map.getCenter();
					var postLatLon = new google.maps.LatLng(post['lat'], post['lon']);
					map.panTo(postLatLon);

					marker.setIcon('../images/markers/'+post.tag_1+'_a.png');
					$('.loading').remove();

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
			marker.setIcon('../images/markers/'+post.tag_1+'.png');
		});

		map.setCenter(lastPosition);
		active = 0;
	}

}

function closePostButton(){
	$.bbq.removeState('p');
}

function scrollTo(id)
{	
	var window_width = $(window).innerWidth();

	var post = $('#'+id);//.parent();

		$('html, body'). animate({
			scrollTop: post.offset().top -100
			}, 500);

}


function getTndrHeader() {
	var headerString = document.createElement('ul');
	headerString.setAttribute('class', 'inline');
	
	var logo = document.createElement('li');
	logo.innerHTML = "<a href='/'><img src='images/logo.png'></a>"		

	var buttons = document.createElement('li');
	buttons.setAttribute('id', 'header-buttons');

	var buttonsUl = document.createElement('ul');
	buttonsUl.setAttribute('data-step', '1');
	buttonsUl.setAttribute('data-intro', 'These buttons let you change the display of the posts and search for both businesses and tags.');
	buttonsUl.setAttribute('data-position', 'top');
	buttonsUl.setAttribute('class', 'inline');

	var formatButtons = document.createElement('li');
	formatButtons.innerHTML = "<div class='btn-group'>"
		+"<button title='Tiles' id='tile' class='format-button btn disabled'><i class='icon-th-large'></i></button>"
		+"<button title='List' id='list' class='format-button btn'><i class='icon-list'></i></button>"
		+"</div>";

	var filterButton = document.createElement('li');
	filterButton.innerHTML = "<button title='Filter' id='filters' class='btn' ><i class='icon-search'></i></button>";

	var searchBar = document.createElement('li');
	searchBar.innerHTML = "<form id='search-bar' class='form-inline'>"
		+"<div class='input-prepend'>"
		+"<span class='add-on'><i class='icon-tags'></i></span>"
		+"<input type='text' id='search' name='search' class='span2' placeholder=''>"
		+"</div><!-- .input-prepend -->"
		+"</form>";

	var categoriesDiv = document.createElement('li');
	categoriesDiv.setAttribute('class', 'btn-group');
	categoriesDiv.setAttribute('id', 'categories-dropdown');
	categoriesDiv.innerHTML = "<a class='btn dropdown-toggle' data-toggle='dropdown' href='#'>"
		+"<i class='icon-folder-open'></i>&nbsp Category "							
		+"<span class='caret'></span>"
		+"</a>";
	var categoriesString = document.createElement('ul');
	categoriesString.setAttribute('class', 'dropdown-menu');
	categoriesString.setAttribute('z-index', '101');
	categoriesString.innerHTML = "<ul class='dropdown-menu'>";
		jQuery.each(categories, function(i, val){
		if(i != 0)
			{
				categoriesString.innerHTML += "<li class='divider'></li>";
			}
			categoriesString.innerHTML += "<li><a href='#c="+val.id+"'><img src='images/icons/"+val.tag+".png' width='30px'>&nbsp"+val.tag+"</a></li>";
		});
	categoriesDiv.appendChild(categoriesString);
	
	buttonsUl.appendChild(formatButtons);
	buttonsUl.appendChild(filterButton);
	buttonsUl.appendChild(searchBar);
	buttonsUl.appendChild(categoriesDiv);

	buttons.appendChild(buttonsUl);

	headerString.appendChild(logo);
	headerString.appendChild(buttons);
	return headerString;
}

function map_initialize(callback) {
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
		zoom: 16,
		minZoom: 14,
		center: temescalLatLon,//myLatLon,
		disableDefaultUI: true,
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