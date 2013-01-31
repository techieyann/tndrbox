<?php
/***********************************************
file: index.php
creator: Ian McEachern

This is the default page. It displays the most
relevant data based on the function scrape_tags()
in includes.php.
 ***********************************************/

function format_rows($raw_posts)
  {
	$i=-1;
	$looper = $raw_posts;
	$processed_id = 0;
	if(isset($raw_posts['p_flag']))
	  {
		$i++;
		$post = $raw_posts[0];
		$processed_id = $post['id'];
		$formatted_postings[$i]['post'] = $post;
			
		$image = $post['photo'];
		if($image != "")
		  {
			$image_source = "images/posts/".$image;
			list($width, $height) = getimagesize($image_source);
			$span_calc = ($width/$height)*1.2;
			$span = ceil($span_calc);
			if(true)//$span < 3)
			  {
				$span = 3;
			  }
			$formatted_postings[$i]['span'] = $span;
		  }
		else
		  {
			$formatted_postings[$i]['span'] = 3;
		  }
		$looper = $raw_posts[1];
	  }
	foreach($looper as $post)
	  {
		if($post['id'] != $processed_id)
		  {
		$i++;
		$formatted_postings[$i]['post'] = $post;
		$image = $post['photo'];
		if($image != "")
		  {
			$image_source = "images/posts/".$image;
			list($width, $height) = getimagesize($image_source);
			$span_calc = ($width/$height)*1.2;
			$span = ceil($span_calc);
			if(true)//$span < 3)
			  {
				$span = 3;
			  }
			$formatted_postings[$i]['span'] = $span;
		  }
		else
		  {
			$formatted_postings[$i]['span'] = 3;
		  }
		  }
	  }

	return $formatted_postings;
  }

function print_formatted_rows($postings)
{
	$num_posts = count($postings);
	$count = 0;
	$total_spans = 0;
	$filler['post'] = "filler";

	//	echo "
	//		<ul class='thumbnails'>";

	echo "<div id='masonry-container' class='row span12' style='padding-left:25px'>";
	for($i=0; $i<$num_posts; $i++)
	  {
		if(($total_spans + $postings[$i]['span']) <= 12)
		  {
			$total_spans += $postings[$i]['span'];
			$usable_posts[$count++] = $postings[$i];
		  }
		else
		  {
			$spans_remaining = 12-$total_spans;
			$j = 0;

			foreach($usable_posts as $post_data)
			  {
				$post_row[$j++] = $post_data;
				if($spans_remaining != 0)
				  {
					$filler['span'] = rand(0,$spans_remaining);
					if($filler['span'] != 0)
					  {
						$spans_remaining -= $filler['span'];
						$post_row[$j++] = $filler;
					  }
  				  }
			  }	
			
			print_post_row($post_row);
			$usable_posts = "";
			$post_row = "";
			$total_spans = 0;
			$count = 0;			
			$total_spans += $postings[$i]['span'];
			$usable_posts[$count++] = $postings[$i];
		  }
	  }


	if($count != 0)
	  {
		$spans_remaining = 12-$total_spans;
			$j = 0;

			foreach($usable_posts as $post_data)
			  {
				$post_row[$j++] = $post_data;
				
			  }
			print_post_row($post_row);
		}
		echo "
				</div>";
}
?>