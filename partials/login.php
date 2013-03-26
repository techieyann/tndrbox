<?php
	echo "<br><br>
	<script>
		$('.signin-form').ajaxForm(function() {
			var window_height = $(window).innerHeight();

			$('.hide-box-button').hide();

			var url = '/partials/members';

			$('#box').animate({
				top: window_height-105
			},500, function(){

				$('#box-js-content').hide();
				$('#box-js-content').load(url, function(){	
	
					var boxJsContent = $('#box-js-content');
					var topPosition= $('#posting-header').height() + $('#box-links').height()+100;

					$('#box').animate({
						top: topPosition
					},1000, function(){ 
						$('.hide-box-button').show(); 
					});
					$('#box-js-content').show();
				});
			});
		});
	</script>
	<div id='signin-form' class='content'>";
	if(isset($_GET['error']))
	  {
		if(strcmp($_GET['error'],"email")==0)
		  {
			echo "
		<h5 class='text-error'>That email format was not recognized</h5>";
		  }
		if(strcmp($_GET['error'],"match")==0)
		  {
			echo "
		<h5 class='text-error'>Incorrect email/password combination</h5>";
		  }
	  }

	echo "
		<form class='signin-form' name='signin-form' action='scripts/validate_login.php' method='post' class='form'>
  	  		<input required type='text' name='email' id='email' maxlength=50 placeholder='Email Address...'>
	   		<input required type='password' name='pass' id='pass' maxlength=16 placeholder='Password...'>
			<br>
			<button class='btn btn-medium float-right' type='submit'>Sign in</button>
		</form>
	</div>
	</div><br><br>";
?>