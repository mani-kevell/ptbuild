<div class="container" id="wrapper">
    
        <div class="col-lg-12">

            <div class="well well-lg ">

                <div id="page_sidebar" class="navbar-default col-sm-2 sidebar" role="navigation">
                    <div class="sidebar-nav ">
                        <div class="sidebar-search">
                            <button class="btn btn-success" id="menu_visibility_label" type="button">
                                Show Menu
                            </button>
                            <i class="fa fa-1x fa-toggle-off hvr-grow" id="menu_visibility_switch"></i>
                        </div>
                        <ul class="nav in" id="side-menu">

                            <?php

                            if ($pageVars["data"]["login_enabled"] == false || in_array($pageVars["data"]["current_user_role"], array("1", "2", "3"))) {

                            ?>

                    <li>
                        <a href="/index.php?control=Index&amp;action=show" class="hvr-bounce-in">
                            <i class="fa fa-dashboard fa-fw hvr-bounce-in"></i> Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="/index.php?control=BuildList&amp;action=show" class="hvr-bounce-in">
                            <i class="fa fa-bars fa-fw hvr-bounce-in"></i> All Pipelines
                        </a>
                    </li>

                    <?php

                    }

                    ?>
                    <?php

                    //if ($pageVars["data"]["login_enabled"] == false || in_array($pageVars["data"]["current_user_role"], array("1", "2", "3"))) {

                    ?>
                    <li>
                        <a href="index.php?control=BuildMonitor&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>" class="hvr-bounce-in">
                            <i class="fa fa-bar-chart-o hvr-bounce-in"></i> Monitors
                        </a>
                    </li>
                    <li>
                        <a href="index.php?control=PipeRunner&action=history&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>"class="hvr-bounce-in">
                            <i class="fa fa-history fa-fw hvr-bounce-in""></i> History <span class="badge"></span>
                        </a>
                    </li>

                    <?php

                    //}

                    if ($pageVars["data"]["login_enabled"] == false || in_array($pageVars["data"]["current_user_role"], array("1", "2"))) {

                        ?>
                        <li>
                            <a href="index.php?control=BuildConfigure&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>" class="hvr-bounce-in">
                                <i class="fa  fa-cog fa-fw hvr-bounce-in"></i> Configure
                            </a>
                        </li>

                        <li>
                            <a href="index.php?control=Workspace&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>" class="hvr-bounce-in">
                                <i class="fa fa-folder-open-o hvr-bounce-in"></i> Workspace
                            </a>
                        </li>
                        <li>
                            <a href="index.php?control=BuildHome&action=delete&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>" class="hvr-bounce-in">
                                <i class="fa fa-trash fa-fw hvr-bounce-in""></i> Delete
                            </a>
                        </li>
                        <li>
                            <a href="index.php?control=PipeRunner&action=start&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>" class="hvr-bounce-in">
                                <i class="fa fa-sign-in fa-fw hvr-bounce-in""></i> Run Now
                            </a>
                        </li>


                        <?php

                    }

                    ?>
                    </ul>
                </div>
            </div>
           
            <div class="row clearfix no-margin">
                <?php

                if ($pageVars["data"]["login_enabled"] == false ||
                    in_array($pageVars["data"]["current_user_role"], array("1", "2"))) {
                    $show_build_button = true ;
                    $row_class = "leftCell" ;
                } else {
                    $show_build_button = false ;
                    $row_class = "fullRow" ;
                }

                if ($pageVars["data"]["pipeline"]["last_status"]===true) { $sclass = "good" ;}
                else if ($pageVars["data"]["pipeline"]["last_status"]===false) { $sclass =  "bad" ; }
                else { $sclass = "unknown" ; }

                ?>
                <div class="<?php echo $row_class ; ?>">
                    <div class="fullRow">
                        <h3 class="text-uppercase text-light "><strong>Pipeline: </strong><?php echo $pageVars["data"]["pipeline"]["project-name"] ; ?></h3>
                    </div>
                    <div class="fullRow">
                        <span>
                            <strong>Slug:</strong>
                            <?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>
                        </span>
                    </div>
                    <div class="fullRow">
                        <span>
                            <strong>Description:</strong>
                            <?php echo $pageVars["data"]["pipeline"]["project-description"] ; ?>
                        </span>
                    </div>
                </div>
                <?php

                if ($show_build_button == true) { ?>

                    <div class="rightCell">
                        <a class="buildNowLarge" href="index.php?control=PipeRunner&action=start&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>">
                        <span>
                            Build Now
                        </span>
                        </a>
                    </div>

                <?php
                }
                ?>

            </div>
            <hr>
            <div class="row clearfix no-margin build-home-properties">

                <div class="pipe-now-status-block pipe-block">
                    <h4 class="propertyTitle">Build Status Currently:</h4>

                    <div class="current_status current_status_<?php echo $sclass ; ?>">
                        <h3>
                            Status:
                        <?php
                            echo ucfirst($sclass) ; ?>
                        </h3>
                    </div>

                </div>

                <?php

                if ($show_build_button == true) { ?>

                    <div class="alert alert-info">
                        <h4>Running Builds </h4>
                        <div id="runningBuilds">
                            <p>
                                No builds of this pipeline currently being executed
                            </p>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h4>Queued Builds </h4>
                        <div id="queuedBuilds">
                            <p>
                                No builds of this pipeline currently queued
                            </p>
                        </div>
                    </div>

                <?php
                }
                ?>

                <?php
                if ($pageVars["data"]["login_enabled"] == false ||
                    in_array($pageVars["data"]["current_user_role"], array("1", "2", "3")) ||
                    (
                        $pageVars["data"]["pipeline"]["settings"]["PublicScope"]["enabled"] === "on" &&
                        $pageVars["data"]["pipeline"]["settings"]["PublicScope"]["build_public_features"] === "on"
                    )) {                ?>

                <div class="pipe-features-block pipe-block">
                    <h4 class="propertyTitle">Build Features:</h4>
                    <div class="col-sm-12">
                    <?php
                    if (isset($pageVars["data"]["features"]) &&
                        count($pageVars["data"]["features"])>0 ) {
                        foreach ($pageVars["data"]["features"] as $build_feature) {
//                            var_dump($build_feature);
                            if (
                                (isset($build_feature["hidden"]) && $build_feature["hidden"] != true)
                                || !isset($build_feature["hidden"]) ) {
                                echo '<div class="build-feature">' ;
                                echo '<a target="_blank" href="'.$build_feature["model"]["link"].'">' ;
                                echo  '<h3>'.$build_feature["model"]["title"].'</h3>' ;
                                echo  '<img src="'.$build_feature["model"]["image"].'" />' ;
                                echo "</a>" ;
                                echo '</div>' ; } } }
                    ?>
                    </div>
                </div>

                <?php
                }

                if ($pageVars["data"]["login_enabled"] == false ||
                    in_array($pageVars["data"]["current_user_role"], array("1", "2", "3")) ||
                    (
                        $pageVars["data"]["pipeline"]["settings"]["PublicScope"]["enabled"] == "on" &&
                        $pageVars["data"]["pipeline"]["settings"]["PublicScope"]["build_public_history"] == "on"
                    )) {
                ?>

                <div class="pipe-history-block pipe-block">
                    <h4 class="propertyTitle">Build History:</h4>
                    <?php
                        if (isset($pageVars["data"]["historic_builds"]) &&
                            count($pageVars["data"]["historic_builds"])>0 ) {
                                foreach ($pageVars["data"]["historic_builds"] as $hb) {
                                    echo 'Build ID: <a href="/index.php?control=PipeRunner&action=summary&item='.$pageVars["data"]["pipeline"]["project-slug"].'&run-id='.$hb.'">'.$hb.'</a><br />' ; } }
                    ?>
                </div>

                <?php
                }
                ?>

               <hr>
                <p class="text-center">
                Visit <a href="http://www.pharaohtools.com">www.pharaohtools.com</a> for more
            </p>
            </div>

        </div>

    </div>
</div>
<link rel="stylesheet" type="text/css" href="/Assets/Modules/BuildHome/css/buildhome.css">
<script type="text/javascript" src="/Assets/Modules/BuildHome/js/buildhome.js"></script>