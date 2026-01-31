<?php
class Gallery {
	var $config, $domain_keyword;
	
	
	function __construct() {
		global $page_special_config;

		$this->config = array(
				'images_path'			    =>	GALLERY_IMAGES_PATH,
				'cache_path'			    =>	GALLERY_IMAGES_CACHE_PATH,
				'max_upload_file_format'	=>	'any2400x2400',
			);

		$this->domain_keyword = $page_special_config['keyword'];
	}
	
	
	// Tipai su ju nustatymais
	function get_type_cfg($type_id) {
		// Galimos reiksmes
		// * width			- paveikslelio plotis
		// * height			- paveikslelio aukstis
		// * crop			- ar nukirpti netelpanti vaizda
		// * background		- je nurodytas, grazina tiksliai pageidaujamo dydzio, likusi plota uzpildant fono spalva
		//						* r - red (0-255)
		//						* g - green (0-255)
		//						* b - blue (0-255)
		// * quality		- paveikslelio kokybe
		// * roundcorners	- je nurodytas, grazina užapvalintais kampais
		//						* radius	- kokio spindulio apskritimas, uzapvalinantis kampa. Kuo didenis tuo kampas apvalesnis
		//						* color		- spalva, kuria uzdazomi kampai
		//										* r - red (0-255)
		//										* g - green (0-255)
		//										* b - blue (0-255)
		//						* angles	- kampai, kuriuos reikia užapvalinti. Masyvas iš elementų
		//										* tr, br, bl, tl
		
		$width			= 100;
		$height			= 100;
		$crop			= false;
		$quality		= 100;
		$cache			= false;
		$round_corners	= false;
		$background_for_transparency	= array(
				'r'	=>	255,
				'g'	=>	255,
				'b'	=>	255
			);
		
		if (substr($type_id, 0, 4) == 'crop') {
			list($width, $height) = explode('x', substr($type_id, 4));
			$cache		= true;
			$crop		= true;
			$quality	= 100;
			$background	= array(
					'r'	=>	255,
					'g'	=>	255,
					'b'	=>	255
				);

		} else if (substr($type_id, 0, 3) == 'any') {
			list($width, $height) = explode('x', substr($type_id, 3));
			$cache		= true;
			$crop		= false;
			$quality	= 100;
		}

		$cfg = array(
				'cache'			=>	$cache,
				'width'			=>	$width,
				'height'		=>	$height,
				'crop'			=>	$crop,
				'quality'		=>	$quality,
				'background_for_transparency'	=>	$background_for_transparency,
			);
		if (isset($background)) {
			$cfg['background'] = $background;
		}
		if (isset($roundcorners)) {
			$cfg['roundcorners'] = $roundcorners;
		}

		return $cfg;
	}



	// Save resized
	function add_resized_item($form, $size) {

		$form5 = [
			'keywords'          =>  $form['keywords'],
			'file_mime'         =>  $form['mime'],
			'time_expire'       =>  ((int)$form['time_expire']) ? $form['time_expire'] : null,
		];
		$id = $this->add_item($form5);

		if ($id) {
			$thumb = $this->make_thumb($id, $size, $form['content']);
			if ($thumb) {
				$form5 = [
					'mime' => image_type_to_mime_type(IMAGETYPE_JPEG),
					'size' => $thumb['file_size'],
					'width' => $thumb['file_width'],
					'height' => $thumb['file_height'],
					'content' => $thumb['file_content'],
				];

				$this->db_update_item($id, $form5);
			}
		}

		return $id;
	}



	// Add item
	function add_item($form) {
		$form5 = array(
			'file_mime'			=>	($form['mime']) ? $form['mime'] : null,
			'file_size'			=>	($form['size']) ? $form['size'] : null,
			'keywords'		=>	trim($form['keywords']),
			'domain_keyword'	=>	$this->domain_keyword,
			'file_width'		=>	($form['width']) ? $form['width'] : null,
			'file_height'		=>	($form['height']) ? $form['height'] : null,
			'time_expire'		=>	($form['time_expire']) ? $form['time_expire'] : null,
		);

		$sql = make_insert_query('gallery', $form5);
		$id = execute_sql_query($sql, 'insert');

		return $id;
	}



	// Add item by image url
	function add_item_by_url($url, $form=null) {

		$file_content = file_get_contents($url);

		if ($file_content) {
			$form5 = [
				'keywords'     => ($form['keywords']) ? $form['keywords'] : null,
				'content'      => $file_content,
				'time_expire'  => ((int)$form['time_expire']) ? $form['time_expire'] : null,
			];

			return $this->add_resized_item($form5, $this->config['max_upload_file_format']);
		}

		return false;
	}



