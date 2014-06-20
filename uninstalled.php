<?php
$postURL = 'http://www.akaqa.com/';
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Toolbar Uninstalled</title>
<script type="text/javascript">
<!--
function postuninstall(){
    window.location = "<?php echo $postURL; ?>"
}
//-->
</script>


<style type="text/css">
<!--
.verdana {
    font-family: Verdana, Geneva, sans-serif;
}
-->
</style>



</head>

<body onLoad="setTimeout('postuninstall()', 10000)">
<h1><span class="verdana">Successfully Uninstalled.</span></h1>
<p class="verdana" style="font-size:14px;"><a href="reset-homepage.php" target="_blank">Resetting Homepage Instructions</a></p>
</body>
</html>
