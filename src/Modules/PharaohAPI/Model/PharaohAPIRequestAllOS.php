<?php

Namespace Model;

class PharaohAPIRequestAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Request") ;

    public function performAPIRequest() {
        // if api is enabled - nah dont need to enable api to make outbound requests
        // get the instance url for the request
        // get the key for the request
        // get the API function call for the request
        // get the API function parameters for the request
        // make the request to the API of target
        // either return an error or a PHP array from the JSON data
        return $api_request_result ;
    }

    protected function findInstanceURL($message) {
        return array (
            'status' => 'error',
            'message' => $message,
        ) ;
    }

}
