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
	$(document).ready(function(){
		var postings_div = document.getElementById('postings');
		var postings_cont_div = document.getElementById('postings-container');

		var listing_format = '';		
		if(postings_cont_div.classList.contains('tile'))
		{
			listing_format = 'tile';
		}
		if(postings_cont_div.classList.contains('list'))
		{
			listing_format = 'list';
		}
		if(postings_cont_div.classList.contains('map'))
		{
			listing_format = 'map';
		}

		for(i in postings)
		{
			var post = calculateMeta(postings[i]);
			formattedPost = formatPost(postings[i], listing_format);
			
			post['string'] = formattedPost['string'];
			formattedPostings[i] = post;
			formattedPostings[i]['id'] = postings[i].id;

			formattedPostings.sort(function(a, b){
				return a['time']-b['time'];
			});
		}
		displayPosts(listing_format);

	});
</script>
<div id='map-canvas'>
</div><!-- #map-canvas -->
<div id='postings'>
</div><!-- #postings -->";

disconnect_from_db();
?>