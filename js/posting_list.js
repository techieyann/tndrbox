   	function formatPost(post, format)
	{
		var result = [];
		switch(format)
		{
		case 'tile':
			result['string'] = "<a id='"+post.id+"' class='modal-trigger' href='?p="+post.id+"'>"
				+"<div class='span3 front-page-button'>"
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
			+"</div>"
		+"</a>";
			break;
		case 'list':
			result['string'] = ""
				+"<tr class='modal-trigger' href='?p="+post.id+"'>"
					+"<td>"+post.title+"</td>"
					+"<td>"+post.date+"</td>"
					+"<td>"+post.business+"</td>"
					+"<td class='hidden-phone'>"+post.tag_1+"<br>"+post.tag_2+"<br>"+post.tag_3+"</td>"

				+"</tr>"
				+"";
			break;
		case 'map':
			
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
		switch(format)
		{
		case 'tile':
			var width = 0;
			if($(window).width() > 1200)
			{
				width = 12;
			}
			$('#postings').masonry({
				itemSelector: '.front-page-button',
				isAnimated: true,
				gutterWidth: width
			});
			var modalTriggerScript = "<script>$('.modal-trigger').click(function(e){"
				+"var id = $(this).attr('href');"
				+"var url = 'partials/modal' + id;"

				+"$('#modal-header').hide();"
				+"$('#modal-body').hide();"
				+"$('#modal-footer').hide();"

				+"$('#post-modal').modal('show');"

				+"$('#modal-loading').show();"

				+"$('#post-modal').load(url, function(){"
					+"$('#modal-loading').hide();"

					+"$('.share-button').popover({"
						+"html:true"
					+"});"
	
					+"$('#modal-header').show();"
					+"$('#modal-body').show();"
					+"$('#modal-footer').show();"
	
					+"var stateObj = id;"
					+"history.pushState(stateObj, null, id);"
				+"});"
	
				+"e.preventDefault();"
				+"});</script>";
			$('#postings').append(modalTriggerScript);
			for(i in formattedPostings)
			{
				$('#postings').append(formattedPostings[i]['string']);
			}
			break;
		case 'list':
			if(postings.classList.contains('masonry'))
			{
				$('#postings').masonry('destroy');
			}
			var table = "<table class='table table-hover span12'>"
				+"<thead>"
				+"<tr>"
					+"<th>Title</th>"
					+"<th>Date</th>"
					+"<th>Business</th>"
					+"<th class='hidden-phone'>Tags</th>"
				+"</tr>"
				+"</thead>"
				+"<tbody>";

			for(i in formattedPostings)
			{
				table += formattedPostings[i]['string'];
			}
			table += "</tbody></table>";
			$('#postings').append(table);
			break;
		case 'map':
			$('#postings').append(getMapScript());
			break;
		default:
			break;
		}
	}
//google maps api asynch load
		function initialize() {
			var mapOptions = {
				zoom: 8,
				center: new google.maps.LatLng(-34.397, 150.644),
				mapTypeId: google.maps.MapTypeId.ROADMAP
			}
			var map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
		}

		function getMapScript() {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.src = 'http://maps.googleapis.com/maps/api/js?key=AIzaSyD0LQT5KDi_tPDcJPP8Rxlj6hOdifAyNO4&sensor=false&callback=initialize';
			return script;
		 }
	$(document).ready(function(){

});