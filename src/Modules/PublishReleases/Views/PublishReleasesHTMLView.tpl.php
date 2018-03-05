<div class="container" id="wrapper">
    <div class="fullRow">
        <h2>
            Current Build Status
        </h2>
        <h4>
            <a href="index.php?control=BuildHome&action=show&item=<?php echo $pageVars["data"]["pipeline"]["project-slug"] ; ?>">
                Return To Build Home :
                <strong>
                    <?php echo $pageVars["data"]["pipeline"]["project-name"] ; ?>
                </strong>
            </a>
        </h4>
    </div>
    <div class="fullRow">
        <hr />
        <?php
            echo $pageVars["data"]["current_status"]["status_data"] ;
        ?>
    </div>
    <div class="fullRow">
        <hr />
        <p class="text-center">
            Visit <a href="http://www.pharaohtools.com">www.pharaohtools.com</a> for more
        </p>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="/Assets/Modules/PublishReleases/css/publishstatus.css">