<pre>
<?php
if (function_exists('bb_current_user_can') && bb_current_user_can('administrate')) {
error_reporting(E_ALL);
$newline="<br><br>";
$f=__FILE__;

echo $f.$newline;

echo "<hr>";

$disabled=ini_get('disable_functions');
echo "disabled functions: ".(empty($disabled) ? "(none)" : $disabled).$newline;

echo "<hr>";

$mct=function_exists ( 'mime_content_type' );
echo "mime_content_type:  ".(($mct) ? " exists. " : " does not exist. ").$newline;
if ($mct) {echo "mct: ".mime_content_type($f).$newline;}

echo "<hr>";

$finfo=function_exists ( 'finfo_open' );
echo "finfo_open:  ".(($finfo) ? " exists. " : " does not exist. ").$newline;
if ($finfo) {$finfo=finfo_open(FILEINFO_MIME); echo "finfo: ".trim(finfo_file($finfo,$f)).$newline;}

echo "<hr>";

$imgt=function_exists ( 'getimagesize' );
echo "imgt:  ".(($imgt) ? " exists. " : " does not exist. ").$newline;
if ($imgt) {$imgt = getimagesize($f); echo "imgt: ".image_type_to_mime_type($imgt[2]).$newline;}

echo "<hr>";

$exec=function_exists ( 'exec' );
echo "exec:  ".(($exec) ? " exists. " : " does not exist. ").$newline;
if ($exec) {echo "exec: ".trim (exec ('file -bi ' . escapeshellarg ( $f ) ) ).$newline;}

echo "<hr>";
echo "Current Attachment Settings:";
echo "<pre>"; print_r($bb_attachments); echo "</pre>";

}
exit();
?>