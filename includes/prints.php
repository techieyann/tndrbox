<?php

function print_head()
{
	echo "
<!--DOCTYPE HTML-->

<html>

<head>
</head>

<body>";

}

function print_foot()
{
  global $version;
  echo "

<footer>version $version</footer>

</body>

</html>";
}

?>
