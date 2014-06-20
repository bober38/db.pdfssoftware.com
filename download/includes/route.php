<?php

/**
 * Класс роутинга
 */


if ( ! defined('ROUTE_PATH'))
{
    $stack = get_included_files();
    define('ROUTE_PATH', dirname($stack[0]) . DIRECTORY_SEPARATOR);
}

class Route {

    protected $_config_file = '.routes.yml';

    protected $_entry_point = '';

    /**
     * keys to retrieve from init config
     */
    public static $init_keys = array(
        'ctid',     // CTID for Conduit|Perion
        'url',      // URL for Conduit|Perion
        'code',     // IAC code
        'tbName',   // IAC tbName
        'tbDomain', // IAC tbDomain
    );


    protected $_yaml_source = '';

    protected $_yaml = array();

    protected $_init = array();


    public $country = '';

    public $locale = '';

    public $browser = '';

    public $flag = '';


    public $install;


    public function __construct($entry_point = 'index', $yaml_source = null)
    {
        $this->_entry_point = $entry_point ? $entry_point : 'index';

        if ($yaml_source === null)
        {
            if (file_exists(ROUTE_PATH . $this->_config_file))
            {
                $yaml_source = file_get_contents(ROUTE_PATH . $this->_config_file);
            }
            else
            {
                $yaml_source = '';
            }
        }

        $this->_yaml_source = $yaml_source;

        $this->_yaml = $this->_parse($yaml_source);

        $this->_init();

        $this->determine_browser();

        $this->determine_country();

        $this->determine_locale();

        $this->determine_flag();
    }

    protected function user_info()
    {
        $user_info = array();
        $user_info['country'] = $this->country;
        $user_info['browser'] = $this->browser;
        $user_info['locale'] = $this->locale;
        $user_info['flag'] = $this->flag;

        return $user_info;
    }

    public function run()
    {
        $include_file = $this->get_include_file();

        if ($include_file && file_exists($include_file))
        {
            if ( ! in_array(ROUTE_PATH . $include_file, get_included_files()))
            {
                $this->include_lp($include_file, $this->last_matches_path());
            }
        }

        return false;
    }

    protected function get_include_file()
    {
        $include_file = $this->match($this->_entry_point, $this->user_info());

        /**
         * получим инсталлятор для страницы
         */
        $this->install = $this->get_installation($include_file, $this->user_info());

        return $include_file;
    }

    public function install_by_set($setname)
    {
        $this->install = $this->get_installation_by_id($setname, $this->user_info());
    }

    public function download()
    {
        $this->get_include_file();

        switch ($this->install->type)
        {
            case 'iac':
                // not implemented
                break;
            case 'conduit':
            case 'perion':
                echo 'Location: ' . $this->install->url;
                break;
        }

    }


    public function download_url()
    {
        #$query = http_build_query(array_merge($_GET, array('id' => $this->_entry_point)));
        $query = $this->install->set;
        return 'rdownload.php?' . $query;
    }

    protected function include_lp($file, $reason)
    {
        $route = $this;
        include $file;
        exit;
    }


    public function _init()
    {
        $init_source = $this->_get('init', true);

        $this->_init = $init_source;
    }


    public function determine_browser()
    {
        if ( ! $this->browser)
        {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome'))
            {
                $this->browser = 'Chrome';
            }
            else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari'))
            {
                $this->browser = 'Safari';
            }
            else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Gecko'))
            {
                if (strpos($_SERVER['HTTP_USER_AGENT'], 'Netscape'))
                {
                    $this->browser = 'Netscape (Gecko/Netscape)';
                }
                else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox'))
                {
                    $this->browser = 'Firefox';
                }
                else
                {
                    $this->browser = 'Mozilla (Gecko/Mozilla)';
                }
            }
            else if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE'))
            {
                $this->browser = 'IE';
            }
            else if (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== false)
            {
                $this->browser = 'Opera';
            }
            else
            {
                $this->browser = 'Other browsers';
            }
        }

        return $this->browser;
    }

    public function determine_country()
    {
        if ( ! $this->country)
        {
            if (isset($_SERVER['COUNTRY']))
            {
                $this->country = $_SERVER['COUNTRY'];
            }
        }

        return $this->country;
    }

    public function determine_locale($default = 'en')
    {
        if ( ! $this->locale)
        {
            $this->locale = $default;
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            {
                $browser_languages = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
                if ($browser_languages)
                {
                    while ($browser_languages)
                    {
                        $cur_locale = array_shift($browser_languages);
                        $cur_locale_and_q = explode(';', $cur_locale);
                        if (isset($cur_locale_and_q[0]))
                        {
                            $this->locale = substr(strtolower(trim($cur_locale_and_q[0])), 0, 2);
                            break;
                        }
                    }
                }
            }
        }

        return $this->locale;
    }

