<div class="container" id="wrapper">
    <div class="navbar-default col-sm-2 sidebar" role="navigation">
        <div class="sidebar-nav ">
            <ul class="nav in" id="side-menu">
                <li class="sidebar-search">
                    <div class="input-group custom-search-form  hvr-bounce-in">
                        <input type="text" class="form-control" placeholder="Search...">
						<span class="input-group-btn">
							<button class="btn btn-default" type="button">
                                <i class="fa fa-search"></i>
                            </button>
                        </span>
                    </div>
                </li>
                <li>
                    <a href="/index.php?control=Index&action=show" class="hvr-bounce-in">
                        <i class="fa fa-dashboard fa-fw hvr-bounce-in"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="index.php?control=BuildHome&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>" class="hvr-bounce-in">
                        <i class="fa fa-home fa-fw hvr-bounce-in"></i>  Pipeline Home
                    </a>
                </li>
                <li>
                    <a href="/index.php?control=BuildList&action=show" class="hvr-bounce-in">
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
                    <a href="index.php?control=PipeRunner&action=start&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>"  class="hvr-bounce-in">
                        <i class="fa fa-sign-in fa-fw hvr-bounce-in"></i> Run Again
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <?php $act= "index.php?control=PipeRunner&action=start&item={$pageVars["data"]["pipeline"]["project-slug"]}" ; ?>

    <form class="form-horizontal custom-form" action="<?php echo $act ; ?>" method="POST">

        <div class="col-sm-9 clearfix main-container">
<!--                <h2 class="text-uppercase text-light"><a href="/"> PTBuild - Pharaoh Tools </a></h2>-->
                <div class="col-sm-10">
                    <div class="row clearfix no-margin">

                    <h3>Project  <?php echo $pageVars["data"]["pipeline"]["project-name"] ; ?></h3>
                    <h5>This run has been queued</h5>
                    <h5 class="text-uppercase text-light" style="margin-top: 15px;">
                        <a href="index.php?control=BuildHome&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>"></a>
                    </h5>
                </div>

                <div>
                    <?php
                  #  var_dump('<pre>', $pageVars, '</pre>') ;
                    ?>
                </div>

                <div class="form-group">
                    Name:
                    <?php
                    echo $pageVars['data']['pipeline']['project-name'] ;
                    ?>
                </div>

                <div class="form-group">
                    Queued On:
                    <?php
                        echo date('H:i:s d/m/Y', $pageVars['data']['queued_run'][0]['entry_time']) ;
                    ?>
                </div>

                <div class="form-group">
                    <div class="col-sm-12"><br>
                        <a href="index.php?control=BuildHome&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>"
                           type="submit"
                           class="btn btn-primary col-sm-12">
                            <h4>
                                Pipeline Home
                            </h4>
                        </a>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-12">

                        <input type="hidden" name="item" id="item" value="<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>" />

                    </div>
                </div>


                <div class="form-group">
                    <div class="col-sm-12">
                        <hr>
                        <p class="text-center">
                            Visit <a href="http://www.pharaohtools.com">www.pharaohtools.com</a> for more
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </form>
</div><!-- /.container -->
