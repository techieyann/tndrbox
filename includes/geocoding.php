<?php
/***********************************************
file: includes/geocoding.php
creator: Ian McEachern

This file contains global functions for geocoding
 ***********************************************/

function addr_to_latlon($address_input)
  {
	$key = $GLOBALS['mapquest_key'];
	$address = urlencode($address_input);

	$url = "http://www.mapquestapi.com/geocoding/v1/address?key=$key&location=$address";


	$json_response = file_get_contents($url);
	$response = json_decode($json_response, true);
	return $response['results'][0]['locations'][0]['latLng'];
  }

?>