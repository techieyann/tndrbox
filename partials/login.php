<?php
	echo "
	<script>
		$('.alert').hide();
		$('.signin-form').ajaxForm({success: parseSigninReturn});
		function parseSigninReturn(responseText, statusText, xhr, \$form)
		{

			if(statusText == 'success')
			{
				if(responseText == 'logged in')
				{
					$.bbq.pushState('b=members&view=new-post');
				}
				else if(responseText == 'email format')
				{
				  addAlert('signin-form', 'error', 'That email format was not recognized');

					$('#email').focus();
				}
				else if(responseText == 'email password combo')
				{
				  addAlert('signin-form', 'error', 'Incorrect email/password combination');
					$('#pass').empty();
					$('#pass').focus();
				}
			}
		}
	</script>
	<div id='signin-form' class='content'>
		<form class='signin-form' name='signin-form' action='scripts/validate_login.php' method='post' class='form'>
  	  		<input required type='text' name='email' id='email' maxlength=50 placeholder='Email Address...'>
	   		<input required type='password' name='pass' id='pass' maxlength=16 placeholder='Password...'>
			<br>
			<button class='btn btn-medium float-right' type='submit'>Sign in</button>
		</form>
	</div>
	</div>";
?>