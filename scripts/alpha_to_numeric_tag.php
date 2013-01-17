<?php
/***********************************************
file: alpha_to_numeric_tag.php
creator: Ian McEachern

 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

if(isset($_GET['tag-search']))
  {
	$tag = $_GET['tag-search'];
	$tag_id = get_tag_id($tag);
  	header("location:../?tag=$tag_id");
  }

disconnect_from_db();

?>