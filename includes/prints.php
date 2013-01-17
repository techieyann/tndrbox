<?php

/***********************************************
file: prints.php
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
			<ul class='nav main-nav' role='navigation'>
				<li class='dropdown'>
					<a href='#' id='category-drop' class='dropdown-toggle' data-toggle='dropdown'>
					Categories
					<b class='caret'></b>
					</a>
					<ul class='dropdown-menu' role='menu' aria-labelledby='category-drop'>";
		$count = 0;
		foreach($GLOBALS['categories'] as $category)
		  {
			if($count++ != 0)
			  {
				echo "
					<li class='divider'></li>";
			  }
			extract($category);
			echo "
					<li><a href='?tag=$id'>$tag</a></li>";
		  }
		echo "
					</ul>
				</li>
			</ul>
			<form action='scripts/alpha_to_numeric_tag.php' method='get' class='navbar-search'>
					<input type='text' id='tag-search' name='tag-search' class='search-query' placeholder='Search Tags...'>
					<div class='icon-search'></div>
			</form>";
	  }
	echo "
		    <div class='nav-collapse collapse'>
				<ul class='nav main-nav pull-right'> 
					<li";
	if($GLOBALS['header_selected_page'] == "business")
	{
		echo " class='active'";
	}
	echo "><a href='business'>Businesses</a></li>
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

function print_formatted_time($time)
{
//YYYY-MM-DD hh:mm:ss -> hh:mm(am/pm) MM/DD/YY

//Hour formatting
$hours = substr($time,11,2);
$pmam = "am ";
if($hours > 12)
{
	$hours -= 12;
	$pmam = "pm ";
}

//Minute extraction
$minutes = substr($time,14,2);

//Month extraction
$month = substr($time,5,2);

//Day extraction
$day = substr($time,8,2);

//Year extraction
$year = substr($time,2,2);

//Output
echo $hours.":".$minutes.$pmam.$month."/".$day."/".$year;


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
		$business = $post_data['business'];

		echo "
			
			<li class='$span front-page-button'>";
		echo "
			<a href='#post-$id-modal' class='thumbnail' role='button' data-toggle='modal'>
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
					<div class='row-fluid span12'>
					<div class='span4'>
						$tags[1]
					</div>
					<div class='span4'>
						$tags[2]  
					</div>
					<div class='span4'>
						$tags[3]
					</div>
					</div>
			</div>
			</a>
			</li>";

		print_formatted_modal($post, $business);
		  }
		else
		  {
			echo "
			<div class='$span'>
			</div>";
		  }
	  }
	//	echo "
	//	</ul>";
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

function print_edit_business_form($business)
{
extract($business);

echo "
	<div id='edit-business'>
		<table>
			<form name=\"$id\" id='edit-business-form' enctype=\"multipart/form-data\" method=\"post\" action=\"scripts/edit_business.php?id=$id\">
			<tr>
				<th><a id='edit-business-cancel' href=''>Cancel</a></th>
			</tr>
			<tr>
				<td>Name</td>
			</tr>
			<tr class='error' id='name_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"name\" id=\"name\" value=\"$name\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>Tag 1</td>
			</tr>
			<tr class='error' id='tag_1_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"tag_1\" id=\"tag_1\" value=\"$tag_1\" maxlength=\"100\"></td>
			</tr>
			<tr>
				<td>Tag 2</td>
			</tr>
			<tr class='error' id='tag_2_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"tag_2\" id=\"tag_2\" value=\"$tag_2\" maxlength=\"100\"></td>
			</tr>
			<tr>
				<td>Address</td>
			</tr>
			<tr class='error' id='address_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"address\" id=\"address\" value=\"$address\" maxlength=\"255\"></td>
			</tr>
		    <tr>
				<td>City</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"city\" id=\"city\" value=\"$city\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>State</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"state\" id=\"state\" value=\"$state\" maxlength=\"2\"></td>
			</tr>
			<tr>
				<td>Zip</td>
			</tr>
			<tr class='error' id='zip_error'>
				<td>This field is required.</td>  
			</tr>
			<tr>
				<td><input type=\"text\" name=\"zip\" id=\"zip\" value=\"$zip\" maxlength=\"5\"></td>
			</tr>
			<tr>
				<td>Number</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"number\" id=\"number\" value=\"$number\" maxlength=\"12\"></td>
			</tr>
		    <tr>
				<td>URL</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"url\" id=\"url\" value=\"$url\" maxlength=\"50\"></td>
			</tr>
			<tr>
				<td>Hours</td>
			</tr>
			<tr>
				<td><input type=\"text\" name=\"hours\" id=\"hours\" value=\"$hours\" maxlength=\"100\"></td>
			</tr>
			<tr>
				<td>Logo</td>
			</tr>
			<tr>
				<td>
				<input type=\"file\" name=\"logo_upload\" id=\"logo_upload\" size=\"10\">
				</td>
			</tr>
			<tr>
				<td style=\"border-bottom: solid 1px black;\">
				Note: filesize must be <60Kb
				</td>
			</tr>
			<tr>
		      	<td align=\"right\"><input type=\"submit\" id='edit-business-submit' name=\"submit\" value=\"Upload\"></td>
			</tr>
			</form>
		</table>
	</div>";
}

function print_formatted_modal($post, $business)
  {
	extract($post);

   	$tags[1] = get_tag($tag_1); 
   	$tags[2] = get_tag($tag_2); 
   	$tags[3] = get_tag($tag_3);

	if($post['alt_address'] == "")
      {
		$alt_address = $business['address']." ".$business['city'].", ".$business['state'].", ".$business['zip'];
	  }
	
	echo "
			<div id='post-$id-modal' class='modal hide fade' tabindex='-1' role='dialog' aria-labelledby='post-$id-modal-label' aria-hidden='true'>
				<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
						<h3 id='post-$id-modal-label'>".$post['title']."</h3>
				</div>
				<div class='modal-body'>
					<div class='row-fluid'>
						<div class='span7 top-left' id='post'>
							<div class='row'>
								<div class='span4 centered'>
									<a href='index?tag=$tag_1'>$tags[1]</a>
								</div>
								<div class='span4 centered'>
									<a href='index?tag=$tag_2'>$tags[2]</a>
								</div>
								<div class='span4 centered'>
									<a href='index?tag=$tag_3'>$tags[3]</a>
								</div>
							</div>
							<div id='posting-border$id' class='posting-border'>
								<div id='posting-title$id' class='posting-title'>
									$title";
	if($date != "")
	  {
	    echo " on $date";
	  }
	echo "
								</div>
								<div class='posting-time$id'>";
   	print_formatted_time($posting_time);
	echo "
								</div>
								<div id='posting-data$id' class='posting-data'>";

	if($photo != "")
	  {
		echo "
									<img src='images/posts/$photo' alt='photo for $title' class='posting-image'>";

	  }

	echo "					
									<div id='posting-blurb$id' class='posting-blurb'>
										$blurb
									</div>";
	if($url != "")
	  {
	    echo "
									<div id='posting-purchase$id' class='posting-purchase'>
										<a href='http://$url'><img src='images/purchase.png'></a>
									</div>";
	  }
	echo "
								</div>
							</div>
				 		</div>
						<div class='span5'>
							<div class='centered content'>
				   			<a href='http://maps.google.com/?q=$alt_address'>
			   				<img src='http://maps.googleapis.com/maps/api/staticmap?center=$alt_address&zoom=16&size=325x250&markers=color:red|$alt_address&sensor=false' class='rounded'>
				   			</a>
							</div>";
	extract($business);
	$category = get_tag($category);
	echo "
							<div class='business-info bottom-right'>
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
							<div class='row'>
							<div class='span6'>
								Hours:<br>";
	$hours = explode(",", $hours);
	foreach($hours as $line)
	{
		echo "
								$line<br>";
	}
	echo "
							</div>
							<div class='span6'>
								<address>
								<a id=\"business-address\" href=\"http://maps.google.com/?q=$address, $city, $state $zip\">
								$address<br>
								$city, $state, $zip<br>
								</a>
								P: $number<br>
								</address>
							</div>
						</div>
						<h4 style='text-align:center'>(<a href='business#$category'>$category</a>)</h4><br>
						</div>
   					</div>
				</div>
		   	</div>
			<div class='modal-footer'>
				<button class='btn' data-dismiss='modal' aria-hidden='true'>Close</button>
			</div>
		</div>";
}

function print_formatted_post($post, $modal="")
{
	extract($post);

	$date = format_date($id);

   	$tags[1] = get_tag($tag_1); 
   	$tags[2] = get_tag($tag_2); 
   	$tags[3] = get_tag($tag_3);


   	echo "
			<div id='posting-border$id' class='posting-border'>
				<div id='posting-title$id' class='posting-title'>
					$title";
	if($date != "")
	  {
	    echo " on $date";
	  }
	echo "
				</div>
				<div class='posting-time$id'>";
		print_formatted_time($posting_time);
		echo "</div>
				<div id='posting-data$id' class='posting-data'>
					<img src='images/posts/$photo' alt='photo for $title' class='posting-image'>
					
					<div id='posting-blurb$id' class='posting-blurb'>
						$blurb
					
					</div>";
		if($url != "")
		  {
		    echo "
<div id='posting-purchase$id' class='posting-purchase'>
<a href='http://$url'><img src='images/purchase.png'></a>
</div>";
		  }
echo "
				</div>
		<div id='static-map'>
			<a href='http://maps.google.com/?q=$alt_address'>
				<img src='http://maps.googleapis.com/maps/api/staticmap?center=$alt_address&zoom=16&size=275x400&markers=color:red|$alt_address&sensor=false'>
			</a>
	    </div>
				<div class='posting-tags'>
				<ul>
						<li><a href='index?tag=$tag_1'>$tags[1]</a></li>
						<li><a href='index?tag=$tag_2'>$tags[2]</a></li>
						<li><a href='index?tag=$tag_3'>$tags[3]</a></li>
					</ul>
				</div>
			</div>";
}

function print_new_user_form($businesses = "")
{
  $new_user_args = "";
  if($businesses != "")
	{
	  $new_user_args = "?admin=1";
	  $businesses_or_captcha  = "
			<div class='control-group'>
				<label class='control-label' for='business'>
					Business *
				</label>
				<div class='controls'>
					<select required name='business' id='business'>
						<option selected='selected'></option>";
	
	  foreach($businesses as $id=>$name)
		{
		  $businesses_or_captcha .= "
						<option value='$id'>$name</option>";
        }
	
	  $businesses_or_captcha .= "
					</select>
				</div>
			</div>";
	}
  else
    {
	$businesses_or_captcha = "
			<legend>";
	require_once('includes/recaptchalib.php');
	$publickey = "6LchVNESAAAAAMenf3lTWgj00YzeyK-hRKS_bozg";
	$captcha = recaptcha_get_html($publickey);
	$businesses_or_captcha .= $captcha."
			</legend>";
    }
	echo "
		<form name='new-user-form' action='scripts/new_user.php".$new_user_args."' method='post' class='form-horizontal'>
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
			</div>";
	if($businesses == "")
	  {
		echo "
			<div class='control-group'>
				<label class='control-label' for='name'>
					Business Name *
				</label>
				<div class='controls'>
					<input required type='text' maxlength=100 name='name' id='name' placeholder='Name...' class='input-medium'>
				</div>
			</div>";
	  }
	echo "
			<div class='form-actions'>
				<button type='submit' class='btn btn-primary' id='submit' name='submit'>Submit</button>	
			</div>
			</fieldset>
		</form>
	</div>";
}

function print_add_post_form($businesses = "")
{
  $new_post_args = "";
  $business_select_box = "";
  if($businesses != "")
	{
	  $new_post_args = "?admin=1";
	  $business_select_box = "
			<div class='control-group'>
				<label class='control-label' for='business'>
					Business *
				</label>
				<div class='controls'>
					<select required name='business' id='business'>
						<option selected='selected'></option>";
	
	  foreach($businesses as $id=>$name)
		{
		  $business_select_box .= "
						<option value='$id'>$name</option>";
        }
	
	  $business_select_box .= "
					</select>
				</div>
			</div>";
	}

echo "
		<form name='new-post-form'  enctype='multipart/form-data' action='scripts/new_post.php".$new_post_args."' method='post' class='form-horizontal'>
			<fieldset>".$business_select_box."
			<div class='control-group'>
				<label class='control-label' for='title'>
					Title *
				</label>
				<div class='controls'>
					<input type='text' maxlength=50 name='title' id='title' placeholder='Insert title here...' class='input-xlarge'>
					<span class='error help-inline' id='title-error'>
						This field is required.	
					</span>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='date'>Date</label>
				<div class='controls'>
					<input type='text' name='date' id='date' placeholder='Click to add date...' class='input-medium'>
				</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='address'>Address</label>
					<div class='controls'>
						<input type='text' maxlength=250 name='address' id='address' placeholder='Insert address of event here...' class='input-xlarge'>
					</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='url'>Purchase URL</label>
					<div class='controls'>
					    <input type='text' maxlength=250 name='url' id='url' placeholder='Do not include \"http://\"' class='input-large'>
					</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='description'>
					Description * 
					<div class='error' id='desc_error'>
						This field is required.
					</div>
				</label>
					<div class='controls'>
					    <textarea name='description' rows=5 maxlength=255 placeholder='Write a description here in less than 250 characters' class='input-xlarge'></textarea>
					</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='tag1'>
					Tags * 
					<div class='error' id='tag_error'>
						This field is required.
					</div>
				</label>
					<div class='controls-row'>
					    <input required type='text' name='tag1' id='tag1' placeholder='Tag 1' class='span2'>
						<input required type='text' name='tag2' id='tag2' placeholder='Tag 2' class='span2'>
						<input required type='text' name='tag3' id='tag3' placeholder='Tag 3' class='span2'>
					</div>
			</div>

			<div class='control-group'>
				<label class='control-label' for='image_upload'>Image</label>
					<div class='controls'>
						<input type='file' name='image_upload' id='image_upload' class='input-xlarge'>
					</div>
			</div>

			<div class='form-actions'>				
				<button type='button' class='btn' id='add-cancel-button'>Cancel</button>
				<button type='submit' class='btn btn-primary' id='add-submit'>Submit</button>
			</div>
			</fieldset>
			</form>";
}

function print_edit_post_form($post, $div_id="")
{
extract($post);
$tag1 = get_tag($tag_1);
$tag2 = get_tag($tag_2);
$tag3 = get_tag($tag_3);

echo "
	<div id=\"edit-posting-form\">	

		<form id='edit-post-form' name='edit-post-form' enctype='multipart/form-data' action='scripts/edit_post.php?p_id=$id' method='post'>
		<table>	  
				<tr>
					<td>Title</td>
					<td>:</td>
					<td colspan=4><input type=\"text\"  size=40 maxlength=50 name=\"title\" id=\"edit-title\" value=\"$title\"></td>
				</tr>
				<tr class='error' id='title_error'>
				<td></td><td></td>
				<td>This field is required.</td>  
				</tr>
<tr>
					<td>Date</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=50 name=\"date\" id=\"edit-date\" value=\"$date\"></td>
				</tr>
<tr>
					<td>Address</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=250 name=\"address\" id=\"edit-address\" value=\"$alt_address\"></td>
<tr>
					<td>Purchase URL</td>
					<td>:</td>
					<td colspan=3><input type=\"text\" size=40 maxlength=100 name=\"url\" id=\"edit-url\" value=\"$url\"></td>
				</tr>

				<tr>
					<td>Description</td>
					<td>:</td>
					<td colspan=4><textarea name=\"description\" id='edit-description' cols=50 rows=5>$blurb</textarea></td>
				</tr>
				<tr class='error' id='desc_error'>
				<td></td><td></td>
				<td>This field is required.</td>  
				</tr>
				<tr>
					<td>Tags</td>
					<td>:</td>
					<td><input type=\"text\" size=8 name=\"tag1\" id=\"edit-tag1\" value=\"$tag1\"></td>
					<td><input type=\"text\" size=8 name=\"tag2\" id=\"edit-tag2\" value=\"$tag2\"></td>
					<td><input type=\"text\" size=8 name=\"tag3\" id=\"edit-tag3\" value=\"$tag3\"></td>
				</tr>
				<tr class='error' id='tag_error'>
				<td></td><td></td>
				<td>This field is required.</td>  
				</tr>";
/*
 				<tr>
					<td>Publish Date</td>
					<td>:</td>
					<td><input type=\"text\" name=\"publish_date\" id=\"publish_date\" value=\"$publish_date\"></td>
					<td>Publish Time</td>
					<td>:</td>
					<td><input type=\"time\" name=\"publish_time\" id=\"publish_time\" value=\"$publish_time\"></td>
				</tr>
*/
echo "
			<tr>
				<td>Image</td>
				<td>:</td>
				<td colspan=4>
				<input type=\"file\" name=\"image_upload\" id=\"image_upload\">
				</td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td colspan=4 style=\"border-bottom: solid 1px black;\">
				Note: filesize must be less than 240Kb
				</td>
			</tr>
			<tr>
				<td><input type='button' id='edit-cancel-button' name='cancel' value='Cancel'></td>
				<td></td>
		      		<td colspan=4 align=\"right\"><input type=\"submit\" id='edit-submit' name=\"submit\" value=\"Submit\"></td>
			</tr>
			</table>
			</form>
	</div>";
}

