<?php
	$settings = array(
					'small'		=> array(
							'path'		=>	'/home/basketnews/domains/basketnews.lt/public_html/images/photos_small/',
							'width'		=>	'115',
							'height'	=>	'130',
						),
					'category'		=> array(
							'path'		=>	'/home/basketnews/domains/basketnews.lt/public_html/images/photos/small/',
							'width'		=>	'57',
							'height'	=>	'65',
						),
					'block'		=> array(
							'path'		=>	'/home/basketnews/domains/basketnews.lt/public_html/images/photos/orgnal/',
							'width'		=>	'185',
							'height'	=>	'300',
						),
					'big'		=> '/home/basketnews/domains/basketnews.lt/public_html/images/photos_big/',
					'watermark'	=> '/home/basketnews/domains/basketnews.lt/public_html/images/web/',
				);
	
	$size = (isset($settings[$_GET['size']])) ? $_GET['size'] : 'small';
	unset($_GET['size']);
	$_GET['src']	= $settings['big'] . $_GET['src'];
	
	switch ($size) {
		//case 'small':
		//case 'category':
		case 'block':
			$_GET['w'] = $settings[$size]['width'];
			$_GET['h'] = $settings[$size]['height'];
			$_GET['q'] = 100;
			break;
		//case 'big':
		//	$_GET['q'] = 100;
		//	$_GET['fltr'] = array("wmi|/themes/default/watermark.png|BR");
		//	break;
	}
	
	include('./phpthumb/phpThumb.php');
	
	//print_r($_SERVER['QUERY_STRING']);
?>
