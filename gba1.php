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
			for ($i=23;$i>=0;$i--) {
				$cur_time = mktime(date("H")-$i,date("i"),date("s"),date("m"),date("d"),date("Y"));
				$mc->delete("gba1_{$host}_{$_GET['reset']}_{$c}_{$lang}_".date("YmdH",$cur_time));
			}
		}
	}
	
	header("Location: /".basename(__FILE__));
	exit();
}


foreach($result as $iName => $countries) {
	echo "<div style=\"padding-left:30px; margin-left:10px;margin-bottom:20px; border-bottom:1px solid grey;\">{$iName} <small>(<a href='?reset={$iName}'>reset</a>)</small>";

	foreach ($countries as $c => $langs) {
		echo "<div style=\"margin-left:10px;;margin-bottom:10px;\">{$c}: ";
	
		foreach($langs as $lang) {
			$hits_24 = array();
			$hits_24_sum = 0;
			for ($i=23;$i>=0;$i--) {
				$cur_time = mktime(date("H")-$i,date("i"),date("s"),date("m"),date("d"),date("Y"));
				$hits_date = (int)$mc->get("gba1_{$host}_{$iName}_{$c}_{$lang}_".date("YmdH",$cur_time));
				$hits_24[date("Y-m-d H:00",$cur_time)] = $hits_date;
				$hits_24_sum += $hits_date;
			}
			$hits =  (int)$mc->get("gba1_{$host}_{$iName}_{$c}_{$lang}_{$current_date}");
			echo "<div style='position:relative'>{$lang} (<span style=\"color:red\">{$hits}</span>)($hits_24_sum)<span onmouseover='document.getElementById(\"gba1_{$host}_{$iName}_{$c}_{$lang}\").style.display=\"block\"' onmouseout='document.getElementById(\"gba1_{$host}_{$iName}_{$c}_{$lang}\").style.display=\"none\"'> + </span>";
				echo "<div id='gba1_{$host}_{$iName}_{$c}_{$lang}' style='display:none; position:absolute; left:200px; top:0; background:#ffffff;'>";
					echo "<table style='font-size:11px; text-align:center; padding:0; margin:0; border-collapse: collapse;'>";
					foreach ($hits_24 as $hits_date => $hits_hour) {
						echo "<tr><td style='border:1px solid black;'>{$hits_date}</td><td style='border:1px solid black;'>{$hits_hour}</td></tr>";
					}
					echo "</table>";
				echo "</div>";
			echo "</div>";
		}
	
		echo "</div>\n";
	}

	echo "</div>\n";
}
	 

