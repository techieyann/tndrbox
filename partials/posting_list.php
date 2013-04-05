<?php
/***********************************************
file: partials/posting_list.php
creator: Ian McEachern

This partial displays the postings on the front 
page in 
 ***********************************************/

require('../includes/includes.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();

echo "
<div id='map-canvas' data-step='3' data-intro='This is our map. It maps things.' data-position='left'>
</div><!-- #map-canvas -->

<div id='post'>
</div><!-- #post -->

";

disconnect_from_db();
?>