<?php
/***********************************************
file: .php
creator: Ian McEachern

About this file
 ***********************************************/


require('includes/includes.php');
require('includes/db_interface.php');

$link = connect_to_db($mysql_user, $mysql_pass, $mysql_db);
analyze_user();
disconnect_from_db($link);

require('includes/prints.php');


print_head();
print_body();
print_foot();

function print_body()
{
  echo "
	<div id=\"\" class =\"content-pane\">
	</div>";
}
?>