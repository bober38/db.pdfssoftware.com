<?php
include '../gba/.lp.php';
$country = $_SERVER['COUNTRY'];
include('includes/determine_browser.php');
include('/var/www/vhosts/common/include/pinba/pinba_timer.php');

if ($browser == "Firefox") {
	//var_dump("Chrome");
	Goldbar::include_lp("cindex_test.php", "Browser:Chrome");
}
if ($country == "DE") {
	Goldbar::include_lp("index_de_test.php", "Country:DE");
}
var_dump("FF");


$downloadURL = '/d/download.php';

if (isset($_GET['trackid']) && $_GET['trackid']) {
    setcookie('adym', $_GET['trackid'], time()+3600*24, '/', '.pdfssoftware.com', false, true); // keep for 24h
}
$tbType = 'conduit';
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>PDF Converter2</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" href="css/index20.css" type="text/css" />
    <script type="text/javascript">
        <?php $btn = 'download-green-7.png';
        include('includes/button_on_js.php'); ?>
    </script>

</head>
<body>
<?= lp::get_gbahit(isset($cmp)?$cmp:false, isset($marker)?$marker:false, isset($tbType)?$tbType:false)?>
<div class="content">
    <h3>PDF Converter</h3>
    <p class="top_paragraph"><span class="text">A free PDF Converter supporting all popular document formats.
      The PdfsSoftware PDF Converter toolbar is easy to use, simple &amp; fast. Within a few seconds you can retrieve the converted PDF or Image. 
      Supported formats: DOC, DOCX, HTML, XLS, JPG, TIFF, BMP, and more.</span></p>
    <div class="white_back">
      <div class="column">
			<table class="steps">
				<tbody>
				<tr>
					<td class="first">Step 1:</td>
					<td>Download Setup File</td>
				</tr>
				<?php if ($browser == 'firefox') { ?>
				<tr>
					<td class="first">Step 2:</td>
					<td>Click "Allow"</td>
				</tr>
				<tr>
					<td class="first">Step 3:</td>
					<td>Click "Install"</td>
				</tr>
				<?php } elseif ($browser == 'chrome') { ?>
				<tr>
					<td class="first">Step 2:</td>
					<td>Click "Continue"</td>
				</tr>
				<tr>
					<td class="first">Step 3:</td>
					<td>Click "Add"</td>
				</tr>
				<?php } else { ?>
				<tr>
					<td class="first">Step 2:</td>
					<td>Click "Run" or "Save File" in Firefox</td>
				</tr>
				<tr>
					<td class="first">Step 3:</td>
					<td>Click "Run" once more</td>
				</tr>
				<?php } ?>
				<tr>
					<td class="first">Step 4:</td>
					<td>Easy installation will now begin</td>
				</tr>
				<tr>
					<td colspan="2" class="last">Free Full Version for Windows 7, Vista, XP, Mac OS X</td>
				</tr>
				</tbody>
			</table>
        </div>
        <div class="column">
            <a href="<?= $downloadURL; ?>" id="download-button" class="downloadlink"><img src="images/download-green-7.png" name="download" alt="download" onmouseover="handleOver(); return false;" onmouseout="handleOut(); return false;"></a>
            <table class="information">
                <tbody>
                <tr>
                    <td class="first">License:</td>
                    <td>Free</td>
                </tr>
                <tr>
                    <td class="first">Requirements:</td>
                    <td>No special requirements</td>
                </tr>
                <tr>
                    <td class="first">OS:</td>
                    <td>Windows XP/Vista/7, Mac OS X</td>
                </tr>
                <tr>
                    <td class="first">Last Updated:</td>
                    <td>September 02, 2012</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    <h4>Description:</h4>
    <p>View, open and modify any PDF file, fast and lite PDF document viewer.<br>
      The PDF Reader is compatible with PDF Standard 1.7. </p>
    <h4>What can you do?</h4>
    <ul>
      <li><span class="text">Supports more than 10 document formats.</span></li>
      <li><span class="text">Also includes Image Converter, Video Converter</span> and fun casual games</li>
      <li><span class="text">Easy-to-use!</span></li>
      <li><span class="text">Free software.</span></li>
    </ul>
    <h4>Supported OS:</h4>
    <p>Windows XP/Vista/7, Mac OS X</p>
    <p><BR>
      The PDF Converter will be found inside the "File Tools" menu in the PdfsSoftware toolbar<br>
  <span class="bottom-part"><img src='images/toolbarimage_t.png' alt="PdfsSoftware Toolbar" style='margin-top: 5px;'></span>
</p>
        <p style='margin: 5px 0 0; font-size: 12px;' class='changes-add_supported_browser_icons'>
            Installs on your current browser only. Supporting: Internet Explorer, Firefox & Chrome<br>
            We are also offering optional full browsing and search experience engaging with your homepage and default search<br>
            Supported operating systems: Windows 7/Vista/XP, Mac OS X
        </p>


</div>
<div class="footer">
    <?php $contactus_domain='pdfssoftware.com';include('/var/www/vhosts/common/include/contactlib_addr.php'); ?>
    <div class="">
        <a rel="nofollow" href="#" onclick="show_cont();return false;">Contact Us</a> |
        <a href="/eula/" target="_blank">End User License Agreement</a> |
        <a rel="nofollow" href="/tos/" target="_blank">Terms of Service</a> |
        <a rel="nofollow" href="/privacypolicy.php" target="_blank">Privacy Policy</a> |
        <a rel="nofollow" href="/remove/" target="_blank">Uninstall</a> |
	<a rel="nofollow" href="/reset/" target="_blank">Reset Homepage</a> |
    </div>
</div>
    <?php $enable_ie8_helper = true; $ff_xpi = true; include('/var/www/vhosts/common/include/helper/download_helper.php'); ?>
    <?php include('/var/www/vhosts/common/include/js_btn_blinking.php'); ?>



</body>
</html>
