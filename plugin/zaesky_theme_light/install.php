<?php

/*
	Xiuno BBS 4.0 xiuno L
*/

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

//给user添加一个字段#avatar_auto
$sql = "ALTER TABLE {$tablepre}user ADD COLUMN bgimg CHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT  '0' COMMENT  '系统背景'";
db_exec($sql);


// 添加插件默认配置
$admin_light_setting_config = array(
	"side_nav_switch" => 1,
	"thread_list_style"=>1,
	"thread_reply_reload"=>0,
	"window_no_console"=>0,
	"thread_top_nav"=>0,
	"nav_search_form"=>0,
	"body_font_style"=>0,
	"header_gold_count"=>0,
	"groupicon_display"=>0,
	"post_show_first_floor"=>0,
	"post_show_floors"=>1,
	"index_userinfo"=>1,
	"credits_progress"=>0,
	"post_show_time"=>0,
	"post_func_position"=>0,
	"post_form_position"=>0,
	"thread_left_switch"=>1,
	"thread_left_func"=>0,
	"thread_left_tools"=>1,
	"back_top"=>1,
	"thread_top_ind"=>1,
	"thread_summary"=>1,
	"thread_summary_word"=>100,
	"login_guide_icon"=>"fas fa-grin-wink",
	"login_guide_1"=>"你好！欢迎来访！",
	"login_guide_2"=>"请登录后探索更多精彩内容！",
	"site_annoucement_switch"=>0,
	"site_annoucement_position"=>0,
	"site_annoucement_style"=>0,
	"site_annoucement_content"=>"",
	"site_annoucement_icon"=>"fas fa-bullhorn",
	"login_annoucement_content"=>"",
	"comment_annoucement_content"=>"",
	"header_my_favorite"=>0,
	"user_online"=>0,
	"new_thread"=>0,
	"dock_switch"=>0,
	"dock_func_3"=>0,
	"thread_forum_name"=>1,
	"index_mobile_statistic"=>1,
	"thread_last_reply"=>1,
	"site_info_bg"=>'',
	"site_info_logo"=>'/view/img/favicon.ico',
	"site_info_switch"=>1,
	"thread_user_bg"=>1,
	"site_info_total"=>1,
	"thread_summary_pic"=>3,
	"navbar_cate"=>1,
	"thread_subject_size"=>'14',
	"thread_quick_at"=>0,
	"disc_page"=>0,
	"disc_page_title"=>'发现',
	"disc_page_icon"=>'fas fa-atom',
	"disc_page_banner"=>'',
	"disc_page_banner_a"=>'https://www.noteweb.top/',
	"user_bg_switch"=>0,
	"user_bg_num"=>'9',
	"dark_mode_switch"=>0,
	"dark_mode_time_1"=>'19',
	"dark_mode_time_2"=>'7',
	"navbar_create_icon"=>1,
	"thread_user_del"=>0,
	"thread_user_upd"=>0,
	"theme_copyright_switch"=>1,
	"pic_lazyload_switch"=>0,
	"new_threadlist"=>1,
	"thread_footer_info"=>0,
	"thread_footer_info_content"=>'',
	"thread_emo_func"=>1,
	"config_exc_info"=>1,
	"contact_qq"=>1,
	"contact_qq_number"=>'',
	"contact_wx"=>1,
	"contact_wx_img"=>'',
	"contact_email"=>1,
	"contact_email_number"=>'',
	"site_bg_custom"=>0,
	"site_bg_custom_img"=>'',
	"site_bg_cover"=>0,
	"site_bg_scroll"=>0,
	"show_all_reply"=>1,
	"disc_page_index"=>0,
	"navbar_cate_func"=>1,
	"disc_nav_bbs"=>1,
	"func_left_custom"=>0,
	"func_left_custom_content"=>'',
	"pre_next_thread"=>0,
	"user_info_card"=>1,
);

setting_set('admin_light_setting', $admin_light_setting_config); 

message(-1, '<h3><i class="icon-cogs"></i> 恭喜您，安装成功！</h3><p>在您使用之请前先阅读以下几条建议：</p><p>1.建议将您的服务器PHP版本调至7.3，并且配置好伪静态以获得最佳体验。</p><p>2.（首次安装请忽略）如果您是迭代升级安装轻鸿3.0或更高版本，请不要覆盖安装！请先将服务器上原来主题包里面同名插件目录及主题目录删除，再上传新版本主题包里面的插件目录，在网站后台重新安装初始化一次配置。并且清除网站后台和浏览器缓存，防止配置出错。</p><p>3.请不要将主题包里面的插件分享外传，插件都是经过主题适配的，如果在别的地方使用出现问题，请自行负责！</p><p>最后感谢您的使用！开发不易，希望您能够支持正版，抵制盗版！谢谢！</p><a role="button" class="btn btn-primary btn-block m-t-1" href="./'.url("plugin-setting-zaesky_theme_light").'">我已了解并开始使用</a>');

?>