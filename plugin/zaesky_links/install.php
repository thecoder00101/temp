<?php


!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

$sql = "create TABLE if not exists {$tablepre}navlinks (
    `lid` int(11) unsigned not null auto_increment,
    `icon` char(255)   not null    default '',
    `name` varchar(255)    not null    default '',
    `link` char(255)    not null    default '',
    `rank` tinyint(11)   not null    default 0,
    primary key (lid)
)ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);
?>
