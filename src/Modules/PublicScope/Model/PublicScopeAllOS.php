<?php

Namespace Model;

class PublicScopeAllOS extends Base {

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
                "name" => "Enable Public Scope for Builds?"
            ),
            "build_public_home" =>
            array(
                "type" => "boolean",
                "optional" => true,
                "name" => "Make Build Home Page Public?"
            ),
            "build_public_features" =>
            array(
                "type" => "boolean",
                "optional" => true,
                "name" => "Make Build Features Public?"
            ),
            "build_public_history" =>
            array(
                "type" => "boolean",
                "optional" => true,
                "name" => "Make Build History Public?"
            ),
            "build_public_reports" =>
            array(
                "type" => "boolean",
                "optional" => true,
                "name" => "Allow Making Individual Reports Public?"
            ),
            "build_public_releases" =>
            array(
                "type" => "boolean",
                "optional" => true,
                "name" => "Allow Making Individual Releases Public?"
            ),
        );
        return $ff ;
    }

    public function getEventNames() {
        return array_keys($this->getEvents());
    }

    // @todo need thee cron execution event to do this
    public function getEvents() {
        $ff = array(
            "getPublicLinks" => array(
                "getPublicPipelines",
            ),
        );
        return $ff ;
    }

    public function getPublicPipelines() {
        $this->params["echo-log"] = true ;
        $this->params["php-log"] = true ;
        $pipelines = $this->getPipelines() ;
        $public_pipelines = array() ;
        foreach ($pipelines as $pipeline_slug => $pipeline) {
            if ($pipeline["settings"]["PublicScope"]["enabled"] == "on") {
                if ($pipeline["settings"]["PublicScope"]["build_public_home"] == "on") {
                    $public_pipelines[] = $pipeline ; } } }
        $public_pipelines_html = $this->getHTMLFromPipelines($public_pipelines) ;
        \Model\RegistryStore::setValue('public_links', $public_pipelines_html) ;
//        var_dump($public_pipelines_html) ;
//        die() ;
        return $public_pipelines ;
    }

    public function getPipelines() {
        $pipelineFactory = new \Model\Pipeline() ;
        $pipeline = $pipelineFactory->getModel($this->params);
        $pipelines = $pipeline->getPipelines();
        return $pipelines ;
    }

    public function getHTMLFromPipelines($public_pipelines) {
        $html = "" ;
        if (count($public_pipelines)>0) {
            $html .= "<h3><strong>Public Builds:</strong></h3>" ;
            foreach ($public_pipelines as $public_pipeline) {
                $html .= "<div>" ;
                $html .= "    <a target='_blank' href='index.php?control=BuildHome&action=show&item={$public_pipeline["project-slug"]}' > " ;
                $html .= "        {$public_pipeline["project-name"]} " ;
                $html .= "    </a>" ;
                $html .= "</div>" ; } }
        return $html ;
    }

}