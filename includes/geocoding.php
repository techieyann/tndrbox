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

	$return['lat'] = $response['results'][0]['locations'][0]['latLng']['lat'];
	$return['lon'] = $response['results'][0]['locations'][0]['latLng']['lng'];

	return $return;
  }

function ip_to_latlon($ip)
  {
	$ip_address = urlencode($ip);
	$url = "http://freegeoip.net/json/$ip_address";
	
	$json_response = file_get_contents($url);
	$response = json_decode($json_response, true);

	$return['lat'] = $response['latitude'];
	$return['lon'] = $response['longitude'];

	return $return;
  }

?>