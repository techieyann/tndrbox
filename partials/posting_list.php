<?php
/***********************************************
file: partials/posting_list.php
creator: Ian McEachern

This partial displays the postings on the front 
page in 
 ***********************************************/

require('../includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();

echo "
<script>
	window.onload = function(){
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
	}

	window.onresize = function(){
		$('#postings').masonry('reload');
		if($(window).width() < 1200)
		{
			$('#postings').masonry('option', {gutterWidth: 0});
		}
		else
		{
			$('#postings').masonry('option', {gutterWidth: 13});
		}
	};

	function formatPost(post, format)
	{
		var result = '';
		switch(format)
		{
		case 'tile':
			var result = \"<a id='\"+post.id+\"' class='modal-trigger' href='?p=\"+post.id+\"'>\"
				+\"<div class='span3 front-page-button'>\"
					+\"<div class='front-page-button-header'>\"+post.tag_1+\"</div>\"
						+\"<div class='front-page-button-body'>\";

			if(post.photo != \"\")
			{
				result += \"<img alt='photo for \"+post.title+\"' src='images/posts/\"+post.photo+\"'>\";
			}

			result += \"<div class='front-page-button-text'>\"
						+\"<h4>\"+post.title+\"</h4>\"
						+\"<p>by, \"+post.business+\"</p>\"
						+\"<p>\"+post.date+\"</p>\"
						+\"<ul class='inline centered'>\"
							+\"<li class='tag' id='\"+post.tag_2_id+\"'>\"+post.tag_2+\"</li>\"
							+\"<li class='tag' id='\"+post.tag_3_id+\"'>\"+post.tag_3+\"</li>\"
						+\"</ul>\"
					+\"</div>\" 
				+\"</div>\"
			+\"</div>\"
		+\"</a>\";
			break;
		default:
			return;
		}
		return result;
	}

	$(document).ready(function(){
		var postings_div = document.getElementById('postings');

		var myLat = json_location.lat;
		var myLon = json_location.lon;

		for(i in postings)
		{
			var post['string'] = formatPost(postings[i], 'tile');
			var latDelta = postings[i].lat-myLat;
			var lonDelta = postings[i].lon-myLon;
			var post['distance'] = (latDelta^2 + lonDelta^2)^(.5);
			var post['time'] = distance/postinds.speed;

			formattedPostings[i] = post;
		}
		for(i in formattedPostings)
		{
			$('#postings').append(formattedPostings[i]['string']);
		}

		$('.modal-trigger').click(function(e){

		var id = $(this).attr('href');
		var url = 'partials/modal' + id;

		//hide content divs
		$('#modal-header').hide();
		$('#modal-body').hide();
		$('#modal-footer').hide();	

		//show modal
		$('#post-modal').modal('show');

		//display loading div
		$('#modal-loading').show();

		//call load
		$('#post-modal').load(url, function(){
			$('#modal-loading').hide();

			$('.share-button').popover({
				html:true
			});
	
			$('#modal-header').show();
			$('#modal-body').show();
			$('#modal-footer').show();
	
			var stateObj = id;	
			history.pushState(stateObj, null, id);
		});
	

		//prevent natural click behavior
		e.preventDefault();
	});
});
</script>
<div id='postings'>
</div><!-- #postings -->";
?>