   	function formatPost(post, format)
	{
		var result = [];
		switch(format)
		{
		case 'tile':
			result['string'] = "<div class='span3 front-page-button'>"
					+"<div class='front-page-button-header'>"+post.tag_1+"</div>"
						+"<div class='front-page-button-body'>";

			if(post.photo != "")
			{
				result['string'] += "<img alt='photo for "+post.title+"' src='images/posts/"+post.photo+"'>";
			}

			result['string'] += "<div class='front-page-button-text'>"
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
			break;
		case 'list':
			result['string'] = "<div class='front-page-list-element'>"
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
			break;
		default:
			return result;
		}
		return result;
	}

	function calculateMeta(post)
	{
		var myLat = json_location.lat;
		var myLon = json_location.lon;

		var latDelta = 1000*(post.lat-myLat);
		var lonDelta = 1000*(post.lon-myLon);
		
		var result = [];
		result['distance'] = (latDelta^2 + lonDelta^2)^(.5);
		result['time'] = result['distance']/post.speed;
	  
		return result;
	}

	function reformatPosts(format)
	{
		for(i in postings)
		{
			var post = formatPost(postings[i], format);
			for(j in formattedPostings)
			{
				if(formattedPostings[j]['id'] == postings[i].id)
				{
					formattedPostings[j]['string'] = post['string'];
				}
			}
		}
	}

	function recalculatePostsMeta()
	{

	}

	function displayPosts(format)
	{
		var postings = document.getElementById('postings');
		postings.innerHTML = "";
		if(postings.classList.contains('masonry'))
			{
				$('#postings').masonry('destroy');
			}
		for(i in formattedPostings)
			{
				var post = document.createElement('a');
				post.innerHTML = formattedPostings[i]['string'];
				post.setAttribute('href','#');
				post.setAttribute('id', formattedPostings[i]['id']);
				post.setAttribute('class', 'modal-trigger');
				postings.appendChild(post);
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
			+"});";
		postings.appendChild(modalScript);

		switch(format)
		{
		case 'tile':
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