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
	try
	  {
		$GLOBALS['DBH'] = new PDO("mysql:host=localhost;dbname=$database;", $username, $password);
		$GLOBALS['DBH']->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	  
	  }
	catch(PDOException $e)
	  {
		echo $e->getMessage();
	  }
  }

function disconnect_from_db()
  {
	$GLOBALS['DBH'] = null;
  }

function query_db($query)
  {
	list($query_type) = explode(' ', trim($query));
	try
	  {
		$STH = $GLOBALS['DBH']->prepare($query);
		switch($query_type)
		  {
		  case 'UPDATE':
			$STH->execute();
		  case 'INSERT':
			return $STH->execute();	
			break;
		  default:
			$STH->execute();
			return $STH->fetchAll();			
		  }
	  }
	catch(PDOException $e)
	  {
		echo $e->getMessage();
	  }
  }

function get_last_insert_ID()
{
  return $GLOBALS['DBH']->lastInsertId();
}
?>
