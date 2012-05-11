<?php
/***********************************************
file: db_interface.php
creator: Ian McEachern

This library translates genereic database 
function calls to specific implementation 
database functions. Currently mysql.
 ***********************************************/
function connect_to_db($username, $password, $database)
{
	$connection = mysql_connect("localhost", $username, $password) or die(mysql_error());
        mysql_select_db($database) or die(mysql_error());
	return $connection;
}

function disconnect_from_db($connection)
{
	mysql_close($connection) or die(mysql_error());
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
