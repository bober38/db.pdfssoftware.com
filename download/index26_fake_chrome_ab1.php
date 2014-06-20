<?php
include('includes/determine_browser.php');
// include('/var/www/vhosts/common/include/pinba/pinba_timer.php');
if (isset($_SERVER["HTTP_REFERER"])) {
    $refer = $_SERVER["HTTP_REFERER"];
    $element = '';
    if (preg_match("/google/i", $refer)) {
        preg_match_all('/\&q\=([^\&]+)/i', $refer, $matches);
        $element = (count($matches[1]) > 0) ? array_pop($matches[1]) : '';
        if ($element) {
            setcookie ("refer_cookie", base64_encode($element), time()+600, '/');
        }
    }
}

include_once '../gba/.lp.php';
$cmp = false;
$marker = false;

$country = $_SERVER['COUNTRY'];

$b_lang = substr(Goldbar::getBrowserLang(), 0,2);


if(!isset($_GET['self'])) {
        include_once "../gba/.lpabstuff.php";
        if(!isset($GLOBALS['lpabindex'])) {
            $GLOBALS['lpabindex'] = new LPABIndex(__FILE__);
            if($GLOBALS['lpabindex']->campaign && $GLOBALS['lpabindex']->campaign->config['active']) {
                if($ri = $GLOBALS['lpabindex']->randomIndex()) {
                    Goldbar::include_lp($ri.'.php', "ab:".$GLOBALS['lpabindex']->campaign->config['name']);
                }
            }
        }
        else {
            if($GLOBALS['lpabindex']->campaign && $GLOBALS['lpabindex']->campaign->config['active'] && !empty($GLOBALS['lpabindex']->campaign->config['name']) && $GLOBALS['lpabindex']->file && $GLOBALS['lpabindex']->marker) {
                $cmp = $GLOBALS['lpabindex']->campaign->config['name'];
                $marker = $GLOBALS['lpabindex']->marker;
            }
        }
}

$tbType='conduit';
$downloadURL = '/d/download_ab1.php';
$test = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>

<!-- Google Analytics Content Experiment code -->
<script>function utmx_section(){}function utmx(){}(function(){var
k='62403780-8',d=document,l=d.location,c=d.cookie;
if(l.search.indexOf('utm_expid='+k)>0)return;
function f(n){if(c){var i=c.indexOf(n+'=');if(i>-1){var j=c.
indexOf(';',i);return escape(c.substring(i+n.length+1,j<0?c.
length:j))}}}var x=f('__utmx'),xx=f('__utmxx'),h=l.hash;d.write(
'<sc'+'ript src="'+'http'+(l.protocol=='https:'?'s://ssl':
'://www')+'.google-analytics.com/ga_exp.js?'+'utmxkey='+k+
'&utmx='+(x?x:'')+'&utmxx='+(xx?xx:'')+'&utmxtime='+new Date().
valueOf()+(h?'&utmxhash='+escape(h.substr(1)):'')+
'" type="text/javascript" charset="utf-8"><\/sc'+'ript>')})();
</script><script>utmx('url','A/B');</script>
<!-- End of Google Analytics Content Experiment code -->


    <meta charset="utf-8">
	<meta content="Free Video Converter Add-on" name="description">
	<title>Download Now</title>
	<style>
        img{
            border: 0;
        }
        body{
            background-color: #D9D9D9;
            font-family: Arial,Helvetica,"sans-serif";
            font-size: 13px;
            margin: 0;
        }
        #header{
            height: 100px;
            width: 920px;
            margin: 40px auto 0;
        }
            #header h1{
                font-size: 26px;
                font-weight: normal;
                margin: 0;
                text-shadow: 0 1px 0 #FFFFFF;
            }
            #header p{
                font-size: 14px;
                margin: 20px 0;
                color: #383838;
            }

        #center{
            width: 877px;
            margin: 0 auto;
            padding: 15px 55px;
            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            -khtml-border-radius: 10px;
            border-radius: 10px;
            background: #fff;
        }
            #center #left{
                color: #2F2F2F;
                float: left;
                margin: 0 40px 0 0;
                width: 460px;
            }
                #left ul{
                    font-size: 16px;
                    list-style-type: none;
                    margin: 20px 0 0;
                    padding: 0;
                }
                    #left ul li{
                        height: 50px;
                        line-height: 50px;
                        position: relative;
                    }
                    #left ul li strong{
                        width: 80px;
                        float: left;
                    }
            #center #right{
                color: #2F2F2F;
                float: left;
                width: 368px;
                position: relative;
            }
                #right a.downloadlink{
                    margin-left: 16px;
                }
                #right ul{
                    border-top: 1px solid #D9D9D9;
                    border-left: 1px solid #D9D9D9;
                    border-right: 1px solid #D9D9D9;
                    font-size: 14px;
                    list-style-type: none;
                    margin: 16px 0 0;
                    padding: 0;
                }
                #right ul li{
                    background: none repeat scroll 0 0 #E8F1FE;
                    border-bottom: 1px solid #D9D9D9;
                    border-top: 1px solid #F9FBFF;
                    height: 30px;
                    line-height: 30px;
                }
                #right ul li.small{
                    font-size: 10px;
                    text-align: center;
                }
                #right ul li strong{
                    margin-left: 10px;
                    width: 120px;
                    float: left;
                }
        #description{
            color: #2F2F2F;
            font-size: 14px;
            margin: 40px auto 0;
            width: 920px;
        }
            #description p{
                margin: 20px 0 12px;
            }
        #copyright{
            color: #919191;
            margin: 60px auto 10px;
            text-align: center;
        }
        #footer{
            background: url("/images/bg_footer.jpg") repeat-x scroll 0 0 transparent;
            color: #CFCFCF;
            height: 97px;
            overflow: hidden;
            width: 100%;
            text-align: center;
        }
            #footer .links{
                font-size: 10px;
                font-weight: bold;
                margin: 6px auto 0;
            }
            #footer p{
                font-size: 10px;
                margin: 8px auto 0;
                width: 830px;
            }
            #footer a{
                color: #CFCFCF;
                text-decoration: none;
            }
        .clear{
            clear: both;
        }
        div.center{
            text-align: center;
        }
        div.for_like{
            margin: 3px 0 0px 18px;
            height: 25px;
        }
	</style>
    <script type="text/javascript">
        <?php $btn = 'vc_download_button.png';
        include('includes/button_on_js.php'); ?>
    </script>

