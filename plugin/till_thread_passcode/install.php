<?php
!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

$sql = "ALTER TABLE {$tablepre}thread ADD COLUMN `passcode` TINYTEXT";
$r = db_exec($sql);
?>