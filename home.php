<?php
/***********************************************
file: home.php
creator: Ian McEachern

This file is the default page for logged in
users. Redirects to index.php if user is not
logged in.
 ***********************************************/


require('includes/includes.php');

analyze_user();

if($GLOBALS['logged_in'] == false)
{
	header("location:/");
	exit;
}

require('includes/prints.php');

print_head();
print_body();
print_foot();

function print_body()
{
  echo "
	<div id=\"\" class =\"content-pane\">
	logged in!
	</div>";
}
?>