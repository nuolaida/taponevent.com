<?php
	//include_once(SERVER_PATH . 'includes/Smarty/libs/Smarty.class.php');
	use Smarty\Smarty;
	$smarty = new Smarty();
	
	$smarty->setCompileDir(SMARTY_COMPILE_PATH . $page_special_config['keyword'] . '/' . get_side() . '/');
	$smarty->setCacheDir(SMARTY_CACHE_PATH . $page_special_config['keyword'] . '/' . get_side() . '/');
	$smarty->setTemplateDir(SMARTY_TEMPLATES_PATH);
	$smarty->use_sub_dirs = true;
	//$smarty->registerPlugin('modifier', 'gall_path', 'gallery_path');
	$smarty->registerPlugin('modifier', 'translate', 'smarty_modifier_translate');
	$smarty->registerPlugin('modifier', 'make_url', 'smarty_modifier_make_url');
	$smarty->registerPlugin('modifier', 'seconds_to_date_format', 'smarty_modifier_seconds_to_date_format');
	
	$smarty->registerPlugin('function', 'actions_log', 'smarty_function_actions_log');
	$smarty->registerPlugin('function', 'md5_encrypt', 'smarty_function_md5_encrypt');
	$smarty->registerPlugin('function', 'concat', 'smarty_concat');
	$smarty->registerPlugin('modifier', 'my_date_format', 'smarty_modifier_my_date_format');
	$smarty->registerPlugin('modifier', 'float_numbers', 'smarty_modifier_float_numbers');
	//$smarty->registerPlugin('function', 'friendly_url', 'smarty_function_friendly_url');
	$smarty->registerPlugin('block', 'dynamic', 'smarty_block_dynamic', false);
	//$smarty->registerPlugin('modifier', 'get_text_date', 'smarty_modifier_get_text_date');
	$smarty->registerPlugin('modifier', 'my_truncate', 'smarty_modifier_my_truncate');
	$smarty->registerPlugin('modifier', 'clearSpaces', 'smarty_modifier_clear_spaces');
	$smarty->registerPlugin('modifier', 'clearXml', 'smarty_modifier_clear_xml');
	//$smarty->registerPlugin('modifier', 'money_format', 'smarty_modifier_money_format');
	
	$smarty->registerPlugin('modifier', 'amake_url', 'smarty_modifier_admin_make_url');
	$smarty->registerPlugin('modifier', 'display_phone', 'smarty_modifier_display_phone');
	$smarty->registerPlugin("modifier", "abs", "abs");
	
	if (class_exists('Memcache')) {
	    $smarty->caching_type = 'memcache';
	
	    include_once(INCLUDES_FILES_PATH . 'smarty.memcache.php');
	}
