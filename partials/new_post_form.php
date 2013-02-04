<?php
/***********************************************
file: partials/new_post_form.php
creator: Ian McEachern

This partial displays the new post form 
 ***********************************************/

require('../includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

$new_post_args = "";
$business_select_box = "";

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
	$query = "SELECT category FROM business WHERE id=".$GLOBALS['b_id'];
	$result = query_db($query);
	$category = $result[0]['category'];
  }
echo "
		<script>
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
						maxDate: '+7D'
					});
				$('.new-post-form').ajaxForm(function() {
					loadContentByURL('posts');
				});
			});
		</script>
		<div id='js-content'>
		<form name='new-post-form' class='new-post-form'  enctype='multipart/form-data' action='scripts/new_post$new_post_args' method='post'>
			<fieldset>
			<div class='row-fluid span12'>
			<div class='span6'>
			<label><strong>Required fields...</strong></label>".$business_select_box."
   			<div class='control-group'>
				<label class='control-label' for='title'>
					Title *
				</label>
				<div class='controls'>
					<input type='text' maxlength=50 name='title' id='title' placeholder='Insert title here...' class='span12'>
				</div>
			</div>
			<div class='control-group'>
				<label class='control-label' for='date'>
					Date *
				</label>
				<div class='controls'>
					<input required type='text' name='date' id='date' placeholder='Click to add date...' class='span12'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='description'>
					Description * 
				</label>
					<div class='controls'>
					    <textarea name='description' rows=5 maxlength=255 placeholder='Write a description here in less than 250 characters' class='span12'></textarea>
					</div>
			</div>
			<div class='control-group'>
				<label class='control-label' for='tag1'>
					Category *
				</label>
				<div class='controls'>
					<select required name='tag1' id='tag1' class='span12'>
						".($category == 0 ? "<option selected='selected'></option>":"");

	$result = get_categories();
	foreach($result as $curr_category)
	  {
		$index = $curr_category['id'];
		$cat= $curr_category['tag'];
		echo "
						<option ".($category == $index ? "selected='selected'":"")."value='$index'>$cat</option>";
      }
	
	echo "
					</select>
				</div>
			</div>
			<div class='control-group'>
				<label class='control-label' for='tag2'>
					Tags * 
				</label>
					<div class='controls-row'>
						<input required type='text' name='tag2' id='tag2' placeholder='$tag2_example' class='span6'>
						<input required type='text' name='tag3' id='tag3' placeholder='$tag3_example' class='span6'>
					</div>
			</div>
			</div>


			<div class='span6'>
			<label><strong>Optional Fields:</strong></label>
			<div class='control-group'>
				<label class='control-label' for='image_upload'>Image (must be less than 2Mb)</label>
					<div class='controls'>
						<input type='file' name='image_upload' id='image_upload' class='span12'>
					</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='address'>Address</label>
					<div class='controls'>
						<input type='text' maxlength=250 name='address' id='address' placeholder='Insert address of event here...' class='span12'>
					</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='url'>Purchase URL</label>
					<div class='controls'>
					    <input type='text' maxlength=250 name='url' id='url' placeholder='Do not include \"http://\"' class='span12'>
					</div>
			</div>

			</div>

			</div>

			<div class='form-actions'>				
				<button type='button' class='btn pull-left' id='cancel-button' onclick='loadContentByURL(\"\"); smartPushState(\"\")' tabindex=-1>Cancel</button>
				<button type='submit' class='btn btn-primary pull-right' id='add-submit'>Submit</button>
			</div>
			</fieldset>
			</form>
			</div>";

disconnect_from_db();
?>