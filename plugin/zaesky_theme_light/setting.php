<?php

!defined('DEBUG') and exit('Access Denied.');

$header['title'] = lang('setting');

if ($method == 'GET') {
	
	$light_config = setting_get('admin_light_setting');
	
	include _include(APP_PATH.'plugin/zaesky_theme_light/view/htm/setting.htm');
	
} else if ($method == 'POST'){
	
	$light_config = array();
	 
	$light_config['side_nav_switch'] = param('side_nav_switch', 1);
	$light_config['thread_list_style'] = param('thread_list_style', 1);
	$light_config['thread_reply_reload'] = param('thread_reply_reload', 0);
	$light_config['window_no_console'] = param('window_no_console', 0);
	$light_config['thread_top_nav'] = param('thread_top_nav', 0);
	$light_config['nav_search_form'] = param('nav_search_form', 0);
	$light_config['body_font_style'] = param('body_font_style', 0);
	$light_config['header_gold_count'] = param('header_gold_count',1);
	$light_config['groupicon_display'] = param('groupicon_display',0);
	$light_config['post_show_first_floor'] = param('post_show_first_floor',0);
	$light_config['post_show_floors'] = param('post_show_floors',1);
	$light_config['index_userinfo'] = param('index_userinfo',1);
	$light_config['credits_progress'] = param('credits_progress',0);
	$light_config['post_show_time'] = param('post_show_time',0);
	$light_config['post_func_position'] = param('post_func_position',0);
	$light_config['post_form_position'] = param('post_form_position',0);
	$light_config['thread_left_switch'] = param('thread_left_switch',1);
	$light_config['thread_left_func'] = param('thread_left_func',0);
	$light_config['thread_left_tools'] = param('thread_left_tools',1);
	$light_config['back_top'] = param('back_top',1);
	$light_config['thread_top_ind'] = param('thread_top_ind',1);
	$light_config['thread_summary'] = param('thread_summary',1);
	$light_config['thread_summary_word'] = param('thread_summary_word',100);
	$light_config['login_guide_1'] = param('login_guide_1',"你好！欢迎来访！");
	$light_config['login_guide_2'] = param('login_guide_2',"请登录后探索更多精彩内容！");
	$light_config['login_guide_icon'] = param('login_guide_icon',"fas fa-grin-wink");
	$light_config['site_annoucement_switch'] = param('site_annoucement_switch',0);
	$light_config['site_annoucement_position'] = param('site_annoucement_position',0);
	$light_config['site_annoucement_style'] = param('site_annoucement_style',0);
	$light_config['site_annoucement_content'] = param('site_annoucement_content','',false);
	$light_config['site_annoucement_icon'] = param('site_annoucement_icon',"fas fa-bullhorn");
	$light_config['login_annoucement_content'] = param('login_annoucement_content','',false);
	$light_config['comment_annoucement_content'] = param('comment_annoucement_content','',false);
	$light_config['header_my_favorite'] = param('header_my_favorite',0);
	$light_config['user_online'] = param('user_online',0);
	$light_config['new_thread'] = param('new_thread',0);
	$light_config['dock_switch'] = param('dock_switch',0);
	$light_config['dock_func_3'] = param('dock_func_3',0);
	$light_config['thread_forum_name'] = param('thread_forum_name',1);
	$light_config['index_mobile_statistic'] = param('index_mobile_statistic',1);
	$light_config['thread_last_reply'] = param('thread_last_reply',1);
	$light_config['site_info_bg'] = param('site_info_bg','');
	$light_config['site_info_logo'] = param('site_info_logo','/view/img/favicon.ico');
	$light_config['site_info_switch'] = param('site_info_switch',1);
	$light_config['thread_user_bg'] = param('thread_user_bg',1);
	$light_config['site_info_total'] = param('site_info_total',1);
	$light_config['thread_summary_pic'] = param('thread_summary_pic',3);
	$light_config['navbar_cate'] = param('navbar_cate',1);
	$light_config['thread_subject_size'] = param('thread_subject_size','14');
	$light_config['thread_quick_at'] = param('thread_quick_at',0);
	$light_config['disc_page'] = param('disc_page',0);
	$light_config['disc_page_title'] = param('disc_page_title','发现',false);
	$light_config['disc_page_icon'] = param('disc_page_icon','fas fa-atom');
	$light_config['disc_page_banner'] = param('disc_page_banner','');
	$light_config['disc_page_banner_a'] = param('disc_page_banner_a','');
	$light_config['user_bg_switch'] = param('user_bg_switch',0);
	$light_config['user_bg_num'] = param('user_bg_num','9');
	$light_config['dark_mode_switch'] = param('dark_mode_switch',0);
	$light_config['dark_mode_time_1'] = param('dark_mode_time_1','19');
	$light_config['dark_mode_time_2'] = param('dark_mode_time_2','7');
	$light_config['navbar_create_icon'] = param('navbar_create_icon',1);
	$light_config['thread_user_del'] = param('thread_user_del',0);
	$light_config['thread_user_upd'] = param('thread_user_upd',0);
	$light_config['theme_copyright_switch'] = param('theme_copyright_switch',1);
	$light_config['pic_lazyload_switch'] = param('pic_lazyload_switch',0);
	$light_config['new_threadlist'] = param('new_threadlist',1);
	$light_config['thread_footer_info'] = param('thread_footer_info',0);
	$light_config['thread_footer_info_content'] = param('thread_footer_info_content','',false);
	$light_config['thread_emo_func'] = param('thread_emo_func',0);
	$light_config['config_exc_info'] = param('config_exc_info',1);
	$light_config['contact_qq'] = param('contact_qq',1);
	$light_config['contact_qq_number'] = param('contact_qq_number','');
	$light_config['contact_wx'] = param('contact_wx',1);
	$light_config['contact_wx_img'] = param('contact_wx_img','');
	$light_config['contact_email'] = param('contact_email',1);
	$light_config['contact_email_number'] = param('contact_email_number','');
	$light_config['site_bg_custom'] = param('site_bg_custom',0);
	$light_config['site_bg_custom_img'] = param('site_bg_custom_img','');
	$light_config['site_bg_cover'] = param('site_bg_cover',0);
	$light_config['site_bg_scroll'] = param('site_bg_scroll',0);
	$light_config['show_all_reply'] = param('show_all_reply',0);
	$light_config['disc_page_index'] = param('disc_page_index',0);
	$light_config['navbar_cate_func'] = param('navbar_cate_func',1);
	$light_config['disc_nav_bbs'] = param('disc_nav_bbs',1);
	$light_config['func_left_custom'] = param('func_left_custom',0);
	$light_config['func_left_custom_content'] = param('func_left_custom_content','',false);
	$light_config['pre_next_thread'] = param('pre_next_thread',0);
	$light_config['user_info_card'] = param('user_info_card',1);
	setting_set('admin_light_setting', $light_config); 

	message(0, jump(lang('admin_setting_config_success'), url('plugin-setting-zaesky_theme_light')));
}

?>