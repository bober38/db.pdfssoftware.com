<?php


if (isset($_GET['backtrace'])) {
	foreach (json_decode(base64_decode(urldecode($_GET['backtrace']))) as $page => $reason) {
		$str .= basename($page,".php")."+".$reason."-";
	}
	$str = str_replace(":", "#",substr($str, 0, strlen($str)-1));
	@touch('./download/.gba/'.$str.".tra");
	$mc = new Memcached();
	$mc->addServer("localhost", 11211);
	$host = basename($_SERVER['HTTP_HOST'],".com");
	date_default_timezone_set("Canada/Pacific");
	$date = date("Ymd");
	$date2 = date("YmdH");
	if (!$mc->increment("gba1_{$host}_{$str}_{$date}")) {
		$mc->set("gba1_{$host}_{$str}_{$date}", 1);
	}
	if (!$mc->increment("gba1_{$host}_{$str}_{$date2}")) {
		$mc->set("gba1_{$host}_{$str}_{$date2}", 1);
	}
}

if(isset($_GET['c'])) {
    $lpabdata = json_decode(base64_decode(urldecode($_GET['c'])), true);
    print_r($lpabdata);
    if($lpabdata && !empty($lpabdata['cmp']) && !empty($lpabdata['marker'])) {
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

        if ($domain) {
            setcookie("gbacam", $lpabdata['cmp'], time()+3600, '/', ".$domain", false, true);
            setcookie("gbaf", $lpabdata['marker'], time()+3600, '/', ".$domain", false, true);
        }
    }
    else {
        $lpabdata = false;
    }
}



header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
fastcgi_finish_request();

if(!empty($lpabdata)) {
    $mc = new Memcached();
    if($mc->addServer("localhost", 11211)) {
        if(!$mc->increment("gba_{$lpabdata['cmp']}_{$lpabdata['marker']}") )
        {
            $mc->set("gba_{$lpabdata['cmp']}_{$lpabdata['marker']}", 1);
        }
    }

}

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

