<?php
/***********************************************
file: .php
creator: Ian McEachern

About this file
 ***********************************************/
require('includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
//verify_logged_in();

//set variables
//body
$id = $GLOBALS['b_id'];

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
	<div id=\"info\" class =\"content-pane\">
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
				       <p>Find the beers you love at Oakland's finest locations any way you choose with information straight from the source.
				       Sort by bar. Sort by style. Sort by price. Set phasers to awesome.</p>
				       <p>We're organizing our local beer community by connecting local producers, 
				       providers and consumers via an interactive, information-based hub.</p>
				       <p>We are a living fabric of people, cultures, neighborhood haunts, regulars, 
				       hobbyists, male underwear models, cute girls, and you.</p>
				       <p>OK, so maybe we were exaggerating about that last part.</p>
       	     	     		  </div>
       	     	     		  <div id=\"why\" class=\"questions\">
       	     	     		       <h3>Why tndrbox?</h3>
			       	       <p>Around the corner, your favorite beer just came in on tap. 
				       Where did your friend say you were going out tonight again? 
				       You have an itch that only an Imperial Stout can scratch. 
				       A fever, and the only cure is more Pilsner. You love IPAs. She loves Belgians. 
				       Where the hell do you go now?</p> 
				       <p>Hell, maybe you want to try something new. A new place, a new style.
				       Something wonderful. Something beautiful and incredible that reaffirms everything you know and hope to learn and quaff.</p>
				       <p>Because inspiration is waiting, my friend. And it wants to find you.</p>
				  </div>
				  <div id=\"how\" class=\"questions\">
				       <h3>How tndrbox?</h3>
			       	       <p>On your laptop or phone. Find your favorite brewery, beer, or style and draw the perfect pub crawl on the go or at home.</p>
				  </div>
				  <div id=\"who\" class=\"questions\">
				       <h3>Who tndrbox?</h3>
				       <p>We are two Oakland natives, fledgling entrepreneurs, amateur homebrewers, tried-and-true Banana Slugs, 
				       all-around rapscallions, critical-thinking businessmen, moonlighting superheroes, and friends since Middle School.</p>
				  </div>
				  <div id=\"when\" class=\"questions\">
				       <h3>When tndrbox?</h3>
				       <p>We are currently undergoing ALPHA development and testing with a select user base (that's you).</p>
				  </div>
				  <div id=\"where\" class=\"questions\">
				       <h3>Where tndrbox?</h3>
				       <p>We are currently based in Oakland, connecting Bay Area communities. Hold on baby, we're coming.</p>
				  </div>
				  <div class=\"questions\" style=\"border-top:solid black 3px;\">
				       <h2 style=\"text-align:center;\">F.A.Q.</h2>
				       <h3>What's your name again?</h3>
				       <p>We're tndrbox, pronounced tinderbox. Nice to meet you.</p>
				       <h3> How much does it cost?</h3>
				       <p>Nothing! It is free for both bars and users.</p>
				       <h3>Which bars are updating their beers?</h3>
                                       <p>Only the best. Find out <a href=\"bars\">here</a> or click on \"Bars\" up there to find out.</p>
                                       <h3>Are you making an app?</h3>
				       <p>Hold your horses city-boy! A mobile website is in the works, and an app is not far behind.</p>
				       <h3>When are you releasing more features?</h3>
                                       <p>While we don't have a public schedule available, <a href=\"changelog.txt\">new features</a> are rolling out almost daily!</p>
                                       <h3>Why wasn't my question listed?</h3>
				       <p>Because no one asked us yet!
				       Feel free to <a href=\"#contact\">contact us</a> with your query.<br>
                                       Don't worry, we're very nice.</p>
				  </div>
			     </div>
	</div>";
}
?>