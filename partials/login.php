<?php
	echo "
	<script>
		$('.alert').hide();
		$('.signin-form').ajaxForm({success: parseSigninReturn});
		function parseSigninReturn(responseText, statusText, xhr, \$form)
		{
			$('.alert').hide();
			if(statusText == 'success')
			{
				if(responseText == 'logged in')
				{
					$.bbq.pushState('b=members&view=new_post');
				}
				else if(responseText == 'email format')
				{
					$('#email-format').show();
					$('#email').focus();
				}
				else if(responseText == 'email password combo')
				{
					$('#email-pass').show();
					$('#pass').empty();
					$('#pass').focus();
				}
			}
		}
	</script>
	<div id='signin-form' class='content'>
		<div id='email-format' class='alert alert-error'>
			That email format was not recognized
		</div>
		<div id='email-pass' class='alert alert-error'>
			Incorrect email/password combination
		</div>

		<form class='signin-form' name='signin-form' action='scripts/validate_login.php' method='post' class='form'>
  	  		<input required type='text' name='email' id='email' maxlength=50 placeholder='Email Address...'>
	   		<input required type='password' name='pass' id='pass' maxlength=16 placeholder='Password...'>
			<br>
			<button class='btn btn-medium float-right' type='submit'>Sign in</button>
		</form>
	</div>
	</div>";
?>