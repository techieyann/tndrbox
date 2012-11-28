<?php
/***********************************************
file: new-business.php
creator: Ian McEachern

This file displays the form for inputting a new
user's business information.
 ***********************************************/
require('includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

//set variables
//body
$id = $GLOBALS['b_id'];

$query = "SELECT name FROM business WHERE id='$id'";
$result = query_db($query);
$res = mysql_fetch_array($result);
$name = $res['name'];

$query = "SELECT id, tag FROM tags where id<0 ORDER BY tag ASC";
$result = query_db($query);
while($category = mysql_fetch_array($result))
  {
	$index = $category['id'];
	$categories[$index] = $category['tag'];
  }

//head
$GLOBALS['header_html_title'] = "tndrbox - ";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  global $id, $name, $categories;


  echo "
	<div id='new-business' class='span6 offset3 column'>
		<form name='new-business-form' enctype='multipart/form-data' action='scripts/new_business.php?id=$id' method='post' class='form-horizontal'>
			<fieldset>
			<legend>Please enter your business information.</legend>
			<div class='control-group'>
				<label class='control-label' for='name'>
					Name *
				</label>
				<div class='controls'>
					<input required type='text' maxlength=100 name='name' id='name' value='$name' class='input-xlarge'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='category'>
					Category *
				</label>
				<div class='controls'>
					<select required name='category' id='category' autofocus='true'>
						<option selected='selected'></option>";
  foreach($categories as $index => $cat)
	{
	  echo "
						<option value='$index'>$cat</option>";
	}
	echo "
					</select>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='address'>
					Address
				</label>
				<div class='controls'>
					<input type='text' maxlength=100 name='address' id='address' placeholder='Address of your business...' class='input-xlarge'>
				</div>
			</div>

			<div class='control-group'>
				<div class='controls'>
					<input type='text' maxlength=32 name='city' id='city' placeholder='City..' class='input-small'>
					<input type='text' maxlength=2 name='state' id='state' value='Ca' class='input-mini'>
					<input type='text' maxlength=5 name='zip' id='zip' placeholder='Zip...' class='input-mini'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='number'>
					Number
				</label>
				<div class='controls'>
					<input type='text' maxlength=12 name='number' id='number' placeholder='Phone number...' class='input-medium'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='url'>
					URL
				</label>
				<div class='controls'>
					<input type='text' maxlength=50 name='url' id='url' placeholder='Do not include \"http://\"' class='input-xlarge'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='hours'>
					Hours
				</label>
				<div class='controls'>
					<input type='text' maxlength=100 name='hours' id='hours' placeholder='Delineate with a comma...' class='input-xlarge'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='logo'>
					Logo
				</label>
				<div class='controls'>
					<input type='file' name='logo' id='logo' class='input-file'>
					<span class='help-block'>
						Note: filesize must be <60Kb
					</span>
				</div>
			</div>

			<div class='form-actions'>
				<button type='submit' class='btn btn-primary' id='submit' name='submit'>Submit</button>	
			</div>
			</fieldset>
		</form>
	</div>";
}
?>