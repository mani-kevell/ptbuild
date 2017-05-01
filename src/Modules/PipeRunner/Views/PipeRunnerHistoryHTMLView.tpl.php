<div class="container" id="wrapper">

         <div id="page_content" class="col-lg-12 well well-lg">
             <div id="page_sidebar" class="navbar-default col-sm-2 sidebar" role="navigation">
                 <div class="sidebar-nav ">
                     <div class="sidebar-search">
                         <button class="btn btn-success" id="menu_visibility_label" type="button">
                             Show Menu
                         </button>
                         <i class="fa fa-1x fa-toggle-off hvr-grow" id="menu_visibility_switch"></i>
                     </div>
                     <ul class="nav in" id="side-menu">
                         <li>
                             <a href="/index.php?control=Index&amp;action=show" class="hvr-bounce-in">
                                 <i class="fa fa-dashboard fa-fw hvr-bounce-in"></i> Dashboard
                             </a>
                         </li>
                         <li>
                             <a href="index.php?control=BuildHome&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>" class="hvr-bounce-in">
                                 <i class="fa fa-home fa-fw hvr-bounce-in"></i> Pipeline Home
                             </a>
                         </li>
                         <li>
                             <a href="/index.php?control=BuildList&amp;action=show" class="hvr-bounce-in">
                                 <i class="fa fa-bars fa-fw hvr-bounce-in"></i> All Pipelines
                             </a>
                         </li>
                         <li>
                             <a href="index.php?control=Workspace&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>"  class="hvr-bounce-in">
                                 <i class="fa fa-folder-open-o hvr-bounce-in"></i> Workspace
                             </a>
                         </li>
                         <li>
                             <a href="index.php?control=BuildMonitor&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>"  class="hvr-bounce-in">
                                 <i class="fa fa-bar-chart-o hvr-bounce-in"></i> Monitors
                             </a>
                         </li>
                         <li>
                             <a href="index.php?control=PipeRunner&action=history&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>"  class="hvr-bounce-in">
                                 <i class="fa fa-history fa-fw hvr-bounce-in"></i> History <span class="badge"></span>
                             </a>
                         </li>
                         <li>
                             <a href="/index.php?control=PipeRunner&action=start&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>"  class="hvr-bounce-in">
                                 <i class="fa fa-sign-in fa-fw hvr-bounce-in"></i> Run Again
                             </a>
                         </li>
                     </ul>
                 </div>
             </div>


            <div class="row clearfix no-margin">

                <?php echo $this->renderLogs() ; ?>

                <h3>
                    Historic Builds of Pipeline <?php echo $pageVars["data"]["pipeline"]["project-name"] ; ?>
                    <i style="font-size: 18px;"></i>
                </h3>

                <h5 class="text-uppercase text-light" style="margin-top: 15px;">
                    <a href="/index.php?control=BuildHome&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>"></a>
                </h5>

                <form class="form-horizontal custom-form" action="<?= $act ; ?>" method="POST">


                    <div role="tabpanel grid">

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active allRowsTab">
                                <a onclick="showFilteredRows('all'); return false ;">All</a>
                            </li>
                            <li role="presentation" class="successRowsTab">
                                <a onclick="showFilteredRows('success'); return false ;">All Success</a>
                            </li>
                            <li role="presentation" class="failedRowsTab">
                                <a onclick="showFilteredRows('failure'); return false ;">All Failed</a>
                            </li>
                            <li role="presentation"class="unstableRowsTab">
                                <a onclick="showFilteredRows('unstable'); return false ;">All Unstable</a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="all">
                                <div class="table-responsive" ">
                                <div class="table table-striped table-bordered table-condensed">
                                    <div>
                                        <div class="blCell cellRowIndex">#</div>
                                        <div class="blCell cellRowStatus">Status</div>
                                        <div class="blCell cellRowSuccess">Success</div>
                                        <div class="blCell cellRowFailure">Failure</div>
                                        <div class="blCell cellRowDuration">Duration</div>
                                    </div>
                                    <div class="allBuildRows table-hover">

                                        <?php

                                        $i = 1;
                                        foreach ($pageVars["data"]["historic_builds"] as $hb_id) {


                                            if ($pipelineDetails["last_status"] === true) {
                                                $successFailureClass = "successRow"  ; }
                                            else if ($pipelineDetails["last_status"] === false) {
                                                $successFailureClass = "failureRow" ; }
                                            else {
                                                $successFailureClass = "unstableRow" ; }

                                                $summary_link_open_tag = '<a href="/index.php?control=PipeRunner&action=summary&item='.
                                                    $pageVars["data"]["pipeline"]["project-slug"].'&run-id='.$hb_id.'">'
                                            ?>

                                            <div class="buildRow <?php echo $successFailureClass ?>"
                                                 id="blRow_<?php echo $pipelineDetails["project-slug"]; ?>" >

                                                <div class="blCell cellRowIndex" scope="row">

                                                    <?php
                                                        echo $summary_link_open_tag ;
                                                        echo $hb_id ;
                                                        echo '</a>';
                                                    ?>
                                                </div>
                                                <div  class="blCell cellRowStatus" <?php

                                                    if ($pageVars["data"]["history_index"][$hb_id]["status"] === 'SUCCESS') {
                                                        echo ' style="background-color:rgb(13, 193, 42);" '; }
                                                    else if ($pageVars["data"]["history_index"][$hb_id]["status"] === 'FAIL') {
                                                        echo ' style="background-color:#D32B2B" '; }
                                                    else {
                                                        echo ' style="background-color:gray" '; }
                                                    ?> >

                                                    <?php

                                                    if (is_null($pageVars["data"]["history_index"][$hb_id]["status"])) {
                                                        $this_build_status = "UNKNOWN" ;
                                                    } else {
                                                        $this_build_status = $pageVars["data"]["history_index"][$hb_id]["status"] ;
                                                    }

                                                    echo '<p>'.$summary_link_open_tag.$this_build_status.'</a></p>' ;

                                                    ?>

                                                </div>

                                                <div class="blCell cellRowStart">
                                                    <?php

                                                    if (is_int($pageVars["data"]["history_index"][$hb_id]["start"])) {
                                                        $start = $pageVars["data"]["history_index"][$hb_id]["start"] ;
                                                        echo $summary_link_open_tag ;
                                                        echo date('d/m/Y H:i:s', $start) ;
                                                        echo '</a>';
                                                    } else {
                                                        echo 'N/A';
                                                    }

                                                    ?>
                                                </div>
                                                <div class="blCell cellRowEnd">
                                                    <?php

                                                    if (is_int($pageVars["data"]["history_index"][$hb_id]["end"])) {
                                                        $end = $pageVars["data"]["history_index"][$hb_id]["end"] ;
                                                        echo $summary_link_open_tag ;
                                                        echo date('d/m/Y H:i:s', $end) ;
                                                        echo '</a>';
                                                    } else {
                                                        echo 'N/A';
                                                    }

                                                    ?>
                                                </div>
                                                <div class="blCell cellRowDuration">
                                                    <?php

                                                    if (is_int($pageVars["data"]["history_index"][$hb_id]["end"]) &&
                                                        is_int($pageVars["data"]["history_index"][$hb_id]["start"])) {

                                                        $duration =
                                                            $pageVars["data"]["history_index"][$hb_id]["end"] -
                                                            $pageVars["data"]["history_index"][$hb_id]["start"] ;

                                                        $dur = date('i:s', $duration) ;
                                                        echo $summary_link_open_tag ;
                                                        echo $dur ;
                                                        echo '</a>';
                                                    } else {
                                                        echo 'N/A';
                                                    }

                                                    ?>
                                                </div>

                                                <hr class="buildRowSpace" />
                                            </div>
                                            <?php
                                            $i++;
                                        }
                                        ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="item" value="<?= $pageVars["data"]["pipeline"]["project-slug"] ;?>" />
                    <input type="hidden" id="pid" value="<?= $pageVars["pipex"] ;?>" />

                </form>
            </div>
            <hr>
            <p class="text-center">
                Visit <a href="http://www.pharaohtools.com">www.pharaohtools.com</a> for more
            </p>

        </div>
</div><!-- /.container -->
<link rel="stylesheet" type="text/css" href="/Assets/Modules/PipeRunner/css/piperunner.css">
<link rel="stylesheet" type="text/css" href="/Assets/Modules/PipeRunner/css/piperunnerhistory.css">
<link rel="stylesheet" type="text/css" href="/Assets/Modules/BuildList/css/buildlist.css">