<?php

include_once('.BasicStat.php');

function logs($data) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

class LPABCampaign {
    public $config = false;


    public function checkConfig() {
        if(empty($this->config['name'])) {
            throw new Exception('checkConfig: Empty campaign name');
        }

        $this->config['name'] = basename($this->config['name']);

        if(!isset($this->config['active'])) {
            $this->config['active'] = false;
        }
        else {
            $this->config['active'] = (bool)$this->config['active'];
        }

        if(!is_array($this->config['indexes'])) {
            $this->config['indexes'] = array();
        }
        else {
            $indexes = array();
            foreach($this->config['indexes'] as $index) {
                if(!empty($index['fn']) && !array_key_exists($index['fn'], $indexes)) {
                    $index['marker'] = md5($index['marker']);
                    $index['prob'] = (int)$index['prob'];
                    $indexes[$index['fn']] = $index;
                }
            }
            $this->config['indexes'] = $indexes;
        }

    }

    public function readConfig($file) {
        if(file_exists(dirname(__FILE__).'/'.$file)) $str = file_get_contents(dirname(__FILE__).'/'.$file);

        if(empty($str)) {
            throw new Exception('Error reading file '.$file);
        }

        $this->config = json_decode($str, true);
        try {
            $this->checkConfig();
        }
        catch(Exception $e) {
            $this->config = false;
            throw new Exception($e->getMessage());
        }
    }

    public function getResults() {
        $results = new LPABCampaignList('lpabcmpresult-'.$this->config['name']);
        $results->getCampaigns();
        foreach($results->campaigns as $cmp) {
            $cmp->calcStats();
        }
        $this->config['results'] = $results->campaigns;
    }

    public function getCampaign($name) {
        $name = trim(basename($name, '.php'));
        if(empty($name)) {
            throw new Exception('getCampaign: Empty campaign name');
        }

        $this->readConfig('./download/.gba/lpabcmpcfg-'.$name);
    }

    public function writeConfig($isNew = true) {
        $this->checkConfig();
        $fn = './download/.gba/lpabcmpcfg-'.$this->config['name'];

        if($isNew && file_exists(dirname(__FILE__).'/'.$fn)){
            throw new Exception('Campaign already exist: '.$this->config['name']);
        }

        if(false === file_put_contents(dirname(__FILE__).'/'.$fn, json_encode($this->config))) {
            throw new Exception('Error writing file '.$fn);
        }

        foreach($this->config['indexes'] as $index) {
            $fn = './download/.gba/lpabindexmap-'.$index['fn'];
            file_put_contents(dirname(__FILE__).'/'.$fn, $this->config['name']);
        }
    }

    public function start() {
        if($this->config['active']) {
            throw new Exception('Campaign '.$this->config['name'].' is already started');
        }

        if(empty($this->config['indexes'])) {
            throw new Exception('Campaign '.$this->config['name'].' has no indexes');
        }

        //@todo take screenshots
        foreach($this->config['indexes'] as &$index) {
            $index['ss'] = '';
        }

        $this->config['active'] = true;
        $this->config['started'] = date(DATE_RFC822);
        $this->config['stopped'] = false;

        $this->writeConfig(false);
    }

    public function saveIndex($fn, $prob = 0) {
        if($this->config['active']) {
            throw new Exception('Campaign '.$this->config['name'].' is already started');
        }

        $fn = basename($fn);

        $fullFn = './download/'.$fn.'.php';

        if(!file_exists(dirname(__FILE__).'/'.$fullFn)) {
            throw new Exception('Index does not exist: '.$fullFn);
        }

        $index = new LPABIndex($fullFn);
        if(!$index->campaign || $index->campaign->config['name'] != $this->config['name']) {
            throw new Exception('Index is not in campaign');
        }

        if(isset($this->config['indexes'][$fn])) $this->config['indexes'][$fn]['prob'] = $prob;

        $this->writeConfig(false);

        $this->config['indexes'][$fn] = array (
            'fn' => $fn,
            'prob' => (int)$prob,
            'marker' => md5($fn)
        );

        $this->writeConfig(false);
    }

    public function addIndex($fn, $prob = 0) {
        if($this->config['active']) {
            throw new Exception('Campaign '.$this->config['name'].' is already started');
        }

        $fn = basename($fn);

        $fullFn = './download/'.$fn.'.php';

        if(!file_exists(dirname(__FILE__).'/'.$fullFn)) {
            throw new Exception('Index does not exist: '.$fullFn);
        }

        $index = new LPABIndex($fullFn);
        if($index->campaign) {
            if($index->campaign->config['name'] == $this->config['name']) {
                throw new Exception('Index is already in campaign');
            }
            else {
                throw new Exception('Index is already in other campaign: '.$index->campaign->config['name']);
            }
        }

        $this->config['indexes'][$fn] = array (
            'fn' => $fn,
            'prob' => (int)$prob,
            'marker' => md5($fn)
        );

        $this->writeConfig(false);
    }

    public function delIndex($fn) {
        if($this->config['active']) {
            throw new Exception('Campaign '.$this->config['name'].' is already started');
        }

        $fn = basename($fn);

        $fullFn = './download/'.$fn.'.php';

        $index = new LPABIndex($fullFn);
        if(!$index->campaign || $index->campaign->config['name'] != $this->config['name']) {
            throw new Exception('Index is not in campaign');
        }

        if(isset($this->config['indexes'][$fn])) unset($this->config['indexes'][$fn]);
        $fn = "./download/.gba/lpabindexmap-{$fn}";

        $this->writeConfig(false);

        @unlink($fn);
    }