	// Make thumb
	function make_thumb($gallery_id, $type_id, $file_content = null) {
		global $conf, $page_special_config;

		$type_cfg = $this->get_type_cfg($type_id);
 		// 2018-06-07 $data = $this->get_item($gallery_id, $type_id);
		if (!$file_content) {
			$data = $this->db_get_item($gallery_id);
			$file_content = $data['file_content'];
		}

		if ($type_cfg && $file_content) {
			$resource = imagecreatefromstring($file_content);

			$res_width = imagesx($resource);
			$res_height = imagesy($resource);

			if ($type_cfg['crop']) {
				// x - <, y - <
				if ($res_width < $type_cfg['width'] && $res_height < $type_cfg['height']) {
					$dst_x = (isset($type_cfg['background'])) ? round(($type_cfg['width'] - $res_width) / 2) : 0;
					$dst_y = (isset($type_cfg['background'])) ? round(($type_cfg['height'] - $res_height) / 2) : 0;
					$dst_w = $res_width;
					$dst_h = $res_height;

					$src_x = 0;
					$src_y = 0;
					$src_w = $res_width;
					$src_h = $res_height;

					$width_new = (isset($type_cfg['background'])) ? $type_cfg['width'] : $res_width;
					$height_new = (isset($type_cfg['background'])) ? $type_cfg['height'] : $res_height;

					// x - >, y - <
				} else if ($res_width > $type_cfg['width'] && $res_height < $type_cfg['height']) {
					$dst_x = 0;
					$dst_y = (isset($type_cfg['background'])) ? round(($type_cfg['height'] - $res_height) / 2) : 0;;
					$dst_w = $type_cfg['width'];
					$dst_h = $res_height;

					$src_x = round(($res_width - $type_cfg['width']) / 2);
					$src_y = 0;
					$src_w = $type_cfg['width'];
					$src_h = $res_height;

					$width_new = $type_cfg['width'];
					$height_new = (isset($type_cfg['background'])) ? $type_cfg['height'] : $res_height;

					// x - <, y - >
				} else if ($res_width < $type_cfg['width'] && $res_height > $type_cfg['height']) {
					$dst_x = (isset($type_cfg['background'])) ? round(($type_cfg['width'] - $res_width) / 2) : 0;
					$dst_y = 0;
					$dst_w = $res_width;
					$dst_h = $type_cfg['height'];

					$src_x = 0;
					$src_y = round(($res_height - $type_cfg['height']) / 2);
					$src_w = $res_width;
					$src_h = $type_cfg['height'];

					$width_new = (isset($type_cfg['background'])) ? $type_cfg['width'] : $res_width;
					$height_new = $type_cfg['height'];

					// x - >, y - >
				} else {
					$coef = min($res_height / $type_cfg['height'], $res_width / $type_cfg['width']);

					$dst_x = 0;
					$dst_y = 0;
					$dst_w = $type_cfg['width'];
					$dst_h = $type_cfg['height'];

					$src_x = round(($res_width - $type_cfg['width'] * $coef) / 2);
					$src_y = round(($res_height - $type_cfg['height'] * $coef) / 2);
					$src_w = round($type_cfg['width'] * $coef);
					$src_h = round($type_cfg['height'] * $coef);

					$width_new = $type_cfg['width'];
					$height_new = $type_cfg['height'];
				}

				// Jei nenukirpti
			} else {
				$coef = max($res_width / $type_cfg['width'], $res_height / $type_cfg['height']);

				$dst_x = (isset($type_cfg['background'])) ? round(($type_cfg['width'] - $res_width / $coef) / 2) : 0;
				$dst_y = (isset($type_cfg['background'])) ? round(($type_cfg['height'] - $res_height / $coef) / 2) : 0;
				$dst_w = ($coef > 1) ? round($res_width / $coef) : $res_width;
				$dst_h = ($coef > 1) ? round($res_height / $coef) : $res_height;

				$src_x = 0;
				$src_y = 0;
				$src_w = $res_width;
				$src_h = $res_height;

				$width_new = (isset($type_cfg['background'])) ? $type_cfg['width'] : $dst_w;
				$height_new = (isset($type_cfg['background'])) ? $type_cfg['height'] : $dst_h;
			}

			$dest_image = imagecreatetruecolor($width_new, $height_new);

			if (isset($type_cfg['background']) || isset($type_cfg['background_for_transparency'])) {
				$bg = ($type_cfg['background']) ? $type_cfg['background'] : $type_cfg['background_for_transparency'];
				$bgcolor = ImageColorAllocate($dest_image, $bg['r'], $bg['g'], $bg['b']);
				ImageFill($dest_image, 0, 0, $bgcolor);
			}
			imagecopyresampled($dest_image, $resource, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

			// Kampu apvalinimas
			if (isset($type_cfg['roundcorners']) && $type_cfg['roundcorners']) {
				$corner_color = imagecolorallocate($dest_image, $type_cfg['roundcorners']['color']['r'], $type_cfg['roundcorners']['color']['g'], $type_cfg['roundcorners']['color']['b']);

				if (in_array('br', $type_cfg['roundcorners']['corners'])) {
					imagearc($dest_image, $width_new-$type_cfg['roundcorners']['radius'],  $height_new-$type_cfg['roundcorners']['radius'], $type_cfg['roundcorners']['radius']*2,  $type_cfg['roundcorners']['radius']*2,  0, 90, $corner_color);
					imagefilltoborder($dest_image, $width_new-1, $height_new-1, $corner_color, $corner_color);
				}

				if (in_array('bl', $type_cfg['roundcorners']['corners'])) {
					imagearc($dest_image, $type_cfg['roundcorners']['radius']-1,  $height_new-$type_cfg['roundcorners']['radius'], $type_cfg['roundcorners']['radius']*2,  $type_cfg['roundcorners']['radius']*2,  90, 180, $corner_color);
					imagefilltoborder($dest_image, 0, $height_new-1, $corner_color, $corner_color);
				}

				if (in_array('tl', $type_cfg['roundcorners']['corners'])) {
					imagearc($dest_image, $type_cfg['roundcorners']['radius']-1, $type_cfg['roundcorners']['radius']-1, $type_cfg['roundcorners']['radius']*2,  $type_cfg['roundcorners']['radius']*2,  180, 270, $corner_color);
					imagefilltoborder($dest_image, 0, 0, $corner_color, $corner_color);
				}

				if (in_array('tr', $type_cfg['roundcorners']['corners'])) {
					imagearc($dest_image, $width_new-$type_cfg['roundcorners']['radius'], $type_cfg['roundcorners']['radius']-1, $type_cfg['roundcorners']['radius']*2,  $type_cfg['roundcorners']['radius']*2,  270, 360, $corner_color);
					imagefilltoborder($dest_image, $width_new-1, 0, $corner_color, $corner_color);
				}
			}


			ob_start();

			imagejpeg($dest_image, null, $type_cfg['quality']);
			ImageDestroy($dest_image);

			$buffer = ob_get_contents();
			ob_end_clean();

			$return  = array(
				'file_content'	=>	$buffer,
				'file_size'		=>	strlen($buffer),
				'file_width'	=>	$dst_w,
				'file_height'	=>	$dst_h,
			);
			return $return;

		}

		return false;
	}



	// Return image
	function get_item($id, $type_id, $cache = true) {
		if (!(int)$id || !$type_id) {
			// default image
			return false;
		}

		$cfg = $this->get_type_cfg($type_id);
		if ($cache && $cfg['cache']) {
			return $this->get_cached_item($id, $type_id);
		} else {
			//die('aaaaaaaaaaaaa');
			return $this->make_thumb($id, $type_id);
		}
		
	}
	
	
	// Return cached image
	function get_cached_item($gallery_id, $type_id) {

		$filename = $this->get_cache_file_path($gallery_id, $type_id);
		if (file_exists($filename)) {
			return array(
				'file_content'	=>	file_get_contents($filename),
				'file_size'		=>	filesize($filename),
			);
		} else {
			return $this->make_cache($gallery_id, $type_id);
		}

	}
	
	
	// Make image prapared for caching
	function make_cache($gallery_id, $type_id) {
		
		$data = $this->make_thumb($gallery_id, $type_id);
		$type_cfg = $this->get_type_cfg($type_id);

		if ($data['file_content'] && $data['file_size'] && $type_cfg['cache']) {
			$this->add_cache_item($gallery_id, $type_id, $data['file_content'], $data['file_size']);
		}
		
		return $data;
	}


	
	// Save to cache
	function add_cache_item($gallery_id, $type_id, $file_content, $file_size) {
		if (isset($gallery_id) && $type_id && $file_content && $file_size) {
			$filename = $this->get_cache_file_path($gallery_id, $type_id);
			$fp = fopen($filename, 'w');
			fwrite($fp, $file_content);
			fclose($fp);
			chmod($filename, 0777);

			return true;
		}
		
		return false;
	}



	// Record from DB
	function db_get_item($id) {
		$sql = "SELECT * FROM gallery WHERE id = " . (int)$id;
		$data = execute_sql_query($sql, 'get row');

		$filename = $this->get_file_path($data['id'], $data['file_mime']);
		$handle = fopen($filename, "r");
		$data['file_content'] = fread($handle, filesize($filename));
		fclose($handle);

		return $data;
	}



	// Update DB record
	function db_update_item($id, $form) {
		$form5 = array();

		if ($form['mime']) {
			$form5['file_mime'] = $form['mime'];
		}
		if ($form['content']) {
			$filename = $this->get_file_path($id, $form5['file_mime']);
			$fp = fopen($filename, 'wb');
			fwrite($fp, $form['content']);
			fclose($fp);
			//chmod($filename, 0777);

			$form5['file_size'] = ($form['size']) ? $form['size'] : strlen($form['content']);
		}
		if ($form['keywords']) {
			$form5['keywords'] = trim($form['keywords']);
		}
		if (isset($form['width'])) {
			$form5['file_width'] = (int)$form['width'];
		}
		if (isset($form['height'])) {
			$form5['file_height'] = (int)$form['height'];
		}

		if (count($form5) && $id) {
			$sql = make_update_query('gallery', $form5, array('id' => $id));
			execute_sql_query($sql, 'update');

			return true;
		}

		return false;
	}



	// Delete
	function db_delete_item($id) {
		$data = $this->db_get_item($id);
		if (!$data) {
			return false;
		}

		$sql = "DELETE FROM gallery WHERE id = " . (int)$id . " AND domain_keyword = '" . $this->domain_keyword . "'";
		$ok = execute_sql_query($sql, 'delete');
		if (!$ok) {
			return false;
		}

		$path = $this->get_file_path($data['id'], $data['file_mime']);
		if (!$path) {
			return false;
		}
		unlink($path);

		return true;
	}


	// kesuojamo failo kategorija
	function get_cache_file_path($gallery_id, $type_id) {
		$split = array(100, 100);

		$path = $this->config['cache_path'];
		if (!file_exists($path)) {
			if (strpos($this->config['cache_path'], SERVER_PATH) !== false) {
				$path = substr($this->config['cache_path'], strlen(SERVER_PATH));
				$arr_path = explode('/', $path);
				$path = SERVER_PATH;
				foreach ($arr_path as $folder) {
					$path = $path . $folder . '/';
					if ($folder && !file_exists($path)) {
						mkdir($path, 0777);
						chmod($path, 0777);
					}
				}
			} else {
				die('cache path does not exists');
			}
		}
		
		$path = $path . $this->domain_keyword . '/';
		if (!file_exists($path)) {
			mkdir($path, 0777);
			chmod($path, 0777);
		}

		$path = $path . ((($gallery_id - ($gallery_id % $split[1])) / $split[1]) % $split[0]) . '/';
		if (!file_exists($path)) {
			mkdir($path, 0777);
			chmod($path, 0777);
		}

		$path = $path . ($gallery_id % $split[1]) . '/';
		if (!file_exists($path)) {
			mkdir($path, 0777);
			chmod($path, 0777);
		}

		$path = $path . (str_replace(array('.', '/', '\\'), '', $type_id)) . '/';
		if (!file_exists($path)) {
			mkdir($path, 0777);
			chmod($path, 0777);
		}

		return $path . $gallery_id . '.jpg';
	}



	// originalaus failo kategorija
	function get_file_path($gallery_id, $file_mime, $split = array(100, 10)) {
		$mimes = array(
				'image/jpeg'	=>	'jpg',
				'image/gif'		=>	'gif',
				'image/png'		=>	'png',
			);

		$path = $this->config['images_path'];
		if (!file_exists($path)) {
			die('file path does not exists: ' . $path);
		}

		if ($gallery_id) {
			if (!is_array($split)) {
				$split = array($split);
			}
			
			$path = $path . $this->domain_keyword . '/';
			if (!file_exists($path)) {
				mkdir($path, 0777);
				chmod($path, 0777);
			}
			
			if (count($split) == 2) {
				$path = $path . ((($gallery_id - ($gallery_id % $split[1])) / $split[1]) % $split[0]) . '/';
				if (!file_exists($path)) {
					mkdir($path, 0777);
					chmod($path, 0777);
				}

				$path = $path . ($gallery_id % $split[1]) . '/';
				if (!file_exists($path)) {
					mkdir($path, 0777);
					chmod($path, 0777);
				}
			} else {
				$path = $path . ($gallery_id % $split[0]) . '/';
				if (!file_exists($path)) {
					mkdir($path, 0777);
					chmod($path, 0777);
				}
			}

			return $path . $gallery_id . '.' . (($mimes[$file_mime]) ? $mimes[$file_mime] : reset($mimes));
		} else {
			return $path . $this->config['default_image'];
		}
	}

}
