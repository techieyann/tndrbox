<?php
/***********************************************
file: partials/new_post_form.php
creator: Ian McEachern

This partial displays the new post form 
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

$new_post_args = "";
$business_select_box = "";
$default_photo_html = "";
$default_address = "";
$tag2_example = "";
$tag3_example = "";

$image_label = "Image ";
$result = get_most_popular_tags(2);
if(isset($result[0]))
{
	$tag2_example = $result[0]['tag'];
	$tag3_example = $result[1]['tag'];
}


if(check_admin())
	{
	  $category = 0;
	  $new_post_args = "?admin=1";
	  $business_select_box = "
			<div class='control-group'>
				<label class='control-label' for='business'>
					Business *
				</label>
				<div class='controls'>
					<select required name='business' id='business' class='span12'>
						<option selected='selected'></option>";
	  $query = "SELECT id, name FROM business";
	  $result = query_db($query);
	  foreach($result as $business)
		{
		  extract($business);
		  $business_select_box .= "
						<option value='$id'>$name</option>";
        }
	
	  $business_select_box .= "
					</select>
				</div>
			</div>";
	}
else
  {
	$b_id = $GLOBALS['b_id'];

	$query = "SELECT address, city, state, zip FROM business WHERE id=$b_id";
	$result = query_db($query);
	if(isset($result[0]))
	{
		extract($result[0]);
		$default_address = "$address, $city, $state, $zip";
	}

	$query = "SELECT category, photo FROM business WHERE id=$b_id";
	$result = query_db($query);
	extract($result[0]);
	if($photo != "")
	  {
		$default_photo_html = "<img src='images/posts/$photo' class='span10 offset1'>";
		$image_label = "Change Image ";
	  }
  }
	$result = get_categories();
disconnect_from_db();

?>
		<script>
			var eventDisplay = false;
			$('#time-group').hide();
			$('#end_time').hide();
<?php if($default_address != ""){ print "$('#post-address').hide();";} ?>


			

			$(function(){
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
						showPeriod: true,
						showLeadingZero: true,
						onSelect: function(time){
							$('#end_time').show();
						}
					});
					$('#end_time').timepicker({
						showPeriod: true,
						showLeadingZero: true,
					});

				$('.new-post-form').ajaxForm({success: parseNewPostReturn});
			});
				function parseNewPostReturn(responseText, statusText, xhr, $form)
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


		</script>
		<div id="js-content">
		<form name="new-post-form" class="new-post-form"  enctype="multipart/form-data" action="scripts/new_post<?php print $new_post_args ?>" method="post">
			<fieldset>
			<div class="row-fluid span12">
			<div class="span6">
			<label><strong>Required fields...</strong></label>
			<?php print $business_select_box ?>
   			<div class="control-group">
				<label class="control-label" for="title">
					Title *
				</label>
				<div class="controls">
					<input type="text" maxlength=50 name="title" id="title" placeholder="Insert title here..." class="span12">
				</div>
			</div>
<?php
	if($default_address != "")
	{
		echo "
			<div id='default-address'>
				<button type='button' class='btn-mini' onclick=\"$('#default-address').hide(); $('#post-address').show(); $('#address, #city, #zip').val('');\">Change Address</button><br>
					$address<br>
					$city, $state, $zip
			</div>";
	}
?>

			<div id='post-address'>

			<div class="control-group">
				<label class="control-label" for="address">Address *</label>
					<div class="controls">
						<input required type="text" maxlength=250 name="address" id="address" <?php
	if($default_address!="") echo "value='$address' ";
						?>placeholder="Address" class="span12">
					</div>
			</div>
			<div class="control-group">
					<div class="controls">
						<input required type="text" maxlength=250 name="city" id="city" <?php
	if($default_address!=""){ echo "value='$city' ";}
						?> placeholder="City" class="span8">
						<input required type="text" maxlength=10 name="zip" id="zip" <?php
	if($default_address!=""){ echo "value='$zip' ";}
						?> placeholder="Zip" class="span4">
					</div>
			</div>
			<?php
				if($default_address!="")
				{
					echo"
				<button type='button' class='btn-mini' onclick=\"$('#post-address').hide(); $('#default-address').show(); $('#address').val('$address'); $('#city').val('$city'); $('#zip').val('$zip');\">Use Default Address</btn>";
				}
			?>

			</div><!-- #post-address -->
			---------
			<div class="control-group">
				<label class="control-label" for="description">
					Description * 
				</label>
					<div class="controls">
					    <textarea name="description" rows=5 maxlength=255 placeholder="Write a description here in less than 250 characters" class="span12"></textarea>
					</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="tag1">
					Category *
				</label>
				<div class="controls">
					<select required name="tag1" id="tag1" class="span12">
<?php
  if($category == 0)
	{
	  print "<option selected='selected'></option>";
	}



	foreach($result as $curr_category)
	  {
		$index = $curr_category['id'];
		$cat= $curr_category['tag'];
		print "
						<option ".($category == $index ? "selected='selected'":"")."value='$index'>$cat</option>";
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
						<input required type="text" name="tag2" id="tag2" placeholder="<?php print $tag2_example ?>" class="span6">
						<input required type="text" name="tag3" id="tag3" placeholder="<?php print $tag3_example?>" class="span6">
					</div>
			</div>
			</div>


			<div class="span6">
			<label><strong>Optional Fields:</strong></label>
			<?php print $default_photo_html ?>
			<div class="control-group">
				<label class="control-label" for="image_upload"><?php print $image_label ?> (must be less than 2Mb)</label>
					<div class="controls">
						<input type="file" name="image_upload" id="image_upload" size=05>
					</div>
			</div>
			<input type="checkbox" id="photo_permission"> I certify that I have the rights to this image and am granting tndrbox permission to reproduce it.<br><br>


			<div class="control-group">
				<label class="control-label" for="date">
					Date 
				</label>
				<div class="controls">
					<input type="text" name="date" id="date" placeholder="Click to add date..." class="span12">
				</div>
			</div>
			

			<div id="time-group">
			<div class="control-group">
				<label class="control-label" for="start_time">
					Time 
				</label>
				<div class="controls">
					<input type="text" name="start_time" id="start_time" placeholder="Start" class="span5"> - <input type="text" name="end_time" id="end_time" placeholder="End" class="span5">
				</div>
			</div>
			</div>




			<div class="control-group">
				<label class="control-label" for="url">URL</label>
					<div class="controls">
					    <input type="text" maxlength=250 name="url" id="url" placeholder="Do not include 'http://'" class="span12">
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

