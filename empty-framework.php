<?php
/***********************************************
file: [NAME].php
creator: Ian McEachern

This file is the skeleton for every page 
generated, and copying this file is the first 
step to creating a new page.

It begins with the basic include, which are 
explained further in their own documentation 
and enumerated in the README.

It then makes a connection to the database and 
inspects what data it has on the user. If the 
user is logged in or doesn't need to be, the 
variables for the page are set, seperated by the
body and head HTML tags. 

Only then does it include the print functions. 
This is to allow the print functions the access 
to the variables. It then prints the website from
head to foot. Finally it disconnects from the 
database.
 ***********************************************/
require('includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables, GLOBALS are enumerated in the README
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