<script type="text/javascript"> var _gaq = _gaq || []; _gaq.push(['_setAccount', 'UA-33646024-1']); _gaq.push(['_trackPageview']); (function() { var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true; ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js'; var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s); })(); </script>
</head>
<body>
    <!-- img src="/gbahit.php?fn=<?= basename(__FILE__, '.php'); ?>&lp=<?= basename($_SERVER['SCRIPT_FILENAME'],".php") ?>" width="1" height="1" / -->
    <?= lp::get_gbahit(isset($cmp)?$cmp:false, isset($marker)?$marker:false, isset($tbType)?$tbType:false)?>
    <script type="text/javascript">window.force_durl="<?=$downloadURL?>";</script>
    <div id='header'>
        <h1><a href="<?=$downloadURL?>" class="downloadlink">Click to start your download...</a></h1>
        <p><span class="text">A free PDF Converter supporting all popular document formats.
                The WiseConvert PDF Converter toolbar is easy to use, simple &amp; fast. Within a few seconds you can retrieve the converted PDF or Image. 
Supported formats: DOC, DOCX, HTML, XLS, JPG, TIFF, BMP, and more.</span></p>
    </div>
    <div id='center'>
        <div id='left'>
            <ul>
				<li><strong>Step 1:</strong><span>Download Setup File</span> <img src='/images/arrow.jpg' style='position: absolute; top: 0; right: 0;'></li>
				<?php if ($browser == 'Firefox') { ?>
				<li><strong>Step 2:</strong><span>Click "Allow"</span></li>
				<li><strong>Step 3:</strong><span>Click "Install"</span></li>
				<?php } elseif ($browser == 'Chrome') { ?>
				<li><strong>Step 2:</strong><span>Click "Continue"</span></li>
				<li><strong>Step 3:</strong><span>Click "Add"</span></li>
				<?php } else { ?>
				<li><strong>Step 2:</strong><span>Click "Run" </span></li>
				<li><strong>Step 3:</strong><span>Click "Run" once more</span></li>
				<?php } ?>
				<li><strong>Step 4:</strong><span>Easy installation will now begin</span></li>
				<li class="small"><img src="/images/windows.jpg">&nbsp;Free Full Version for Windows 7, Vista, XP<span class='changes-add_supported_os'>, Mac OS X</span></li>
            </ul>
        </div>
        <div id='right'>
            <a href="<?=$downloadURL?>" class="downloadlink"><img alt="Download" name="download"  onmouseover="handleOver(); return false;" onmouseout="handleOut(); return false;" src='/images/vc_download_button.png'></a>
            <div class='for_like'>
                <iframe src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwiseconvert.com%2Fd%2F&amp;send=false&amp;layout=button_count&amp;width=350&amp;show_faces=false&amp;action=like&amp;colorscheme=light&amp;font=arial&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:350px; height:21px;" allowTransparency="true"></iframe>
            </div>
            <div class='center'>
                <b>Free toolbar installs in seconds.</b><br/>
                Uninstaller included.
                <div class="note"> By installing this toolbar you agree to the <a href="/tos/" target="_blank">terms of service</a>
                    <?php if ($browser == 'Firefox') { ?>
                        <br />
                     This extension may change your browser's default search and homepage. You can control these settings right after installation.
                    <?php } ?>
                </div>
            </div>
            <ul>
                <li><strong>License:</strong><span>Freeware</span></li>
                <li><strong>Requirements:</strong><span>No special requirements</span></li>
                <li><strong>OS:</strong><span>Windows 7,Vista, XP</span><span class='changes-add_supported_os'>, Mac OS X</span></li>
                <li><strong>Last Updated:</strong><span>April 1st, 2012</span></li>
            </ul>
        </div>
        <br class='clear'>
    </div>
    <div id='description'>
        <p><span class="small">What can you do?</span></p>
        <ul>
          <li><span class="text">Supports more than 10 document formats.</span></li>
          <li><span class="text">Also includes Image Converter, Video Converter</span> and fun casual games</li>
          <li><span class="text">Easy-to-use!</span></li>
          <li><span class="text">Free software.</span></li>
          <li class='changes-add_new_features'><span class="text">Get toolbar and optional default search and homepage search.</span></li>
        
        </ul>
      <BR>
        The PDF Converter will be found inside the "File Tools" menu in the WiseConvert toolbar<span class="bottom-part"><img src='images/toolbarimage_t.png' alt="WiseConvert Toolbar" style='margin-top: 5px;'></span><BR>
        <p style='margin: 5px 0 0; font-size: 12px;' class='changes-add_supported_browser_icons'>
            Installs on your current browser only. Supporting: Internet Explorer, Firefox & Chrome<br>
            We are also offering optional full browsing and search experience engaging with your homepage and default search<br>
            Supported operating systems: Windows 7/Vista/XP, Mac OS X
        </p>

        
</div>
    <div id="copyright">
        <img src="/images/qsvkha.png"/><br/>
    </div>
    <?php $contactus_domain='wiseconvert.com';include('/var/www/vhosts/common/include/contactlib_addr.php'); ?>
    <div id="footer">
        <div class="links">
            <a rel="nofollow" href="#" onclick="show_cont();return false;"> Contact Us </a> |
            <a class='chages-add_new_link_eula' href="/eula/" target="_blank">End User License Agreement</a> |
            <a href="/privacypolicy.php" target="_blank">Privacy Policy</a> | <a href="/remove/" target="_blank">Uninstall</a> | <a href="/reset/" target="_blank">Reset Homepage</a>
        </div>
        <p>
            Any third party products, brands or trademarks listed above are the sole property of their respective owner.<br/>
            No affiliation or endorsement is intended or implied.
        </p>
    </div>
    <?php $ff_helper_on = false; $enable_ie8_helper = true; include('/var/www/vhosts/common/include/helper/download_helper.php'); ?>
    <?php include('/var/www/vhosts/common/include/js_btn_blinking.php'); ?>
    
    <!-- Google Code for Converting users - PDF converter Remarketing List -->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = 1006081641;
var google_conversion_language = "en";
var google_conversion_format = "3";
var google_conversion_color = "ffffff";
var google_conversion_label = "_DrZCN_9pQMQ6aze3wM";
var google_conversion_value = 0;
/* ]]> */
</script>
<script type="text/javascript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<div style="display:inline;">
<img height="1" width="1" style="border-style:none;" alt="" src="http://www.googleadservices.com/pagead/conversion/1006081641/?label=_DrZCN_9pQMQ6aze3wM&amp;guid=ON&amp;script=0"/>
</div>
</noscript>

<?php //'Moshik_Preinstall' ?>
<img src="http://ad.yieldmanager.com/pixel?id=2127539&t=2" width="1" height="1" />
</body>
</html>
