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
			+"<p class='muted'>"+post.business+"</p>";

		if(post.date != null)
		{
			result['tile'] += "<p>"+post.date+"</p>";
		}
			result['tile'] +="</div>" 
				+"</div>"
			+"</div>";

			result['list'] = "<div class='post-mini li'>"
					+"<img src='images/icons/"+post.tag_1+".png' width='35'>"+post.title+" by "+post.business;
		if(post.date!= null)
		   {			
			   result['list'] += " on "+post.date;
		   }
				result['list'] += "</div>";

		return result;
	}

	function calculateMeta(post)
	{
		var result = [];
		
/*		//distance and speed
		var myLat = json_location.lat;
		var myLon = json_location.lon;

		var latDelta = 1000*(post.lat-myLat);
		var lonDelta = 1000*(post.lon-myLon);
		

		result['distance'] = (latDelta^2 + lonDelta^2)^(.5);
		result['time'] = result['distance']/post.speed;
*/

		//map marker
		var postLatLon = new google.maps.LatLng(post.lat, post.lon);
		var marker = new google.maps.Marker({
			position: postLatLon,
			title: post.title,
			id: post.id,
			icon: 'images/markers/'+post.tag_1+'.png'
		});

		google.maps.event.addListener(marker, 'mouseover', function(e){highlightPosting(post.id);});
		google.maps.event.addListener(marker, 'mouseout', function(e){lowlightPosting(post.id);});
		result['marker'] = marker;
		return result;
	}

	function recalculatePostsMeta()
	{

	}

function formatPosts()
{
		var postings_div = document.getElementById('postings');
		var postings_cont_div = document.getElementById('postings-container');

		for(i in postings)
		{
			var post = calculateMeta(postings[i]);
			post['content'] = postings[i];


			post['id'] = postings[i].id;
			formattedPostings[i] = post;
		}
		for(i in formattedPostings)
		{	
			var post = formattedPostings[i];
			post['index'] = i;
			formattedPost = formatPost(post['content']);			
			post['tile'] = formattedPost['tile'];
			post['list'] = formattedPost['list'];
			formattedPostings[i] = post;
			if(!initialized)
			{
				activePostings[i] = i;
			}
		}

}

	function displayPosts()
	{
		var postings= document.getElementById('tndr'), 
		markers = [],
		activeFlag, postLink, id;

		postings.innerHTML = "";
		if(postings.classList.contains('masonry'))
			{
				$('#tndr').masonry('destroy');
			}
		for(i in formattedPostings)
			{
				var index = i;
				activeFlag = false;
				button = document.createElement('div');

				postLink = document.createElement('a');
				id = formattedPostings[index]['id'];
				markers[i] = formattedPostings[index]['marker'];
				markers[i].setMap();
				postLink.innerHTML = formattedPostings[index][postingsFormat];
				postLink.setAttribute('href','#p='+id);
				postLink.setAttribute('id', id);
				postLink.setAttribute('class', 'post-trigger');
				postLink.setAttribute('index', formattedPostings[index]['index']);
				button.setAttribute('class', 'posting-list-button ' + postingsFormat);

				button.appendChild(postLink);
				button.innerHTML += "<div class='post-big'></div>";
				postings.appendChild(button);
				for(j=0; j<=i; j++)
				{
					if(activePostings[j] == index)
					{
						activeFlag = true;
					}
				}
				if(activeFlag)
				{
					markers[i].setMap(map);
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
		postings.appendChild(postScript);

		repositionContainers();
			if(postingsFormat == 'tile')
			{
				$('#tndr').masonry('reload');
			}
	}