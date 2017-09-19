<div class="container" id="wrapper">

    <div class="col-lg-12">
        <div class="well well-lg">
<!--                    	<h2 class="text-uppercase text-light"><a href="/">PTBuild - Pharaoh Tools</a></h2>-->

                        <?php echo $this->renderLogs() ; ?>

            <div class="row clearfix no-margin">

                <?php
                $act = '/index.php?control=ApplicationConfigure&action=save' ;
                ?>

                <form class="form-horizontal custom-form" action="<?= $act ; ?>" method="POST">


                    <h3 class="text-uppercase text-light" style="margin-top: 15px;">
                        Module Configuration <i style="font-size: 18px;" class="fa fa-chevron-right"></i>
                    </h3>


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
                                    <a href="/index.php?control=Index&action=show" class=" hvr-bounce-in">
                                        <i class="fa fa-dashboard fa-fw hvr-bounce-in"></i> Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a href="/index.php?control=ApplicationConfigure&action=show"class=" active hvr-bounce-in">
                                        <i class="fa fa-cogs fa-fw hvr-bounce-in"></i> Configure PTBuild</a>
                                </li>
                                <li>
                                    <a href="/index.php?control=UserManager&action=show"class=" hvr-bounce-in">
                                        <i class="fa fa-group hvr-bounce-in"></i> User Manager
                                    </a>
                                </li>
                                <li>
                                    <a href="/index.php?control=ModuleManager&action=show"class=" hvr-bounce-in">
                                        <i class="fa fa-suitcase hvr-bounce-in"></i> Module Manager
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <?php

                        if (is_array($pageVars["data"]["mod_configs"]) && count($pageVars["data"]["mod_configs"]>0)) {
                            foreach ($pageVars["data"]["mod_configs"] as $module_name => $one_mod_confs) {
                                echo '  <div class="col-sm-12">' ;
                                echo '      <h4>Module: '.$module_name.'</h4>';
                                foreach ($one_mod_confs as $one_config_slug => $one_conf_tails) {
                                    echo '<div class="form-group">' ;
                                    echo '  <label for="'.$one_config_slug.'" class="col-sm-3 control-label text-left">'.$one_conf_tails["label"].'</label>' ;
                                    echo '  <div class="col-sm-9">' ;
                                    switch ($one_conf_tails["type"]) {
                                        case "boolean" :
                                            if ( (isset($pageVars["data"]["current_configs"]["mod_config"][$module_name]["".$one_config_slug])) &&
                                                $pageVars["data"]["current_configs"]["mod_config"][$module_name]["".$one_config_slug] == "on" ) {
                                                $onoff = "on" ; }
                                            else if ( (isset($pageVars["data"]["current_configs"]["mod_config"][$module_name][$one_config_slug])) &&
                                                $pageVars["data"]["current_configs"]["mod_config"][$module_name][$one_config_slug] != "on" ) {
                                                $onoff = "off" ; }
                                            else {
                                                $df = (isset($one_conf_tails["default"])) ? $one_conf_tails["default"] : "" ;
                                                $onoff = (!isset($onoff) || is_null($onoff))
                                                    ? $one_conf_tails["default"]
                                                    : $onoff ; }
                                            if ($onoff === "on") { $onoff = 'checked="checked"' ;}
                                            else {$onoff = "" ;}
                                            echo '<input name="mod_config['.$module_name.']['.$one_config_slug.']" id="mod_config['.$module_name.']['.$one_config_slug.']" type="checkbox" '.$onoff.' />' ;
                                            break ;
                                        case "text" :
                                            if (isset($pageVars["data"]["current_configs"]["mod_config"][$module_name][$one_config_slug]) &&
                                                strlen($pageVars["data"]["current_configs"]["mod_config"][$module_name][$one_config_slug])>0) {
                                                $val = ' value="'.$pageVars["data"]["current_configs"]["mod_config"][$module_name][$one_config_slug].'" ';  }
                                            else {
                                                $val = "" ; }
                                            if ($val == "" || is_null($val)) {
                                                $placeholder = ' placeholder="'.$one_conf_tails["default"].'" ' ; }
                                            else {
                                                $placeholder = "" ; }
                                            echo '<input name="mod_config['.$module_name.']['.$one_config_slug.']" id="mod_config['.$module_name.']['.$one_config_slug.']" type="text" class="form-control" '.$val.' '.$placeholder.' ></input>' ;
                                            break ;
                                        case "textarea" :
                                            if (isset($pageVars["data"]["current_configs"]["mod_config"][$module_name][$one_config_slug]) &&
                                                strlen($pageVars["data"]["current_configs"]["mod_config"][$module_name][$one_config_slug])>0) {
                                                $val = $pageVars["data"]["current_configs"]["mod_config"][$module_name][$one_config_slug] ;  }
                                            else {
                                                $val = "" ; }
                                            echo '<textarea name="mod_config['.$module_name.']['.$one_config_slug.']" id="mod_config['.$module_name.']['.$one_config_slug.']" class="form-control" >'.$val.'</textarea>' ;
                                            break ; }
                                    echo '  </div>';
                                    echo '</div>'; }
                                echo '  </div>';
                            } }
                        else {
                            echo '<div class="form-group">' ;
                            echo '    <p>No Modules are providing editable configurations</p>';
                            echo '</div>'; }

                    ?>

                    <div class="form-group">
                        <div class="col-sm-12">
                            <button type="submit" class="btn btn-success hvr-float-shadow">Save Configuration</button>
                            <button type="button" class="btn btn-primary hvr-float-shadow" onclick="$('input').val('')">Clear</button>
                          
                            <a href="/"> <button type="button" class="btn btn-warning hvr-float-shadow">Cancel</button></a>
                        </div>
                    </div>

                </form>

            </div><hr>
              <p class="text-center">
                Visit <a href="http://www.pharaohtools.com">www.pharaohtools.com</a> for more
            </p>
    </div>

</div>
