$signature = param('my_signature', '', $htmlspecialchars = FALSE);
$signature = strip_tags($signature,"<b>,<br>,<a>,<img>,<span>");
include _include(APP_PATH.'plugin/art_signature/model/xss.php');
$signature = remove_xss($signature);
$signature = htmlspecialchars($signature);