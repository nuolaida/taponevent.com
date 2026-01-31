<?php
	error_reporting(E_ERROR | E_PARSE);
	
	require __DIR__ . '/../vendor/autoload.php';

	function is_development_version() {
		$is_development_version = false;
		preg_match("/.*\.(.*)$/", $_SERVER['HTTP_HOST'], $matches);
		if ($matches[1] == 'localhost') {
			$is_development_version = true;
		}

		return $is_development_version;
	}


	$id = (int)$_GET['id'];
	$type = $_GET['type'];
	require_once($_SERVER['DOCUMENT_ROOT'] . '/config.globals.php');
	require_once(CONFIG_FILES_PATH . 'config.bn2.php');
	require_once(CONFIG_FILES_PATH . 'config.db.php');
	$test = false;
	
	if ($type == 'or') {
		die();
	}

	// Domain config
	if (file_exists(SERVER_PATH . 'config/' . $domain_keyword . '/config.php')) {
		require_once SERVER_PATH . 'config/' . $domain_keyword . '/config.php';
		$page_special_config = $domain_config;
	}


	
	require_once(LIBS_MAIN_PATH . 'gallery.class.php');
	$Gallery = new Gallery;
	if ($test) {
		$adodb->debug = true;
	}
	
	$data = $Gallery->get_item($id, $type);

	
	if (!$test) {
		header('Content-type: image/jpeg');
		header('Accept-Ranges: bytes');
		header('Content-Length: ' . $data['file_size']);
		outputCacheHeaders(time(), $type);
	}
	echo $data['file_content'];
	
	die();
	
	
function outputCacheHeaders($image_time, $type) {
	$image_time = ($image_time) ? $image_time : time();

	header("Cache-Control: max-age=" . (7*24*60*60)); //Tarkim nieko nekešuojam ilgiau nei valanda

	$send_304 = false;
	if (php_sapi_name() == 'apache') {

		$ar = apache_request_headers();
		if (isset($ar['If-Modified-Since']) && // If-Modified-Since should exists
		($ar['If-Modified-Since'] != '') && // not empty
		(strtotime($ar['If-Modified-Since']) >= $image_time)) // and grater than
		$send_304 = true;                                     // image_time
	}
   
   
	if ($send_304)
	{
		// Sending 304 response to browser
		// "Browser, your cached version of image is OK
		// we're not sending anything new to you"
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', $image_time).' GMT', true, 304);
   
		exit(); // bye-bye
	}

	// outputing Last-Modified header
	header('Last-Modified: '.gmdate('D, d M Y H:i:s', $image_time).' GMT', true, 200);
   
	// Set expiration time +24 hour
	// We do not have any photo re-uploading
	// so, browser may cache this photo for quite a long time
	header('Expires: '.gmdate('D, d M Y H:i:s',  $image_time + 60*60*24*7).' GMT', true, 200);
	
	header('Pragma: ');
}
	
?>
