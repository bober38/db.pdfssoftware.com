<?php

$domain = $_SERVER['HTTP_HOST'];

require_once "./.BasicAuth.php";
BasicAuth::SimpleCheck(array('admingba'=>'pe8gtynk'));


//==============================ROUTE MAP=========================
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
        if(!$root->getParent() ){
            echo '<div>';
        }
        else {
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
//=============================CUT HERE===================================


require_once "./.lpabstuff.php";

$a = getParam('a');

switch($a) {
    case 'create':
        $result = createCampaign();
        print json_encode($result);
        exit;
        break;
    case 'start':
        $result = startCampaign(false);
        print json_encode($result);
        exit;
        break;
    case 'stop':
        $result = stopCampaign(false);
        print json_encode($result);
        exit;
        break;
    case 'clear':
        $result = clearCampaign(false);
        print json_encode($result);
        exit;
        break;
    case 'addIndex':
        $result = addIndex(false);
        print json_encode($result);
        exit;
        break;
    case 'delIndex':
        $result = delIndex(false);
        print json_encode($result);
        exit;
        break;
    case 'saveIndex':
        $result = saveIndex(false);
        print json_encode($result);
        exit;
        break;
    default:
        $cmps = showCampaigns();
        break;
}

function getParam($name, $default = false) {
    return isset($_GET[$name])?$_GET[$name]:$default;
}

function postParam($name, $default = false) {
    return isset($_POST[$name])?$_POST[$name]:$default;
}

function postOrGetParam($name, $default = false) {
    return isset($_POST[$name])?$_POST[$name]:getParam($name, $default);
}

function showCampaigns() {
    $cmps = new LPABCampaignList();
    $cmps->getCampaigns();

    foreach($cmps->campaigns as $cmp) {
        if(!$cmp->config['active']) {
            $cmp->config['started'] ='not started';
        }
        $cmp->getStats();
        $cmp->getResults();

        calcPercents($cmp->config['indexes']);

        foreach($cmp->config['results'] as $r) {
            calcPercents($r->config['indexes']);
        }
    }

    return $cmps;
}

function calcPercents(&$indexes) {
    $total = 0;
    foreach($indexes as $index) {
        $total += $index['prob'];
    }

    foreach($indexes as &$index) {
        if($total != 0) {
            $index['probp'] = round(100*$index['prob']/$total,2);
        }
        else {
            $index['probp'] = 0;
        }
    }
}

function createCampaign() {
    try {
        $name = trim(postOrGetParam('name'));

        if(empty($name)) {
            throw new Exception('Empty campaign name');
        }

        $cmp = new LPABCampaign();

        $cmp->config['name'] = basename($name);
        $cmp->config['active'] = false;

        $cmp->writeConfig(true);

        $result = array('ok' => true);
    }
    catch (Exception $e) {
        $result = array('ok' => false, 'err' => $e->getMessage());
    }

    return $result;
}

function startCampaign() {
    try {
        $name = trim(postOrGetParam('name'));

        if(empty($name)) {
            throw new Exception('startCampaign: Empty campaign name');
        }

        $cmp = new LPABCampaign();

        $cmp->getCampaign($name);

        $cmp->start();

        $result = array('ok' => true);
    }
    catch (Exception $e) {
        $result = array('ok' => false, 'err' => $e->getMessage());
    }

    return $result;
}

function saveIndex() {
    try {
        $name = trim(postOrGetParam('name'));

        if(empty($name)) {
            throw new Exception('asaveIndex: Empty campaign name');
        }

        $iname = trim(postOrGetParam('iname'));

        if(empty($iname)) {
            throw new Exception('saveIndex: Empty index name');
        }

        $prob = (int)postOrGetParam('prob');

        $cmp = new LPABCampaign();

        $cmp->getCampaign($name);

        $cmp->saveIndex($iname, $prob);

        $result = array('ok' => true);
    }
    catch (Exception $e) {
        $result = array('ok' => false, 'err' => $e->getMessage());
    }

    return $result;
}

function addIndex() {
    try {
        $name = trim(postOrGetParam('name'));

        if(empty($name)) {
            throw new Exception('addIndex: Empty campaign name');
        }

        $iname = trim(postOrGetParam('iname'));

        if(empty($iname)) {
            throw new Exception('addIndex: Empty index name');
        }

        $prob = (int)postOrGetParam('prob');

        $cmp = new LPABCampaign();

        $cmp->getCampaign($name);

        $cmp->addIndex($iname, $prob);

        $result = array('ok' => true);
    }
    catch (Exception $e) {
        $result = array('ok' => false, 'err' => $e->getMessage());
    }

    return $result;
}

function delIndex() {
    try {
        $name = trim(postOrGetParam('name'));

        if(empty($name)) {
            throw new Exception('addIndex: Empty campaign name');
        }

        $iname = trim(postOrGetParam('iname'));

        if(empty($iname)) {
            throw new Exception('addIndex: Empty index name');
        }

        $cmp = new LPABCampaign();

        $cmp->getCampaign($name);

        $cmp->delIndex($iname);

        $result = array('ok' => true);
    }
    catch (Exception $e) {
        $result = array('ok' => false, 'err' => $e->getMessage());
    }

    return $result;
}

function stopCampaign() {
    try {
        $name = trim(postOrGetParam('name'));

        if(empty($name)) {
            throw new Exception('Empty campaign name');
        }

        $cmp = new LPABCampaign();

        $cmp->getCampaign($name);

        $cmp->stop();

        $result = array('ok' => true);
    }
    catch (Exception $e) {
        $result = array('ok' => false, 'err' => $e->getMessage());
    }

    return $result;
}

function clearCampaign() {
    try {
        $name = trim(postOrGetParam('name'));

        if(empty($name)) {
            throw new Exception('Empty campaign name');
        }

        $cmp = new LPABCampaign();

        $cmp->getCampaign($name);

        $cmp->clearResults();

        $result = array('ok' => true);
    }
    catch (Exception $e) {
        $result = array('ok' => false, 'err' => $e->getMessage());
    }

    return $result;
}

?><!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>

  <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
  
  <!-- script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script -->
  <script type="text/javascript" src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

  <script type="text/javascript">
  $(document).ready(function() {
    $("#tabs").tabs();
  });
  </script>

  <!-- ROUTE MAP -->
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
  <!-- /ROUTE MAP -->

  <!-- AB -->
  <script type="text/javascript">
  function newCClick() {
      var cname = window.prompt('Enter campaign name');
      if ( cname != null && $.trim(cname) != '' ) {
          $('input.cname').each(function()
          {
              if($(this).val() == cname) {
                  alert('Campaign '+cname+' already exist!');
                  newCClick();
                  return false;
              }
          });

          $.ajax ({
              type    : "POST",
              url     : "/allnewgba.php?a=create&name="+cname,
              cache   : false,
              dataType: 'json',
              success : function(data)
              {
                  if(data.ok) {
                      window.location.reload();
                  }
                  else {
                      console.log(data.err);
                      alert('error(see console): '+data.err)
                  }
              },
              error: function(data) {
                  console.log('some error(1)');
              }
          })
      }
  }

  function saveIClick(me, cname) {
      var p = $(me).parents('tr.index');
      var iname = p.find('input.iname').val();
      var prob = p.find('input.iprob').val();
      saveIndex(cname, iname, prob);
  }

  function delIClick(me, cname) {
      var p = $(me).parents('tr.index');
      var iname = p.find('input.iname').val();
      delIndex(cname, iname);
  }

  function addIClick(me, cname) {
      var p = $(me).parents('tr.index');
      var iname = p.find('input.iname').val();
      var prob = p.find('input.iprob').val();

      addIndex(cname, iname, prob);
  }

  function saveIndex(cname, iname, prob) {
      $.ajax ({
          type    : "POST",
          url     : "/allnewgba.php?a=saveIndex&name="+cname,
          cache   : false,
          dataType: 'json',
          data: {iname: iname, prob: prob},
          success : function(data)
          {
              if(data.ok) {
                  window.location.reload();
              }
              else {
                  console.log(data.err);
                  alert('error(see console): '+data.err)
              }
          },
          error: function(data) {
              console.log('some error(1)');
          }
      })
  }

  function addIndex(cname, iname, prob) {
      $.ajax ({
          type    : "POST",
          url     : "/allnewgba.php?a=addIndex&name="+cname,
          cache   : false,
          dataType: 'json',
          data: {iname: iname, prob: prob},
          success : function(data)
          {
              if(data.ok) {
                  window.location.reload();
              }
              else {
                  console.log(data.err);
                  alert('error(see console): '+data.err)
              }
          },
          error: function(data) {
              console.log('some error(1)');
          }
      })
  }

  function delIndex(cname, iname) {
      $.ajax ({
          type    : "POST",
          url     : "/allnewgba.php?a=delIndex&name="+cname,
          cache   : false,
          dataType: 'json',
          data: {iname: iname},
          success : function(data)
          {
              if(data.ok) {
                  window.location.reload();
              }
              else {
                  console.log(data.err);
                  alert('error(see console): '+data.err)
              }
          },
          error: function(data) {
              console.log('some error(1)');
          }
      })
  }

  function startCClick(cname) {
      if(typeof(cname)!='undefined' && $.trim(cname) != '') {
          $.ajax ({
              type    : "POST",
              url     : "/allnewgba.php?a=start&name="+cname,
              cache   : false,
              dataType: 'json',
              success : function(data)
              {
                  if(data.ok) {
                      window.location.reload();
                  }
                  else {
                      console.log(data.err);
                      alert('error(see console): '+data.err)
                  }
              },
              error: function(data) {
                  console.log('some error(1)');
              }
          })
      }
      else {
          alert('Emprt camaign name');
      }
  }

  function stopCClick(cname) {
      if(typeof(cname)!='undefined' && $.trim(cname) != '') {
          $.ajax ({
              type    : "POST",
              url     : "/allnewgba.php?a=stop&name="+cname,
              cache   : false,
              dataType: 'json',
              success : function(data)
              {
                  if(data.ok) {
                      window.location.reload();
                  }
                  else {
                      console.log(data.err);
                      alert('error(see console): '+data.err)
                  }
              },
              error: function(data) {
                  console.log('some error(1)');
              }
          })
      }
      else {
          alert('Emprt camaign name');
      }
  }

  function clearCClick(cname) {
      if(typeof(cname)!='undefined' && $.trim(cname) != '') {
          $.ajax ({
              type    : "POST",
              url     : "/allnewgba.php?a=clear&name="+cname,
              cache   : false,
              dataType: 'json',
              success : function(data)
              {
                  if(data.ok) {
                      window.location.reload();
                  }
                  else {
                      console.log(data.err);
                      alert('error(see console): '+data.err)
                  }
              },
              error: function(data) {
                  console.log('some error(1)');
              }
          })
      }
      else {
          alert('Empty camaign name');
      }
  }

  function toggleResult(ci, ri) {
      $("#cmpResult-"+ci+'-'+ri).toggleClass('visible');
  }
  function toggleResults(ci) {
      $("#cmpResultsContainer-"+ci).toggleClass('active');
  }
  function toggleCmp(ci) {
      $("#cmpConfigContainer-"+ci).toggleClass('active');
      $("#cmpResults-"+ci).toggleClass('active');
  }
  </script>
  <!-- /AB -->

  <style type="text/css">
    iframe {
        border: none;
        width:100%;
        height:600px;
    }
    #cmps { margin-top: 10px;}
    .cmp {margin-bottom: 30px; padding: 5px; border: 1px solid #000000;}
    .cmpTitle {font: bold 14px Arial; margin-bottom: 5px; background-color: gray; padding: 3px;}
    .cmpTitle.active {background-color:rgb(173,230,117);}
    .cmpTitle .cmpSH {display: inline-block; width:30px; cursor: pointer;}
    .cmpTitle.active .cmpSH{}
    .cmpConfigContainer {margin-bottom: 10px; font: 12px Arial; display: none;}
    .cmpConfigContainer.active {display: block;}
    .cmpConfigTable {margin-bottom: 10px;}
    .cmpConfigTable th {font: bold 12px Arial;}
    .cmpResults {display: none;}
    .cmpResults.active {display: block;}
    .cmpResults.empty {display: none !important;}
    .cmpResultsTitle {font: bold 13px Arial;}
    .cmpResultTitle {font: bold 12px Arial; cursor: pointer; padding:3px; margin: 3px; background-color: lightgray;}
    .cmpResult {font: 12px Arial; display: none;}
    .cmpResult.visible {display: block;}
    .cmpResult th {font: bold 12px Arial; width: 100%;}

    html {
        background: #f4f4f4
    }

    html img {
        border: 0
    }

    .item_table td {
        border: 1px solid grey
    }

    td.hits_today a {
        color: red;
        cursor: pointer;
    }

    td.last_hits a {
        color: green
    }

    .displayroutes a {
        text-decoration: none;
        color: #05469B;
    }
  </style>
</head>
<body style="font-size:62.5%;">

<div id="tabs">
    <ul>
        <li><a href="#map"><span>map</span></a></li>
        <li><a href="#ab"><span>A/B</span></a></li>
    </ul>
    <div id="map">
        <div class='displayroutes' style='margin-left:-200px'>
            <?php $root->displayRoutes($root); ?>
        </div>
    </div>
    <div id="ab">

        <input type=button id="newc" value="new campaign" onclick="newCClick();">

        <div id="cmps">
            <?php foreach($cmps->campaigns as $ci=>$c) { ?>
                <div class="cmp" id="cmp-<?php echo $ci; ?>">
                    <div class="cmpTitle <?php echo $c->config['active']?'active':''; ?>">
                        <span><?php echo $c->config['name']; ?><input type="hidden" class="cname" value="<?php echo $c->config['name']; ?>"></span>
                        <span>(<?php echo $c->config['started']; ?>)</span>
                    <span>
                        <input type=submit name="a" value="start" onclick="startCClick('<?php echo $c->config['name']; ?>');" <?php echo $c->config['active']?'disabled="disabled"':''; ?>>
                        <input type=submit name="a" value="stop" onclick="stopCClick('<?php echo $c->config['name']; ?>');" <?php echo $c->config['active']?'':'disabled="disabled"'; ?>>
                        <input type=submit name="a" value="clear" onclick="clearCClick('<?php echo $c->config['name']; ?>');" <?php echo $c->config['active']?'':'disabled="disabled"'; ?>>
                    </span>
                        <span class="cmpSH" id="cmpSH-<?php echo $ci; ?>" onclick="toggleCmp('<?php echo $ci; ?>')">hide/show</span>
                    </div>
                    <div class="cmpConfigContainer <?php echo $c->config['active']?'active':''; ?>" id="cmpConfigContainer-<?php echo $ci; ?>">
                        <table border=1 class="cmpConfigTable">
                            <tr>
                                <th><span class="title" title="File name">Filename</span></th>
                                <th><span class="title" title="Weight">Weight</span></th>
                                <th><span class="title" title="Screenshot">Screen</span></th>
                                <th><span class="title" title="Downloads">Dl</span></th>
                                <th><span class="title" title="Installations">Wl</span></th>
                                <th><span class="title" title="Conversion">Conv, %</span></th>
                                <th><span class="title" title="B/A-1">B/A-1, %</span></th>
                                <th><span class="title" title="Confidence">C, %</span></th>
                                <th>&nbsp;</th>
                            </tr>
                            <?php foreach($c->config['indexes'] as $i) { ?>
                                <tr class="index">
                                    <td><a target="_blank" href="/d/<?php echo $i['fn']; ?>.php?self"><?php echo $i['fn']; ?></a><input type="hidden" class="iname" value="<?php echo $i['fn']; ?>"></td>
                                    <td><input size="4" class="iprob" value="<?php echo $i['prob']; ?>" <?php echo $c->config['active']?'disabled="disabled"':''; ?> autocomplete="off">(<?php echo $i['probp']; ?>%)</td>
                                    <td><a target=_blank href="/images/<?php echo $i['ss']; ?>">click</a></td>
                                    <td><?php echo $i['dl']; ?></td>
                                    <td><?php echo $i['wl']; ?></td>
                                    <td><?php echo $i['conv']; ?></td>
                                    <td><?php echo $i['ba1']; ?></td>
                                    <td><?php echo $i['c']; ?></td>
                                    <td>
                                        <input type=submit name="a" value="del" onclick="delIClick(this, '<?php echo $c->config['name']; ?>');" <?php echo $c->config['active']?'disabled="disabled"':''; ?>>
                                        <input type=submit name="a" value="save" onclick="saveIClick(this, '<?php echo $c->config['name']; ?>');" <?php echo $c->config['active']?'disabled="disabled"':''; ?>>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr class="index">
                                <td><input class="iname" value="" <?php echo $c->config['active']?'disabled="disabled"':''; ?> autocomplete="off"></td>
                                <td><input size="4" class="iprob" value="0" <?php echo $c->config['active']?'disabled="disabled"':''; ?> autocomplete="off"></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td><input type=submit name="a" value="add" onclick="addIClick(this, '<?php echo $c->config['name']; ?>');" <?php echo $c->config['active']?'disabled="disabled"':''; ?>></td>
                            </tr>
                        </table>
                    </div>
                    <div class="cmpResults <?php echo $c->config['active']?'active':''; ?> <?php echo empty($c->config['results'])?'empty':''; ?>" id="cmpResults-<?php echo $ci; ?>">
                        <div class="cmpResultsTitle">Results</div>
                        <?php if(!empty($c->config['results']))foreach($c->config['results'] as $ri=>$r) { ?>
                            <div class="cmpResultContainer" id="cmpResultContainer-<?php echo $ci.'-'.$ri; ?>">
                                <div class="cmpResultTitle" id="cmpResultTitle-<?php echo $ri; ?>" onclick="toggleResult('<?php echo $ci; ?>', '<?php echo $ri; ?>');">
                                    <?php echo $r->config['started'].' - '.$r->config['stopped']; ?>
                                </div>
                                <div class="cmpResult" id="cmpResult-<?php echo $ci.'-'.$ri; ?>">
                                    <table border=1>
                                        <tr>
                                            <th><span class="title" title="File name">Filename</span></th>
                                            <th><span class="title" title="Weight">Weight</span></th>
                                            <th><span class="title" title="Screenshot">Screen</span></th>
                                            <th><span class="title" title="Downloads">Dl</span></th>
                                            <th><span class="title" title="Installations">Wl</span></th>
                                            <th><span class="title" title="Conversion">Conv, %</span></th>
                                            <th><span class="title" title="B/A-1">B/A-1, %</span></th>
                                            <th><span class="title" title="Confidence">C, %</span></th>
                                        </tr>
                                        <?php foreach($r->config['indexes'] as $i) { ?>
                                            <tr>
                                                <td><a target="_blank" href="/d/<?php echo $i['fn']; ?>.php?self"><?php echo $i['fn']; ?></a></td>
                                                <td><?php echo $i['prob']; ?>(<?php echo $i['probp']; ?>%)</td>
                                                <td><a target=_blank href="/images/<?php echo $i['ss']; ?>">click</a></td>
                                                <td><?php echo $i['dl']; ?></td>
                                                <td><?php echo $i['wl']; ?></td>
                                                <td><?php echo $i['conv']; ?></td>
                                                <td><?php echo $i['ba1']; ?></td>
                                                <td><?php echo $i['c']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php }; ?>
        </div>
    </div>
</div>
</body>
</html>