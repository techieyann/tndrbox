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

//check that id was passed
if(isset($_GET['id']))
  {
	$id = $_GET['id'];
	
	//get post data
	$query = "SELECT * FROM postings WHERE id=$id";
	$result = query_db($query);
	extract($result[0]);
	
	//check authorship of the post
	if(check_admin() == false && $a_id != $GLOBALS['m_id'])
	  {
		header('location:settings');
	  }

	$tag1 = get_tag($tag_1);
	$tag2 = get_tag($tag_2);
	$tag3 = get_tag($tag_3);
	
	//write prepopulated form
	echo "
		<script>
			$(document).ready(function(){
				$('#tag1').autocomplete({source:'includes/tag_search.php'});
				$('#tag2').autocomplete({source:'includes/tag_search.php'});	
				$('#tag3').autocomplete({source:'includes/tag_search.php'});

				$('#date').datepicker({
					dateFormat:'yy-mm-dd'
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
				<label class='control-label' for='description'>
					Description * 
				</label>
					<div class='controls'>
					    <textarea name='description' rows=5 maxlength=255 placeholder='Write a description here in less than 255 characters' class='span12'>$blurb</textarea>
					</div>
			</div>
			<div class='control-group'>
				<label class='control-label' for='tag1'>
					Tags * 
				</label>
					<div class='controls-row'>
					    <input required type='text' name='tag1' id='tag1' value='$tag1' placeholder='Tag 1' class='span4'>
						<input required type='text' name='tag2' id='tag2' value='$tag2' placeholder='Tag 2' class='span4'>
						<input required type='text' name='tag3' id='tag3' value='$tag3' placeholder='Tag 3' class='span4'>
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
				<label class='control-label' for='date'>Date</label>
				<div class='controls'>
					<input type='text' name='date' id='date' ".($date != '0000-00-00' ? "value='$date'":"")." placeholder='Click to add date...' class='span12'>
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
disconnect_from_db();
?>