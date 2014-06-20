<?php

function obfuscate($ctid) {
    $code=base64_encode($ctid);
    $code=str_rot13($code);
    $code=urlencode($code);
    return $code;
}
 
if ($_SERVER['COUNTRY'] == 'IL') $postoffer=0;
else $postoffer = 1; /// EDIT THIS LINE

if(isset($_GET['CTID'])) {
    $id = $_GET['CTID'];
    $id = obfuscate($id);
    $offerURL = 'offers.php?papa='.$id;
}
else {
    $offerURL = 'offers.php?tb=Translator';
}

$random_flag = mt_rand(1, 1000);
$offerURL = "{$offerURL}&_fw={$random_flag}";

$adpixel_code = "";
if (isset($_COOKIE['adym']) && $_COOKIE['adym']) {
    $adpixel_code = $_COOKIE['adym'];
    setcookie('adym', false, time()-3600, '/', '.pdfssoftware.com', false, true);
}

if (!empty($_COOKIE['iflp'])) {
    $coming_from_lp = true;
    setcookie("iflp", false, time()+3600, '/', ".pdfssoftware.com", false, true);
}
else {
    $coming_from_lp = false;
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Installed!</title>

<?php if ($postoffer): ?>
    <script type="text/javascript">
    <!--
    function delayer(){
        window.location = "<?php echo $offerURL; ?>"
    }
    //-->
    </script>
<?php endif; ?>

<style type="text/css">
<!--
.verdana {
    font-family: Verdana, Geneva, sans-serif;
}
<?php if ($coming_from_lp) { ?>  body { background-color: #f0fff0; } <?php } ?>
-->
</style>
<script type="text/javascript"> var _gaq = _gaq || []; _gaq.push(['_setAccount', 'UA-33646715-1']); _gaq.push(['_trackPageview']); (function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })(); </script>
</head>

<body <?php if ($postoffer) { ?>  onLoad="setTimeout('delayer()', 1500)" <?php } ?>>
<img src="http://ad.yieldmanager.com/pixel?id=2294456&t=2&context=<?=$adpixel_code?>" width="1" height="1" />
<img src="http://ad.yieldmanager.com/pixel?id=2330172&t=2&context=<?=$adpixel_code?>" width="1" height="1" />
<img src="http://ad.yieldmanager.com/pixel?id=2146735&t=2" width="1" height="1" />

<h2><span class="verdana">Successfully Installed!</span></h2>
<h2 class="verdana">&nbsp;</h2>
<?php if ($postoffer) { ?> <h2 class="verdana">Please wait while loading a special offer for our users...</h2> <?php } ?>


<script type="text/javascript">
var fb_param = {};
fb_param.pixel_id = '6005627656938';
fb_param.value = '0.00';
(function(){
  var fpw = document.createElement('script');
  fpw.async = true;
  fpw.src = (location.protocol=='http:'?'http':'https')+'://connect.facebook.net/en_US/fp.js';
  var ref = document.getElementsByTagName('script')[0];
  ref.parentNode.insertBefore(fpw, ref);
})();
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/offsite_event.php?id=6005627656938&amp;value=0" /></noscript>
<?php
    include('download/includes/determine_browser.php');
    include('/var/www/vhosts/common/include/pinba/pinba_timer.php');
?>
<img src="http://ads.bluelithium.com/pixel?id=2373456&t=2" width="1" height="1" />
<img src="/gba/gbacnv.php?t=conduit&tr=<?php echo urlencode(basename(__FILE__,'.php')); ?>" width="1" height="1" /></body>
</html>
