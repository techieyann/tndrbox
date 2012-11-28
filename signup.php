<?php
/***********************************************
file: signup.php
creator: Ian McEachern

This file presents a signup dialogue with 
recaptcha and parses error messages
 ***********************************************/
require('includes/includes.php');

require('includes/prints.php');

//head
$GLOBALS['header_html_title'] = "tndrbox - Signup";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "";


print_head();
print_body();
print_foot();

function print_body()
{
  	echo "
	<div id='new-user' class='column'>";
	
	if(isset($_GET['error']))
	  {
		echo "
		<h3 class=\"red-text\">Please try again:</h3>";
		if(strcmp($_GET['error'],"captcha")==0)
		  {
			echo "
		<p class=\"red-text\">The captcha tricked you again, robot.</p>";
		  }
		elseif(strcmp($_GET['error'],"email")==0)
		  {
			echo "
		<p class=\"red-text\">That email format was not recognized</p>";
		  }
		elseif(strcmp($_GET['error'],"dup")==0)
		  {
			echo "
		<p class=\"red-text\">That email is already in use</p>";
		  }
		elseif(strcmp($_GET['error'],"bus_dup")==0)
		  {
			echo "
		<p class=\"red-text\">That business name is already taken</p>";
		  }
		elseif(strcmp($_GET['error'],"password")==0)
		  {
			echo "
		<p class=\"red-text\">Passwords do not match</p>";
		  }
		elseif(strcmp($_GET['error'],"db")==0)
		  {
			echo "
		<p class=\"red-text\">Sorry, there was a database error</p>";
		  }
	  }

	echo "
		<form name='new-user-form' action='scripts/new_user.php' method='post' class='form-horizontal'>
			<fieldset>
			<legend>";
	require_once('includes/recaptchalib.php');
	$publickey = "6LchVNESAAAAAMenf3lTWgj00YzeyK-hRKS_bozg";
	echo recaptcha_get_html($publickey);
	echo "
			</legend>
			<div class='control-group'>
				<label class='control-label' for='email'>
					Email *
				</label>
				<div class='controls'>
					<input required type='text' maxlength=50 name='email' id='email' placeholder='Email...' class='input-medium'>
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

			<div class='control-group'>
				<label class='control-label' for='name'>
					Business Name *
				</label>
				<div class='controls'>
					<input required type='text' maxlength=100 name='name' id='name' placeholder='Name...' class='input-medium'>
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