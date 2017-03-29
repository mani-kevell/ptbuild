<?php

Namespace Model;

class SystemDetectionGenerateAllOS extends Base {

    public function __construct() {
    }

    public function generate() {

        $sys = new \Model\SystemDetectionAllOS() ;
        $target_file = PFILESDIR.PHARAOH_APP.DS.PHARAOH_APP.DS."system_detection" ;

        $ray = array (
            "os" => $sys->os,
            "distro" => $sys->os,
            "linuxType" => $sys->linuxType,
            "version" => $sys->version,
            "architecture" => $sys->architecture,
            "hostName" => $sys->hostName,
        );
        $json = json_encode($ray) ;
        file_put_contents($target_file, $json) ;
    }


}
