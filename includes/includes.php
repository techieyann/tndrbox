<?php
/***********************************************
file: includes.php
creator: Ian McEachern

This creatively titled file is necessary the 
first line of each php file generating a 
website. It includes the necessary definitions 
of globals, the interface to the database, and
the functions used by every page. It then 
initializes php settings.
 ***********************************************/

//Fail to load the page if it does not include the required files
require('defines.php');
require('db_interface.php');
require('db_functions.php');


//Output warnings and errors
ini_set('error_reporting', E_ALL|E_STRICT);
ini_set('display_errors', 1);

?>