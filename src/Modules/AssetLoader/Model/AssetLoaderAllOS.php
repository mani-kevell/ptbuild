<?php

Namespace Model;

class AssetLoaderAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function getData() {
        $ret["asset"] = $this->getAsset();
        $ret["mime-type"] = $this->getMimeType();
        $ret["asset-filename"] = $this->params["asset"];
        return $ret ;
    }

    public function getAsset() {
        $type = $this->params["type"] ;
        $asset = $this->params["asset"] ;
        $path = $this->ensureTrailingSlash($this->params["path"]) ;
        if (isset($this->params["location"]) && $this->params["location"] == 'root') {
            $assPath = PFILESDIR.PHARAOH_APP.DS.$path.$asset ; }
        else {
            $modDir = \Core\AutoLoader::findModulePath($this->params["module"]) ;
            $assPath = $modDir.DS.'Assets'.DS.$type.$asset ; }
        $r = null ;
//        var_dump('apx:', $assPath) ;
        if (file_exists($assPath)) {
            $r = file_get_contents($assPath); }
        return $r ;
    }

    public function getMimeType() {
        $out = "" ;
        $type = $this->params["type"] ;
        switch ($type) {
            case "binary" :
                $out = "application/octet-stream";
                break ;
            case "raw" :
                $out = "text/plain";
                break ;
            case "css" :
                $out = "text/css";
                break ;
            case "image" :
                $out = $this->getImageMimeType();
                break ;
            default :
                break ; }
        return $out ;
    }

    private function getImageMimeType() {
        $ext = substr($this->params["asset"], strrpos($this->params["asset"], ".")+1 ) ;
		$mime="";
		if ($ext == "jpg") { $ext = "jpeg" ; }
        if (in_array($ext, array("png", "jpeg", "gif"))) { $mime = 'image/'.$ext ; }
        else if ($ext == "svg") { $mime = 'image/svg+xml' ; }
        return $mime ;
    }

}
