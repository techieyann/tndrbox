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

$title = add_slashes($title);
$description = add_slashes($description);
$address = add_slashes($address);
$url = add_slashes($url);
	
	//check authorship of the post
	if(!$admin_flag && $a_id != $GLOBALS['m_id'])
	  {
		header('location:../settings');
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
	
	//write prepopulated form
	echo "
		<script>
			$(document).ready(function(){
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
					maxDate: '+28D'
				});

				$('.edit-post-form').ajaxForm(function() {
					loadContentByURL('posts');
				});
			});
		</script>
		<div id='js-content'>
		<form class='edit-post-form' name='edit-post-form'  enctype='multipart/form-data' action='scripts/edit_post?id=$id' method='post'>
			<fieldset>
			<div class='row-fluid span12'>
			<div class='span6'>
			<label><strong>Required fields...</strong></label>
   			<div class='control-group'>
				<label class='control-label' for='title'>
					Title *
				</label>
				<div class='controls'>
					<input type='text' maxlength=50 name='title' id='title' value='$title' placeholder='Insert title here...' class='span12'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='date'>
					Date *
				</label>
				<div class='controls'>
					<input required type='text' name='date' id='date', value='$date', class='span12'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='description'>
					Description * 
				</label>
					<div class='controls'>
					    <textarea name='description' rows=5 maxlength=255 placeholder='Write a description here in less than 255 characters' class='span12'>$blurb</textarea>
					</div>
			</div>



			<div class='control-group'>
				<label class='control-label' for='tag1'>
					Category *
				</label>
				<div class='controls'>
					<select required name='tag1' id='tag1' class='span12'>";

	$result = get_categories();
	foreach($result as $curr_category)
	  {
		$index = $curr_category['id'];
		$cat= $curr_category['tag'];
		echo "
						<option ".($tag_1 == $index ? "selected='selected'":"")."value='$index'>$cat</option>";
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
						<input required type='text' name='tag2' id='tag2' value='$tag2' class='span6'>
						<input required type='text' name='tag3' id='tag3' value='$tag3' class='span6'>
					</div>
			</div>





			</div>


			<div class='span6'>
			<label><strong>Optional Fields:</strong></label>
			$photo_html
			<div class='control-group'>
				<label class='control-label' for='image_upload'>$image_label (must be less than 2Mb)</label>
					<div class='controls'>
						<input type='file' name='image_upload' id='image_upload' class='span12'>
					</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='address'>Address</label>
					<div class='controls'>
						<input type='text' maxlength=250 name='address' id='address' value='$alt_address' placeholder='Insert address of event here...' class='span12'>
					</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='url'>Purchase URL</label>
					<div class='controls'>
					    <input type='text' maxlength=250 name='url' id='url' value='$url' placeholder='Do not include \"http://\"' class='span12'>
					</div>
			</div>

			</div>

			</div>

			<div class='form-actions'>				
				<button type='button' class='btn pull-left' id='cancel-button' onclick='loadContentByURL(\"posts\"); smartPushState(\"posts\")' tabindex=-1>Cancel</button>
				<button type='submit' class='btn btn-primary pull-right' id='edit-submit'>Submit</button>
			</div>
			</fieldset>
			</form>
			</div>";
	  }
  }
disconnect_from_db();
?>