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
	$query = "SELECT num_ref FROM tags WHERE id='$id'";
	$result = query_db($query);
	extract($result);
	if(isset($num_ref))
	{
		$num_ref++;
		$query = "UPDATE postings SET num_ref=$num_ref WHERE id=$tags_id";
		$result = query_db($query);
	}
}

function decrement_tag($tag_id)
{
	$query = "SELECT num_ref FROM tags WHERE id='$id'";
	$result = query_db($query);
	extract($result);
	if(isset($num_ref))
	{
		$num_ref--;
		$query = "UPDATE postings SET num_ref=$num_ref WHERE id=$tags_id";
		$result = query_db($query);
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
		$query = "INSERT INTO tags (tag, num_ref) VALUES ('$tag', 1)";
		$result = query_db($query);
		$query = "SELECT id FROM tags WHERE tag='$tag'";
		$result = query_db($query);
		extract($result);
		return $id;
	}
}

?>
