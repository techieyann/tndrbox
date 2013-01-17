<?php
/***********************************************
file: business.php
creator: Ian McEachern

This file outputs the active posting for a given
business and the relevant business data. Or if
no business is selected shows a list of 
businesses.
 ***********************************************/
require('includes/includes.php');
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body

	$db_query = "SELECT name, id, logo, category FROM business WHERE active_post=1 ORDER BY category, name";
	$result = query_db($db_query);
	$i=0;
	foreach($result as $business)
	  {
		$cat = $business['category'];
		$businesses_result[$cat][$i++] = $business;
  	  }
	
	$categories = get_active_categories();


//head


$GLOBALS['header_html_title'] = "tndrbox - Businesses";


$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "business";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  global $businesses_result, $categories;
	

		echo "
			<div class='row-fluid white-bg'>
				<div class='span3 bs-docs-sidebar'>
					<ul class='nav nav-pills nav-stacked bs-docs-sidelist'>";
		$count=0;
		foreach($categories as $category)
		  {
			extract($category);
			echo "
						<li><h3><a href='#$tag'>$tag</a></h3></li>";
			$count--;
		  }
		echo "
					</ul>
				</div>
				<div class='span9'>";
		foreach($businesses_result as $category_id => $business_by_category)
		  {
			$category = get_tag($category_id);
			echo "<h3 id='$category'>$category</h3>";
			foreach($business_by_category as $business)
		{
			extract($business);	
			if($count == 0)
			{
				echo "
	<div class='row-fluid'>";
			}
				
				echo "
		<div>
			<a href='index?b=$id' title='$name'>";
			if($logo == "")
			{
				echo "
				<div class='bus_button'>
					<span>".$name."</span>
		     	</div>";
			}
		    else
			{
				echo "
				<img src='images/logos/$logo' alt='$name' border='0' max-width='100%'>";
			}
			echo "
			</a>
		</div>";
			if(++$count == 4)
			{
		  		$count = 0;
	   			echo "
		</div>";
			} 	
		}
		}

	echo "	
			</div>
		</div>
		</div>";
}
?>