function print_mini_post($post, $div_id="")
{
	extract($post);
	$query = "SELECT name, tag_1, tag_2 FROM business WHERE id='$b_id'";
   	$result = query_db($query);
   	$business_result = mysql_fetch_array($result);

	$date = format_date($id);

   	$name = $business_result['name'];

   	$tags[1] = get_tag($tag_1); 
   	$tags[2] = get_tag($tag_2); 
   	$tags[3] = get_tag($tag_3);

   	echo "
			
			<div id=\"posting-border$div_id\" class=\"posting-border\">
				<div id=\"posting-content$div_id\" class=\"posting-content\">
				<div id=\"posting-title$div_id\" class=\"posting-title\">
					<a href=\"business?b_id=$b_id\">$title from $name";
	if($date != "")
	  {
	    echo " on $date";
	  }
	echo "</a>
				</div>";
	
		echo substr($blurb, 0, 100);
		echo "...
				</div>
				<div id=\"posting-meta$div_id\" class=\"posting-meta\">
				<div id=\"posting-time$div_id\" class=\"posting-time\">";
		print_formatted_time($posting_time);
		echo "</div>
					<ul>
						<li><a href=\"index?tag=$tag_1\">$tags[1]</a></li>
						<li><a href=\"index?tag=$tag_2\">$tags[2]</a></li>
						<li><a href=\"index?tag=$tag_3\">$tags[3]</a></li>
					</ul>";
	if(isset($GLOBALS['m_id']))
	{
		if($a_id == $GLOBALS['m_id'])
		{
			echo "
		<div id=\"posting-delete$div_id\" class=\"posting-delete\">
		<a href=\"scripts/delete_post.php?p_id=$id\">Delete</a>
		</div>";
		}
	}
	echo "
				</div>
				</div>
			</div>";
}

function print_old_post($post, $div_id="")
{
	extract($post);

	$date = format_date($id);

   	echo "
			<h3>$title posted @ ";
	print_formatted_time($posting_time);
	echo "</h3>
			<div id=\"posting-border$div_id\" class=\"posting-border\">
				<div id=\"posting-content$div_id\" class=\"posting-content\">
				<div id=\"posting-title$div_id\" class=\"posting-title\">
					$title";
	if($date != "")
	  {
	    echo " on $date";
	  }
	echo "
				</div>";
	
		echo substr($blurb, 0, 100);
		echo "...
				</div>
				<div id=\"posting-meta$div_id\" class=\"posting-meta\">
				<div id=\"posting-time$div_id\" class=\"posting-time\">";
		print_formatted_time($posting_time);
		echo "</div>
				<a id='make-current$div_id' href=''>Make current post</a>
				</div>
			</div>";
}

?>
