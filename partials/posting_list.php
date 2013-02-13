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
				function formatPost(post, format)
				{
					var resultNode = '';
					switch(format)
					{
					case 'tile':
						
						break;
					default:
						return;
					}
					return resultNode;
				}
				$(document).ready(function(){
					var postings_div = document.getElementById('postings');

					for(i in postings[0])
					{
						var post = formatPost(postings[0][i], 'tile');

						postings_div.appendChild(post);
					}

				});
				</script>
				<div id='postings'>
				</div><!-- #postings -->";

?>