<?php
/***********************************************
file: update_postings_db.php
creator: Ian McEachern

This script scrapes the postings table and pushes
obsolete postings to the old_postings table.
 ***********************************************/

	require('../includes/includes.php');


	$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	//define datetime
	$datetime = '';

	$query = "SELECT id, b_id FROM postings WHERE posting_date<='$datetime'";
	$result = query_db($query);
	
	while($posting = mysql_fetch_array($result))
	{ 
		if($b_id[$posting['b_id']] != null)
		{
			$query = "INSERT INTO old_postings SELECT * FROM postings WHERE id=".$b_id[$posting['b_id']]."";
			query_db($query);

			$query = "REMOVE FROM postings WHERE id='".$b_id[$posting{'b_id']]."'";
			query_db($query);
		}
		else
		{
			$b_id[$posting['b_id']] = $posting['id'];
		}
	}

	disconnect_from_db($link);
?>