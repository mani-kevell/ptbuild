<?php

Namespace Model;

class UserProfileUpdateUserAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("UpdateUser") ;

    public function getData() {
        $ret["data"] = $this->createUser();
        return $ret ;
    }

    public function updateUser() {

//        $create_perms = $this->checkCreationPermissions() ;
//        if ($create_perms !== true) { return $create_perms ; }

        $valid = $this->validateUserDetails() ;
        if ($valid !== true) { return $valid ; }

        $createdUser = $this->updateTheUser() ;
        if ($createdUser !== true) { return $createdUser ; }

        $return = array(
            "status" => true ,
            "message" => "User Updated",
            "user" => $this->getOneUserDetails($this->params["create_username"]) );
        return $return ;

    }

    public function validateUserDetails() {
        if ($this->userAlreadyExists()) {
            $return = array(
                "status" => false ,
                "message" => "This username already exists" );
            return $return ; }
        $presult = $this->passwordInvalid() ;
        if ($presult !== true) {
            $return = array(
                "status" => false ,
                "message" => $presult );
            return $return ; }
        return true ;
    }

    private function userAlreadyExists() {
        $allusers = $this->getAllUserDetails() ;
        foreach ($allusers as $oneuser) {
            if ($oneuser->username == $this->params["create_username"]) {
                return true ; } }
        return false ;
    }

    private function passwordInvalid() {

        if ($this->params["update_password"] !== $this->params["update_password_match"]) {
            $return =  "Passwords must match" ;
            return $return ; }

        if (strlen($this->params["update_password"]) <3 ) {
            $return = "Password must be longer than three characters" ;
            return $return ; }

        return true ;
    }

    private function getAllUserDetails() {
        $signupFactory = new \Model\Signup();
        $signup = $signupFactory->getModel($this->params);
        $me = $signup->getLoggedInUserData() ;
        $rid = $signup->getUserRole($me->email);
        if ($rid == 1) {
            $au =$signup->getUsersData();
            return $au; }
        return array() ;
    }

    private function getOneUserDetails($username) {
        $signupFactory = new \Model\Signup();
        $signup = $signupFactory->getModel($this->params);
        $au =$signup->getUsersData();
        foreach ($au as $oneuser) {
            if ($oneuser->username == $this->params["create_username"]) {
                return $oneuser ; } }
        return array() ;
    }

    private function updateTheUser() {

        $user = new \StdClass() ;
        $user->password = $this->params["update_password"] ;

        $signupFactory = new \Model\Signup();
        $signup = $signupFactory->getModel($this->params);
        $cu = $signup->updateUser($user);

        if ($cu == false) {
            $return = array(
                "status" => false ,
                "message" => "Unable to update this user password" );
            return $return ; }

        return true ;
    }

}
