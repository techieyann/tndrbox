<?php
	push_old_post($b_id);	

	$query = "UPDATE business SET active_post=1, last_touched=CURRENT_TIMESTAMP WHERE id=$b_id";
	query_db($query);
	$query = "UPDATE postings SET active=1 WHERE id=$post_id";
	query_db($query);
?>