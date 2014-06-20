<?php
$domain = "pdfssoftware.com";
if (isset($_COOKIE['gbaf']) && isset($_COOKIE['gbacam'])) {
    $from = $_COOKIE['gbaf'];
    $camp = $_COOKIE['gbacam'];

    setcookie("gbaf", false, time()-10, '/', ".$domain", false, true);
    setcookie("gbacam", false, time()-10, '/', ".$domain", false, true);
    $mc = new Memcached();
    if($mc->addServer("localhost", 11211)) {
        if (!$mc->get("gba_{$camp}_{$from}_conv")) {
            $mc->set("gba_{$camp}_{$from}_conv", 1);
        }
        else {
            $mc->increment("gba_{$camp}_{$from}_conv");
        }
    }
}

header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
