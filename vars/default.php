<?php

$variables = array() ;
$variables['application_slug'] = 'jobjacker' ;
$variables['description'] = 'Job Jacker Client' ;
$variables['subdomain'] = 'www' ;
$variables['webclientsubdomain'] = 'www' ;
$variables['server_subdomain'] = 'server' ;
$variables['domain'] = 'jobjacker.com' ;
$variables['friendly_app_slug'] = 'JobJacker' ;
$variables['desktop_app_slug'] = $variables['friendly_app_slug'] ;
$variables['random_port_suffix'] = '39' ;

if (ISOPHP_EXECUTION_ENVIRONMENT == 'UNITER') {
    \ISOPHP\js_core::$console->log('before loop') ;
    \ISOPHP\js_core::$console->log($variables) ;
    $temp_config = \Model\Configuration::$config ;
    foreach ($variables as $this_key => $this_value) {
        \ISOPHP\js_core::$console->log('this key is ' . $this_key) ;
        \ISOPHP\js_core::$console->log('this value is ' . $this_value) ;
        \ISOPHP\js_core::$console->log('config is ', $temp_config) ;
        $temp_config[$this_key] = $this_value ;
    }
    \Model\Configuration::$config = $temp_config ;
    \ISOPHP\js_core::$console->log('registry dump') ;
    \ISOPHP\js_core::$console->log(\Model\Configuration::$config) ;
}
