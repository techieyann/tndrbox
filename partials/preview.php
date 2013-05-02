<?php
/***********************************************
file: partials/preview.php
creator: Ian McEachern

This partial displays a preview of a post.
 ***********************************************/

if(isset($_GET['id']))
  {
	require('../includes/includes.php');
	require('../includes/tags.php');

	connect_to_db($mysql_user, $mysql_pass, $mysql_db);

	analyze_user();
	verify_logged_in();

	$id = $_GET['id'];

	$query = "SELECT active, title, name, tag_1, postings.photo, postings.lat, postings.lon FROM postings INNER JOIN business ON business.id=postings.b_id WHERE postings.id=$id ";
	$result = query_db($query);

	if($result)
	  {
		extract($result[0]);
		$date = format_date($id);
		$tag_1 = get_tag($tag_1);
		$center = $result[0]['lat'].",".$result[0]['lon'];
		$map_url = "http://maps.googleapis.com/maps/api/staticmap?center=$center&zoom=16&size=250x150&markers=color:%23A33539%7c$center&sensor=false";
	$map_preview = "<div id='map-preview'><img src='$map_url' alt='map at $center'></div>";



	$list_button_preview = "<div class='posting-list-button list'>
						<div class='post-mini li'>
							<ul class='inline'><li><div class='".$tag_1."_sm'></div></li><li>$title</li><li>by $name</li>".($date != null ? "<li>on $date</li>":"")."</ul>
						</div></div>";

	$tile_button_preview = "
<div class='posting-list-button tile'>
						<div class='post-mini button'>
							<div class='front-page-button-header'>$tag_1</div>
							<div class='front-page-button-body'>
								".($photo != "" ? "<img alt='photo for $title+' src='images/posts/$photo'>":"").
								"<div class='front-page-button-text'>
									<h4>$title</h4>
									<p class='muted'>$name</p>
									".($date != null ? "<p>$date</p>":"").
								"</div>
							</div>
						</div></div>";
	  }


	disconnect_from_db();
	
  }
else
  {

  }
?>

<script>
$(document).ready(function(){
	var postPreviewDiv = $('#posting-content')
	postPreviewDiv.hide();
	postPreviewDiv.load('partials/posting?type=preview&p=<?php print $id ?>', function(){
		$('#preview-loading').hide();
		postPreviewDiv.show('slow', function(){
			repositionContainers();
		  });

	  });
 });
</script>

<ul class="inline centered hidden-phone" style="padding-bottom:10px; border-bottom:solid 1px black;">
<li><a class="btn" title="Save" href="#b=members&view=posts"><i class="icon-folder-close"></i> Save</a></li>
<li><a class="btn" title="Edit" href="#b=members&view=edit-post&id=<?php print $id ?>"><i class="icon-pencil"></i> Edit</a></li>
<?php 
  if($active == 1)
	{
	  echo "
<li><a class='btn' title='Deactivate' href='#b=members&view=deactivate-post&id=$b_id'><i class='icon-remove-sign'></i> Deactivate</a></li>";
	}
   else
	 {
	  echo "
<li><a class='btn' title='Activate' href='#b=members&view=activate-post&id=$id'><i class='icon-check'></i> Activate</a></li>";
	 }
?>
<li><a class="btn" title="Delete" href="#b=members&view=delete-post&id=<?php print $id ?>"><i class="icon-trash"></i> Delete</a></li>

</ul>
<ul class="inline centered visible-phone" style="padding-bottom:10px; border-bottom:solid 1px black;">
  <li><a class="btn" title="Save" href="#b=members&view=posts"><i class="icon-folder-close"></i></a></li>
<li><a class="btn" title="Edit" href="#b=members&view=edit-post&id=<?php print $id ?>"><i class="icon-pencil"></i></a></li>
<?php 
  if($active == 1)
	{
	  echo "
<li><a class='btn' title='Deactivate' href='#b=members&view=deactivate-post&id=$b_id'><i class='icon-remove-sign'></i></a></li>";
	}
   else
	 {
	  echo "
<li><a class='btn' title='Activate' href='#b=members&view=activate-post&id=$id'><i class='icon-check'></i></a></li>";
	 }
?>

<li><a class="btn" title="Delete" href="#b=members&view=delete-post&id=<?php print $id ?>"><i class="icon-trash"></i></a></li>
</ul>
<br>
<ul class="inline">
<li class="pull-left" style="max-width:100%"><?php print $map_preview ?></li>
<li><?php print $tile_button_preview ?></li>
</ul>



<br><br><br>
<div ><?php print $list_button_preview ?></div>

<div id="posting-preview">
<div id="posting-content" class="posting-list-button">

</div>
<div id="preview-loading">
	<img src="images/loading.gif" alt="loading...">
</div>
</div>

