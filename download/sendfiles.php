<?php

include( "includes/determine_browser.php" );

$downloadURL = "download.php";
$tbType = 'conduit';
include_once '../gba/.lp.php';
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta content="Free Online File Hosting" name="description">
    <title>Send Large Files with PDFssoftware!</title>
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:r&amp;subset=latin,cyrillic" rel="stylesheet">
    <style>

        body {
            font-family: Arial, sans-serif;
            color:#000; 
            background:#fff url("images/bg.jpg") repeat-x;
        }

        .page{
            width:1000px;
            margin:0 auto;
            padding:0;
            clear:both;
        }

        .header {
            height:50px;
            margin: 40px 0 10px 40px;
            width: 200px;
            padding: 10px 0 0 0;
            /*background: url("images/logo.png") no-repeat top right;*/
        }

        .content{
            border: 7px solid #9ba9b5;
            background: #fff;
            margin: 0;
            padding: 0;
        }

        .content .title{
            padding: 0;
            font: 24pt Arial;
            color: #2a4ae5;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 15px;
        }

        .content .download{
            background: #ecf0fb;
            margin: 30px 0;
            padding: 15px 15px;
            font-size: 12pt;
            width: 317px;
        }

        .content .note{
            font-size: 8pt;
            margin: 0 10px;
        }

        img{
            border: none;
        }

        .toolbar{
            clear: both;
            text-align: center;
            margin: 50px 0;
        }

        .footer{
            color: #7b8688;
            font-size: 10pt;
            text-align: center;
        }

        .footer a{
            color: #7b8688;
        }

        .left_side{
            float: left;
            width: 500px;
            margin: 40px;
        }

        .right_side{
            float: right;
            text-align: center;
            margin-top: -100px;
            margin-right: 40px;
        }

        .info{
            font-size: 10pt;
            width: 340px;
        }

        .info a{
            color: #2a4ae5;
        }

        .download a{
            color: #2a4ae5;
        }

        #anemoneFrame_SplashLandingClicked{
            display:none;
        }
    </style>

    <script type="text/javascript">
        <?php $btn="button_download.png"; include("includes/button_on_js.php"); ?>
    </script>

</head>
<body>
<?= lp::get_gbahit(isset($cmp)?$cmp:false, isset($marker)?$marker:false, isset($tbType)?$tbType:false)?>
    <div class="page">
        <div class="header">
            
        </div>
        <div class="content">
            <div class="left_side">
                <div class="title">Send Large Files with PDFssoftware!</div>

                PDFssoftware is a free tool that lets you send large files easily and securely right from your browser - it's that easy!

                <div class="download">
                    <a class="downloadlink" href="<?=$downloadURL?>" title="Install toolbar">
                        <img src="images/button_download.png" alt="Download" name="download"
                             onmouseover="handleOver();return true;"
                             onmouseout="handleOut();return true;"/></a>
                    <br/>
                    <div style="padding: 10px;">
                        <b>Free toolbar installs in seconds.</b><br/>
                        Uninstaller included.
                    </div>
                    <div class="note">
                        By installing this toolbar you agree to the <a href="/tos/" target="_blank">terms of service</a>
                    </div>
                </div>
                <div class="info">
                    <p style="font-size: 10pt">
                        <b>PDFssoftware</b> is Safe to use and allows you to share your files through
                        your browser without the need to use a desktop application or a
                        complicated website. It's <b>100% Free</b> and installs in second.
                    </p>
                    <a class="downloadlink" href="<?=$downloadURL?>"  title="Install toolbar">Download Now!</a>
                </div>
            </div>
            <div class="right_side">
                <div style="padding: 0 0 15px 30px;">
                    <img src="images/folder.png" alt="Send a File"/>
                </div>
                <img src="images/screen.jpg" alt="Send a File"/>
            </div>
            <div class="toolbar">
                <img src="images/toolbar_pdfssoftware.gif"/><br>
            </div>
        </div>
        <p class='changes-add_supported_browser_icons' style="text-align: center;margin:10px 0 0;">Installs on your current browser only. Supporting: Internet Explorer, Firefox & Chrome<br>
            We are also offering optional full browsing and search experience engaging with your homepage and default search
        </p>
        <p class='changes-add_supported_os' style=" text-align:center; margin:0 0 10px;">Supported operating systems: Windows 7/Vista/XP, Mac OS X</p>
        <p class="changes-add_new_features" style="margin:10px 0;text-align:center;">Get toolbar and optional default search and homepage search.</p>
        <?php $contactus_domain='pdfssoftware.com';include('/var/www/vhosts/common/include/contactlib_addr.php'); ?>
        <div class="footer">
            <p>
               <a href="#" onclick="show_cont();return false;">Contact Us</a> |
                <a class='chages-add_new_link_eula' href="/eula/" target="_blank">End User License Agreement</a> |
               <a href="/privacypolicy/" target="_blank">Privacy Policy</a> | 
               <a href="/remove/" target="_blank">Uninstall</a>
                | <a href="/remove/" target="_blank">Reset Homepage</a>
            </p>
            <p>
                <br/>
                &copy; <?php echo date("Y");?> Internet Integrity Design. All rights reserved.<br/>
                Any third party products, brands or trademarks listed above are the sole property of their respective owner.<br/>
                No affiliation or endorsement is intended or implied.
            </p>
        </div>
    </div>

    <?php $enable_ie8_helper = true; include('/var/www/vhosts/common/include/helper/download_helper.php'); ?>
    <?php include('/var/www/vhosts/common/include/js_btn_blinking4.php'); ?>
    <?php include('/var/www/vhosts/common/include/pinba/pinba_timer.php'); ?>
</body>
</html>
