<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>Resetting homepage</title>
	<link rel="stylesheet" href="/css/service_page.css">
	<script type="text/javascript" src="/js/jquery.js"></script>
	<script type="text/javascript" src="/js/jquery.ifixpng.js"></script>
	<script type="text/javascript">$(document).ready(function(){ $('.png').ifixpng(); });</script>
</head>

<body>
<div class="all">

	<div class="clear">&nbsp;</div>

	<div class="logo">
		<a href="/"><img src="/images/logo-pdfssoftware.png" class="png" alt="" /></a>
	</div>

	<div class="dashboard-wrapper">

		<div class="dashboard-top"></div>

		<div class="dashboard">

		<?php require( "/var/www/vhosts/common/include/service_pages/conduit_reset_homepage_html.inc" ); ?>

		</div>

		<div class="dashboard-bottom"></div>
	</div>

	<? $contactus_domain='pdfssoftware.com'; include('/var/www/vhosts/common/include/contactlib_addr.php'); ?>

	<div class="footer">
		<a href="#" onclick="show_cont();return false;">Contact Us</a>&nbsp;&nbsp;
		|&nbsp;&nbsp;<a href="/privacypolicy.php">Privacy Policy</a>
	</div>

	<div class="clear"></div>

</div>
</body>
</html>
