   	function formatPost(post)
	{
		var result = [];
			result['tile'] = "<div class='post-mini button'>"
					+"<div class='front-page-button-header'>"+post.tag_1+"</div>"
						+"<div class='front-page-button-body'>";

			if(post.photo != "")
			{
				result['tile'] += "<img alt='photo for "+post.title+"' src='images/posts/"+post.photo+"'>";
			}

			result['tile'] += "<div class='front-page-button-text'>"
						+"<h4>"+post.title+"</h4>"
			+"<p>"+post.business+"</p>";

		if(post.date != null)
		{
			result['tile'] += "<p>"+post.date+"</p>";
		}
			result['tile'] +="</div>" 
				+"</div>"
			+"</div>";

			result['list'] = "<div class='post-mini li row'>"
					+"<div class='span7'>"+post.title+" by "+post.business+"</div>"
					+"<ul class='inline span5'>"
							+"<li class='tag' id='"+post.tag_1_id+"'>"
								+"<img src='images/icons/"+post.tag_1+".png' width='35'>"
								+post.tag_1+"</li>"
							+"<li class='tag' id='"+post.tag_2_id+"'>"+post.tag_2+"</li>"
							+"<li class='tag' id='"+post.tag_3_id+"'>"+post.tag_3+"</li>"
						+"</ul>"
						+"<p>on "+post.date+"</p>"
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
			icon: 'images/markers/'+post.tag_1+'.png'
		});
		google.maps.event.addListener(marker, 'click', function(e){loadPost(post.id);});
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
		$('#postings').hide();
		$('#loading').show();
		var postings, postLink, id, markers;
		postings  = document.getElementById('postings');
		markers = [];
		postings.innerHTML = "";
		if(postings.classList.contains('masonry'))
			{
				$('#postings').masonry('destroy');
			}
		for(i in formattedPostings)
			{
				button = document.createElement('div');

				postLink = document.createElement('a');
				id = formattedPostings[i]['id'];
				markers[i] = formattedPostings[i]['marker'];

				postLink.innerHTML = formattedPostings[i][postingsFormat];
				postLink.setAttribute('href','#');
				postLink.setAttribute('id', id);
				postLink.setAttribute('class', 'post-trigger');
				postLink.setAttribute('index', formattedPostings[i]['index']);
				button.setAttribute('class', 'posting-list-button');
				button.appendChild(postLink);
				button.innerHTML += "<div class='post-big'></div>";
				postings.appendChild(button);
				

				markers[i].setMap(this.map);
			}
		var postScript = document.createElement('script');
		postScript.innerHTML = "$('.post-trigger').click(function(e){"
			+"var id = $(this).attr('id');"
			+"if(!document.getElementById(id).classList.contains('triggered')){"
			+"loadPost(id);"
			+"var stateObj = id;"
			+"var search = window.location.search;"
			+"var uri = addParameter(search, 'p', id);"
			+"history.pushState(stateObj, null, uri);}"
			+"e.preventDefault();"
			+"});"
			+"$('.posting-list-button').hover(function(e){"
			+"var i = $(this).children('.post-trigger').attr('index');"
			+"highlightPosting($(this).children('.post-trigger').attr('id'));"
			+"formattedPostings[i]['marker'].setAnimation(google.maps.Animation.BOUNCE);"
			+"}, function(e){"
			+"var i = $(this).children('.post-trigger').attr('index');"
			+"lowlightPosting($(this).children('.post-trigger').attr('id'));"
			+"formattedPostings[i]['marker'].setAnimation(null);"
			+"});";
		postings.appendChild(postScript);
		$('#loading').hide();
		$('#postings').show();
		switch(postingsFormat)
		{
		case 'tile':
			resizeContainer();
			$('#postings').masonry({
				itemSelector: '.posting-list-button',
				isAnimated: true,
				gutterWidth: 10
			});
			break;
		default:
			break;
		}

	}