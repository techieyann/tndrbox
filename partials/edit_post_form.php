<?php
/***********************************************
file: partials/edit_post_form.php
creator: Ian McEachern

This partial displays the edit form for the 
indicated post 
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();
check_admin() ? $admin_flag = true : $admin_flag = false;

//check that id was passed
if(isset($_GET['id']))
  {
	$id = $_GET['id'];
	
	//get post data
	$query = "SELECT * FROM postings WHERE id=$id";
	$result = query_db($query);
	if(isset($result[0]))
	  {

	extract($result[0]);
	
	//check authorship of the post
	if(!$admin_flag && $a_id != $GLOBALS['m_id'])
	  {
		//		header('location:../settings');
	  }

	$event_flag = $start_flag = $end_flag = $alt_address_flag = false;
	if($date != "0000-00-00")
	{
		$event_flag = true;
	}
	if($start_time != "00:00:00")
	{
		$start_flag = true;
	}
	if($end_time != "00:00:00")
	{
		$end_flag = true;
	}
	if($address != "")
	{
		$alt_address_flag = true;
	}

	$image_label = "Image ";
	$photo_html = "";
	if($photo != "")
	  {
		$image_label = "Change Image ";
		$photo_html = "<img src='images/posts/$photo' class='span10 offset1'>";
	  }

	$tag2 = get_tag($tag_2);
	$tag3 = get_tag($tag_3);
	$b_id = $GLOBALS['b_id'];

	$query = "SELECT address, city, state, zip FROM business WHERE id=$b_id";
	$result = query_db($query);
	if(isset($result[0]))
	{
		$business = $result[0];
		$default_address = $business['address'].", ".$business['city'].", ".$business['state'].", ".$business['zip'];
	}
	  }
  }
	$result = get_categories();
disconnect_from_db();
?>	


		<script>
			var eventDisplay = <?php print $event_flag?>;
			$(document).ready(function(){
			toggleEventDisplay();
			if(eventDisplay)
			{
				$('#time-group').hide();
			}
			<?php print ($end_flag ? "":"$('#end_time').hide();")?>
			<?php print ($alt_address_flag ? "$('#default-address').hide(); $('#alternate-address').show();":"$('#default-address').show(); $('#alternate-address').hide();") ?>

				$('#tag2').autocomplete({
					source:'scripts/search_tag',
					select: function(event, ui){
						$('#tag2').val(ui.item.label);
						return false;
					}
				});	
				$('#tag3').autocomplete({
					source:'scripts/search_tag',
					select: function(event, ui){
						$('#tag3').val(ui.item.label);
						return false;
					}
				});	
				$('#date').datepicker({
					dateFormat: 'yy-mm-dd',
					minDate: 0,
					maxDate: '+28D',
					onSelect: function(){
						$('#time-group').show();
					}
				});
				$('#start_time').timepicker({
					onSelect: function(time){
						$('#end_time').show();
					}
				});
				$('#end_time').timepicker();

				$('.edit-post-form').ajaxForm({success: parseEditPostReturn});
				function parseEditPostReturn(responseText, statusText, xhr, $form)
				{
				  if(statusText == 'success')
					{
					  if(responseText.substring(0,7) == 'postId=')
						{
							
						  var id = responseText.substring(7);
						  $.bbq.pushState({'b':'members','view':'preview-post', 'id':id});
						}
						  else if(responseText.substring(0,6) == 'error=')
							{
							  var errorCode = responseText.substring(6);
							  console.log(errorCode)
							}
					  
					}

				}
			});
			function toggleEventDisplay(){
				if(eventDisplay == true)
				{
					$('#event-fields').show();
				}
				else
				{
					$('#event-fields').hide();
				}
				eventDisplay = !eventDisplay;
			}
		</script>
		<div id='js-content'>
		<form class='edit-post-form' name='edit-post-form'  enctype='multipart/form-data' action='scripts/edit_post?id=<?php print $id ?>' method='post'>
			<fieldset>
			<div class="row-fluid span12">
			<div class="span6">
			<label><strong>Required fields...</strong></label>
   			<div class="control-group">
				<label class="control-label" for="title">
					Title *
				</label>
				<div class="controls">
					<input type="text" maxlength=50 name="title" id="title" value="<?php print $title?>" placeholder="Insert title here..." class="span12">
				</div>
			</div>

			<div class="control-group">
				<label class="control-label" for="description">
					Description * 
				</label>
					<div class="controls">
					    <textarea name="description" rows=5 maxlength=255 placeholder="Write a description here in less than 250 characters" class="span12"><?php print $blurb ?></textarea>
					</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="tag1">
					Category *
				</label>
				<div class="controls">
					<select required name="tag1" id="tag1" class="span12">
<?php


	foreach($result as $curr_category)
	  {
		$index = $curr_category['id'];
		$cat= $curr_category['tag'];
		echo "
						<option ".($tag_1 == $index ? "selected='selected'":"")."value='$index'>$cat</option>";
      }
?>
					</select>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="tag2">
					Tags * 
				</label>
					<div class="controls-row">
						<input required type="text" name="tag2" id="tag2" value="<?php print $tag2 ?>" class="span6">
						<input required type="text" name="tag3" id="tag3" value="<?php print $tag3 ?>" class="span6">
					</div>
			</div>
			</div>


			<div class="span6">
			<label><strong>Optional Fields:</strong></label>
			<?php print $photo_html ?>
			<div class="control-group">
				<label class="control-label" for="image_upload"><?php print $image_label ?> (must be less than 2Mb)</label>
					<div class="controls">
						<input type="file" name="image_upload" id="image_upload" size=05>
					</div>
			</div>

			<input type="checkbox" name="event-check" onchange="toggleEventDisplay()" <?php print ($event_flag ? "checked=true":"")?>> Event <br>


			<div id="event-fields">
			<div class="control-group">
				<label class="control-label" for="date">
					Date 
				</label>
				<div class="controls">
					<input type="text" name="date" id="date" <?php print ($event_flag ? "value='$date'": "") ?> placeholder="Click to add date..." class="span12">
				</div>
			</div>
			

			<div id="time-group">
			<div class="control-group">
				<label class="control-label" for="start_time">
					Time 
				</label>
				<div class="controls">
					<input type="text" name="start_time" id="start_time" <?php print ($start_flag ? "value='$start_time'":"") ?> placeholder="Start" class="span5"> - <input type="text" name="end_time" id="end_time" <?php print ($end_flag ? "value='$end_time'":"") ?> placeholder="End" class="span5">
				</div>
			</div>
			</div>



			</div>
			---------
<?php
	if($default_address != "")
	{
		echo "
			<div id='default-address'>
				<button type='button' class='btn-mini' onclick=\"$('#default-address').hide(); $('#alternate-address').show();\">Change Address</button><br>
					".$business['address']."<br>
					".$business['city'].", ".$business['state'].", ".$business['zip']."
			</div>";
	}
?>

			<div id='alternate-address'>

			<div class="control-group">
				<label class="control-label" for="address">Alternate Address</label>
					<div class="controls">
						<input type="text" maxlength=250 name="address" id="address" <?php print ($alt_address_flag ? "value='$address'" : "") ?> placeholder="Nearest cross-street" class="span12">
					</div>
			</div>
			<div class="control-group">
					<div class="controls">
						<input type="text" maxlength=250 name="city" id="city" <?php print ($alt_address_flag ? "value='$city'" : "") ?> placeholder="<?php print ($default_address!="" ? $business['city']:"City") ?>" class="span8">
						<input type="text" maxlength=10 name="zip" id="zip" <?php print ($alt_address_flag ? "value='$zip'" : "") ?> placeholder="<?php print ($default_address!="" ? $business['zip']:"Zip") ?>" class="span4">
					</div>
			</div>
			<div <?php print ($default_address=="" ? "class='hidden'":"")?>>
			<button type='button' class='btn-mini' onclick="$('#alternate-address').hide(); $('#default-address').show(); $('#address, #city, #zip').val('');">Use Default Address</btn>
			</div>
			</div>
			---------
			<div class="control-group">
				<label class="control-label" for="url">URL</label>
					<div class="controls">
					    <input type="text" maxlength=250 name="url" id="url" value="<?php print $url ?>" placeholder="Do not include 'http://'" class="span12">
					</div>
			</div>

			</div>

			</div>

			<div class="form-actions">				
				<button type="button" class="btn pull-left" id="cancel-button" onclick="$.bbq.pushState({'b':'members', 'view':'posts'})" tabindex=-1>Cancel</button>
				<button type="submit" class="btn btn-primary pull-right" id="add-submit">Submit</button>
			</div>
			</fieldset>
			</form>
			</div>
	

