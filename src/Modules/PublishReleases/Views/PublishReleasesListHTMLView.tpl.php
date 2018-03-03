<?php



if ($pageVars["data"]["pipeline"]["last_status"]===true) {
    $sclass = "good" ;
    $img = "pass" ; }
else if ($pageVars["data"]["pipeline"]["last_status"]===false) {
    $sclass =  "bad" ;
    $img =  "fail" ; }
else {
    $sclass = "unknown" ;
    $img = "unknown" ; }

//
//    var_dump($pageVars["data"]["pipeline"]["last_status"]) ;
//die();


if ($pageVars["route"]["action"] === 'image') {


    header('Content-Type: image/png');

    $app_root = PFILESDIR.PHARAOH_APP.DS.PHARAOH_APP.DS ;
    $relative_path = 'src/Modules/PublishReleases/Assets/images/'.$img.'.png' ;
    $file_path = $app_root.$relative_path ;
    $file_data = file_get_contents($file_path) ;

    echo $file_data ;

} else {
?>


<div class="container" id="wrapper">
    <div class="col-lg-12">
        <h2>
            All Releases List for Job: <strong><?php echo $pageVars["data"]["pipeline"]["project-name"] ; ?></strong>
        </h2>

        <div class="current_status current_status_<?php echo $sclass ; ?>">
            <h3>
                Status:
                <?php
                echo ucfirst($sclass) ; ?>
            </h3>
        </div>

    </div>
    <div class="col-lg-12">

        <h3 class="text-center">
            Releases List
        </h3>

        <?php

        $mn = 'PublishReleases' ;
        foreach ($pageVars["data"]["releases_list"]["custom_release"] as $releaseHash => $releaseDetail) {

            ?>

            <div class="col-sm-12 one_release_type">

                <div class="col-sm-12">
                    <h2 class="text-center">
                        <?php echo $releaseDetail['release_title'] ; ?>
                    </h2>
                </div>

                <?php
                    $ordered = array_reverse($pageVars["data"]["releases_available"][$releaseHash], true) ;
                    $current_ordered_count = count($ordered) ;
                    $row_count = 0 ;
                    foreach ($ordered as $one_current_runid => $one_available_release) {
                        if ($row_count > 9) {
                            $hide_further_string = 'hidden_published_release hidden_published_release_'.$releaseHash ;
                        } else {
                            $hide_further_string = '';
                        }

                        ?>

                        <div class="col-sm-12 <?php echo $hide_further_string; ?>" id="hidden_published_release_<?php echo $releaseHash; ?>">
                            <div class="col-sm-6">
                                <h3>
                                    Build ID: <?php echo $one_current_runid; ?>
                                </h3>
                            </div>
                            <div class="col-sm-6">
                                <h3>
                                    Asset:
                                    <?php

                                    foreach ($one_available_release as $one_download_file) {

                                        $path = "pipes/{$pageVars["data"]["pipeline"]["project-slug"]}/ReleasePackages/{$releaseHash}/{$one_current_runid}";
                                        ?>

                                        <a href="/index.php?control=AssetLoader&action=show&location=root&path=<?php echo $path; ?>&asset=<?php echo $one_download_file; ?>&output-format=FILE&type=binary">
                                            <?php echo $one_download_file; ?>
                                        </a>
                                        <?php
                                    }

                                    ?>
                                </h3>
                            </div>
                        </div>

                        <?php

                        $row_count++;
                    }

                ?>

            </div>

            <?php

            if ($row_count > 9) {
                ?>
                <div class="col-sm-12">
                    <button class="btn btn-success see-more-button" data-release-hash="<?php echo $releaseHash ; ?>" id="see-more-button_<?php echo $releaseHash; ?>">
                        See More...
                    </button>
                </div>
                <?php
            }
            unset($row_count) ;

        }


        ?>
    </div>
    <div class="col-lg-12">
        <hr>
        <p class="text-center">
            Visit <a href="http://www.pharaohtools.com">www.pharaohtools.com</a> for more
        </p>
    </div>
</div>
<link rel="stylesheet" type="text/css" href="/Assets/Modules/BuildHome/css/buildhome.css">
<link rel="stylesheet" type="text/css" href="/Assets/Modules/PublishReleases/css/publishreleases.css">


<?php
}
?>