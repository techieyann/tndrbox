<?php
/***********************************************
file: home.php
creator: Ian McEachern

This file is the default page for logged in
users. It displays the user's business info and
the five most current postings. 
Redirects to index.php if user is not
logged in.
 ***********************************************/


require('includes/includes.php');
require('includes/db_interface.php');
require('includes/tags.php');

connect_to_db($mysql_user, $mysql_pass, $mysql_db);

analyze_user();
verify_logged_in();

//head
$GLOBALS['header_html_title'] = "tndrbox - ";
$GLOBALS['header_scripts'] = "";
$GLOBALS['header_title'] = "";
$GLOBALS['header_body_includes'] = "";
$GLOBALS['header_selected_page'] = "";

require('includes/prints.php');

print_head();
print_body();
print_foot();

disconnect_from_db();

function print_body()
{
        echo "
        <div id=\"\" class =\"content-pane\">";

        //print business info
        $b_id = $GLOBALS['b_id'];
        $query = "SELECT * FROM business where id=$b_id";
        $result = query_db($query);
        if(mysql_num_rows($result)==1)
        {
          $business = mysql_fetch_array($result);
                extract($business);
                $tag1 = get_tag($tag_1);
                $tag2 = get_tag($tag_2);
                echo "
                <div id=\"business_info\">
                        <a href=\"/edit-business.php?name=$name&tag_1=$tag1&tag_2=$tag2&address=$address&city=$city&state=$state&zip=$zip&number=$number&url=$url&hours=$hours\">Edit</a>
                      <table width=\"95%\"><tr>
                             <td><h2>";
                if($url != "")
                {
                        echo "<a href=\"http://$url\">";
                        if($logo != "")
                        {
                                echo "<img src=\"images/$logo\" width=\"300\" title=\"$name\" alt=\"$name\">";
                        }
                        else
                        {
                                echo $name;
                        }
                        echo "</a>";
                }
                else
                {
                        if($logo != "")
                        {
                                echo "<img src=\"images/$logo\" width=\"300\" title=\"$name\" alt=\"$name\">";
                        }
                        else
                        {
                                echo $name;
                        }
                }
                echo "</h2>
                     <br>
                     <a href=\"tags?tag=$tag_1\">$tag1</a>
                     <a href=\"tags?tag=$tag_2\">$tag2</a>
                        <br><h3>$address<br>";
                echo "
                        $city, $state, $zip<br><br>";
                echo "
                        $number</h3><br>";
                $hours = explode(",", $hours);
                foreach($hours as $line)
                {
                        echo "
                        $line<br>";
                }
                echo "
                        </td>
                        <td style=\"text-align:right\">";
                if($lat == "" || $lat == 0 ||  $lon == "" || $lon = 0)
                {
                        echo "
                        <img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$address $city $state $zip&zoom=16&size=300x400&markers=color:red|$address $city $state $zip&sensor=false\">";
                }
                else
                {
                        echo "
                        <img src=\"http://maps.googleapis.com/maps/api/staticmap?center=$lat,$lon&zoom=16&size=300x400&markers=color:red|$lat,$lon&sensor=false\">";    
                }
                echo "
                        </td>
                        </tr></table>
                        </div>
                </div>";
        }

        echo "<h3 id=\"tagline-left\">Add a <a href=\"/new-post\">New posting</a></h3><br><br>";
        //print business posting queue limit 5
        $query = "SELECT * FROM postings WHERE b_id=$b_id ORDER BY posting_time ASC LIMIT 5";
        $result = query_db($query);
        $i = 0;
        while($posting = mysql_fetch_array($result))
          {
                extract($posting);
                $query = "SELECT tag FROM tags WHERE id='$tag_1' OR id='$tag_2' OR id='$tag_3'";
                $tags_result = query_db($query);
                $j = 0;
                while($tag = mysql_fetch_array($tags_result))
                  {
                        if(++$j == 1)
                          {
                                $tags[$tag_1] = $tag['tag']; 
                          }
                        else if($j == 2)
                          {
                                $tags[$tag_2] = $tag['tag'];
                          }
                        else if($j == 3)
                          {
                                $tags[$tag_3] = $tag['tag'];
                          }
                  }
                echo "
                        <br>
                        <div id=\"posting_border_".$i++."\" class=\"content-pane\">
                                <div class=\"posting-$i-title\">$title</div>
                                <div class=\"posting-$i-time\">$posting_time</div>
                                <div class=\"posting-$i-edit\">
                                        <a href=\"/edit-posting.php?p_id=$id&title=$title&blurb=$blurb&photo=$photo&tag_1=$tag_1&tag_2=$tag_2&tag_3=$tag_3&posting_time=$posting_time\">Edit</a>
                                </div>
                                <div class=\"posting-$i-delete\">
                                        <a href=\"scripts/delete_post.php?p_id=$id\">Delete</a>
                                </div>
                                <div class=\"posting-$i-data\">
                                        <img src=\"$photo\" alt=\"photo for $title\" class=\"posting-image\">
                                        <div class=\"posting-$i-blurb\">
                                                $blurb
                                        </div>
                                        <ul>
                                                <li><a href=\"tags?tag=$tag_1\">$tags[$tag_1]</a></li>
                                                <li><a href=\"tags?tag=$tag_2\">$tags[$tag_2]</a></li>
                                                <li><a href=\"tags?tag=$tag_3\">$tags[$tag_3]</a></li>
                                        </ul>
                                </div>
                        </div>";
          }

                echo "
        </div>";
}
?>