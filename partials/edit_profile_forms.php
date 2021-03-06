<?php
/***********************************************
file: partials/edit_profiles_form.php
creator: Ian McEachern

This partial displays the edit forms for the 
indicated business and user
 ***********************************************/

require('../includes/includes.php');
require('../includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

$id = "";
$u_id = "";
if(isset($_GET['id']))
  {
	$id = $_GET['id'];
	$query = "SELECT admin_id FROM business WHERE id=$id";
	$result = query_db($query);
	$u_id =  $result[0]['admin_id'];
  }
echo "
		<script>
			$(function(){
				$('.accordion').accordion({
					collapsible:true,
					active:false,
					heightStyle:'content',
					activate:function(){repositionContainers();}
				});


				$('.edit-business-form').ajaxForm(function() {
					location.reload();
				});

				$('.edit-user-form').ajaxForm(function() {
					location.reload();
				});
			});
		</script>
		<div id='js-content'>
		
		<div class='accordion'>
			<h3>Edit Business Information</h3>
			<div>";

print_edit_business_form($id);
echo "
			</div>";
if($u_id == "" || $u_id != 0)
  {
	echo "
			<h3>Edit User Information</h3>
			<div>";

	print_edit_user_form($u_id);

	echo "
			</div>";
  }
echo "
		</div>
		</div>";

disconnect_from_db();

function print_edit_business_form($id="")
  {
	if($id=="")
	  {
		$b_id = $_SESSION['b_id'];
		$append_string = "";	
	  }
	else
	  {
		$b_id = $id;
		$append_string = "?id=$id";
	  }

$query = "SELECT * FROM business WHERE id=$b_id";
$result = query_db($query);
$business = $result[0];
extract($business);

$name = add_slashes($name);
$address = add_slashes($address);
$city = add_slashes($city);
$state = add_slashes($state);
$url = add_slashes($url);
$number = add_slashes($number);
$hours = add_slashes($hours);

echo "
							<div class='business-info business-card'>
								<h3 style='text-align:center'>";
	$close_link = "";
	if($url != "")
	{
		echo "<a href=\"http://$url\">";
		$close_link = "</a>";
	}
	if($logo != "")
    {
 		echo "<img src='images/logos/$logo' title='$name' alt='$name'>";
   	}
   	else
   	{
   		echo $name;
   	}
	echo $close_link."</h3><br>
							<div class='row'>";
	if($hours != "")
	  {
	echo "
							<div class='span6'>
								Hours:<br>";
	$exploded_hours = explode(",", $hours);
	foreach($exploded_hours as $line)
	{
		echo "
								$line<br>";
	}
	echo "
							</div>";
	  }
	echo "
							<div class='span6'>
								<address>
								<a id=\"business-address\" href=\"http://maps.google.com/?q=$address, $city, $state $zip\">
								$address<br>
								$city, $state, $zip<br>
								</a>
								".($number != "" ? "P: $number<br>":"")."
								</address>
							</div>
						</div>
					</div>
		<form name='edit-business-form' class='edit-business-form' enctype='multipart/form-data' action='scripts/edit_business$append_string' method='post'>
			<fieldset>

			<div class='row-fluid span12'>

			<div class='span6'>
			<label><strong>Required fields:</strong></label>

			<div class='control-group'>
				<label class='control-label' for='name'>
					Name *
				</label>
				<div class='controls'>
					<input required autofocus='true' type='text' maxlength=100 name='bus_name' id='bus_name' value='$name' placeholder='Type your business name here...' class='span12'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='address'>
					Address *
				</label>
				<div class='controls'>
					<input required type='text' maxlength=100 name='address' id='address' value='$address' placeholder='Address of your business...' class='span11'>
				</div>
			</div>

			<div class='control-group'>
				<div class='controls'>
					<input required type='text' maxlength=32 name='city' id='city' value='$city' placeholder='City...' class='span5'>
					<input required type='text' maxlength=2 name='state' id='state' value='$state' placeholder='State...' class='span2'>
					<input required type='text' maxlength=5 name='zip' id='zip' value='$zip' placeholder='Zip...' class='span4'>
				</div>
			</div>
			
			<div class='control-group'>
				<label class='control-label' for='category'>
					Default Category *
				</label>
				<div class='controls'>
					<select required name='category' id='category' class='span12'>";
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

			</div>
			
			<div class='span6'>
			<label><strong>Optional fields...</strong></label>
			<div class='control-group'>
				<label class='control-label' for='logo_upload'>
					Logo (must be smaller than 2Mb)
				</label>
				<div class='controls'>
					<input type='file' name='logo_upload' id='logo_upload' size=5>
				</div>
			</div>";
	if($photo!="")
	  {
		echo "<img src='images/posts/$photo' alt='default photo for your business' style='width:100%'>";
	  }
	echo "
			<div class='control-group'>
				<label class='control-label' for='photo_upload'>
					Default Post Photo (must be smaller than 2Mb)
				</label>
				<div class='controls'>
					<input type='file' name='photo_upload' id='photo_upload' size=5>
				</div>
			</div>
			<div class='control-group'>
				<label class='control-label' for='number'>
					Number
				</label>
				<div class='controls'>
					<input type='text' maxlength=12 name='number' id='number' value='$number' placeholder='Phone number...' class='span12'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='url'>
					URL
				</label>
				<div class='controls'>
					<input type='text' maxlength=50 name='url' id='url' value='$url' placeholder='Do not include \"http://\"' class='span12'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='hours'>
					Hours
				</label>
				<div class='controls'>
					<input type='text' maxlength=100 name='hours' id='hours' value='$hours' placeholder='Delineate with a comma...' class='span12'>
				</div>
			</div>

			</div>

			</div>

			

			<div class='form-actions'>
				<button type='button' class='btn' id='cancel-button' onclick='$(\".accordion\").accordion(\"option\", \"active\",\"false\")' tabindex=-1>Cancel</button>
				<button type='submit' class='btn btn-primary pull-right' id='submit' name='submit'>Submit</button>	
			</div>
			</fieldset>
		</form>";
}






function print_edit_user_form($id="")
  {
	if($id == "")
	  {
		$id = $_SESSION['m_id'];
		$append_string = "";
	  }
	else
	  {
		$append_string = "?id=$id";
	  }
	$query = "SELECT email FROM members WHERE id=$id";
	$result = query_db($query);
	extract($result[0]);
	echo "
		<form name='edit-user-form' class='edit-user-form'  action='scripts/edit_user$append_string' method='post' class='form-horizontal'>
			<fieldset>";
	if(check_admin())
	  {
		echo "
			<div class='control-group'>
				<label class='control-label' for='email'>
					Email *
				</label>
				<div class='controls'>
					<input required type='text' maxlength=50 name='email' id='email' value='$email' placeholder='Email...' class='input-medium'>
				</div>
			</div>";
	  }
	echo "
			<div class='control-group'>
				<label class='control-label' for='pass1'>
					Password
				</label>
				<div class='controls'>
					<input type='password' maxlength=16 name='pass1' id='pass1' placeholder='Password...' class='input-medium'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='pass2'>
					Re-enter
				</label>
				<div class='controls'>
					<input type='password' maxlength=16 name='pass2' id='pass2' placeholder='Confirm your password...' class='input-medium'>
				</div>
			</div>
			<div class='form-actions'>
				<button type='button' class='btn' id='cancel-button' onclick='$(\".accordion\").accordion(\"option\", \"active\",\"false\")' tabindex=-1>Cancel</button>
				<button type='submit' class='btn btn-primary' id='submit' name='submit'>Submit</button>	
			</div>
			</fieldset>
		</form>";
}
?>