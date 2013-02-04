<?php
/***********************************************
file: about.php
creator: Ian McEachern

About this file
 ***********************************************/
require('includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body

//head
$GLOBALS['header_html_title'] = "tndrbox - About";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "about";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
  echo "
	<div id=\"info\" class='content rounded'>
	<div id=\"about\">
			     	  <div id=\"email\">
			     	  <table><tr><td>
			     	  <div rel=\"FXZJLOUJ\" class=\"lrdiscoverwidget\" data-logo=\"off\" data-background=\"off\" data-share-url=\"tndrbox.com/about\" data-css=\"\"></div><script type=\"text/javascript\" src=\"http://launchrock-ignition.s3.amazonaws.com/ignition.1.1.js\"></script>
			     	  </td><td>
				  <div id=\"contact\">
				       <h2 style=\"text-align:center;padding:20px;\">Contact Us</h2>
				       <p>Interested in getting your establishment on our site? 
				       Questions about functionality? Want to sing our praises? 
				       Even if you just had a bad day and need to talk,</p> 
				       <br><br><table>
					<tr>
						<td>send us an email:</td>
				       		<td><strong><a href=\"mailto:tndrbox@gmail.com\">tndrbox@gmail.com</a></strong></td>
				       	</tr>
				       </table>
				  </div>
				  </td></tr></table>
				  </div>
				  <h2 style=\"text-align:center;padding:20px;\">About</h2>
				  <div id=\"what\" class=\"questions\">
				       <h3>What tndrbox?</h3>
					   <p>tndrbox is a realtime, grassroots newspaper. A groundswell momentum generator. A why-isn't-somebody-doing-that idea broadcaster. A local flavor combinator. A how-many-of-this-do-I-order, direct-to-your-customers landline. A speak common sense to power megaphone. A grassroots cause ignition system. A community incubator. A make the world a better place machine. </p>
       	     	  </div>
       	     	  <div id=\"why\" class=\"questions\">
       	     	       <h3>Why tndrbox?</h3>
					   <p>Because broadcasting witticisms or what beer you're drinking right now is useless. Because the roots of the city around you go deeper than anyone can imagine and what's down there is the lifeblood of your community. Because you want to know what is happening in your city and what you can do about it, with it, for it.</p>
					   <p>You want to participate, not consume. You want to be present, not show up. You want to be a part of something, not in attendance.</p>
					   <p>So do we. Welcome.</p>
				  </div>
				  <div id=\"how\" class=\"questions\">
				       <h3>How tndrbox?</h3>
			       	       <p>On your computer or phone. Find out what's happening when you're out and about or at home.</p>
				  </div>
				  <div id=\"who\" class=\"questions\">
				       <h3>Who tndrbox?</h3>
				       <p>We are Oakland natives who love our town. We're building the site that we need to connect to the community around us - a site that does what we love.</p>
					   <p>We're tired of social media blasts that mean nothing, hip-as-shit city guides that ask us to spend our paycheck on fashionable frivolity and mass coupons that undercut small businesses. We want to grow with our community as a part of it, helping it grow not just financially, but stronger and more vibrant as the fabric that makes up our city.</p>
				  </div>
				  <div id=\"when\" class=\"questions\">
				       <h3>When tndrbox?</h3>
				       <p>We are currently undergoing beta development and testing with a select user base (that's you). If you want to get in on the fun, please email us.</p>
				  </div>
				  <div id=\"where\" class=\"questions\">
				       <h3>Where tndrbox?</h3>
				       <p>We are based in Oakland, CA. Hold on baby, we're coming.</p>
				  </div>
				  <div class=\"questions\" style=\"border-top:solid black 3px;\">
				       <h2 style=\"text-align:center;\">F.A.Q.</h2>
				       <h3>What's your name again?</h3>
				       <p>We're tndrbox, pronounced tinderbox. Nice to meet you.</p>
				       <h3> How much does it cost?</h3>
				       <p>Nothing! It is free.</p>
				       <h3>So how are you going to make money?</h3>
                       <p>We're just focused on building the thing. But since you asked: we keep our costs low with a DIY mentality and don't need that much to operate. Profitability comes from doing something great, and doing something great is a product of love. We love what we do.</p>
					   <p>We can promise you this: you are not the product. Ever.</p>
                       <h3>Are you making an app?</h3>
				       <p>Hold your horses city-boy! A mobile website is in the works, and an app is not far behind.</p>
				       <h3>When are you releasing more features?</h3>
                       <p>Right now, we're focused on the foundation - having good content and good relations with our community. We don't have a public schedule available, but if you don't see something you'd like, pleae  <a href=\"#contact\">email us!</a></p>
                       <h3>Why wasn't my question listed?</h3>
				       <p>Because no one asked us yet!
				       Feel free to <a href=\"#contact\">contact us</a> with your query.<br>
                                       Don't worry, we're very nice.</p>
				  </div>
			     </div>
	</div>";
}
?>