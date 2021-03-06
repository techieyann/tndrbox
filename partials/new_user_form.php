<?php
/***********************************************
file: partials/new_user_form.php
creator: Ian McEachern

This partial displays the new user form
 ***********************************************/

require('../includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

if(check_admin())
  {
	$query = "SELECT id, name FROM business WHERE admin_id=0";
	$businesses = query_db($query);

	$businesses_or_captcha  = "
			<div class='control-group'>
				<label class='control-label' for='business'>
					Business *
				</label>
				<div class='controls'>
					<select required name='business' id='business'>
						<option selected='selected'></option>";
	
	foreach($businesses as $business)
	  {
		extract($business);
		$businesses_or_captcha .= "
						<option value='$id'>$name</option>";
	  }
	
	$businesses_or_captcha .= "
					</select>
				</div>
			</div>";
	$append_string = "?admin=1";
  }
else
  {
	$businesses_or_captcha = "
			<legend>";
	require_once('../includes/recaptchalib.php');
	$publickey = "6LchVNESAAAAAMenf3lTWgj00YzeyK-hRKS_bozg";
	$captcha = recaptcha_get_html($publickey);
	$businesses_or_captcha .= $captcha."
			</legend>";
	$append_string = "";
  }

echo "
		<script>
			$(function(){
				$('.new-user-form').ajaxForm(function() {
					loadContentByURL('new_post');
				});
			});
		</script>
		<div id='js-content'>
		<form name='new-user-form' class='new-user-form form-horizontal' action='scripts/new_user.php$append_string' method='post'>
			<fieldset>".$businesses_or_captcha."
			<div class='control-group'>
				<label class='control-label' for='email'>
					Email *
				</label>
				<div class='controls'>
					<input required type='text' maxlength=50 name='email' id='email' placeholder='Email...' class='input-medium'>
				</div>
			</div>
			<div class='control-group'>
				<label class='control-label' for='member-type'>
					Type *
				</label>
				<div class='controls'>
					<select required name='member-type' id='member-type'>
						<option selected='selected' value=1>1. Standard</option>
						<option value=2>2. Organizer</option>
					</select>
				</div>
			</div>
			<div class='control-group'>
				<label class='control-label' for='pass1'>
					Password *
				</label>
				<div class='controls'>
					<input required type='password' maxlength=16 name='pass1' id='pass1' placeholder='Password...' class='input-medium'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='pass2'>
					Re-enter *
				</label>
				<div class='controls'>
					<input required type='password' maxlength=16 name='pass2' id='pass2' placeholder='Confirm your password...' class='input-medium'>
				</div>
			</div>
			<div class='form-actions'>
				<button type='submit' class='btn btn-primary' id='submit' name='submit'>Submit</button>	
			</div>
			</fieldset>
		</form>
	</div>";

disconnect_from_db();
?>