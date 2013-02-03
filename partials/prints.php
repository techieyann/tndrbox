<?php
/***********************************************
file: partials/prints.php
creator: Ian McEachern


 ***********************************************/

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


function print_edit_business_form($id="")
  {
	if($id=="")
	  {
		$b_id = $GLOBALS['b_id'];
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

echo "
		<form name='edit-business-form' enctype='multipart/form-data' action='scripts/edit_business$append_string' method='post'>
			<fieldset>

			<div class='row-fluid span12'>

			<div class='span6'>
			<label><strong>Required fields:</strong></label>

			<div class='control-group'>
				<label class='control-label' for='name'>
					Name *
				</label>
				<div class='controls'>
					<input required autofocus='true' type='text' maxlength=100 name='name' id='name' value='$name' placeholder='Type your busines name here...' class='span12'>
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
					Category *
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
				<label class='control-label' for='logo'>
					Logo (must be smaller than 2Mb)
				</label>
				<div class='controls'>
					<input type='file' name='logo_upload' id='logo_upload' class='span8'>
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
		$id = $GLOBALS['m_id'];
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
		<form name='new-user-form' action='scripts/edit_user$append_string' method='post' class='form-horizontal'>
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






function print_modal($id)
  {
	$query = "SELECT * FROM postings WHERE id=$id";
	$result = query_db($query);
	$post = $result[0];

	$b_id = $post['b_id'];

	if($post['active'] == 1)
	  {
		$active_flag = true;
	  }
	else
	  { 
		$active_flag = false;
	  }
	$owner_flag = false;
	if(isset($GLOBALS['m_id']))
	  {
		$m_id = $GLOBALS['m_id'];

		if($m_id == $post['a_id'] || check_admin())
		  {
			$owner_flag = true;
		  }
	  }

	extract($post);
	$p_id = $id;

if($id == null)
  {
	echo "		<script>
					
				</script>
				<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
					<h4 class='error'>Error: couldn't find post...</h4>
				</div>";
  }

else
  {
	$query = "UPDATE postings SET viewed = viewed + 1 WHERE id=$id";
	query_db($query);
	$query = "SELECT * FROM business WHERE id=$b_id";
	$result = query_db($query);
	$business = $result[0];



   	$tags[1] = get_tag($tag_1); 
   	$tags[2] = get_tag($tag_2); 
   	$tags[3] = get_tag($tag_3);

	if($post['alt_address'] == "")
      {
		$alt_address = $business['address']." ".$business['city'].", ".$business['state'].", ".$business['zip'];
	  }
	$date = format_date($id);
	
	echo "
			
				<div class='modal-header'>
					<button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>";
    if($owner_flag)
	  {
		echo "
					<ul class='inline pull-right'>
						<li><a class='post-link' title='Edit' href='edit_post' id='$id'><i class='icon-pencil'></i></a></li>
						<li><a class='post-link' title='Deactivate' href='deactivate_post' id=$b_id><i class='icon-ban-circle'></i></a></li>
						<li><a class='post-link' title='Delete' href='delete_post' id='$id'><i class='icon-trash'></i></a></li>						
					</ul>";
	  }

	echo "
						<h3 id='post-modal-label' class='centered'><strong>".($url!="" ? "<a href='http://$url'>$title</a>":"$title")."</strong>".($date != "" ? " <i>on $date</i>" :"")."</h3>
				</div>
				<div class='modal-body'>
					<div class='row-fluid'>
						<div class='span7 top-left' id='post'>
							<div class='row span12'>
								<div class='span12 centered'>";

	$map_used_flag = false;

	if($photo != "")
	  {
		echo "
									<img src='images/posts/$photo' alt='photo for $title' class='posting-image'>";

	  }
	else
	  {
		echo "
						   			<a href='http://maps.google.com/?q=$alt_address'>
						   				<img src='http://maps.googleapis.com/maps/api/staticmap?center=$alt_address&zoom=16&size=325x250&markers=color:red|$alt_address&sensor=false' class='rounded'>
						   			</a>";
		$map_used_flag = true;
	  }

	
	
	echo "
								</div>
							</div>
							<ul class='inline centered'>
								<li><a href='index?tag=$tag_1'>$tags[1]</a></li>
								<li><a href='index?tag=$tag_2'>$tags[2]</a></li>
								<li><a href='index?tag=$tag_3'>$tags[3]</a></li>
							</ul>

   							<div id='posting-blurb' class='posting-blurb content'>
								<strong>$blurb</strong>
							</div>

								<div class='row span11'>								
									<div class='posting-time pull-right muted'>
										<p>Posted at <strong>";
	print_formatted_time($posting_time);
	echo "</strong></p>
									</div>
								</div>

				 		</div>
						<div class='span5'>";

	if(!$map_used_flag)
	  {
		echo "
							<div class='centered content'>
				   			<a href='http://maps.google.com/?q=$alt_address'>
			   				<img src='http://maps.googleapis.com/maps/api/staticmap?center=$alt_address&zoom=16&size=325x250&markers=color:red|$alt_address&sensor=false' class='rounded'>
				   			</a>
							</div>";
	  }
	extract($business);
	$category_id = $category;
	$category = get_tag($category_id);
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
						<h4 style='text-align:center'>(<a href='?tag=$category_id'>$category</a>)</h4><br>
						</div>
   					</div>
				</div>
		   	</div>
			<div class='modal-footer'>

				<button id='share-button' class='share-button btn btn-info pull-left' title=\"
Select your method:
'<button type='button' class='close' onclick='$(&quot;#share-button&quot;).popover(&quot;hide&quot;);'>&times;</button>\" data-content=\"

<a href='https://twitter.com/share' class='twitter-share-button' data-url='http://tndrbox.com/?p=$p_id' data-text='$title at $name' data-count='none' data-dnt='true'>Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src='//platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document,'script','twitter-wjs');</script>


<iframe src='//www.facebook.com/plugins/like.php?href=http%3A%2F%2Ftndrbox.com%2F%3Fp%3D$p_id&amp;send=false&amp;layout=button_count&amp;width=450&amp;show_faces=false&amp;font=arial&amp;colorscheme=light&amp;action=recommend&amp;height=21' scrolling='no' frameborder='0' style='border:none; overflow:hidden; width:450px; height:21px;' allowTransparency='true'></iframe>
\"
				>Share</button>

				<button class='btn pull-right' data-dismiss='modal' aria-hidden='true'>Close</button>
			</div>";
  }
  }



function print_business_posts($id)
{
$m_id = $GLOBALS['m_id'];
$query = "SELECT id, title, b_id FROM postings WHERE a_id=$m_id AND b_id=$id AND active=1";
$active_posts = query_db($query);

$query = "SELECT id, title, b_id FROM postings WHERE a_id=$m_id AND b_id=$id AND active=0 ORDER BY b_id";
$old_posts = query_db($query);

foreach($active_posts as $active_post)
  {
	$id = $active_post['id'];
	$b_id = $active_post['b_id'];

	echo "
			<div class='span12 modal white-bg' style='position:relative; left:auto; right:auto; margin:0; max-width:100%;'>";
	print_modal($id);
	echo "
			</div>";
  }
echo "
		<br><br><table class='table table-hover'>
			<caption><h3>Archived Posts</h3></caption>
			<tbody>";
foreach($old_posts as $old_post)
  {
	extract($old_post);
	echo "
			<tr>
				<td>
				<h4>$title</h4>
				</td>
				<td>
		   			<ul class='inline pull-right'> 
						<li><a class='post-link' title='Activate' href='edit_post' id='$id'><i class='icon-fire large'></i></a></li>
						<li><a class='post-link'title='Delete' href='delete_post' id='$id'><i class='icon-trash'></i></a></li>
					</ul>
				</td>
			</tr>";
  }
echo "
			</tbody>
		</table>";
}

?>
