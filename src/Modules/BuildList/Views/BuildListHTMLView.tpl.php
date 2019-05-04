<div class="container" id="wrapper">

	<div class="col-lg-12">

		<div class="well well-lg">

			<div class="row clearfix no-margin">

				<h2>Build Jobs</h2>

                <div id="page_sidebar" class="col-sm-2 sidebar" role="navigation">
                    <div class="sidebar-nav ">
                        <div class="sidebar-search">
                            <button class="btn btn-success" id="menu_visibility_label" type="button">
                                Show Menu
                            </button>
                            <i class="fa fa-1x fa-toggle-off hvr-grow" id="menu_visibility_switch"></i>
                        </div>
                        <ul class="nav in" id="side-menu">
                            <li>
                                <a href="/index.php?control=Index&action=show" class=" hvr-bounce-in"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>
                            <li>
                                <a href="/index.php?control=ApplicationConfigure&action=show" class=" hvr-bounce-in"> <i class="fa fa-cogs fa-fw"></i> Configure PTBuild<span class="fa arrow"></span> </a>
                                <ul class="nav nav-second-level collapse">
                                    <li>
                                        <a href="/index.php?control=ApplicationConfigure&action=show" class=" hvr-curl-bottom-right">Application</a>
                                    </li>
                                    <li>
                                        <a href="/index.php?control=UserManager&action=show" class=" hvr-curl-bottom-right">User Manager</a>
                                    </li>
                                    <li>
                                        <a href="/index.php?control=UserProfile&action=show" class=" hvr-curl-bottom-right">User Profile</a>
                                    </li>
                                    <li>
                                        <a href="/index.php?control=ModuleManager&action=show" class=" hvr-curl-bottom-right">Modules</a>
                                    </li>
                                    <li>
                                        <a href="/index.php?control=Integrations&action=show" class=" hvr-curl-bottom-right">Integrations</a>
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <li>
                                <a href="/index.php?control=BuildConfigure&action=new" class="hvr-bounce-in"><i class="fa fa-edit fa-fw hvr-bounce-in"></i> New Pipeline</a>
                            </li>
                            <li>
                                <a href="/index.php?control=BuildConfigure&action=copy" class="hvr-bounce-in"><i class="fa fa-edit fa-fw hvr-bounce-in"></i> Copy Pipeline</a>
                            </li>
                            <li>
                                <a href="/index.php?control=BuildList&action=show " class="active  hvr-bounce-in"><i class="fa fa-bars fa-fw hvr-bounce-in"></i> All Pipelines</a>
                            </li>
                            <br />

                            <li>
                                <div class="alert alert-info updatable_menu_list">
                                    <h4>Running Builds </h4>
                                    <div class="runningBuilds">
                                        <p>
                                            No builds currently being executed
                                        </p>

                                    </div>
                                </div>
                                <div class="alert alert-info updatable_menu_list">
                                    <h4>Queued Builds</h4>
                                    <div class="queuedBuilds">
                                        <p>
                                            No builds currently queued
                                        </p>

                                    </div>
                                </div>
                            </li>

                        </ul>
                    </div>

                </div>

				<div role="tabpanel grid">

                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active allRowsTab">
                            <a class="build_list_filter" id='build_list_filter_all' data-filter='all'>All</a>
                        </li>
                        <li role="presentation" class="successRowsTab">
                            <a class="build_list_filter" id='build_list_filter_success' data-filter='success'>All Success</a>
                        </li>
                        <li role="presentation" class="failedRowsTab">
                            <a class="build_list_filter" id='build_list_filter_failed' data-filter='failure'>All Failed</a>
                        </li>
                        <li role="presentation"  class="unstableRowsTab">
                            <a class="build_list_filter" id='build_list_filter_unstable' data-filter='unstable'>All Unstable</a>
                        </li>
                    </ul>


                    <script type='text/javascript'>
                        var build_run_params = [] ;
                        var build_run_metadata = [] ;
                    </script>

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="all">
							<div class="table-responsive">
                                <div class="table table-striped table-bordered table-condensed">
                                    <div>
                                        <div class="blCell cellRowIndex">#</div>
                                        <div class="blCell cellRowName">Pipeline</div>
                                        <div class="blCell cellRowRun">Action</div>
                                        <div class="blCell cellRowStatus">Status</div>
                                        <div class="blCell cellRowSuccess">Success</div>
                                        <div class="blCell cellRowFailure">Failure</div>
                                        <div class="blCell cellRowDuration">Duration</div>
                                    </div>
                                <div class="allBuildRows table-hover">

                                <?php

                                $i = 1;
                                foreach ($pageVars["data"]["pipelines"] as $pipelineSlug => $pipelineDetails) {


                                    if ($pipelineDetails["last_status"] === true) {
                                        $successFailureClass = "successRow"  ; }
                                    else if ($pipelineDetails["last_status"] === false) {
                                        $successFailureClass = "failureRow" ; }
                                    else {
                                        $successFailureClass = "unstableRow" ; }

                                    ?>

                                <div class="buildRow <?php echo $successFailureClass ?>" id="blRow_<?php echo $pipelineSlug; ?>" >
                                    <div class="blCell cellRowIndex" scope="row"><?php echo $i; ?> </div>
                                    <div class="blCell cellRowName"><a href="/index.php?control=BuildHome&action=show&item=<?php echo $pipelineSlug; ?>" class="pipeName"><?php echo $pipelineDetails["project-name"]; ?>  </a> </div>

                                    <div class="blCell cellRowRun">
                                        <?php
                                        echo '<div class="col-sm-12">' ;
                                        echo '    <div class="col-sm-3">' ;
                                        echo '        <a href="/index.php?control=BuildConfigure&action=show&item=' . $pipelineDetails["project-slug"] . '">';
                                        echo '        <i class="fa fa-cog fa-2x hvr-grow-shadow"></i></a>';
                                        echo '    </div>' ;
                                        echo '    <div class="col-sm-3">' ;
                                        echo '        <a href="/index.php?control=Workspace&action=show&item=' . $pipelineDetails["project-slug"] . '">';
                                        echo '        <i class="fa fa-folder-open-o fa-2x hvr-grow-shadow"></i></a>';
                                        echo '    </div>' ;
                                        echo '    <div class="col-sm-3">' ;
                                        echo '        <a href="/index.php?control=PipeRunner&action=history&item=' . $pipelineDetails["project-slug"] . '">';
                                        echo '        <i class="fa fa-history fa-2x hvr-grow-shadow"></i></a>';
                                        echo '    </div>' ;
                                        echo '    <div class="col-sm-3">' ;
                                        echo '        <a href="/index.php?control=PipeRunner&action=start&item=' . $pipelineDetails["project-slug"] . '">';
                                        echo '        <i class="fa fa-play fa-2x hvr-grow-shadow" style="color:rgb(13, 193, 42);"></i></a>';
                                        echo '    </div>' ;
                                        echo '</div>' ;
                                        ?>
                                    </div>

                                    <?php

                                    if ($pipelineDetails["last_status"] === true) {
                                        $sty = ' style="background-color:rgb(13, 193, 42);" '; }
                                    else if ($pipelineDetails["last_status"] === false) {
                                        $sty = ' style="background-color:#D32B2B" '; }
                                    else {
                                        $sty = ' style="background-color:gray" '; }
                                    ?>
                                    <div class="blCell cellRowStatus" <?php echo $sty ; ?> >

                                        <?php

                                        if ($pipelineDetails["last_run_build"] > 0) {
                                            $summary_link_open_tag = '<a href="/index.php?control=PipeRunner&action=summary&item='.
                                                $pipelineDetails["project-slug"].'&run-id='.$pipelineDetails["last_run_build"].'">' ;
                                            echo $summary_link_open_tag.'<p> #'.$pipelineDetails["last_run_build"].'</p></a>' ;
                                        } else {
                                            echo '<p> #'.$pipelineDetails["last_run_build"].'</p>' ;
                                        }

                                        ?>

                                    </div>

                                    <div class="blCell cellRowSuccess">
                                        <?php

                                        $today = new DateTime(); // This object represents current date/time
                                        $actualToday = $today; // copy original for display
                                        $today->setTime( 0, 0, 0 ); // reset time part, to prevent partial comparison

                                        if ($pipelineDetails["last_success"] != false) {

                                            $match_date = new DateTime(date('d.m.Y H:i', $pipelineDetails["last_success"]));

            //                                var_dump($match_date) ;

                                            $diff = $today->diff( $match_date );
                                            $diffDays = (integer)$diff->format( "%R%a" ); // Extract days count in interval

                                            $date = date($pipelineDetails["last_success"]);
                                            if( $diffDays == 0 ) {
                                                echo date_format($match_date, 'g:ia')." Today"; }
                                            else if( $diffDays == -1 ) {
                                                echo date_format($match_date, 'g:ia')." Yesterday"; }
                                            else {
                                                echo date_format($match_date, 'g:ia \o\n D jS M Y'); }
                                            echo ' (#' . $pipelineDetails["last_success_build"] . ')'; }
                                        else {
                                            echo 'N/A'; }
                                        ?>
                                    </div>
                                    <div class="blCell cellRowFailure">
                                        <?php
                                        if ($pipelineDetails["last_fail"] != false) {

                                            $match_date = new DateTime(date('d.m.Y H:i', $pipelineDetails["last_fail"]));
            //                                var_dump($match_date) ;

                                            $diff = $today->diff( $match_date );
                                            $diffDays = (integer)$diff->format( "%R%a" ); // Extract days count in interval

                                            $date = date($pipelineDetails["last_fail"]);
                                            if( $diffDays == 0 ) {
                                                echo date_format($match_date, 'g:ia')." Today"; }
                                            else if( $diffDays == -1 ) {
                                                echo date_format($match_date, 'g:ia')." Yesterday"; }
                                            else {
                                                echo date_format($match_date, 'g:ia \o\n D jS M Y'); }
                                            echo ' (#' . $pipelineDetails["last_fail_build"] . ')';}
                                        else {
                                            echo 'N/A'; }

                                        ?>
                                    </div>
                                    <div class="blCell cellRowDuration">
                                        <?php
                                        if ($pipelineDetails["duration"] != false) {
                                            echo gmdate("H:i:s", $pipelineDetails["duration"]).' ('.$pipelineDetails["duration"] . ' s)';
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

			</div>

		</div>

        <hr />

        <div class="col-lg-12">
            <div class="col-lg-6">

                <div class="updatable_footer_list alert alert-info fullWidth">
                    <h4>Running Builds </h4>
                    <div class="runningBuilds">
                        <p>
                            No builds currently being executed
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="updatable_footer_list alert alert-info fullWidth">
                    <h4>Queued Builds</h4>
                    <div class="queuedBuilds">
                        <p>
                            No builds currently queued
                        </p>
                    </div>
                </div>
            </div>
        </div>

	</div>
</div><!-- /.container -->
<link rel="stylesheet" type="text/css" href="/Assets/Modules/BuildList/css/buildlist.css">
