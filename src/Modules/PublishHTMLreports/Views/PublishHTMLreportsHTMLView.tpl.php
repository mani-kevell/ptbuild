<div class="container" id="wrapper">
    <div class="col-lg-9">
        <div class="fullRow">
            <h2>
                <?php
                    echo $pageVars["data"]["current_report"]["feature_data"]["Report_Title"] ;
                ?>
            </h2>
        </div>
        <div class="fullRow">
            <hr />
            <h4> Report: </h4>
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
</div>