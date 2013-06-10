<?php
require('../includes/includes.php');
require('../includes/tags.php');


connect_to_db($mysql_user, $mysql_pass, $mysql_db);

$query = "SELECT id
FROM  `postings` 
WHERE DATE !='0000-00-00'
AND TO_DAYS( DATE ) < TO_DAYS( NOW( ) ) 
AND active =1";
$result = query_db($query);
if(isset($result[0]))
{
	foreach($result as $post)
	{
		deactivate_post($post['id']);
	}
}

disconnect_from_db();
?>