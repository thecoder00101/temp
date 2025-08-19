<?php
/*

 */

!defined('DEBUG') AND exit('Forbidden');
$tablepre = $db->tablepre;

$sql = "CREATE TABLE IF NOT EXISTS `{$tablepre}user_foxpay`(
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(11) unsigned NOT NULL default '0' COMMENT '用户ID',
  `money` decimal(11,2) NOT NULL default '0.00' COMMENT '支付金额',
  `rmbs` int(11) unsigned NOT NULL default '0' COMMENT '到账金额',
  `orderid` bigint(20) unsigned NOT NULL default '0' COMMENT '订单ID',
  `trade_no` char(32) NOT NULL default '' COMMENT '交易号',
  `pay_type` tinyint(1) unsigned NOT NULL default '0' COMMENT '支付类型',
  `status` tinyint(1) unsigned NOT NULL default '0' COMMENT '支付状态', # 默认列表排序，0: 未扫码，1: 已关闭，2: 已付款，3: 待付款，4: 已退款
  `paycode` int(5) unsigned NOT NULL default '0' COMMENT '支付码',
  `paymethod` char(20) NOT NULL default '0' COMMENT '支付方式',
  `message` char(128) NOT NULL default '' COMMENT '附加信息',
  `create_date` int(11) unsigned NOT NULL default '0' COMMENT '创建时间',
  `pay_date` int(11) unsigned NOT NULL default '0' COMMENT '到账时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `orderid` (`orderid`)
) ENGINE=MyISAM default CHARSET=utf8 COLLATE=utf8_general_ci;";
db_exec($sql);

$initial_data = kv_get('fox_alipay');
if(empty($initial_data)){
    $initial_data = array(
        'pay_rmbstr'=>'5|10|20|30|50',
        'pay_min'=>1,
        'pay_ratio'=>1,
        'pay_email'=>'',
        'pay_appid'=>'',
        'pay_publickey'=>'',
        'pay_privatekey'=>'',
        'pay_timeout'=>'3m',
        'pay_debug'=>0
    );
    kv_cache_set('fox_alipay', $initial_data);
}

$logpath = $conf['log_path'] . date('Ym', $time);
!is_dir($logpath) AND mkdir($logpath, 0755, true);
$logfilepath = $logpath."/paylog.php";
if(!file_exists($logfilepath)){
    $s = "<?php exit;?>\r\n";
    file_put_contents($logfilepath, $s, FILE_APPEND);
}
?>