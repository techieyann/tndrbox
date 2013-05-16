<?php
/***********************************************
file: scripts/spark_post.php
creator: Ian McEachern


 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);
$active_flag = false;
if(isset($_GET['p']))
  {
	$id = $_GET['p'];
	$return_text[0] = 'success';
	print json_encode($return_text);
  }

disconnect_from_db();
?>