<?php

/***********************************************
file: tags.php
creator: Ian McEachern

This file contains the functions which interact
and maintain the validity of the metadata in the
tags database.

Make sure to include db_interface before this
file, and connect to the db before calling any
function.
 ***********************************************/

function increment_tag($tag_id)
{
	$query = "SELECT num_ref FROM tags WHERE id='$tag_id'";
	$result = query_db($query);
	$res = mysql_fetch_array($result);
	extract($res);
	if(isset($num_ref))
	{
		$num_ref++;
		$query = "UPDATE tags SET num_ref=$num_ref WHERE id=$tag_id";
		$result = query_db($query);
	}
}

function decrement_tag($tag_id)
{
	$query = "SELECT num_ref FROM tags WHERE id='$tag_id'";
	$result = query_db($query);
	$res = mysql_fetch_array($result);
	extract($res);
	if(isset($num_ref))
	{
		$num_ref--;
		$query = "UPDATE tags SET num_ref=$num_ref WHERE id=$tag_id";
		$result = query_db($query);
	} 
}

function get_tag($tag)
{
	if(is_numeric($tag))
	{
		$query = "SELECT tag FROM tags WHERE id=$tag";
		$result = query_db($query);
		$res = mysql_fetch_array($result);
		return $res['tag'];
	}
	else
	{
		return $tag;
	}
}

function add_tag($tag)
{
	if(is_numeric($tag))
	{
		increment_tag($tag);
		return $tag;
	}
	else
	{
	  if($tag == "")
		{
		  return null;
		}
	  else
		{

	  $query = "SELECT id FROM tags WHERE tag='$tag'";
	  $result = query_db($query);
	  if(mysql_num_rows($result) == 0)
		{
		$query = "INSERT INTO tags (tag, num_ref) VALUES ('$tag', 1)";
		$result = query_db($query);
		$query = "SELECT id FROM tags WHERE tag='$tag'";
		$result = query_db($query);
		$res = mysql_fetch_array($result);
		extract($res);
		}
	  else
		{
		  $db_tag = mysql_fetch_array($result);
		  $id = $db_tag['id'];
		  increment_tag($id);
		}
		return $id;
		}
	}
}

?>
