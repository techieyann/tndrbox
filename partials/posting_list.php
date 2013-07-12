<?php
/***********************************************
file: partials/posting_list.php
creator: Ian McEachern

This partial displays the postings on the front 
page in 
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');
connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();

$result = array();
$post_flag = false;

if(isset($_GET['p']))
  {
	if(is_numeric($_GET['p']))
	  {
		$p_id = $_GET['p'];
		$query = "SELECT active, b_id FROM postings WHERE id=$p_id";
		$active_result = query_db($query);
		if(isset($active_result[0]))
		  {
			extract($active_result[0]);
			if($active == 1)
			  {
				$query = "SELECT postings.id, title, date, start_time, posting_time, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE postings.id='$p_id'";
			  }
			else
			  {
				$query = "SELECT postings.id, title, date, start_time, posting_time, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE b_id=$b_id AND active=1";
			  }
	
			$result = query_db($query);
			if(isset($result[0]))
			  {
				$post_flag = true;
				$result['post_flag'] = 1;
			  }
		  }
	  }
  }
elseif(isset($_GET['b']))
  {
	if(is_numeric($_GET['b']))
	  {
		$b_id = $_GET['b'];
		$query = "SELECT postings.id, title, date, start_time, posting_time, postings.photo, tag_1, tag_2, tag_3, postings.lat, postings.lon, business.name FROM postings INNER JOIN business ON postings.b_id=business.id WHERE b_id=$b_id and active=1";

		$result = query_db($query);
	
		if(isset($result[0]))
		  {
			$post_flag = true;
			$p_id = $result[0]['id'];
			$result['post_flag'] = 1;
		  }
	  }
  }
array_push($result, default_front_page_posts());
$processed_postings = process_postings($result);
$json_postings = json_encode($processed_postings);

print $json_postings;

disconnect_from_db();

function process_posting($raw_post)
{
		$now = time();
		extract($raw_post);

		$processed_post['id'] = $id;
		$processed_post['title'] = $title;
		$processed_post['date'] = $date;
		$processed_post['formatted_date'] = format_date($id);
		if($date == "0000-00-00")
		{
			$processed_post['time_delta'] = abs($now-strtotime('yesterday midnight'));
		}
		else
		{
			$processed_post['time_delta'] = abs(strtotime($date." ".$start_time)-$now);
		}
		$processed_post['photo'] = $photo;
		$processed_post['tag_1_id'] = $tag_1;
		$processed_post['tag_1'] = get_tag($tag_1);
		$processed_post['tag_2_id'] = $tag_2;
		$processed_post['tag_2'] = get_tag($tag_2);
		$processed_post['tag_3_id'] = $tag_3;
		$processed_post['tag_3'] = get_tag($tag_3);
		$processed_post['lat'] = $lat;
		$processed_post['lon'] = $lon;
		$processed_post['business'] = $name;
		//need to calculate speed here
		//		$processed_post['speed'] = 1;
		$tag_1 = $processed_post['tag_1'];
		$formatted_date = $processed_post['formatted_date'];
		$processed_post['list'] = "<div class='post-mini li'><ul class='inline'><li><div class='".$tag_1."_sm'></div></li><li><h4>$title</h4></li><li class='muted'>by $name</li>".($formatted_date != null ? "<li>on $formatted_date</li>":"")."</ul></div>";

		$processed_post['tile'] = "<div class='post-mini button'><div class='front-page-button-header'>$tag_1</div><div class='front-page-button-body'>".($photo != "" ? "<img alt='photo for $title+' src='slir/w200-q80/images/posts/$photo'>":"")."<div class='front-page-button-text'><h4>$title</h4><p class='muted'>$name</p>".($formatted_date != null ? "<p>$formatted_date</p>":"")."</div></div></div>";
		return $processed_post;
}

function process_postings($raw_posts)
  {
	$looper=$raw_posts[0];
	$processed_posts = array();
	$processed_id = 0;
	$index = 0;
	if(isset($raw_posts['post_flag']))
	  {
		$processed_posts[0] = process_posting($looper);

		$processed_id = $processed_posts[0]['id'];
		$index++;

		$looper = $raw_posts[1];
	  }
	class postingHeap extends SplMinHeap
	{
		public function compare($array1, $array2)
		{
			$values1 = array_values($array1);
			$values2 = array_values($array2);

			if($values1[3] == $values2[3])
			{
				return 0;
			}
			return $values1[3] < $values2[3] ? 1:-1;
		}
	}
	$postsHeap = new postingHeap();
	foreach($looper as $post)
	  {
		if(isset($post['id']) && $post['id'] != $processed_id)
		  {
			$postsHeap->insert(process_posting($post));
		  }
	  }
	$postsHeap->top();

	while($postsHeap->valid())
	{	
		$processed_posts[$index] = $postsHeap->current();
		$index++;
		$postsHeap->next();
	}
	return $processed_posts;
  }
?>
