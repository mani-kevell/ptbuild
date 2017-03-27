<?php

echo "Executing Queued Builds\n" ;

if (count($pageVars["data"]["queued"])>0) {
    foreach ($pageVars["data"]["queued"] as $pipe) {
        // echo $pipe["project-name"]."\n" ;
    }
    foreach ($pageVars["data"]["executions"] as $pipeTailSlug => $pipeTailDetails) {
        echo "Slug: ".$pipeTailSlug.", Name: ".$pipeTailDetails["name"].", Run ID ".$pipeTailDetails["result"]."\n" ; } }
else {
    echo "No Builds queued to run now\n" ; }