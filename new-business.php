<?php
/***********************************************
file: new-business.php
creator: Ian McEachern

This file displays the form for inputting a new
user's business information.
 ***********************************************/
require('includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

//set variables
//body
$id = $GLOBALS['b_id'];

$query = "SELECT name FROM business WHERE id='$id'";
$result = query_db($query);
$res = mysql_fetch_array($result);
$name = $res['name'];

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
	global $id, $name, $categories;

	echo "
	<div id='new-business' class='column span12'>";
	print_new_business_form($id, $name);
	echo "
	</div>";
}
?>