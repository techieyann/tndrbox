<?php
/***********************************************
file: includes/geocoding.php
creator: Ian McEachern

This file contains global functions for geocoding
 ***********************************************/

function addr_to_latlon($address_input)
  {

	$address = urlencode($address_input);

	$url = "http://maps.googleapis.com/maps/api/geocode/json?address=$address&sensor=false";


	$json_response = file_get_contents($url);
	$response = json_decode($json_response, true);

	
	$return['lat'] = $response['results']['geometry']['location']['lat'];
	$return['lon'] = $response['results']['geometry']['location']['lng'];

	return $return;
  }

//expects 'lat, lon'
function latlon_to_addr($lat_lon)
  {

	$address = urlencode($address_input);

	$url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=$lat_lon&sensor=false";

	$json_response = file_get_contents($url);
	$response = json_decode($json_response, true);
	$address = $response['results']['formatted_address'];

	return $address;
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