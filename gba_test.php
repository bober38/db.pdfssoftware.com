<?php
$fList = glob('./download/.gba/*.tra');
date_default_timezone_set("Canada/Pacific");
$current_date = date("Ymd");
$host = basename($_SERVER['HTTP_HOST'],".com");
$mc = new Memcached();
$mc->addServer("localhost", 11211);

if (isset($_GET['reset'])) {
	$mc->delete("gba1_{$host}_{$_GET['reset']}_{$current_date}");
	for ($i=23;$i>=0;$i--) {
		$cur_time = mktime(date("H")-$i,date("i"),date("s"),date("m"),date("d"),date("Y"));
		$mc->delete("gba1_{$host}_{$_GET['reset']}_".date("YmdH",$cur_time));
	}

	header("Location: /".basename(__FILE__));
	exit();
}

class TraceObject {
	private $page, $reason, $parent,$memstr, $children = array();
	private $hits24 = array();
	private $hits24_sum = 0;
	private $mc, $col;
	private $route = false;
	
	function __construct() {
		$this->mc = new Memcached();
		$this->mc->addServer("localhost", 11211);
	}
	
	function getPage() {
		return $this->page;
	}
	function setPage($page) {
		$this->page = $page;
		return $this;
	}
	function getReason() {
		return $this->reason;
	}
	function setReason($reason) {
		$this->reason = $reason;
		return $this;
	}
	function getChildren() {
		return $this->children;
	}
	function addChild($page, $reason) {
		foreach ($this->children as $child) {
			if($child->getReason() == $reason && $child->getPage() == $page) {
				return $child;
			}
		}
		
		$child = new TraceObject();
		$child->setPage($page)->setReason($reason);
		$child->setParent($this);
		$this->children[] = $child;
		return $child;
	}
	
	function getParent() {
		if (isset($this->parent)) {
			return $this->parent;
		}
		return false;
	}
	
	function setParent($parent) {
		$this->parent = $parent;
		return $this;
	}
	
	function getHits24Sum() {
		return $this->hits24_sum;
	}
	
	function displayHits24() {
		echo "<div id='gba1_{$this->getRouteStr()}' style='display:none; position:absolute; right:20px; top:50px; background:#E6E6E6; z-index:1;'>";
			echo "<table style='font-size:12px; text-align:center; padding:0; margin:0; border-collapse: collapse;'>";
				foreach ($this->hits24 as $hits_date => $hits_hour) {
					echo "<tr><td style='border:1px solid black;'>{$hits_date}</td><td style='border:1px solid black; width:30px; text-align:center;'>{$hits_hour}</td></tr>";
				}
			echo "</table>";
			echo "<a href='#' onClick='document.getElementById(\"gba1_{$this->getRouteStr()}\").style.display=\"none\"'>close</a>";
		echo "</div>";
	}
	
	function displayRoutes(TraceObject $root, $level=0) {
		if ($root->getParent()) {
			echo "<div style='margin-top:10px; margin-left:275px;'>";
			echo "<div style='width:230px; height:43px; position:relative; text-align:center; background:#E6E6E6;'>";
			echo "<div style='position:absolute; left:120px; top:43px; width:2px; height:".($root->countChildren()*53-20)."px; background:url(\"/images/gba_vertical.png\") repeat-y;'></div>";
			echo "<div style='text-align:right; position:absolute; top:0; left:-154px; width:133px; height:43px; padding-right:20px; background:url(\"/images/gba_arrow.png\")'>".$root->getReason()."</div>";
			$root->setHits24();
			echo "<table rel='{$root->getPage()}' cellpadding='0' cellspacing='0' class='item_table' style='border-collapse: collapse; width:100%; height:100%; margin:0; padding:0; text-align:center'><tr><td width='120px'>Page</td><td>Today</td><td>24h</td><td style='padding:0 3px;' rowspan='2'><a title='Clear stats for this page' href='?reset=".urlencode($root->getRouteStr())."' onClick='if (confirm(\"Reset hits?\")){return true;}else{return false;}'><img src='/images/reset.png' alt='reset hits'/></a></td></tr><tr>";
			echo "<td><a href='http://".basename($_SERVER['HTTP_HOST'])."/d/".$root->getPage().".php' target=\"_blank\">".$root->getPage()."</a></td>";
			echo "<td class='hits_today'><a title='hits in".date("l")."'>".$root->getHitsToday()."</a></td><td class='last_hits'><a title='click for more information' href='#' onClick='hits = document.getElementById(\"gba1_{$root->getRouteStr()}\"); if (hits.style.display == \"none\"){hits.style.display=\"block\"}else{hits.style.display=\"none\"};'>".$root->getHits24Sum()."</a></td>";
			echo "</tr></table>";
			$root->displayHits24();
			echo "</div>";
			$level++;
		}
		foreach ($root->getChildren() as $child) {
			$this->displayRoutes($child, $level);
		}
		echo "</div>";
	}
	
	function getRouteStr() {
		if ($this->route) {
			return $this->route;
		}
		if ($this->getParent()) {
			if ($this->getParent()->getRouteStr()) $str = $this->getParent()->getRouteStr()."-";  
			$str .= $this->getPage()."+".str_replace(":", "#", $this->getReason());
			$this->route = $str;		
			return $str;
		}
		return false;
	}
	
	function getHitsToday() {
		return (int)$this->mc->get("gba1_".basename($_SERVER['HTTP_HOST'],".com")."_".$this->getRouteStr()."_".date("Ymd"));
	}
	
	function setHits24() {
		for ($i=23;$i>=0;$i--) {
			$cur_time = mktime(date("H")-$i,date("i"),date("s"),date("m"),date("d"),date("Y"));
			$hits_date = (int)$this->mc->get("gba1_".basename($_SERVER['HTTP_HOST'],".com")."_".$this->getRouteStr()."_".date("YmdH",$cur_time));
			$this->hits24[date("Y-m-d H:00",$cur_time)] = $hits_date;
			$this->hits24_sum += $hits_date;
		}
	}
	
	
	function countChildren() {
		$col = 0;
		$children = $this->children;
		if (empty($children)) return 0;
		foreach ($children as $child) {
			$col += $child->countChildren();
		}
		return $col + count($children);
	}
}

$root = new TraceObject();
$host = basename($_SERVER['HTTP_HOST'],".com");
$root->setPage($host);

foreach($fList as $fn) {
	$fn = basename(str_replace("#", ":", $fn),".tra");
	$traces = array();
	foreach (explode('-', $fn) as $trace) {
		$parts = explode('+',$trace);
		$traces[$parts[1]] = $parts[0];
	}
	
	$curNode = $root;
	foreach ($traces as $reason => $page) {
		$curNode = $curNode->addChild($page, $reason);
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html xmlns="http://www.w3.org/1999/html">
<head>
	<title><?= $host?> route stats</title>
	<script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
	<script type="text/javascript">
	$( document ).ready(function() {
		$("table[class='item_table']").hover(
			function() {
				$("table[rel='"+$(this).attr('rel')+"']").css("border","2px solid #05469B");
			},
			function() {
				$("table[rel='"+$(this).attr('rel')+"']").css("border","1px solid grey");
			}
		);
	});
	</script>
</head>
<body>
<?php
echo "<style>html{background:#f4f4f4}html img{border:0}.item_table td{border:1px solid grey}td.hits_today a{color:red; cursor:pointer;} td.last_hits a{color:green}.displayroutes a{text-decoration:none; color: #05469B;}</style>";
echo "<div class='displayroutes'; style='margin-left:-200px'>";
$root->displayRoutes($root);
echo "</div>";
?>

</body>
</html>