    public function determine_flag()
    {
        if ( ! $this->flag)
        {
            if (isset($_REQUEST['flag']))
            {
                $this->flag = $_REQUEST['flag'];
            }
        }

        return $this->flag;
    }

    public function get_installation($page, $user = array())
    {
        $result = new Route_Install;

        $result->page = $page;

        foreach ($this->_init as $group => $sets)
        {
            foreach ($sets as $setname => $set)
            {
                if (in_array($page, $set['files']))
                {
                    $result->type = $group;
                    $result->set = $setname;

                    foreach (static::$init_keys as $key)
                    {
                        if (isset($set[$key]))
                        {
                            $this->_no_match = true;
                            $result->$key = $this->common_match($set[$key], $user);
                            $this->_no_match = false;
                        }
                    }

                    return $result;
                }
            }
        }
    }

    public function get_installation_by_id($id, $user = array())
    {
        $result = new Route_Install;

        $result->page = $page;

        foreach ($this->_init as $group => $sets)
        {
            foreach ($sets as $setname => $set)
            {
                if ($setname == $id)
                {
                    $result->type = $group;
                    $result->set = $setname;

                    foreach (static::$init_keys as $key)
                    {
                        if (isset($set[$key]))
                        {
                            $this->_no_match = true;
                            $result->$key = $this->common_match($set[$key], $user);
                            $this->_no_match = false;
                        }
                    }

                    return $result;
                }
            }
        }
    }


    public function common_match($rules, $user = array())
    {
        foreach ((array)$rules as $rule)
        {
            $return = $this->parse_rule($rule, $user);
            if ($return)
            {
                return $return;
            }
        }
    }


    protected function _parse($yaml_source)
    {
        if ($this->_check_yaml_source($yaml_source))
        {
            return yaml_parse($yaml_source);
        }
        return array();
    }

    protected function _check_yaml_source($yaml_source)
    {
        $yaml_source = preg_replace('/^#.*$/m', '', $yaml_source);
        $yaml_source = trim($yaml_source);

        return $yaml_source && preg_match('/^\w+:/m', $yaml_source);
    }

    public function _get($key, $drop_key = false)
    {
        $value = isset($this->_yaml[$key]) ? $this->_yaml[$key] : '';

        if ($drop_key && isset($this->_yaml[$key]))
        {
            unset($this->_yaml[$key]);
        }

        return $value;
    }

    public function rules($entry_point)
    {
        return $this->_get($entry_point);
    }

    protected $_last_matches_path = array();

    public function match($entry_point, $user = array())
    {
        $this->_last_matches_path = array();

        foreach ((array)$this->rules($entry_point) as $rule)
        {
            $return = $this->parse_rule($rule, $user);
            if ($return)
            {
                return $return;
            }
        }
    }

    public function last_matches_path()
    {
        return implode(':', $this->_last_matches_path);
    }

    protected $_no_match = false;

    public function parse_rule($rule, $user)
    {
        if (is_string($rule))
        {
            return $rule;
        }
        else
        {
            foreach ((array)$rule as $key => $value)
            {
                if (is_int($key))
                {
                    $return = $this->parse_rule($value, $user);
                    if ($return !== null)
                    {
                        return $return;
                    }
                }

                $key = $this->parse_rule_key($key);

                if (isset($user[$key[0]]))
                {
                    $in = in_array(strtolower($user[$key[0]]), $key[2]);
                    if ($key[1] == 'not')
                    {
                        $in = ! $in;
                    }

                    if ($in)
                    {
                        if (is_string($value))
                        {
                            if ( ! $this->_no_match)
                            {
                                $this->_last_matches_path[] = $key[0] . ':' . ($key[1] == 'not' ? '!' : '') . implode(',', $key[2]);
                                //$page = $this->get_installation($value, $user);
                                //$this->_last_matches_path[] = $page->type;
                            }
                            return $value;
                        }
                        else
                        {
                            $return = $this->parse_rule($value, $user);
                            if ($return !== null)
                            {
                                return $return;
                            }
                        }
                    }
                    else
                    {
                    }
                }


            }

        }
    }

    public function parse_rule_key($key)
    {
        $a = explode(' ', $key, preg_match('#^\w+ +not +#i', $key) ? 3 : 2);
        if (count($a) == 2)
        {
            return array(strtolower($a[0]), 'in', array_map('strtolower', array_map('trim', explode(',', $a[1]))));
        }
        else if (count($a) == 3)
        {
            return array(strtolower($a[0]), $a[1], array_map('strtolower', array_map('trim', explode(',', $a[2]))));
        }
        return array('', 'in', '');
    }

    public function entry_points()
    {
        return array_keys($this->_yaml);
    }


}


class Route_Install {

    public $page;

    public $type;

    public $set;

    public $code;

    public $ctid;

    public $url;

    public $tbName;

    public $tbDomain;

}