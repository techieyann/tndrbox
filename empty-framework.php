<?php
/***********************************************
file: .php
creator: Ian McEachern

About this file
 ***********************************************/
require('includes/includes.php');
require('includes/db_interface.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body
$id = $GLOBALS['b_id'];

//head
$GLOBALS['header_html_title'] = "tndrbox - ";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  echo "
	<div id=\"\" class =\"content-pane\">
	</div>";
}
?>