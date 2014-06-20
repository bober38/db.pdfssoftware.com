<?php

    $domain = $_SERVER['HTTP_HOST'];

    require_once "./.BasicAuth.php";
    BasicAuth::SimpleCheck(array('admingba'=>'pe8gtynk'));

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

?><!DOCTYPE HTML>
    <html>
    <head>
        <title>pdfssoftware new gba</title>
        <style>

        </style>
        <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
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
        <style>
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

        </style>
    </head>
    <body>

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
    </body>
    </html>