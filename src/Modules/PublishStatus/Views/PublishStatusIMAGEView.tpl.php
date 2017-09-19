<?php

if ($pageVars["data"]["pipe"]["last_status"]===true) {
    $sclass = "fail" ; }
else if ($pageVars["data"]["pipe"]["last_status"]===false) {
    $sclass =  "pass" ; }
else {
    $sclass = "unknown" ; }


    echo $sclass ;
?>
