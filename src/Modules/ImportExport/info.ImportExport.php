<?php

Namespace Info;

class ImportExportInfo extends PTConfigureBase {

    public $hidden = false;

    public $name = "Import or Export a build configuration after Pipeline Save";

    public function _construct() {
        parent::__construct();
    }

    public function routesAvailable() {
        return array("ImportExport" => array_merge(parent::routesAvailable(), array('import', 'export') ) );
    }

    public function routeAliases() {
        return array("importexport" => "ImportExport", "import-export" => "ImportExport");
    }

    public function helpDefinition() {
       $help = <<<"HELPDATA"
    This extension imports or exports the configuration of a build pipeline to another location when saving.
    It provides code functionality, but no extra CLI commands.

    importexport
    
        - import
        Import a Build Job
        example: ptconfigure importexport import --yes --guess
            --source=/path/to/source/job/directory
            --force

        - export
        Export a Build Job
        example: ptconfigure importexport export --yes --guess
            --target=/path/to/save/job

HELPDATA;
      return $help ;
    }

}
