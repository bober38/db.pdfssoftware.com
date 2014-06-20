<?php
if (isset($_COOKIE['gbaf']) && isset($_COOKIE['gbacam'])) {
    $from = $_COOKIE['gbaf'];
    $camp = $_COOKIE['gbacam'];

    $mc = new Memcached();
    $mc->addServer("localhost", 11211);
    if (!$mc->get("gba_{$camp}_{$from}")) {
        $mc->set("gba_{$camp}_{$from}", 1);
    }
    else {
        $mc->increment("gba_{$camp}_{$from}");
    }
}

if ( !empty($_GET['fn']) &&  !empty($_GET['lp'])) {
	$fn = $_GET['fn'];
	if( preg_match('/^([_a-z0-9]*)?_([a-z]{2})[0-9]*$/i', $fn, $matches) ) {
		if(!empty($matches[2])) {
			$lp = $_GET['lp']==$fn?"direct":$_GET['lp'];
			$lang = $matches[2];
			$c = isset($_SERVER['COUNTRY'])?$_SERVER['COUNTRY']:'unknown country';
			$str = "{$lp}-{$c}-{$fn}";

			@touch('./download/.gba/'.$str);
			$mc = new Memcached();
			$mc->addServer("localhost", 11211);
			$host = basename($_SERVER['HTTP_HOST'],".com");
			date_default_timezone_set("Israel");
			$date = date("Ymd");
			$date2 = date("YmdH");
		    if (!$mc->increment("gba1_{$host}_{$lp}_{$c}_{$fn}_{$date}")) {
		        $mc->set("gba1_{$host}_{$lp}_{$c}_{$fn}_{$date}", 1);
		    }
		    if (!$mc->increment("gba1_{$host}_{$lp}_{$c}_{$fn}_{$date2}")) {
		    	$mc->set("gba1_{$host}_{$lp}_{$c}_{$fn}_{$date2}", 1);
		    }
		}
	}
}

header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
fastcgi_finish_request();

// fastcgi_finish_request();

// include ".lp.php";
// $country = $_SERVER['COUNTRY'];
// $b_lang = substr(Goldbar::getBrowserLang(), 0,2);

// $countries_breakdown = array('US','GB','CA');
// $mc = new Memcached();
// $mc->addServer("localhost", 11211);
// if (in_array($country, $countries_breakdown)) {
//     if (!$mc->get("gba_pconv_{$country}_{$b_lang}")) {
//         $mc->set("gba_pconv_{$country}_{$b_lang}", 1);
//     }
//     else {
//         $mc->increment("gba_pconv_{$country}_{$b_lang}");
//     }

// 	if (!file_exists(__DIR__."/locales/{$country}-{$b_lang}.loc")) {
// 		touch(__DIR__."/locales/{$country}-{$b_lang}.loc");
//     }
// }

