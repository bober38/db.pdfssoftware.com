<?php

require_once "./.BasicAuth.php";
BasicAuth::SimpleCheck(array('admingba'=>'pe8gtynk'));

$cmps =array(
		//"pdfs_o" => array("index" => "orig", "index_overlay" => "index_o"),
);

$mc = new Memcached();
$mc->addServer("localhost", 11211);
date_default_timezone_set("Israel");
$current_date = date("Ymd");

if (isset($_GET['clear'])) {
    if (!isset($_GET['cmp'])) {
        die ("cmp not set");
    }
    if (!isset($cmps[$_GET['cmp']])) {
        die ("Unknown campaign: {$_GET['cmp']}");
    }

    $cmp = $_GET['cmp'];
    $pages = $cmps[$cmp];
    foreach ($pages as $page) {
        $mc->delete("gba_{$cmp}_{$page}");
        $mc->delete("gba_{$cmp}_{$page}_conv");
    }
    //print "Campaign $cmp cleared<br>";
    header('Location: /gba.php');
    exit;
}

$host = basename($_SERVER['HTTP_HOST'],".com");
if (isset($_GET['del'])) {
	$delname = basename($_GET['del']);
	array_map("unlink", glob("./download/.gba/{$delname}-*"));
	header("Location: /gba.php");
	exit();
}

$fList = glob('./download/.gba/*');

$result = array();


foreach($fList as $fn) {
	$fn = basename($fn);

	list($iName, $c, $lang) = explode('-', $fn);

	if(!empty($iName) && !empty($c) && !empty($lang)) {
		if(!isset($result[$iName])) $result[$iName] = array();

		if(!isset($result[$iName][$c])) $result[$iName][$c] = array();

		$result[$iName][$c][] = $lang;
	}
}

if (isset($_GET['reset'])) {
	foreach ($result[$_GET['reset']] as $c => $langs) {
		foreach($langs as $lang) {
			$mc->delete("gba1_{$host}_{$_GET['reset']}_{$c}_{$lang}_{$current_date}");
		}
	}
	header("Location: /gba.php");
	exit();
}

if ($cmps) {
	require_once "./.BasicStat.php";
	
	foreach ($cmps as $cmp=>$pages) {
	    print "<div style='display:inline-block; border: solid black 1px; margin: 20px;'";
	    print "<h1>$cmp</h1> <small>(<a href='?clear=1&cmp={$cmp}'>clear</a>)</small>";
	    print "<table>";
	    print "<tr><th>Name</th><th>Hits</th><th>Convs</th><th>Conv</th><th>B/A-1</th><th>Confidence</th>";
	    $fileNames = array_keys($pages);
	    $pageMarkers = array_values($pages);
	    foreach ($pageMarkers as $i=>$page) {
	        if(isset($fileNames[$i]) && $fileNames[$i] != (string)$i) {
	            $link = '<a target="_blank" href="/d/'.$fileNames[$i].'.php?self">'.$page.'</a>';
	        }
	        else {
	            $link = $page;
	        }
	        $hits = $mc->get("gba_{$cmp}_{$page}");
	        $convs = $mc->get("gba_{$cmp}_{$page}_conv");
	        $conv = $hits ? ($convs/$hits) : 0;
	        $conv = $conv * 100;
	        if($i==0) {//@attention original lp must goes first in list!
	            $origD = $hits;
	            $origI = $convs;
	            $origC = $conv;
	        }
	        print "<tr><td>$link</td><td>$hits</td><td>$convs</td><td>".round($conv, 2)."%</td>";
	        print "<td>";
	        if( $i !=0 ) {
	            $p = ba1($origC, $conv);
	            if($p === false) {print "---";}
	            else {print $p."%";}
	        }
	        else {
	            print "&nbsp;";
	        };
	        print "</td>";
	        print "<td>";
	        if( $i !=0 ) {
	            $p = confidence($origD, $origI, $hits, $convs);
	            if($p === false) {print "---";}
	            else {print $p."%";}
	        }
	        else {
	            print "&nbsp;";
	        };
	        print "</td></tr>";
	    }
	    print "</table>";
	    print "</div>";
	}

} else {
	print "No A/B tests running";
}

echo "<hr>";
foreach($result as $iName => $countries) {
	echo "<div style=\"padding-left:30px; margin-left:10px;margin-bottom:20px; border-bottom:1px solid grey;\">{$iName} <small>(<a href='?reset={$iName}'>reset</a>)</small>";

	foreach ($countries as $c => $langs) {
	echo "<div style=\"margin-left:10px;;margin-bottom:10px;\">{$c}: ";

	foreach($langs as $lang) {
	$hits =  $mc->get("gba1_{$host}_{$iName}_{$c}_{$lang}_{$current_date}");
	echo "<div>{$lang} (<span style=\"color:red\">{$hits}</span>)</div>";
	}

	echo "</div>\n";
	}

	echo "</div>\n";
	}
	 

