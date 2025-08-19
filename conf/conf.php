<?php
return array (
  'db' => 
  array (
    'type' => 'pdo_mysql',
    'mysql' => 
    array (
      'master' => 
      array (
        'host' => '127.0.0.1',
        'user' => 'ziyuanmiao_com',
        'password' => 'Y873eBmC7eYzBJp6',
        'name' => 'ziyuanmiao_com',
        'tablepre' => 'bbs_',
        'charset' => 'utf8mb4',
        'engine' => 'myisam',
      ),
      'slaves' => 
      array (
      ),
    ),
    'pdo_mysql' => 
    array (
      'master' => 
      array (
        'host' => '127.0.0.1',
        'user' => 'ziyuanmiao_com',
        'password' => 'Y873eBmC7eYzBJp6',
        'name' => 'ziyuanmiao_com',
        'tablepre' => 'bbs_',
        'charset' => 'utf8mb4',
        'engine' => 'myisam',
      ),
      'slaves' => 
      array (
      ),
    ),
  ),
  'cache' => 
  array (
    'enable' => true,
    'type' => 'memcached',
    'memcached' => 
    array (
      'host' => '127.0.0.1',
      'port' => '11211',
      'cachepre' => 'bbs_',
    ),
    'redis' => 
    array (
      'host' => 'localhost',
      'port' => '6379',
      'cachepre' => 'bbs_',
    ),
    'xcache' => 
    array (
      'cachepre' => 'bbs_',
    ),
    'yac' => 
    array (
      'cachepre' => 'bbs_',
    ),
    'apc' => 
    array (
      'cachepre' => 'bbs_',
    ),
    'mysql' => 
    array (
      'cachepre' => 'bbs_',
    ),
  ),
  'tmp_path' => './tmp/',
  'log_path' => './log/',
  'view_url' => 'view/',
  'upload_url' => 'upload/',
  'upload_path' => './upload/',
  'logo_mobile_url' => 'view/img/logo.png',
  'logo_pc_url' => 'view/img/logo.png',
  'logo_water_url' => 'view/img/water-small.png',
  'sitename' => '资源喵网 - 免费分享优质稀缺资源。',
  'sitebrief' => '综合资源站，搜罗全网优质稀缺资源，包括但不限于电影、美剧、小说、动漫、番剧、吃瓜、歌曲音乐、学习资料、网赚项目等。',
  'timezone' => 'Asia/Shanghai',
  'lang' => 'zh-cn',
  'runlevel' => 5,
  'runlevel_reason' => 'The site is under maintenance, please visit later.',
  'cookie_domain' => '',
  'cookie_path' => '',
  'auth_key' => 'QAV6TB3NPYGD5R487D9Z3CW3MB8Y8NRW2HQQ8JSMHEZ6QQZDTZA3S4XMJAD685F9',
  'pagesize' => 18,
  'postlist_pagesize' => 10,
  'cache_thread_list_pages' => 10,
  'online_update_span' => 120,
  'online_hold_time' => 3600,
  'session_delay_update' => 0,
  'upload_image_width' => 927,
  'order_default' => 'lastpid',
  'attach_dir_save_rule' => 'Ym',
  'update_views_on' => 1,
  'user_create_email_on' => 1,
  'user_create_on' => 1,
  'user_resetpw_on' => 1,
  'nav_2_on' => 1,
  'nav_2_forum_list_pc_on' => 0,
  'nav_2_forum_list_mobile_on' => 0,
  'admin_bind_ip' => 0,
  'cdn_on' => 0,
  'url_rewrite_on' => 1,
  'disabled_plugin' => 0,
  'version' => '4.0.4',
  'static_version' => '?1.0',
  'installed' => 1,
  'site_keywords' => '',
  'user_create_io' => 0,
  'attach_maxsize' => 20480000,
);
?>