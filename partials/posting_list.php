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

		for(i in postings)
		{
			var post = calculateMeta(postings[i]);
			post['content'] = postings[i];


			post['id'] = postings[i].id;
			formattedPostings[i] = post;

			formattedPostings.sort(function(a, b){
				return a['time']-b['time'];
			});
		}
		for(i in formattedPostings)
		{	
			var post = formattedPostings[i];
			post['index'] = i;
			formattedPost = formatPost(post['content']);			
			post['tile'] = formattedPost['tile'];
			post['list'] = formattedPost['list'];
			formattedPostings[i] = post;
		}
	});
</script>

<div id='map-canvas'>
</div><!-- #map-canvas -->

<div id='post'>
</div><!-- #post -->

<div id='postings' class='pull-left'>
</div><!-- #postings -->";

disconnect_from_db();
?>