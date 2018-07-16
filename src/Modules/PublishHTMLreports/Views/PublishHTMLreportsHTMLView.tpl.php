<div class="col-sm-12" id="wrapper">
    <div class="fullRow">
        <h2>
            <?php
                echo $pageVars["data"]["current_report"]["feature_data"]["Report_Title"] ;
            ?>
        </h2>
        <h4>
            Return To Build Home :
            <a href="index.php?control=BuildHome&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>">
                <strong>
                    <?php echo $pageVars["data"]["pipeline"]["project-name"] ; ?>
                </strong>
            </a>
        </h4>
    </div>
    <div class="fullRow">
        <hr />
        <?php
        # requested_run_id
        if (isset($pageVars["data"]["current_report"]['requested_run_id'])) {
            ?>
                <h5> Requested Run ID for this Test Set:
                    <strong>
                        <?php echo $pageVars["data"]["current_report"]['requested_run_id'] ; ?>
                    </strong>
                </h5>
            <?php
        } else {
            ?>
            <h5> Latest Run ID for this Test Set:
                <strong>
                    <?php echo $pageVars["data"]["current_report"]['last_run_id'] ; ?>
                </strong>
            </h5>
            <?php
        }
        ?>
    </div>
    <div class="fullRow">
        <hr />
        <?php
            echo $pageVars["data"]["current_report"]["report_data"] ;
        ?>
    </div>
    <div class="fullRow">
        <hr />
        <p class="text-center">
            Visit <a href="http://www.pharaohtools.com">www.pharaohtools.com</a> for more
        </p>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="/Assets/Modules/PublishHTMLreports/css/publishhtmlreports.css">