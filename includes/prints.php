<?php

/***********************************************
file: includes/prints.php
creator: Ian McEachern

This file contains the functions which print
often used sections of the site. For example, 
print_head() prints the site from the start of 
the output file to the start of the body tag.

These functions utilize values set by 
analyze_user() in includes.php. They will only
work properly if called after analyze_user().

This file also needs to be included after the
call to analyze_user() in order for the code to
access the metadata variables.
 ***********************************************/

function print_head()
{
	echo "
<!DOCTYPE html>

<html>

<head>

<script src='js/jquery.js' type='text/javascript'></script>
<!-- Bootstrap -->
<link href='css/bootstrap.min.css' rel='stylesheet' media='screen'>
<meta name='viewport' content='width=device-width, initial-scale=1.0'>

<link rel='stylesheet' type='text/css' href='css/styles.css' media='all'>
".$GLOBALS['header_scripts']."


<title>".$GLOBALS['header_html_title']."</title>
</head>

<body>

<div id='top' class='navbar navbar-inverse navbar-static-top'>
	<div class='navbar-inner'>
		<div class='container'>
			<a class='btn btn-navbar' data-toggle='collapse' data-target='.nav-collapse'>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
			</a>
			<a href='index' id='tndrbox-logo' class='brand'></a>";
   	if($GLOBALS['header_selected_page'] == "landing")
	  {
		echo "
			<form action='scripts/alpha_to_numeric_tag.php' method='get' class='navbar-search'>
					<input type='text' id='tag-search' name='tag-search' class='search-query' placeholder='Search Tags...'>
					<div class='icon-search'></div>
			</form>";
	  }
	echo "
		    <div class='nav-collapse collapse'>
				<ul class='nav main-nav pull-right'>";
	/*
					<li";
	if($GLOBALS['header_selected_page'] == "business")
	{
		echo " class='active'";
	}
	echo "><a href='business'>Businesses</a></li>
	*/
	if($GLOBALS['header_selected_page'] != "login" && $GLOBALS['header_selected_page'] != "about")
	  {
	echo "
			<li class='dropdown'>
					<a href='#' id='category-drop' class='dropdown-toggle' data-toggle='dropdown'>
					Categories
					<b class='caret'></b>
					</a>
					<ul class='dropdown-menu' role='menu' aria-labelledby='category-drop'>";
		$count = 0;
		$categories = get_active_categories();
		foreach($categories as $category)
		  {
			if($count++ != 0)
			  {
				echo "
					<li class='divider'></li>";
			  }
			extract($category);
			echo "
					<li><a href='index?tag=$id'>$tag</a></li>";
		  }
		echo "
					</ul>
				</li>";
	  }
	echo "
					<li";
	if($GLOBALS['header_selected_page'] == "about")
	{
		echo " class='active'";
	}
	echo "><a href='about'>About</a></li>
					<li";
	if($GLOBALS['logged_in'] == false)
	{
		if($GLOBALS['header_selected_page'] == "login")
		{
			echo " class='active'";
		}
		echo "><a href='login'>Login</a></li>";
	}
	else
	{
		if($GLOBALS['header_selected_page'] == "settings")
		{
			echo " class='active'";
		}
		echo "><a href='settings'>Settings</a></li>
					<li><a href='scripts/logout'>Logout</a></li>";
	}
	echo "	
				</ul>
			</div>
        </div>
	</div>	
</div>
<div id='body-container' class='container'><br>";

}

function print_foot()
{
  echo "
</div><br>
<div id='footer'>
	<a href='#top'>
	   <img id='footer-icon' src='images/footer-logo.png' alt='footer-logo' width='50' height='62'>
	</a>
	<br>
	<p class = 'white'>version ".$GLOBALS['version']."</p>
</div>

<!--Bootstrap-->
<script src='js/bootstrap.min.js'></script>

</body>

</html>";
}

function print_post_row($post_row)
{

	foreach($post_row as $post_data)
	  {
		$post = $post_data['post'];

		$span = "span".$post_data['span'];
		if($post != "filler")
		  {
		$id = $post['id'];

		echo "
			
			<li class='$span front-page-button'>";
		echo "
			<a href='?p=$id' class='modal-trigger thumbnail'>
			<div class='thumbnail'>";

		if($post['photo'] != "")
		  {
			$img_src = "images/posts/".$post['photo'];
			echo "
   			<img src='$img_src' alt='photo for ".$post['title']."'>";

		  }

		$tag_1 = $post['tag_1'];
		$tag_2 = $post['tag_2'];
		$tag_3 = $post['tag_3'];

		$tags[1] = get_tag($tag_1); 
		$tags[2] = get_tag($tag_2); 
		$tags[3] = get_tag($tag_3);

		echo "
			<h4>".$post['title']."</h4>";

		$date = format_date($id);

		if($date != "")
		  {
			echo "
				<p>$date</p>";
		  }
		echo "
					<ul class='inline centered'>
					<li>$tags[1]</li>
					<li>$tags[2]</li>
					<li>$tags[3]</li>
					</ul>
			</div>
			</a>
			</li>";
		  }
		else
		  {
			echo "
			<div class='$span'>
			</div>";
		  }
	  }

}
function print_new_business_form($id="0", $name="")
{
	echo "
		<form name='new-business-form' enctype='multipart/form-data' action='scripts/new_business.php?id=$id' method='post' class='form-horizontal'>
			<fieldset>
			<legend>Please enter your business information.</legend>
			<div class='control-group'>
				<label class='control-label' for='name'>
					Name *
				</label>
				<div class='controls'>
					<input required autofocus='true' type='text' maxlength=100 name='name' id='name' value='$name' class='input-xlarge'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='category'>
					Category *
				</label>
				<div class='controls'>
					<select required name='category' id='category'>
						<option selected='selected'></option>";
	$result = get_categories();
	foreach($result as $category)
	  {
		$index = $category['id'];
		$cat= $category['tag'];
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
					<input type='text' maxlength=32 name='city' id='city' value='Oakland' class='input-small'>
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
		</form>";
}
?>
