<?php

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome'))
    {
        $browser = 'Chrome';
    }
    else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari'))
    {
        $browser = 'Safari';
    }
    else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko'))
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape'))
        {
            $browser = 'Netscape (Gecko/Netscape)';
        }
        else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox'))
        {
            $browser = 'Firefox';
        }
        else
        {
            $browser = 'Mozilla (Gecko/Mozilla)';
        }
    }
    else if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
    {
        $browser = 'IE';
    }
    else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false)
    {
        $browser = 'Opera';
    }
    else
    {
        $browser = 'Other browsers';
    }

//$aid_js_download = 'pageTracker._trackPageview(\'/outgoing/download\');';
$aid_href_needed=1;
$aid_js_download="";
if ($browser == 'Firefox') {
    $aid_js_download .= 'OnFFDownload();';
    $aid_href_needed=0;
    $br_suf='_ff';
}
if ($browser == 'IE'){ //Pizdec! // Re: Ne to slovo blyat!!!!!111
    $aid_js_download .= 'OnIEDownload();';
    $aid_href_needed=0;
}
$ff_aid_cmd = 'onClick="javascript:'.$aid_js_download.'"';

?>