    public function stop() {
        if(!$this->config['active']) {
            throw new Exception('Campaign '.$this->config['name'].' is not started yet');
        }

        $this->config['active'] = false;
        $this->config['stopped'] = date(DATE_RFC822);

        $this->getStats();

        $this->saveResults();

        $this->clearResults();

        $this->config['started'] = false;

        $this->writeConfig(false);
    }

    public function saveResults() {
        $this->checkConfig();
        $fn = './download/.gba/lpabcmpresult-'.$this->config['name'].'-'.uniqid();//.$this->config['name'].'-'.$this->config['started'].'-'.$this->config['stopped'];

        if(false === file_put_contents(dirname(__FILE__).'/'.$fn, json_encode($this->config))) {
            throw new Exception('Error writing file '.$fn);
        }
    }

    public function clearResults() {
        $mc = new Memcached();
        if($mc->addServer("localhost", 11211)) {
            foreach($this->config['indexes'] as $index) {
                $mc->delete("gba_{$this->config['name']}_{$index['marker']}");
                $mc->delete("gba_{$this->config['name']}_{$index['marker']}_conv");
            }
        }
    }

    public function getStats() {
        $mc = new Memcached();
        if($mc->addServer("localhost", 11211)) {
            foreach($this->config['indexes'] as  &$index) {
                $index['dl'] = $mc->get("gba_{$this->config['name']}_{$index['marker']}");
                $index['wl'] = $mc->get("gba_{$this->config['name']}_{$index['marker']}_conv");
            }

            $this->calcStats();
        }
        else {
            foreach($this->config['indexes'] as  &$index) {
                $index['dl'] = '---';
                $index['wl'] = '---';
                $index['conv'] = '---';
                $index['ba1'] = '---';
                $index['c'] = '---';
            }
        }
    }

    public function calcStats() {
        self::calcStatsStatic($this->config['indexes']);
    }

    public static function calcStatsStatic(&$indexes) {
        $first = true;
        foreach($indexes as  &$index) {
            if(isset($index['dl']) && isset($index['wl']) && $index['wl']!='---' && $index['dl']!='---') {
                $index['conv'] = (int)$index['dl']?((100*$index['wl'])/$index['dl']):false;
                if($first) {
                    $origD = $index['dl'];
                    $origI = $index['wl'];
                    $origC = $index['conv'];
                    $first = false;
                }
                else {
                    $ba1 = ba1($origC, $index['conv']);
                    $index['ba1'] = (false == $ba1)?'---':$ba1;
                    $c = confidence($origD, $origI, $index['dl'], $index['wl']);
                    $index['c'] = (false == $c)?'---':$c;
                }
                $index['conv'] = (false == $index['conv'])?'---':round($index['conv'], 2);
            }
            else {
                $index['dl'] = '---';
                $index['wl'] = '---';
                $index['conv'] = '---';
                $index['ba1'] = '---';
                $index['c'] = '---';
            }
        }
    }

    public function randomIndex() {
        if(!empty($this->config['indexes'])) {
            $total = 0;
            foreach($this->config['indexes'] as $index) {
                $total += $index['prob'];
            }

            $r = rand(0, $total - 1);

            $total = 0;
            $current = false;
            foreach($this->config['indexes'] as $index) {
                $current = $index;
                $total += $index['prob'];
                if($r < $total) return $index['fn'];
            }
            return $current['fn'];
        }
        return false;
    }

}

class LPABCampaignList {

    public $prefix = 'lpabcmpcfg';
    public $campaigns = array();

    public function __construct($prefix = 'lpabcmpcfg') {
        $this->prefix = $prefix;
    }

    public function getCampaigns() {
        $flist = glob('./download/.gba/'.$this->prefix.'-*');

        if(!empty($flist)) {
            foreach($flist as $file) {
                $cmp = new LPABCampaign();
                try {
                    $cmp->readConfig($file);
                    $this->campaigns[] = $cmp;
                }
                catch (Exception $e) {
                    //log
                }
            }
        }
    }
}

class LPABIndex {

    public $campaign = false;
    public $marker = false;
    public $file = false;

    function __construct($file) {
        $this->file = basename($file, '.php');
        if($cmp = $this->getCampaign())
        {
            $this->campaign = $cmp;
            $this->marker = $cmp->config['indexes'][$this->file]['marker'];
        }
        else {
            $this->file = false;
        }
    }

    private function getCampaign() {
        //lpabindexmap-indexFileName must contain campaign's name
        $fn = "./download/.gba/lpabindexmap-{$this->file}";
        if(file_exists(dirname(__FILE__).'/'.$fn)) $str = file_get_contents(dirname(__FILE__).'/'.$fn);

        if(!empty($str)) {
            $cmp = new LPABCampaign();
            $cmp->getCampaign($str);//read cmp config

            if(isset($cmp->config['indexes'][$this->file])) {//check if cmp actually contains this indexfile
                return $cmp;
            }
        }

        return false;
    }

    public function setCookie() {
        if($this->file) {
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

            if (!$domain) {
                return false;
            }

            setcookie("gbacam", $this->campaign->config['name'], time()+3600, '/', ".$domain", false, true);
            setcookie("gbaf", $this->marker, time()+3600, '/', ".$domain", false, true);

            return true;
        }
        return false;
    }

    public function randomIndex() {
        if(!$this->campaign) return false;

        $fn = $this->campaign->randomIndex();


        if(!empty($fn)) {
            $this->file = $fn;
            $this->marker = $this->campaign->config['indexes'][$this->file]['marker'];
            return $fn;
        }
        else {
            return false;
        }
    }

}