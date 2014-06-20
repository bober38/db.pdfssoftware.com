<?php

require_once "/var/www/vhosts/common/include/Goldbar.php";
LP::initWithBacktrace(debug_backtrace());

if (!function_exists('include_ga_code')) {
    function include_ga_code() {}
}

