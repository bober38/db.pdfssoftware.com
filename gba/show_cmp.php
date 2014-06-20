<?php
if ($_SERVER['HTTP_X_FORWARDED_FOR'] == '94.154.83.10') {
    error_reporting(E_ALL); ini_set("display_errors", 1);

    require_once( "/var/www/vhosts/common/include/stats/stats.php");
    /*define( 'MY_PATH', dirname(__FILE__) );*/
	/*include_once "../gba/.lpabstuff.php";*/

	if(!isset($GLOBALS['lpabindex'])) {
		$GLOBALS['lpabindex'] = new LPABIndex($fn);
	}

 		if($GLOBALS['lpabindex']->campaign) {
 			$cmp_name = $GLOBALS['lpabindex']->campaign->config['name'];
 			$cmp_status = $GLOBALS['lpabindex']->campaign->config['active']?"<span style='color:green;'>enabled</span>":"<span style='color:red;'>disabled</span>";
 			if (!isset($_GET['forceab'])) {
 				$GLOBALS['lpabindex']->campaign->getStats(true);
 			}
 			$indexes = $GLOBALS['lpabindex']->campaign->config['indexes'];
			echo '<div style="position: absolute; top:0; right: 0; padding:0 20px 10px 20px; width: 300px; background-color: #E0E0E0; border: 1px #8A8A8A solid;">';
				echo '<div style="position:absolute; right:6px; top:-1px; padding:3px; font-size:16px; font-weight:bold;" onClick="this.parentNode.style.display=\'none\'">x</div>';
				echo '<div style="text-align: center; font-weight:bold; margin-bottom:10px; margin-top: 10px;">'.$cmp_name.' ['.$cmp_status.']</div>';
				echo '<div style="clear: both;"><div style="float: left; width: 150px; border-bottom:1px solid black;">filename</div><div style="float: left; width: 70px; border-bottom:1px solid black;">dl</div><div style="float: left; width: 70px; border-bottom:1px solid black;">wl</div></div>';
				foreach ($indexes as  $index) {
					echo '<div style="clear: both;">';
						echo '<div style="float: left; width: 150px;">';
							if (basename($fn,".php") == $index['fn']) {
								echo $index['fn'];
							} else {
								echo '<a href="/d/'.$index['fn'].'.php?self">'.$index['fn'].'</a>';
							}
						echo '</div>';
						echo '<div style="float: left; width: 70px;">'.$index['dl'].'</div>';
						echo '<div style="float: left; width: 70px;">'.$index['wl'].'</div>';
					echo '</div>';
				}
			echo '</div>'; 		
 		}       
}
?>
