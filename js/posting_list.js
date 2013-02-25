   	function formatPost(post)
	{
		var result = [];
			result['tile'] = "<div class='span3 front-page-button'>"
					+"<div class='front-page-button-header'>"+post.tag_1+"</div>"
						+"<div class='front-page-button-body'>";

			if(post.photo != "")
			{
				result['tile'] += "<img alt='photo for "+post.title+"' src='images/posts/"+post.photo+"'>";
			}

			result['tile'] += "<div class='front-page-button-text'>"
						+"<h4>"+post.title+"</h4>"
						+"<p>by, "+post.business+"</p>"
						+"<p>"+post.date+"</p>"
						+"<ul class='inline centered'>"
							+"<li class='tag' id='"+post.tag_2_id+"'>"+post.tag_2+"</li>"
							+"<li class='tag' id='"+post.tag_3_id+"'>"+post.tag_3+"</li>"
						+"</ul>"
					+"</div>" 
				+"</div>"
			+"</div>";

			result['list'] = "<div class='front-page-list-element'>"
					+"<h3 class='centered'>"+post.title+"</h3>"
					+"<ul class='inline'>"
						+"<li class='third'><ul class='inline'>"
							+"<li class='tag' id='"+post.tag_1_id+"'>"
								+"<img src='images/icons/"+post.tag_1+".png' width='35'>"
								+post.tag_1+"</li>"
							+"<li class='tag' id='"+post.tag_2_id+"'>"+post.tag_2+"</li>"
							+"<li class='tag' id='"+post.tag_3_id+"'>"+post.tag_3+"</li>"
						+"</ul></li>"
						+"<li class='centered third'>by "+post.business+"</li>"
						+"<li class='pull-right'>on "+post.date+"</li>"
					+"</ul>"
				+"</div>";

		return result;
	}

	function calculateMeta(post)
	{
		var result = [];
		
		//distance and speed
		var myLat = json_location.lat;
		var myLon = json_location.lon;

		var latDelta = 1000*(post.lat-myLat);
		var lonDelta = 1000*(post.lon-myLon);
		

		result['distance'] = (latDelta^2 + lonDelta^2)^(.5);
		result['time'] = result['distance']/post.speed;


		//map marker
		var postLatLon = new google.maps.LatLng(post.lat, post.lon);
		var marker = new google.maps.Marker({
			position: postLatLon,
			title: post.title,
			icon: 'images/icons/'+post.tag_1+'.png'
		});
		google.maps.event.addListener(marker, 'click', function(e){loadModal(post.id);});
		google.maps.event.addListener(marker, 'mouseover', function(e){highlightPosting(post.id); scrollTo(post.id);});
		google.maps.event.addListener(marker, 'mouseout', function(e){lowlightPosting(post.id);});
		result['marker'] = marker;
		return result;
	}

	function recalculatePostsMeta()
	{

	}

	function displayPosts()
	{
		var postings, post, id, markers;
		postings  = document.getElementById('postings');
		markers = [];
		postings.innerHTML = "";
		if(postings.classList.contains('masonry'))
			{
				$('#postings').masonry('destroy');
			}
		for(i in formattedPostings)
			{
				post = document.createElement('a');
				id = formattedPostings[i]['id'];
				markers[i] = formattedPostings[i]['marker'];

				post.innerHTML = formattedPostings[i][postingsFormat];
				post.setAttribute('href','#');
				post.setAttribute('id', id);
				post.setAttribute('class', 'modal-trigger');
				post.setAttribute('index', formattedPostings[i]['index']);
				postings.appendChild(post);
				

				markers[i].setMap(this.map);
			}
		var modalScript = document.createElement('script');
		modalScript.innerHTML = "$('.modal-trigger').click(function(e){"
			+"var id = $(this).attr('id');"
			+"loadModal(id);"
			+"var stateObj = id;"
			+"var search = window.location.search;"
			+"var uri = addParameter(search, 'p', id);"
			+"history.pushState(stateObj, null, uri);"
			+"e.preventDefault();"
			+"});"
			+"$('.modal-trigger').hover(function(e){"
			+"var i = $(this).attr('index');"
			+"highlightPosting($(this).attr('id'));"
			+"formattedPostings[i]['marker'].setAnimation(google.maps.Animation.BOUNCE);"
			+"}, function(e){"
			+"var i = $(this).attr('index');"
			+"lowlightPosting($(this).attr('id'));"
			+"formattedPostings[i]['marker'].setAnimation(null);"
			+"});";
		postings.appendChild(modalScript);

		switch(postingsFormat)
		{
		case 'tile':
			resizeContainer();
			$('#postings').masonry({
				itemSelector: '.front-page-button',
				isAnimated: true,
				gutterWidth: 10
			});
			break;
		default:
			break;
		}
	}