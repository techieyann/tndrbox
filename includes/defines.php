<?php
/***********************************************
file: defines.php
creator: Ian McEachern

This file defines global values for site usage.
 ***********************************************/
//site metadata
	$version	='0.3.2';

//mysql metadata
	$mysql_user	='phpdaemon';
	$mysql_pass	='q!32Hj~';
	$mysql_db	='cream'; //_live';

//google analytics metadata
	$GLOBALS['ga_account'] = 'UA-29414041-1';

//google maps metadata
	$GLOBALS['gm_account'] = 'AIzaSyD0LQT5KDi_tPDcJPP8Rxlj6hOdifAyNO4';

//mapquest metadata
	$GLOBALS['mapquest_key'] = 'Fmjtd%7Cluub21u2lu%2Cb2%3Do5-96t2da';

//distance metadata
	$GLOBALS['default_latlon_delta'] = .1; //~6 miles
	$GLOBALS['distance_multiplier'] = 1000;
?>