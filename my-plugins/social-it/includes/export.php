<?php
include("../../../bb-load.php");
if (bb_current_user_can('administrate')) {
    $socialit_plugopts = bb_get_option("SocialIt");
    if($_GET['url'] != "1"){
        unset($socialit_plugopts['shorturls']);
    }
    $content = var_export($socialit_plugopts, true);
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-disposition: attachment; filename=socialit-options.txt");
    header("Content-Type: text/plain");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ". mb_strlen($content), 'latin1');
    echo $content;
    exit();
}else{
    echo "You do not have permissions to do this!";
}
?>