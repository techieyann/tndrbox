<?php

function connect_to_db($username, $password, $database)
{
	mysql_connect("localhost", $username, $password) or die(mysql_error());
        mysql_select_db($database) or die(mysql_error());
}

function query_db($query)
{
	if(!$result = mysql_query($query))
	{
		return die(mysql_error());
	}
	return $result;
}

function sanitize($query)
{
	$cleaned_query = mysql_real_escape_string($query);
	return $cleaned_query;
}
?>
