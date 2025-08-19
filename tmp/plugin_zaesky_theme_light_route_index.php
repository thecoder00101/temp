<?php

if(isset($light_config['disc_page']) && $light_config['disc_page'] && $light_config['disc_page_index']) {

	
	include _include(APP_PATH.'plugin/zaesky_theme_light/view/htm/discovery.htm');
} else {
	
	
	include _include(APP_PATH.'route/index.php');
}

?>