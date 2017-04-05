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
    $relative_path = 'src/Modules/PublishStatus/Assets/images/'.$img.'.png' ;
    $file_path = $app_root.$relative_path ;
    $file_data = file_get_contents($file_path) ;

    echo $file_data ;

} else {
?>


<div class="container" id="wrapper">
    <div class="col-lg-12">
        <h2>
            Current Build Status: <strong><?php echo $pageVars["data"]["pipeline"]["project-name"] ; ?></strong>
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
        <hr>
        <h4>
            Permalink for Status Image
        </h4>

        <?php
        if ($pageVars['data']['is_https'] === true) {
            $proto = 'https://' ;
        } else {
            $proto = 'http://' ;
        }

        $url = $proto ;
        $url .= $_SERVER['HTTP_HOST'] ;
        $url .= '/index.php?control=PublishStatus&action=image&item=' ;
        $url .= $pageVars["data"]["pipeline"]['project-slug'] ;

        ?>

        <h3>
            <a href="<?php echo $url ; ?>"
               target="_blank"
               class="image_permalink"><?php echo $url ; ?></a>
        </h3>

        <?php

        $mn = 'PublishStatus' ;
        if (isset($steps[$mn]["enabled"]) && $steps[$mn]["enabled"] === "on") {
            foreach ($pageVars["data"]["status_list"]["statuses"] as $statusHash => $statusDetail) {

                $dir = $statusDetail["Report_Directory"];
                if (substr($dir, -1) !== DS) {
                    $dir = $dir . DS ; }
                $indexFile = $statusDetail["Index_Page"];
                $statusTitle = $statusDetail["Report_Title"];

                echo '<a href="index.php?control=PublishStatus&action=status&item=' ;
                echo $pageVars["data"]["pipeline"]["project-slug"].'">' ;
                echo '  '.$statusTitle ;
                echo '</a>' ;

            } }

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


<?php
}
?>