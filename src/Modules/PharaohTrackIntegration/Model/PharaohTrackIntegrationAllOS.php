<?php

Namespace Model;

class PharaohBuildIntegrationAllOS extends Base {

    // Compatibility
    public $os = array("any") ;
    public $linuxType = array("any") ;
    public $distros = array("any") ;
    public $versions = array("any") ;
    public $architectures = array("any") ;

    // Model Group
    public $modelGroup = array("Default") ;

    public function getSettingTypes() {
        return array_keys($this->getSettingFormFields());
    }

    public function getSettingFormFields() {
        $ff = array(
            "enabled" =>
            array(
                "type" => "boolean",
                "optional" => true,
                "name" => "Enable Integrating with Pharaoh Build job/s?"
            ),

            "fieldsets" => array(
                "build_jobs" => array(
                    "url" =>
                        array(
                            "type" => "text",
                            "name" => "Build Job URL",
                            "slug" => "joburl"),
                    "title" =>
                        array(
                            "type" => "text",
                            "name" => "Job Title",
                            "slug" => "jobtitle"),
                )
            ),

        );
        return $ff ;
    }

}