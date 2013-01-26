<?php
/***********************************************
file: partials/new_business_form.php
creator: Ian McEachern

This partial displays the new business form
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();
echo "
		<div id='js-content'>
		<form name='edit-business-form' enctype='multipart/form-data' action='../scripts/new_business' method='post'>
			<fieldset>

			<div class='row-fluid span12'>

			<div class='span6'>
			<label><strong>Required fields:</strong></label>

			<div class='control-group'>
				<label class='control-label' for='name'>
					Name *
				</label>
				<div class='controls'>
					<input required autofocus='true' type='text' maxlength=100 name='name' id='name' placeholder='Type your busines name here...' class='span12'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='address'>
					Address *
				</label>
				<div class='controls'>
					<input required type='text' maxlength=100 name='address' id='address' placeholder='Address of your business...' class='span11'>
				</div>
   			</div>

			<div class='control-group'>
				<div class='controls'>
					<input required type='text' maxlength=32 name='city' id='city' placeholder='City...' class='span5'>
					<input required type='text' maxlength=2 name='state' id='state' placeholder='State...' class='span2'>
					<input required type='text' maxlength=5 name='zip' id='zip' placeholder='Zip...' class='span4'>
				</div>
			</div>
			
			<div class='control-group'>
				<label class='control-label' for='category'>
					Category *
				</label>
				<div class='controls'>
					<select required name='category' id='category' class='span12'>
						<option selected='selected'></option>";
	$result = get_categories();
	foreach($result as $curr_category)
	  {
		$index = $curr_category['id'];
		$cat= $curr_category['tag'];
		echo "
						<option value='$index'>$cat</option>";
      }
	
	echo "
					</select>
				</div>
			</div>

			</div>
			
			<div class='span6'>
			<label><strong>Optional fields...</strong></label>
			<div class='control-group'>
				<label class='control-label' for='logo'>
					Logo (must be smaller than 2Mb)
				</label>
				<div class='controls'>
					<input type='file' name='logo' id='logo' class='span8'>
				</div>
			</div>
			<div class='control-group'>
				<label class='control-label' for='number'>
					Number
				</label>
				<div class='controls'>
					<input type='text' maxlength=12 name='number' id='number' placeholder='Phone number...' class='span12'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='url'>
					URL
				</label>
				<div class='controls'>
					<input type='text' maxlength=50 name='url' id='url' placeholder='Do not include \"http://\"' class='span12'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='hours'>
					Hours
				</label>
				<div class='controls'>
					<input type='text' maxlength=100 name='hours' id='hours' placeholder='Delineate with a comma...' class='span12'>
				</div>
			</div>

			</div>

			</div>

			

			<div class='form-actions'>
				<button type='submit' class='btn btn-primary pull-right' id='submit' name='submit'>Submit</button>	
			</div>
			</fieldset>
		</form>
		</div>";



disconnect_from_db();
?>