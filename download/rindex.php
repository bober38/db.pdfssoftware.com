<?php
include_once '../gba/.lp.php';
$country = $_SERVER['COUNTRY'];
include('includes/determine_browser.php');
//include('/var/www/vhosts/common/include/pinba/pinba_timer.php');
$downloadURL = '/d/download.php';

$tbType = 'conduit';



include_once 'includes/route.php';

$route = new Route(basename(__FILE__, '.php'));

$route->run();


?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Routes index</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/index20.css" type="text/css" />
</head>
<body>
<div class="content">
    <h3>Routes index default</h3>
	<PRE><?php print_r($route->install); ?></PRE>
	<p><a href="<?= $route->download_url() ?>">Download</a></p>
</div>
</body>
</html>
