<?php
/*
	Funkcijos visiems gyvenimo atvejams  :)
*/


// Permetame kur norime
function Location($URL, $addToken = 1, $send_header = 0) {
	$questionORamp = (strstr($URL, "?"))?"&":"?";
	//$location = ($addToken && substr($URL, 0, 7) != "http://")?$URL.$questionORamp.$SID:$URL; //append the sessionID ($SID) by default
	$location = $URL;
	ob_end_clean(); //clear buffer, end collection of content
	if(headers_sent() || $send_header) {
		print('<script language="JavaScript"><!--
			location.replace("'.$location.'");
			// --></script>
			<noscript><META http-equiv="Refresh" content="0;URL='.$location.'"></noscript>');
	} else {
		header('Location: '.$location); //forward to another page
		exit; //end the PHP processing
	}
}


	// make html from bb code
	function do_ubb ($article, $full=1) {
		$article = str_replace("\"", "&quot;", $article);
		$article = str_replace("<", "&lt;", $article);
		$article = str_replace(">", "&gt;", $article);
		$article = str_replace("\n", "<br>", $article);
		$article = str_replace("  ", "&nbsp; ", $article);
		if ($full) {
			$article = preg_replace("/\[b\](.*?)\[\/b\]/i","<strong>\\1</strong>", $article);
			$article = preg_replace("/\[i\](.*?)\[\/i\]/i","<i>\\1</i>", $article);
			$article = preg_replace("/\[u\](.*?)\[\/u\]/i","<u>\\1</u>", $article);
			$article = preg_replace("/\[link\=(.*?)\](.*?)\[\/link\]/i","<a target=\"_top\" href=\"\\1\">\\2</a>", $article);
			$article = preg_replace("/\[url\=(.*?)\](.*?)\[\/url\]/i","<a target=\"_blank\" href=\"\\1\">\\2</a>", $article);
			$article = preg_replace("/\[url\](.*?)\[\/url\]/i","<a target=\"_blank\" href=\"\\1\">\\1</a>", $article);
			//$article = preg_replace("/\[icq\=(.*?)\](.*?)\[\/icq\]/i","<a target=\"_blank\" href=\"http://wwp.icq.com/scripts/Search.dll?to=\\1\">\\2</a>",$article);
			//$article = preg_replace("/\[color\=(.*?)\](.*?)\[\/color\]/i","<font color=\\1>\\2</font>", $article);
			//$article = preg_replace("/\[email\=(.*?)\](.*?)\[\/email\]/i","<a href=\"mailto:\\1\">\\2</a>", $article);
			$article = preg_replace("/\[div (.*?)\](.*?)\[\/div\]/i","<div align=\"\\1\">\\2</div>", $article);
			$article = preg_replace("/\[quote\](.*?)\[\/quote\]/i","<blockquote>\\1</blockquote>", $article);
		}
		// Pasaliname ')' ir '!' pertekliu, nes IE turi bug'a ne wrapinti.
		$article = preg_replace("/([!]{3,})/i","!!!", $article);
		$article = preg_replace("/([\)]{3,})/i",")))", $article);
		$article = preg_replace("/([\(]{3,})/i","(((", $article);
		$article = preg_replace("/([\?]{3,})/i","???", $article);
		$article = preg_replace("/([\/]{3,})/i","///", $article);

		$article = addslashes($article);

		return $article;
	}

	// back to ubb from html
	function undo_ubb ($article) {
		$article = str_replace("<br>","\n",$article);
		$article = str_replace("&nbsp; ", "  ", $article);
		$article = str_replace("<b>","[b]", $article);
		$article = str_replace("</b>","[/b]", $article);
		$article = str_replace("<i>","[i]", $article);
		$article = str_replace("</i>","[/i]", $article);
		$article = str_replace("<u>","[u]", $article);
		$article = str_replace("</u>","[/u]", $article);
		$article=preg_replace("/\<a target\=\"_top\" href\=\"(.*?)\"\>(.*?)\<\/a\>/i","[link=\\1]\\2[/link]", $article);
		$article=preg_replace("/\\<a target=\"_blank\" href=\"(.*?)\"\\>([^<\[]*)<\/a>/i","[url=\\1]\\2[/url]", $article);
		$article=preg_replace("/\<a target\=\"_blank\" href=\"http:\/\/wwp.icq.com\/scripts\/Search.dll\\?to=(.*?)\"\>(.*?)\<\/a\>/i","[icq=\\1]\\2[/icq]", $article);
		$article=preg_replace("/\<font color\=(.*?)\>(.*?)\<\/font\>/i" ,"[color=\\1]\\2[/color]", $article);
		$article=preg_replace("/\<a href\=\"mailto:(.*?)\"\>(.*?)\<\/a\>/i","[email=\\1]\\2[/email]", $article);
		$article=preg_replace("/\<div align\=\"(.*?)\"\>(.*?)\<\/div\>/i","[div \\1]\\2[/div]", $article);

		return $article;
	}


	// Debuging
	function debug($arr, $show = true) {
		if (!$show) {
			$tmp = "\n\n<!--\nBegin:\n";
			$tmp .= print_r($arr, true);
			$tmp .= "$tmp\n:end\n\n-->";
			return $tmp;
		} else {
			echo "\n\n<hr>Begin:<pre>";
			var_dump($arr);
			echo "</pre>:end<hr>\n\n";
		}
	}


	// Perduoto kintamojo reiksmems padaro stripslashes
	function my_strip($v) {
		if (is_array($v)){
			foreach ($v as $id => $value){
				$v[$id] = my_strip($value);
			}
		} else {
			$v = stripslashes($v);
		}
		return $v;
	}


	// isvalo failo pavadinima
	function clean_file_name($name) {
		$name = strtolower($name);
		return preg_replace("[^0-9a-z_\.\-]", "_", $name);
	}


	// jei jau yra failas tokiu pavadinmu - ji padaro unikalu pridedant gale skaiciu
	function unique_file_name($name, $path) {
		if (!preg_match("/^.*\/$/", $path)) {
			$path = $path . '/';
		}

		while (file_exists($path . $name)) {
			$this_file_name = file_extention($name, 1);
			$this_file_ext = file_extention($name);

			if (preg_match("/^.+_\d+$/", $this_file_name)) {
				$this_file_name_real = preg_replace("/^(.+)_\d+$/", "\\1", $this_file_name);
				$this_file_name_cnt = preg_replace("/^.+_(\d+)$/", "\\1", $this_file_name);
			} else {
				$this_file_name_real = $this_file_name;
				$this_file_name_cnt = 0;
			}

			$this_file_name_cnt++;
			$name = $this_file_name_real . '_' . $this_file_name_cnt . '.' . $this_file_ext;

		}
		return $name;
	}


	// Grazina failo tipa
	function file_extention($name, $part = 2) {
		if ($part == 1) {
			return preg_replace("/(.+)\.\w+/", "\\1", $name);
		} else {
			return preg_replace("/.+\.(\w+)/", "\\1", $name);
		}
	}


	// Siek tiek perdaryta preg_quot funkcija
	function my_preg_quote($string) {
		$string = preg_quote($string);
		$string = str_replace('"', '\"', $string);
		$string = str_replace('/', '\/', $string);

		return $string;
	}

	// substr string by space
	function substr_x($text, $length) {
		if (strlen($text) > $length) {
			$new_text = substr($text, 0, ($length + 1));
			$i = $length;
			while (($new_text[$i] != ' ') && ($i > ($length / 100 * 40))) {
				$new_text = substr($text, 0, $i--);
			}
			if ($i <= ($length / 100 * 40)) {
				$new_text = substr($text, 0, $length);
			}

			return $new_text . '...';
		} else {
			return $text;
		}
	}

	// Removes averything like <*>
	function clear_from_html ($text) {
		preg_match_all("/\<(.*?)\>/", $text, $matches, PREG_PATTERN_ORDER);
		foreach ($matches[0] as $key => $value) {
			$text = str_replace($value, '', $text);
		}

		$text = str_replace('&nbsp;', ' ', $text);
		//$text = str_replace($text, '&quot;', $text);

		return $text;
	}

	// Removes averything like [*]
	function clear_from_ubb ($text) {
		preg_match_all("/\[(.*?)\]/", $text, $matches, PREG_PATTERN_ORDER);
		foreach ($matches[0] as $key => $value) {
			$text = str_replace($value, '', $text);
		}

		return $text;
	}

	// Removes averything starting http or https
	function clear_from_links($text) {
		return preg_replace("/\shttp([s]*):\/\/(.*?)\s/", " ", $text);
	}


	// Nukerta ir gale daugtaski prideda
	function cut_string($string, $length) {
		if (strlen($string) > $length) {
			if (preg_match("/[^a-zA-Z0-9 \.]/", substr($string, $length - 1, 1))) {
				if (preg_match("/[^a-zA-Z0-9 \.]/", substr($string, $length, 1))) {
					$length++;
				}
			}
			if (strlen($string) > $length) {
				$string = substr($string, 0, $length) . "...";
			}
		}
		return $string;
	}

	// Tikriname ar geras el pasto adresas
	function check_email($email) {
		$email = trim($email);
		if (preg_match("/^[0-9a-z\.\-_\+]+@[0-9a-z\.\-_]+\.\w{2,4}$/i", $email)) {
			return $email;
		} else {
			return false;
		}
	}
	
	
	function split_email($text) {
		$arr_emails = [];
		$text = str_replace([',', ';'], ' ', $text);
		$text = preg_replace("/\s/", " ", $text);
		$text = preg_replace("/(\s)+/", " ", $text);
		$list = explode(' ', $text);
		if ($list) {
			foreach ($list as $item) {
				if (trim($item)) {
					$item = check_email(strtolower(trim($item)));
					if ($item) {
						$arr_emails[] = $item;
					}
				}
			}
		}
		
		return $arr_emails;
	}
	
	
	// Tikriname ar gera data
	function check_date($date) {
		$oki = true;
		$date = trim($date);

		if (strlen($date) == 10) {
			$arr = explode('-', $date);
			$y = $arr[0] + 0;
			$m = $arr[1] + 0;
			$d = $arr[2] + 0;
			if ($y < 1900 || $y > date("Y")) {
				$oki = false;
			}
			if ($m + 0 < 1 || $m + 0 > 12) {
				$oki = false;
			}
			if ($d + 0 < 1 || $d + 0 > 31) {
				$oki = false;
			}
			$in = array(4, 6, 9, 11);
			if (in_array($m, $in) && $d > 30) {
				$oki = false;
			}
			if ($m == 2) {
				if (($y % 4 != 0 || $y % 100 == 0) && $d > 28) {
					$oki = false;
				} else if ($d > 29) {
					$oki = false;
				}
			}
		} else {
			$oki = false;
		}

		return ($oki) ? $date : $oki;
	}

	// Generates random password
	function randpasswd($length) {
		return random_string($length, "1234567890");
	}



	// return Random String
	function random_string($length, $values_string=false) {
    	$symbols = ($values_string) ? $values_string : "1234567890abcdefghjkmnprstuvz";
	    $pass = "";

	    while(strlen($pass) < $length) {
			$rnd = rand(0, strlen($symbols) - 1);
	        $pass .= substr($symbols, $rnd, 1);
	    }

	    return $pass;
	}



	// Server path info
	function server_path_info($part = 'public') {
		switch ($part) {
			case 'admin':
				return (preg_match("/admin\.php\//", $_SERVER['REQUEST_URI'])) ? '../' : '';
				break;
			case 'app':
				return (preg_match("/app\.php\//", $_SERVER['REQUEST_URI'])) ? '../' : '';
				break;
			default:
				return (preg_match("/index\.php\//", $_SERVER['REQUEST_URI'])) ? '../' : '';
				break;
		}
	}


	// adds spaces skipping bbcode every $length symbols
	function wordwrap_ubb($text, $length) {
		$new_text = str_replace('<br>', ' ', $text);
		$new_text = clear_from_html($new_text);

		$arr = explode(' ', $new_text);
		$replaceArr = array();
		foreach ($arr as $key => $value) {
			if (strlen($value) > $length) {
				$replaceArr[] = $value;
			}
		}

		$new_text = ' ' . $text . ' ';
		foreach ($replaceArr as $key => $value) {
			$str_length = strlen($value);
			$string = '';
			for ($i=0; $i<$str_length;) {
				$string .= substr($value, $i, $length) . ' ';
				$i += $length;
			}
			$new_text = preg_replace("/([\> ])" . preg_quote($value, "/") . "([ \<])/i", "\\1" . trim($string) . "\\2", $new_text);
		}

		return $new_text;
	}


	// Grazina pirmaja teksto raide
	function first_letter($text) {
		$text = trim($text);
		$text = mb_strtoupper($text, "utf-8");
		$letter = mb_substr($text, 0, 1);
		if (!preg_match("/[a-zA-Z0-9]/", $letter)) {
			$letter = mb_substr($text, 0, 2);
		}
		return $letter;
	}

	function smarty_modifier_first_letter($text) {
		return first_letter($text);
	}


	// Statistikai graziai padaromi skaiciai
	function float_numbers($value, $dec=2) {
		$rez = $value;
		$rez = round($rez, $dec);

		$arr = explode('.', $rez);
		$arr[1] = (isset($arr[1])) ? $arr[1] : 0;
		if ($dec) {
			$rez = $arr[0] . ',' . str_pad($arr[1], $dec, "0", STR_PAD_RIGHT);
		} else {
			$rez = $arr[0];
		}

		return $rez;
	}
	function stat_numbers($value, $dec) {
		return float_numbers($value, $dec);
	}
	function smarty_modifier_float_numbers($value, $dec=2) {
		return float_numbers($value, $dec);
	}


	// Grazina data zodziu jei reikia
	function get_text_date($date, $format = 'default') {
		global $Translate;
		$time = (is_numeric($date)) ? $date : strtotime($date);

		if ($time) {
			$time = time() - $time;
			$old_date = my_date_format($date, 'middle');

			$min = floor($time / 60);
			$hours = floor($min / 60);
			$min = $min - $hours * 60;

			if ($hours < 12) {
				$r = $Translate->get_item('before');
				if ($hours > 0) {
					$r .= ' ' . $hours . ' val.';
				}
				if ($min != 0 || $hours == 0) {
					$r .= ($hours == 0) ? ' ' . $min . ' min.' : ' ir ' . $min . ' min.';
				}
				return $r;
			} else {
				return $old_date;
			}
		}

		return $date;
	}
	function smarty_modifier_get_text_date($date) {
		return get_text_date($date);
	}


	// Paskaiciuoja kiek dienu liko iki nurodytos datos
	function left_days($date) {
		if (!preg_match("/^\d{4}-\d{2}-\d{2}/", $date)) {
			return false;
		}

		$rec_time = mktime(0, 0, 0, substr($date, 5, 2), substr($date, 8, 2), substr($date, 0, 4));
		$left = $rec_time - time();
		if ($left > 0) {
			$day = 60 * 60 * 24;
			return ceil($left / $day);
		} else {
			return 0;
		}
	}



	// tekste pakeicia [image=...] i tai, ka reikia
	function text_images2($text) {
		global $adodb, $Translate;


		// Nauji paveiksleliai
		preg_match_all("/<img.*?src=[^>]*?paveikslelis\-(\d+)\-([^\.]+).*?\/>/im", $text, $matches, PREG_SET_ORDER);
		foreach ($matches as $item) {
			$image = array(
					'html'	=>	$item[0],
					'id'	=>	$item[1],
					'type'	=>	$item[2],
				);

			// Images for mobile ver.
			$mobile_version = (is_mobile_version()) ? true : false;
			if ($mobile_version) {
				switch ($image['type']) {
					case 'bg':
						$image['type'] = 'any310x600';
						break;
				}
			}

			if (preg_match("/.*align=.*(left|right).*/im", $item[0], $match )) {
				$image['align'] = $match[1];
			} else if (preg_match("/.*float:[\s]*(left|right).*/im", $item[0], $match )) {
				$image['align'] = $match[1];
			}
			if (preg_match("/.*width=[^\d](\d+).*/im", $item[0], $match )) {
				$image['width'] = $match[1];
			}
			if (preg_match("/.*height=[^\d](\d+).*/im", $item[0], $match )) {
				$image['height'] = $match[1];
			}
			if (preg_match("/.*title=\"(.*?)\".*/im", $item[0], $match )) {
				$image['title'] = str_replace('"', '`', $match[1]);
			} else if (preg_match("/.* alt=\"(.*?)\".*/im", $item[0], $match )) {
				$image['title'] = str_replace('"', '`', $match[1]);
			}

			$rs = $adodb->getRow("SELECT * FROM bn2_gallery3 WHERE id = " . (int)$image['id']);
			if ($rs) {
				if ($rs['rec_author']) {
					$image['author'] = $rs['rec_author'];
				}
				if (!$image['title'] && $rs['rec_description']) {
					$image['title'] = str_replace('"', '`', $rs['rec_description']);
				}
			}

			$image['new_html'] = '<div class="image_block"';
			if ($image['align']) {
				$image['new_html'] .= ' style="float: ' . $image['align'] . ';';
				$image['new_html'] .= ($image['align'] == 'left') ? ' padding-right: 10px;' : ' padding-left: 10px;';
				if ($image['width'] && $mobile_version) {
					$image['new_html'] .= ' width: ' . $image['width'] . ';';
				}
				$image['new_html'] .= '"';
			}
			$image['new_html'] .= '>';
			$image['new_html'] .= '<div class="image">';
			$image['new_html'] .= '<img src="/paveikslelis-' . $image['id'] . '-' . $image['type'] . '.jpg"';
			if ($image['title']) {
				$image['new_html'] .= ' title="' . $image['title'] . '"';
				$image['new_html'] .= ' alt="' . $image['title'] . '"';
			} else {
				$image['new_html'] .= ' alt=""';
			}
			if ($image['width'] && !$mobile_version) {
				$image['new_html'] .= ' width="' . $image['width'] . '"';
			}
			if ($image['height'] && !$mobile_version) {
				$image['new_html'] .= ' height="' . $image['height'] . '"';
			}
			$image['new_html'] .= ' border="0" />';
			$image['new_html'] .= '</div>';
			if ($image['title']) {
				$image['new_html'] .= '<div class="description">';
				$image['new_html'] .= $image['title'];
				$image['new_html'] .= '</div>';
			}
			if ($image['author']) {
				$image['new_html'] .= '<div class="author">';
				$image['new_html'] .= $Translate->get_item('photo short') . ' ' . $image['author'];
				$image['new_html'] .= '</div>';
			} else if ($rs['is_watermark']) {
				$image['new_html'] .= '<div class="author">';
				$image['new_html'] .= $Translate->get_item('photo short') . ' <a href="http://www.basketnews.lt">BasketNews.lt</a>';
				$image['new_html'] .= '</div>';
			}
			$image['new_html'] .= '</div>';

			$text = str_replace($image['html'], $image['new_html'], $text);

			//echo htmlspecialchars($image['new_html']) . "<hr />";
			//debug($image);
		}




		// Seni paveiksleliai
		//preg_match_all("/\[image=(\d+)\s+([^\s\]\[]+)(.*?)\]/im", $text, $matches, PREG_SET_ORDER);
		preg_match_all("/\[image=[^\]]+?\]/im", $text, $matches, PREG_SET_ORDER);
		foreach ($matches as $item) {
			$item = $item_or = $item[0];
			$item = preg_replace("/\n|\r/", " ", $item);
			preg_match_all("/\[image=(\d+)\s+([^\s\]\[]+)(.*?)\]/im", $item, $matches2, PREG_SET_ORDER);
			$item = $matches2[0];

			$rs = $adodb->getRow("SELECT * FROM bn2_gallery WHERE id = " . addslashes($item[1]));
			if ($rs) {
				if (trim($item[3])) {
					$comment = htmlspecialchars(trim($item[3]));
				} else {
					$comment = $rs['rec_comment'];
				}
				$comment = (trim($comment)) ? '<tr><td class="comment">' . $comment . '</td></tr>' : '';
				if (preg_match("/[^\s]{3,}\.\w{2,4}/", $rs['rec_author'])) {
					$rs['rec_author'] = preg_replace("/(.*?)([^\s]{3,}\.\w{2,4})(.*)/", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>\\3", $rs['rec_author']);
				}
				$author = (trim($rs['rec_author'])) ? '<tr><td class="author">Nuotr. ' . $rs['rec_author'] . '</td></tr>' : '';
				switch ($item[2]) {
					case 'left':
						$text = str_replace($item_or, '<table cellpadding="0" cellspacing="0" border="0" width="1" align="' . $item[2] . '" class="photo_left"><tr><td class="image"><img src="/' . gallery_path($rs['path']) . $rs['file_name'] . '" alt=""/></td></tr>' . $comment . $author . '</table>', $text);
						break;
					case 'right':
						$text = str_replace($item_or, '<table cellpadding="0" cellspacing="0" border="0" width="1" align="' . $item[2] . '" class="photo_right"><tr><td class="image"><img src="/' . gallery_path($rs['path']) . $rs['file_name'] . '" alt=""/></td></tr>' . $comment . $author . '</table>', $text);
						break;
					default:
						$text = str_replace("[image={$item[1]} {$item[2]}{$item[3]}]", '<img src="/' . gallery_path($rs['path']) . $rs['file_name'] . '" class="photo"/>', $text);
						$text = str_replace($item_or, '<img src="/' . gallery_path($rs['path']) . $rs['file_name'] . '" border="0"/>', $text);
						break;
				}
			}
		}

		return $text;
	}





	// Youtube video update
	function text_video_youtube($text, $width=false) {
		$links = array();

		// video links
		$links = array();
		//2016-07-08 preg_match_all("/[^\"](http[s]*:\/\/www\.youtube\.com\/.*?[&=]*v=)([^&#\s<]+)([^\s<]*)/is", $text, $matches, PREG_SET_ORDER);
		preg_match_all("/<p>(http[s]*:\/\/www\.youtube\.com\/.*?[&=]*v=)([^&#\s<]+)([^\s<]*)<\/p>/ism", $text, $matches, PREG_SET_ORDER);
		if ($matches) {
			foreach ($matches as $item) {
				$links[] = array(
						0 => $item[1] . $item[2] . $item[3],
						1 => $item[2],
					);
			}
		}
		//2016-07-08 preg_match_all("/[^\"](http[s]*:\/\/youtu\.be\/)([^&#\s<]+)/is", $text, $matches, PREG_SET_ORDER);
		preg_match_all("/<p>(http[s]*:\/\/youtu\.be\/)([^&#\s<]+)<\/p>/is", $text, $matches, PREG_SET_ORDER);
		if ($matches) {
			foreach ($matches as $item) {
				$links[] = array(
						0 => $item[1] . $item[2],
						1 => $item[2],
					);
			}
		}

		if ($links) {
			if (!(int)$width) {
				$width = 500;
			}
			$height = round($width * 9 / 16);
			foreach ($links as $item) {
				$html_code = '<iframe width="' . $width . '" height="' . $height . '" src="//www.youtube.com/embed/' . $item[1] . '" frameborder="0" allowfullscreen></iframe>';
				$text = str_replace($item[0], $html_code, $text);
			}
		}


		// mobile version
		if (is_mobile_version()) {
			preg_match_all("/<iframe[^>]*height=\"(\d+)\"[^>]*width=\"(\d+)\"[^>]*src=\"\/\/www\.youtube\.com.*?<\/iframe>/is", $text, $matches, PREG_SET_ORDER);
			preg_match_all("/<iframe[^>]*width=\"(\d+)\"[^>]*height=\"(\d+)\"[^>]*src=\"\/\/www\.youtube\.com.*?<\/iframe>/is", $text, $matches2, PREG_SET_ORDER);
			if ($matches2 && count($matches2)) {
				foreach ($matches2 as $key => $value) {
					$w = $matches2[$key][1];
					$matches2[$key][1] = $matches2[$key][2];
					$matches2[$key][2] = $w;
				}
			}

			$matches = array_merge($matches, $matches2);

			if ($matches && count($matches)) {
				foreach ($matches as $item) {
					$new_code = str_replace('height="' . $item[1] . '"', 'height="' . trim(round(300*$item[1]/$item[2])) . '"', $item[0]);
					$new_code = str_replace('width="' . $item[2] . '"', 'width="300"', $new_code);
					$new_code = preg_replace("/src=\"([^\"]+)\"/", "src=\"\\1?fmt=35\"", $new_code);
					$new_code = "<div class=\"block_youtube_video\">" . $new_code . "</div>";
					$text = str_replace($item[0], $new_code, $text);
				}
			}
		}

		return $text;
	}



	// Replace special tags in text
	function text_tags($text) {
		global $dispatch;

		$new_text = $text;

		$tags = array(
				'twitter'	=>	array(
						'type'	=>	'quotation',
						'class'	=>	'block_twitter',
					),
				'facebook'	=>	array(
						'type'	=>	'quotation',
						'class'	=>	'block_facebook',
					),
				'googleplus'	=>	array(
						'type'	=>	'quotation',
						'class'	=>	'block_googleplus',
					),
				'user'	=>	array(
						'type'	=>	'user',
						'class'	=>	'block_user',
					),
				'block'	=>	array(
						'type'	=>	'block',
						'class'	=>	'block_block',
					),
			);
		if (!class_exists('Photos')) {
			require_once(LIBS_USER_PATH . 'photos.class.php');
		}
		$Users = new Users;

		foreach ($tags as $tag_name => $tag_conf) {
			$matches = array();
			preg_match_all("/\[" . $tag_name . "\s*(\d*)\](.*?)\[\/" . $tag_name . "\]/is", $text, $matches, PREG_SET_ORDER);
			if ($matches) {
				foreach ($matches as $block) {
					switch ($tag_conf['type']) {
						case 'quotation':
							$new_block_text = '<div class="' . $tag_conf['class'] . '">' . $block[2] . '</div>';
							$new_text = str_replace($block[0], $new_block_text, $new_text);
							break;
						case 'user':
							if ((int)$block[1]) {
								$user_item = $Users->get_item_id($block[1]);
								$new_block_text = '<div class="' . $tag_conf['class'] . '">';
								$new_block_text .= '<img src="' . $dispatch->buildUrl("/index.php?module=images&action=show&id=" . $user_item['photo_gallery3_id'] . "&type=crop65x65") . '" alt="" />';
								$new_block_text .= $block[2];
								$new_block_text .= '<div class="main_clear"></div>';
								$new_block_text .= '</div>';
								$new_text = str_replace($block[0], $new_block_text, $new_text);
							}
							break;
						case 'block':
							if ((int)$block[1]) {
								if (!class_exists('News')) {
									require_once(LIBS_USER_PATH . 'news.class.php');
								}
								$News = new News;
								$news_block = $News->get_blocks_item($block[1]);
								$new_block_text = '';
								if ($news_block) {
									$new_block_text .= '<div class="' . $tag_conf['class'] . '">';
									if ($news_block['rec_name']) {
										$new_block_text .= '<div class="title">' . $news_block['rec_name'] . '</div>';
									}
									if ($news_block['gallery3_id']) {
										switch ($news_block['rec_type']) {
											// photo
											case 1:
												$image_size = 'crop400x250';
												break;

											//full
											default:
												$image_size = 'nlsm';
												break;
										}
										$new_block_text .= '<div class="image image_' . $news_block['rec_type'] . '"><img src="' . $dispatch->buildUrl("/index.php?module=images&action=show&id=" . $news_block['gallery3_id'] . "&type=" . $image_size) . '" alt="" /></div>';
									}
									if ($news_block['rec_author']) {
										$new_block_text .= '<div class="author">' . $news_block['rec_author'] . '</div>';
									}
									if ($news_block['rec_text']) {
										$new_block_text .= '<div class="text">' . $news_block['rec_text'] . '</div>';
									}
									$new_block_text .= '<div class="main_clear"></div>';
									$new_block_text .= '</div>';
								}
								$new_text = str_replace($block[0], $new_block_text, $new_text);
							}
							break;
					}
				}
			}
		}

		return $new_text;
	}



	function date_normalize($date) {
		$date = trim($date);

		if (preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $date)) {
			return $date;
		} else if (preg_match("/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/", $date)) {
			return $date;
		} else if (preg_match("/^\d{14}$/", $date)) {
			return preg_replace("/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/", "\\1-\\2-\\3 \\4:\\5:\\6", $date);
		} else if (preg_match("/^\d{10}$/", $date)) {
			return date("Y-m-d H:i:s", $date);
		} else if (preg_match("/^\d{8}$/", $date)) {
			return preg_replace("/^(\d{4})(\d{2})(\d{2})$/", "\\1-\\2-\\3", $date);
		} else {
			return false;
		}
	}
	// Data pavercia i unix timestamp
	function date_to_unixtimestamp($date, $finalise_current = true) {
		$date = trim($date);

		if (preg_match("/^\d{4}\-\d{2}\-\d{2}$/", $date)) {
			$y = substr($date, 0, 4);
			$m = substr($date, 5, 2);
			$d = substr($date, 8, 2);
		} else if (preg_match("/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}$/", $date)) {
			$y = substr($date, 0, 4);
			$m = substr($date, 5, 2);
			$d = substr($date, 8, 2);
			$h = substr($date, 11, 2);
			$i = substr($date, 14, 2);
		} else if (preg_match("/^\d{4}\-\d{2}\-\d{2} \d{2}:\d{2}:\d{2}$/", $date)) {
			$y = substr($date, 0, 4);
			$m = substr($date, 5, 2);
			$d = substr($date, 8, 2);
			$h = substr($date, 11, 2);
			$i = substr($date, 14, 2);
			$s = substr($date, 17, 2);
		} else if (preg_match("/^\d{14}$/", $date)) {
			$y = substr($date, 0, 4);
			$m = substr($date, 4, 2);
			$d = substr($date, 6, 2);
			$h = substr($date, 8, 2);
			$i = substr($date, 10, 2);
			$s = substr($date, 12, 2);
		} else if (preg_match("/^\d{8}$/", $date)) {
			$y = substr($date, 0, 4);
			$m = substr($date, 4, 2);
			$d = substr($date, 6, 2);
		}

		if ($y && $m && $d) {
			if ($finalise_current) {
				$h = ($h) ? $h : date("H");
				$i = ($i) ? $i : date("i");
				$s = ($s) ? $s : date("s");
			} else {
				$h = ($h) ? $h : 0;
				$i = ($i) ? $i : 0;
				$s = ($s) ? $s : 0;
			}

			return mktime($h, $i, $s, $m, $d, $y);
		} else {
			return false;
		}
	}



	// Smarty RSS
	function smarty_rss_text($text, $cut=false) {
		global $page_special_config;

		$text = trim($text);
		$text = text_images2($text);
		$text = text_video($text);
		$text = preg_replace("/<span.*?>/i", "", $text);
		$text = preg_replace("/<font.*?>/i", "", $text);
		$text = str_replace(array('<o:p>', '</o:p>', '</span>', '</SPAN>', '</font>', '</FONT>'), '', $text);
		$text = str_replace('<br>', '<br />', $text);
		$text = preg_replace("/<p .*?>/im", '<p>', $text);
		//$rs[$key]['rec_text'] = substr_x(clear_from_html(clear_from_ubb($value['rec_text'])), 300);
		$text = str_replace("href=\"/", "href=\"http://" . $page_special_config['domain'] . "/", $text);
		$text = preg_replace("/(href=\".*?)\"/i", "\\1?utm_medium=rss&amp;utm_content=link\"", $text);
		$text = str_replace("src=\"/", "src=\"http://" . $page_special_config['domain'] . "/", $text);
		$text = preg_replace("/<td.*?>/i", "<td>", $text);
		$text = str_replace("</TD>", "</td>", $text);
		$text = preg_replace("/<!--.*?-->/i", "", $text);
		$text = preg_replace("/<SCRIPT.*?<\/SCRIPT>/eis", "", $text);
		$text = preg_replace_callback('/>([^<]*)?</', 'smarty_rss_text_quotes', $text);
		$text = preg_replace("/(\[user \d+\])/eis", "", $text);
		$text = preg_replace("/(\[\/user])/eis", "", $text);
		$text = preg_replace("/(\[twitter\])/eis", "", $text);
		$text = preg_replace("/(\[\/twitter])/eis", "", $text);
		$text = preg_replace("/(\[facebook\])/eis", "", $text);
		$text = preg_replace("/(\[\/facebook])/eis", "", $text);
		$text = preg_replace("/(\[googleplus\])/eis", "", $text);
		$text = preg_replace("/(\[\/googleplus])/eis", "", $text);
		$text = preg_replace("/(\[block \d+\]\[\/block\])/eis", "", $text);
		$text = preg_replace("/(\[video=\d+\])/eis", "", $text);

		$text = text_video_youtube($text);

		return $text;
	}
	function smarty_modifier_smarty_rss_text($text, $cut=false) {
		return smarty_rss_text($text, $cut);
	}
	function smarty_rss_text_quotes($m) {
		$text = $m[0];
		return str_replace(array('„', '"', '“'), "&quot;", $text);
	}



	// XML text clear
	function xml_text_clear_code($text) {

		$text = preg_replace("/(\[user \d+\])/eis", "", $text);
		$text = preg_replace("/(\[\/user])/eis", "", $text);
		$text = preg_replace("/(\[twitter\])/eis", "", $text);
		$text = preg_replace("/(\[\/twitter])/eis", "", $text);
		$text = preg_replace("/(\[facebook\])/eis", "", $text);
		$text = preg_replace("/(\[\/facebook])/eis", "", $text);
		$text = preg_replace("/(\[googleplus\])/eis", "", $text);
		$text = preg_replace("/(\[\/googleplus])/eis", "", $text);
		$text = preg_replace("/(\[block \d+\]\[\/block\])/eis", "", $text);
		$text = preg_replace("/(\[video=\d+\])/eis", "", $text);

		return $text;
	}



	// Cut html text
	function cut_html_text($text, $cut) {
		if ((int)$cut) {
			$text = clear_from_ubb($text);
			$text = clear_from_html($text);
			$text = str_replace(array("\n", "\r"), " ", $text);
			$text = substr($text, 0, $cut+100);
			$text = preg_replace("/(\s)+/", "\\1", $text);
			$text = substr_x($text, $cut);
		}

		return $text;
	}
	function smarty_modifier_cut_html_text($text, $cut) {
		return cut_html_text($text, $cut);
	}




	function smarty_modifier_make_url($url) {
		global $dispatch;

		return $dispatch->buildUrl($url);
	}


	function smarty_concat($params, &$smarty) {
		if (is_array($params)) {
			$items = array();
			foreach ($params as $key => $value) {
				switch ($key) {
					case 'assign':
						$assign = $value;
						break;
					default:
						$items[] = $value;
						break;
				}
			}
			$return = implode(' ', $items);

			if (isset($assign)) {
				$smarty->assign($assign, $return);
				return null;
			} else {
				return $return;
			}
		} else {
			return '';
		}
	}


	// Kodavimo funkcijos (3)
	function get_rnd_iv($iv_len) {
		$iv = '';

		while ($iv_len-- > 0) {
			$iv .= chr(mt_rand() & 0xff);
		}
		return $iv;
	}
	function md5_encrypt($plain_text, $iv_len = 16) {
		GLOBAL $conf;
		$password = $conf["crypt_password"];

		$plain_text .= "\x13";
		$n = strlen($plain_text);
		if ($n % 16) $plain_text .= str_repeat("\0", 16 - ($n % 16));
		$i = 0;
		$enc_text = get_rnd_iv($iv_len);
		$iv = substr($password ^ $enc_text, 0, 512);
		while ($i < $n) {
			$block = substr($plain_text, $i, 16) ^ pack('H*', md5($iv));
			$enc_text .= $block;
			$iv = substr($block . $iv, 0, 512) ^ $password;
			$i += 16;
		}

		$enc_text = base64_encode($enc_text);
		$enc_text = str_replace("=", "", base64_encode($enc_text));
		return $enc_text;
	}
	function smarty_function_md5_encrypt($params, &$smarty) {
		$enc = '';
		if (isset($params['text'])) {
			$enc = md5_encrypt($params['text']);
		}
		if (!empty($params['assign'])){
            $smarty->assign($params['assign'], $enc);
        } else {
			return $enc;
		}
	}
	function md5_decrypt($enc_text, $iv_len = 16) {
		GLOBAL $conf;
		$password = $conf["crypt_password"];

		$enc_text = base64_decode($enc_text);
		$enc_text = base64_decode($enc_text);
		$n = strlen($enc_text);
		$i = $iv_len;
		$plain_text = '';
		$iv = substr($password ^ substr($enc_text, 0, $iv_len), 0, 512);
		while ($i < $n) {
			$block = substr($enc_text, $i, 16);
			$plain_text .= $block ^ pack('H*', md5($iv));
			$iv = substr($block . $iv, 0, 512) ^ $password;
			$i += 16;
		}
		return preg_replace('/\\x13\\x00*$/', '', $plain_text);
	}


	// Sutvarko url
	function friendly_url($name, $number=0) {
		$symbols_from	= array('ą', 'č', 'ę', 'ė', 'į', 'š', 'ų', 'ū', 'ž', 'а', 'б', 'в', 'г', 'д', 'е', 'ё',  'ж',  'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч',  'ш',  'щ',   'ъ', 'ы', 'ь', 'э', 'ю',  'я');
		$symbols_to		= array('a', 'c', 'e', 'e', 'i', 's', 'u', 'u', 'z', 'a', 'b', 'v', 'g', 'd', 'e', 'yo', 'zh', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'x', 'c', 'ch', 'sh', 'shh', '',  'y', '',  '',  'yu', 'ya');

		$name = mb_strtolower($name, 'utf-8');
		$name = str_replace(array('&quot;', '&nbsp;'), ' ', $name);
		$name = str_replace($symbols_from, $symbols_to, $name);
		$name = substr($name, 0, 200);
		if ($number) {
			$name = $name . ' ' . $number;
		}
		$name = str_replace("-", " ", $name);
		$name = str_replace("_", " ", $name);
		$name = preg_replace("/[^a-z0-9 \-]/", "", $name);
		$name = preg_replace("/(\s)+/", " ", $name);
		$name = trim($name);
		$name = str_replace(" ", "-", $name);

		return $name;
	}
	function smarty_function_friendly_url($params, &$smarty) {
		$rec_url = '';
		if (isset($params['text'])) {
			$rec_url = friendly_url($params['text']);
		}
		if (!empty($params['assign'])){
            $smarty->assign($params['assign'], $rec_url);
        } else {
			return $rec_url;
		}
	}


	// Datos ustvarkymas
	function my_date_format($string, $format) {
		global $Translate;
		$cfg_format = array(
				'day'			=>	'j',
				'month_day'		=>	'm-d',
				'short'			=>	'Y-m-d',
				'YYYY-MM-DD'	=>	'Y-m-d',
				'middle'		=>	'Y-m-d H:i',
				'long'			=>	'Y-m-d H:i:s',
				'time_middle'	=>	'H:i',
                'weekday'		=>	'N',
                'year'		    =>	'Y',
			);

		if (!preg_match("/(\d){9,10}/", $string)) {
			$timestamp = strtotime($string);
			if ($timestamp < 0) {
				$timestamp = 0;
			}
		} else {
			$timestamp = $string;
		}

		$date_format = isset($cfg_format[$format]) ? $cfg_format[$format] : null;
		if ($timestamp) {
			if ($date_format) {
				return date($date_format, $timestamp);
			} else if ($format == 'day_weekday') {
				return date("j", $timestamp) . ' ' . $Translate->get_item('day short') . ', ' . $Translate->get_item('weekdays_' . strtolower(date("D", $timestamp)));
			} else if ($format == 'month_name') {
				return $Translate->get_item('month name 2 ' . date("n", $timestamp));
			} else if ($format == 'month_name_day') {
				return $Translate->get_item('month name 2 ' . date("n", $timestamp)) . ' ' . date("d", $timestamp);
			} else if ($format == 'month_name_short_day') {
				return $Translate->get_item('month name short ' . date("n", $timestamp)) . ' ' . date("d", $timestamp);
			} else if ($format == 'weekday_month_day') {
				return $Translate->get_item('weekdays_' . strtolower(date("D", $timestamp))) . ', ' . $Translate->get_item('month name 2 ' . strtolower(date("n", $timestamp))) . ' ' . date("j", $timestamp) . ' ' . $Translate->get_item('day short');
			} else if ($format == 'weekday_short') {
				return $Translate->get_item('weekdays_' . strtolower(date("D", $timestamp)) . '_short');
			} else if ($format == 'DATE_RFC822') {
				$return = date(DATE_RFC822, $timestamp);
				$return = preg_replace("/([a-zA-Z]+) (\d\d) /", "\\1 " . date("Y", $timestamp) . " ", $return);
				return $return;
			} else if ($format == 'before24') {
				$return = time() - $timestamp;
				if ($return > 60*60*24) {
					return date("Y-m-d", $timestamp);
				} else if ($return > 60*60) {
					return trim($Translate->get_item('time prefix before') . ' ' . floor($return / 60 / 60) . ' ' . $Translate->get_item('hours short') . ' ' . $Translate->get_item('time suffix before'));
				} else if ($return > 60) {
					return trim($Translate->get_item('time prefix before') . ' ' . floor($return / 60) . ' ' . $Translate->get_item('minutes short') . ' ' . $Translate->get_item('time suffix before'));
				} else {
					return trim($Translate->get_item('time prefix before') . ' ' . $return . ' ' . $Translate->get_item('seconds short') . ' ' . $Translate->get_item('time suffix before'));
				}
			} else if ($format == 'before_days') {
				$return = time() - $timestamp;
				if ($return > 60*60*24) {
					return round($return / 60 / 60 / 24) . ' ' . $Translate->get_item('days short');
				} else if ($return > 60*60) {
					return round($return / 60 / 60) . ' ' . $Translate->get_item('hours short');
				} else {
					return round($return / 60) . ' ' . $Translate->get_item('minutes short');
				}
			}
		}

		return false;
	}
	function smarty_modifier_my_date_format($string, $format) {
		return my_date_format($string, $format);
	}


// Datos ustvarkymas
function seconds_to_date_format($number, $format=null) {
	global $Translate;

	switch ($format) {
		case 'hours':
			return round($number / 60 / 60) . ' ' . $Translate->get_item('days short');
			break;

		case 'dayshours':
			$d = floor($number / 60 / 60 / 24);
			$h = round(($number - $d * 24 * 60 * 60) / 60 / 60);
			if ($h == 24) {
				$d++;
				$h = 0;
			}
			$return = '';
			if ($d) {
				$return .= $d . ' ' . $Translate->get_item('days short') . ' ';
			}
			if ($h) {
				$return .= $h . ' ' . $Translate->get_item('hours short');
			}
			return trim($return);
			break;

		case 'days':
		default:
			return round($number / 60 / 60 / 24) . ' ' . $Translate->get_item('days short');
			break;
	}

	return false;
}
function smarty_modifier_seconds_to_date_format($number, $format=null) {
	return seconds_to_date_format($number, $format);
}



	// Vertimai
	function smarty_modifier_translate($string) {
		global $Translate;

		return $Translate->get_item($string);
	}


	// Kabutes
	function smarty_modifier_quotes($string) {
		global $Translate;

		switch ($string) {
			case 'start':
			case 'end':
				return $Translate->get_item("quotes " . $string . " item");
				break;
		}

		return '';

	}





	// Send emails using smtp
	// * body_html
	// * template
	// * subject
	// * to
	// * bcc
	// * atachments
	// * str_atachments
	function mail_customize_smtp($params) {
		global $conf, $smarty, $page_special_config;

		$phpMail = new PHPMailer\PHPMailer\PHPMailer();
		//$phpMail->SMTPDebug = 3;

		if (is_development_version()) {
			$phpMail->SMTPOptions = array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
		}

		$config_smtp = false;
		if ($page_special_config['email']['use_smtp']) {
			$config_smtp = $page_special_config['email']['use_smtp'];
		} else if ($conf['email']['use_smtp']) {
			$config_smtp = $conf['email']['use_smtp'];
		}

		if ($config_smtp) {
			$phpMail->IsSMTP(); // telling the class to use SMTP
			try {
				// smtp server
				$phpMail->Host = $config_smtp['host'];
				// enables SMTP debug information (for testing)
				if ($params['debug']) {
					$phpMail->SMTPDebug  = 4;
				}
				if ($config_smtp['port']) {
					$phpMail->Port = $config_smtp['port'];
				}
				if ($config_smtp['secure']) {
					$phpMail->SMTPSecure = $config_smtp['secure'];
				}
				if ($config_smtp['username']) {
					$phpMail->SMTPAuth = true;
					$phpMail->Username = $config_smtp['username'];
					$phpMail->Password = $config_smtp['password'];
				}
			} catch (phpmailerException $e) {
				// pretty error messages from PHPMailer
				echo $e->errorMessage();
				die('1');
			} catch (Exception $e) {
				// boring error messages from anything else!
				echo $e->getMessage();
				die('2');
			}
		}


		// to
		if (!$params['to']) {
			return false;
		}
		if (!is_array($params['to'])) {
			$params['to'] = array($params['to']);
		}
		foreach ($params['to'] as $value) {
			if (is_array($value)) {
				$phpMail->AddAddress($value['email'], $value['name']);
			} else {
				$phpMail->AddAddress($value);
			}
		}


		// bcc
		if ($params['bcc']) {
			if (!is_array($params['bcc'])) {
				$params['bcc'] = array($params['bcc']);
			}
			foreach ($params['bcc'] as $value) {
				$phpMail->AddBcc($value);
			}
		}


		// atachments
		if ($params['atachments']) {
			if (!is_array($params['atachments'])) {
				$params['atachments'] = array($params['atachments']);
			}
			foreach ($params['atachments'] as $value) {
				$phpMail->AddAttachment($value['file'], $value['name']);
			}
		}


		// string atachments
		//------------------
		// array (
		//			text - file content
		//			name - file name
		//		);
		if ($params['str_atachments']) {
			if (!is_array($params['str_atachments'])) {
				$params['str_atachments'] = array($params['str_atachments']);
			}
			foreach ($params['str_atachments'] as $value) {
				$phpMail->AddStringAttachment(str_replace("\n", "\r\n", $value['text']), $value['file']);
			}
		}


		// smarty
		if (!is_object($smarty)) {
			require_once(CONFIG_FILES_PATH . 'config.templates.php');
		}
		$smarty_compile_dir = $smarty->getCompileDir();
		$cache_dir          = $smarty->getCacheDir();
		$template_dir       = $smarty->getTemplateDir();
		$smarty->setCompileDir(SMARTY_COMPILE_PATH);
		$smarty->setCacheDir(SMARTY_CACHE_PATH);
		$smarty->setTemplateDir(SMARTY_TEMPLATES_PATH);


		// body
		$smarty->assign('body', $params['body_html']);

		$smarty->assign('website_url', $page_special_config['protocol'] . '://' . $page_special_config['domain']);

		// logo image
		if (isset($page_special_config['email']['logo_file'])) {
			$logo_image_file = $page_special_config['email']['logo_file'];
			$smarty->assign('logo_image_file', $logo_image_file);
		}

		// smarty
		if ($params['template']) {
			$body_html = $smarty->fetch($params['template']);
		} else {
			$body_html = $smarty->fetch('mail.layout.tpl');
		}
		$smarty->setCompileDir($smarty_compile_dir);
		$smarty->setCacheDir($cache_dir);
		$smarty->setTemplateDir($template_dir);


		$phpMail->ContentType = 'text/html';
		$params['subject'] = (isset($params['subject_prefix']) && $params['subject_prefix'] === false) ? $params['subject'] : $conf['mail']['subj_prefix'] . $params['subject'];
		$subject = "=?utf-8?B?" . base64_encode($params['subject']) . "?=";


		$phpMail->From		= ($params['from']['email']) ? $params['from']['email'] : $page_special_config['email']['from_email'];
		$phpMail->FromName	= ($params['from']['name']) ? $params['from']['name'] : $page_special_config['email']['from_name'];
		$phpMail->CharSet	= "utf-8";
		$phpMail->Subject	= $subject;
		if ($logo_image_file) {
			$phpMail->AddEmbeddedImage($logo_image_file, 'logo', 'logofile');
		}
		$phpMail->Body		= $body_html;
		$phpMail->AltBody	= mail_text_from_html($params['body_html']);

		if ($phpMail->Send()) {
			$phpMail->ClearAddresses();
			$phpMail->ClearAttachments();

			return true;
		} else {
			echo $phpMail->ErrorInfo;
			die('3');

			return false;
		}
	}



	// Is html teksto padarome tekstini
	function mail_text_from_html($body) {
		$text_body = preg_replace("/\n/", " ", $text_body);
		$text_body = preg_replace("/<\!\-\- header.*?\/header \-\->/", "", $text_body);
		$text_body = preg_replace("/<\!\-\- footer.*?\/footer \-\->/", "", $text_body);
		$text_body = str_replace("</div>", "</div>\n", $text_body);
		$text_body = str_replace("</p>", "</p>\n\n", $text_body);
		$text_body = str_replace(array('<br>', '<br/>', '<br />'), "<br />\n", $text_body);
		$text_body = preg_replace("/<.*?>/", "", $text_body);
		$text_body = preg_replace("/&quot;/", '"', $text_body);
		$text_body = preg_replace("/&\w{4};/", " ", $text_body);
		$text_body = preg_replace("/( +)/", " ", $text_body);
		$text_body = str_replace("\t", "    ", $text_body);
		$text_body = preg_replace("/\n /", "\n", $text_body);

		return $text_body;
	}



	function smarty_function_tv_images_path($params, &$smarty) {
		global $conf;
		return 'http://' . $conf["site_domain"] . '/images/web/';
	}



	function smarty_block_dynamic($param, $content, &$smarty) {
		return $content;
	}




	// DB assist
	function make_insert_query($table_name, $form, $wysiwyg_keys=[], $ignore=false) {
		if (is_array($form)) {
			$values = $keys = array();
			foreach ($form as $key => $value) {
				$keys[]		= "`" . addslashes(trim($key)) . "`";
				if (!in_array($key, $wysiwyg_keys)) {
					if (is_null($value)) {
						$values[]	= "null";
					} else {
						$values[]	= "'" . do_ubb($value) . "'";
					}
				} else {
					$values[]	= "'" . addslashes(str_replace(array('&scaron;', '&Scaron;'), array('š', 'Š'), $value)) . "'";
				}
			}
			return "INSERT " . (($ignore) ? 'IGNORE ' : '') . "INTO `" . addslashes($table_name) . "` (" . implode(",", $keys) . ") VALUES (" . implode(",", $values) . ")";
		}
		return false;
	}
	function make_update_query($table_name, $form, $where, $wysiwyg_keys=array()) {
		if (is_array($form) && is_array($where)) {
			$restricted_values = array('id', 'domain_keyword');
			if (count($restricted_values) > 0) {
				foreach ($restricted_values as $value) {
					if (isset($form[$value])) {
						unset($form[$value]);
					}
				}
			}

			$values = array();
			foreach ($form as $key => $value) {
				if (!in_array($key, $wysiwyg_keys)) {
					if (is_null($value)) {
						$values[]	= "`" . addslashes(trim($key)) . "` = null";
					} else {
						$values[]	= "`" . addslashes(trim($key)) . "` = '" . do_ubb($value) . "'";
					}
				} else {
					$values[]	= "`" . addslashes(trim($key)) . "` = '" . addslashes(str_replace(array('&scaron;', '&Scaron;'), array('š', 'Š'), $value)) . "'";
				}
			}
			$values_where = array();
			foreach ($where as $key => $value) {
				if (preg_match("/^\[(.+){1,2}\].*/i", $value, $matches)) {
					$values_where[] = "`" . addslashes(trim($key)) . "` " . addslashes($matches[1]) . " '" . addslashes(trim(substr($value, strlen($matches[1])+2))) . "'";
				} else {
					$values_where[] = "`" . addslashes(trim($key)) . "` = '" . addslashes($value) . "'";
				}
			}

			return "UPDATE `" . addslashes($table_name) . "` SET " . implode(",", $values) . " WHERE " . implode(" AND ", $values_where);
		}

		return false;
	}
	function make_replace_query($table_name, $form, $wysiwyg_keys=array()) {
		if (is_array($form)) {
			$values = $keys = array();
			foreach ($form as $key => $value) {
				$keys[]		= "`" . addslashes(trim($key)) . "`";
				if (!in_array($key, $wysiwyg_keys)) {
					$values[]	= "'" . do_ubb($value) . "'";
				} else {
					$values[]	= "'" . addslashes(str_replace(array('&scaron;', '&Scaron;'), array('š', 'Š'), $value)) . "'";
				}
			}
			return "REPLACE INTO `" . addslashes($table_name) . "` (" . implode(",", $keys) . ") VALUES (" . implode(",", $values) . ")";
		}
		return false;
	}
	
	
	
	
	
	
	// Execute sql query
	function execute_sql_query($sql, $function, $inputarr=false) {
		global $adodb;
		
		if (is_array($inputarr) && !empty($inputarr)) {
			preg_match_all('/(?<!:):([a-zA-Z0-9_]+)/', $sql, $matches);
			$found_placeholders = $matches[1];
			if (!empty($found_placeholders)) {
				foreach ($found_placeholders as $placeholder) {
					$val = isset($inputarr[$placeholder]) ? $inputarr[$placeholder] : "";
					$safe_val = $adodb->qstr($val);
					$sql = preg_replace('/:' . $placeholder . '\b/', $safe_val, $sql);
				}
			}
		}
		$inputarr = false;
		$function = strtolower($function);
		// echo "<pre>Final SQL: $sql</pre>";
		
		switch ($function) {
			case 'get row':
			case 'getrow':
				return $adodb->getRow($sql . " LIMIT 0, 1", $inputarr);
				break;
			case 'get one':
			case 'getone':
				return $adodb->getOne($sql, $inputarr);
				break;
			case 'get col':
			case 'getcol':
				return $adodb->getCol($sql, $inputarr);
				break;
			case 'execute':
			case 'update':
			case 'replace':
			case 'delete':
				return $adodb->Execute($sql, $inputarr);
				break;
			case 'add':
			case 'insert':
				$adodb->Execute($sql, $inputarr);
				return $adodb->Insert_ID();
				break;
			case 'get all':
			default:
				return $adodb->getAll($sql, $inputarr);
				break;
		}
	}

/**
 * Replaces special characters in a string with their "non-special" counterpart.
 *
 * Useful for friendly URLs.
 *
 * @access public
 * @param string
 * @return string
 */
function convertAccentsAndSpecialToNormal($string) {
    $table = array(
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Ă'=>'A', 'Ā'=>'A', 'Ą'=>'A', 'Æ'=>'A', 'Ǽ'=>'A',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'ă'=>'a', 'ā'=>'a', 'ą'=>'a', 'æ'=>'a', 'ǽ'=>'a',

        'Þ'=>'B', 'þ'=>'b', 'ß'=>'Ss',

        'Ç'=>'C', 'Č'=>'C', 'Ć'=>'C', 'Ĉ'=>'C', 'Ċ'=>'C',
        'ç'=>'c', 'č'=>'c', 'ć'=>'c', 'ĉ'=>'c', 'ċ'=>'c',

        'Đ'=>'Dj', 'Ď'=>'D', 'Đ'=>'D',
        'đ'=>'dj', 'ď'=>'d',

        'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ĕ'=>'E', 'Ē'=>'E', 'Ę'=>'E', 'Ė'=>'E',
        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ĕ'=>'e', 'ē'=>'e', 'ę'=>'e', 'ė'=>'e',

        'Ĝ'=>'G', 'Ğ'=>'G', 'Ġ'=>'G', 'Ģ'=>'G',
        'ĝ'=>'g', 'ğ'=>'g', 'ġ'=>'g', 'ģ'=>'g',

        'Ĥ'=>'H', 'Ħ'=>'H',
        'ĥ'=>'h', 'ħ'=>'h',

        'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'İ'=>'I', 'Ĩ'=>'I', 'Ī'=>'I', 'Ĭ'=>'I', 'Į'=>'I',
        'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'į'=>'i', 'ĩ'=>'i', 'ī'=>'i', 'ĭ'=>'i', 'ı'=>'i',

        'Ĵ'=>'J',
        'ĵ'=>'j',

        'Ķ'=>'K',
        'ķ'=>'k', 'ĸ'=>'k',

        'Ĺ'=>'L', 'Ļ'=>'L', 'Ľ'=>'L', 'Ŀ'=>'L', 'Ł'=>'L',
        'ĺ'=>'l', 'ļ'=>'l', 'ľ'=>'l', 'ŀ'=>'l', 'ł'=>'l',

        'Ñ'=>'N', 'Ń'=>'N', 'Ň'=>'N', 'Ņ'=>'N', 'Ŋ'=>'N',
        'ñ'=>'n', 'ń'=>'n', 'ň'=>'n', 'ņ'=>'n', 'ŋ'=>'n', 'ŉ'=>'n',

        'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ō'=>'O', 'Ŏ'=>'O', 'Ő'=>'O', 'Œ'=>'O',
        'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ō'=>'o', 'ŏ'=>'o', 'ő'=>'o', 'œ'=>'o', 'ð'=>'o',

        'Ŕ'=>'R', 'Ř'=>'R',
        'ŕ'=>'r', 'ř'=>'r', 'ŗ'=>'r',

        'Š'=>'S', 'Ŝ'=>'S', 'Ś'=>'S', 'Ş'=>'S',
        'š'=>'s', 'ŝ'=>'s', 'ś'=>'s', 'ş'=>'s',

        'Ŧ'=>'T', 'Ţ'=>'T', 'Ť'=>'T',
        'ŧ'=>'t', 'ţ'=>'t', 'ť'=>'t',

        'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ũ'=>'U', 'Ū'=>'U', 'Ŭ'=>'U', 'Ů'=>'U', 'Ű'=>'U', 'Ų'=>'U',
        'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ũ'=>'u', 'ū'=>'u', 'ŭ'=>'u', 'ů'=>'u', 'ű'=>'u', 'ų'=>'u',

        'Ŵ'=>'W', 'Ẁ'=>'W', 'Ẃ'=>'W', 'Ẅ'=>'W',
        'ŵ'=>'w', 'ẁ'=>'w', 'ẃ'=>'w', 'ẅ'=>'w',

        'Ý'=>'Y', 'Ÿ'=>'Y', 'Ŷ'=>'Y',
        'ý'=>'y', 'ÿ'=>'y', 'ŷ'=>'y',

        'Ž'=>'Z', 'Ź'=>'Z', 'Ż'=>'Z',
        'ž'=>'z', 'ź'=>'z', 'ż'=>'z',

        '“'=>'"', '”'=>'"', '‘'=>"'", '’'=>"'", '•'=>'-', '…'=>'...', '—'=>'-', '–'=>'-', '¿'=>'?', '¡'=>'!', '°'=>' degrees ',
        '¼'=>' 1/4 ', '½'=>' 1/2 ', '¾'=>' 3/4 ', '⅓'=>' 1/3 ', '⅔'=>' 2/3 ', '⅛'=>' 1/8 ', '⅜'=>' 3/8 ', '⅝'=>' 5/8 ', '⅞'=>' 7/8 ',
        '÷'=>' divided by ', '×'=>' times ', '±'=>' plus-minus ', '√'=>' square root ', '∞'=>' infinity ',
        '≈'=>' almost equal to ', '≠'=>' not equal to ', '≡'=>' identical to ', '≤'=>' less than or equal to ', '≥'=>' greater than or equal to ',
        '←'=>' left ', '→'=>' right ', '↑'=>' up ', '↓'=>' down ', '↔'=>' left and right ', '↕'=>' up and down ',
        '℅'=>' care of ', '℮' => ' estimated ',
        'Ω'=>' ohm ',
        '♀'=>' female ', '♂'=>' male ',
        '©'=>' Copyright ', '®'=>' Registered ', '™' =>' Trademark ',
    );

    $string = strtr($string, $table);
    // Currency symbols: £¤¥€  - we dont bother with them for now
    $string = preg_replace("/[^\x9\xA\xD\x20-\x7F]/u", "", $string);

    return $string;
}

	// Execute sql query
	function get_table_row_with_default_values($table) {
		$sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '" . addslashes($table) . "'";
		$rs = execute_sql_query($sql, 'get all');

		if ($rs) {
			$arr = array();
			foreach ($rs as $item) {
				$arr[$item['COLUMN_NAME']] = $item['COLUMN_DEFAULT'];
			}

			return $arr;
		}

		return false;
	}



	// Header function
	function my_header_code($code) {
		switch ($code) {
			case 404:
				header("HTTP/1.0 404 Not Found");
				break;
			case 301:
				header("HTTP/1.1 301 Moved Permanently");
				break;
		}
	}



	// Paging validation
	function paging_validate($pg, $pg_items, $pg_records) {
		if ($pg % $pg_items) {
			$pg = $pg - ($pg % $pg_items);
		}
		$pg_records = $data_items;
		if ($pg_records && $pg >= $pg_records) {
			$pg = ($pg_records % $pg_items) ? ($pg_records - $pg_records % $pg_items) : ($pg_records - $pg_items);
		}

		return $pg;
	}





	// Smarty modifier Truncate
	function smarty_modifier_my_truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
		if ($length == 0)
			return '';

		if (mb_strlen($string, 'UTF-8') > $length) {
			$length -= min($length, mb_strlen($etc, 'UTF-8'));
			if (!$break_words && !$middle) {
				$string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length+1, 'UTF-8'));
			}
			if (!$middle) {
				return mb_substr($string, 0, $length, 'UTF-8') . $etc;
			} else {
				return mb_substr($string, 0, $length/2, 'UTF-8') . $etc . mb_substr($string, $length/2, $length, 'UTF-8');
			}
		} else {
			return $string;
		}
	}

	//Converts youtube link to iframe
    function conver_youtube_link_to_embed($string) {
        return preg_replace(
            "/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i",
            "<iframe src=\"//www.youtube.com/embed/$2\" allowfullscreen></iframe>",
            $string
        );
    }

	// My smarty fetch function
	function my_fetch($template, $cache_id = null, $compile_id = null, $parent = null, $change_templates_side=false) {
		global $smarty, $page_special_config;

		// Administration
		if (is_admin_side() && $change_templates_side != 'frontend') {
			$templates_path = (substr(SMARTY_TEMPLATES_PATH, 0, 2) == './') ? substr(SMARTY_TEMPLATES_PATH, 2) : SMARTY_TEMPLATES_PATH;
			if ($smarty->templateExists('file:' . SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/templates/admin/' . $template)) {
				$cache_id = ($cache_id) ? ($cache_id . '|' . $page_special_config['keyword']) : $page_special_config['keyword'];
				$compile_id = ($compile_id) ? ($compile_id . '|' . $page_special_config['keyword']) : $page_special_config['keyword'];
				return $smarty->fetch('file:' . SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/templates/admin/' . $template, $cache_id, $compile_id, $parent);
			} else if ($template == 'index.tpl') {
				return $smarty->fetch(SMARTY_TEMPLATES_PATH . 'admin/' . $template, $cache_id, $compile_id, $parent);
			} else {
				return $smarty->fetch(SMARTY_TEMPLATES_PATH . 'admin/modules/' . $template, $cache_id, $compile_id, $parent);
			}
			
		// APP
		} elseif (is_app_side()) {
			$templates_path = (substr(SMARTY_TEMPLATES_PATH, 0, 2) == './') ? substr(SMARTY_TEMPLATES_PATH, 2) : SMARTY_TEMPLATES_PATH;
			$pt = 'app/';
			if ($smarty->templateExists('file:' . SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/templates/' . $pt . $template)) {
				$cache_id = ($cache_id) ? ($cache_id . '|' . $page_special_config['keyword']) : $page_special_config['keyword'];
				$compile_id = ($compile_id) ? ($compile_id . '|' . $page_special_config['keyword']) : $page_special_config['keyword'];
				return $smarty->fetch('file:' . SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/templates/' . $pt . $template, $cache_id, $compile_id, $parent);
			} else {
				return $smarty->fetch(SMARTY_TEMPLATES_PATH . $pt . $template, $cache_id, $compile_id, $parent);
			}
			
		// User side
		} else {
			if ($smarty->templateExists('file:' . SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/templates/' . $template) ){
				$cache_id = ($cache_id) ? ($cache_id . '|' . $page_special_config['keyword']) : $page_special_config['keyword'];
				$compile_id = ($compile_id) ? ($compile_id . '|' . $page_special_config['keyword']) : $page_special_config['keyword'];
				return $smarty->fetch('file:' . SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/templates/' . $template, $cache_id, $compile_id, $parent);
			} else {
				return $smarty->fetch($template, $cache_id, $compile_id, $parent);
			}
		}

	}



	// My smarty isCached function
	function my_is_cached($template, $cache_id = null, $compile_id = null, $parent = null) {
		global $smarty, $page_special_config;

		if (substr($_SERVER['HTTP_HOST'], 0, 2) != 'm.') {
			if ($smarty->templateExists('file:' . SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/templates/' . $template) ){
				$cache_id = ($cache_id) ? ($cache_id . '|' . $page_special_config['keyword']) : $page_special_config['keyword'];
				$compile_id = ($compile_id) ? ($compile_id . '|' . $page_special_config['keyword']) : $page_special_config['keyword'];
				return $smarty->isCached('file:' . SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/templates/' . $template, $cache_id, $compile_id, $parent);
			}
		}

		return $smarty->isCached($template, $cache_id, $compile_id, $parent);
	}



	// Is it Admin side
	function is_admin_side() {
		return ($_SERVER['SCRIPT_NAME'] == '/admin.php') ? true : false;
	}
	function is_app_side() {
		return ($_SERVER['SCRIPT_NAME'] == '/app.php') ? true : false;
	}
	function get_side() {
		switch ($_SERVER['SCRIPT_NAME']) {
			case '/admin.php':
				return 'admin';
			case '/app.php':
				return 'app';
			case '/index.php':
				return 'public';
			default:
				return false;
		}
	}


	// Count age by birth date
	function count_age_by_date($date) {
		list($byear, $bmonth, $bday) = explode('-', $date);
		if ($byear && $bmonth & $bday) {
			$age = date("Y") - $byear - 1;
			if ($bmonth > date("m") || $bmonth == date("m") && $bday > date("d")) {
				$age++;
			}

			return $age;
		}

		return false;
	}





	// clear spaces
	function clear_spaces($text) {
		return preg_replace("/(\s)+/is", "\\1", $text);
	}
	function smarty_modifier_clear_spaces($text) {
		return clear_spaces($text);
	}



	// Embeded video to link
	function embeded_video_code_to_link($text) {
		$new_text = str_replace(array("\n", "\r"), " ", $text);

		// Facebook
		// video
		// <iframe src="https://www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Fghettogames%2Fvideos%2F1084606408275272%2F&show_text=0&width=560" width="560" height="315" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe>
		preg_match_all("/<iframe[^>]+src=\"(http[s]*:)*(\/\/www\.facebook\.com[^\"]+video\.php.*?)\".*?<\/iframe>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				$link = urldecode(preg_replace("/.*[\?\*]+href=(.*?)\&.*/i", "\\1", $item[2]));
				preg_match_all("/width=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$width = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				preg_match_all("/height=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$height = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				$key = preg_replace("/.*\/videos\/([0-9]+).*/i", "\\1", $link);

				$new_text = str_replace($item[0], '<p class="embeded-video" data-source="facebook" data-width="' . $width . '" data-height="' . $height . '" data-id="' . $key . '">' . $link . '</p>', $new_text);
			}
		}
		// https://www.facebook.com/nsonline/videos/10154338620626941/
		preg_match_all("/<(p|pre)[^>]*>(http[s]*:)*(\/\/www.facebook\.com[^\"]+\/videos\/.*?)<\/(p|pre)>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				if (!strpos($item[0], 'class="embeded-video"')) {
					$link = $item[3];
					$key = preg_replace("/.*\/videos\/([0-9]+).*/i", "\\1", $link);

					$new_text = str_replace($item[0], '<p class="embeded-video" data-source="facebook" data-id="' . $key . '">' . $link . '</p>', $new_text);
				}
			}
		}
		// posts or post
		// <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fvidmantas.balkunas%2Fposts%2F10201706623705177&width=500" width="500" height="627" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
		// <iframe src="https://www.facebook.com/plugins/post.php?href=https%3A%2F%2Fwww.facebook.com%2Fdesigndautore%2Fphotos%2Fa.247560408617357.69420.112397672133632%2F589473511092710%2F%3Ftype%3D3&width=500" width="500" height="607" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
		preg_match_all("/<iframe[^>]+src=\"(http[s]*:)*(\/\/www\.facebook\.com\/plugins\/post\.php.*?)<\/iframe>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				$link_text = '<p class="embeded-post" data-source="facebook"';
				$link = preg_replace("/(.*?)\".*/i", "\\1", $item[2]);
				preg_match_all("/width=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$width = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($width) {
					$link_text .= ' data-width="' . $width . '"';
				}
				preg_match_all("/height=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$height = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($height) {
					$link_text .= ' data-height="' . $height . '"';
				}
				$key = preg_replace("/.*href=(.*?)(&|\").*/i", "\\1", $link);

				$link_text .= ' data-id="' . $key . '"';
				$link_text .= '>' . $link . '</p>';

				if ($key) {
					$new_text = str_replace($item[0], $link_text, $new_text);
				}
			}
		}


		// Twitter
		// <blockquote class="twitter-video" data-lang="en"><p lang="en" dir="ltr">Omg they just keep coming! Why do I have no memory?! <a href="https://twitter.com/hashtag/tbt?src=hash">#tbt</a> <a href="https://t.co/rY9tBZENcf">pic.twitter.com/rY9tBZENcf</a></p>&mdash; Gillian Anderson (@GillianA) <a href="https://twitter.com/GillianA/status/751124032158830594">July 7, 2016</a></blockquote>
		// <script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
		preg_match_all("/<blockquote[^>]+class=\"twitter-video\".*?<\/blockquote>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				preg_match_all("/(http[s]*:\/\/twitter\.com\/[^\/]+\/status\/[0-9]+)/", $item[0], $matches2, PREG_SET_ORDER);
				$link = $matches2[0][1];
				$key = preg_replace("/.*\/status\/([0-9]+).*/i", "\\1", $link);

				$new_text = str_replace($item[0], '<p class="embeded-video" data-source="twitter" data-id="' . $key . '">' . $link . '</p>', $new_text);
			}
			$new_text = preg_replace("/(<script[^>]+src=\"\/\/platform\.twitter\.com\/widgets.js\".*?<\/script>)/i", "", $new_text);
		}
		// https://twitter.com/GillianA/status/751124032158830594
		preg_match_all("/<(p|pre)[^>]*>(http[s]*:\/\/twitter\.com.*?)<\/(p|pre)>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				if (!strpos($item[0], 'class="embeded-video"') && !strpos($item[0], 'class="embeded-post"')) {
					$link = $item[2];
					$key = preg_replace("/.*\/status\/([0-9]+).*/i", "\\1", $link);

					$new_text = str_replace($item[0], '<p class="embeded-video" data-source="twitter" data-id="' . $key . '">' . $link . '</p>', $new_text);
                }
			}
		}
		// post
		// <blockquote class="twitter-tweet" data-lang="en">  <p dir="ltr" lang="en">Yilmaz cashes in to put <a href="https://twitter.com/hashtag/TUR?src=hash">#TUR</a> <a href="https://twitter.com/TFF_Org">@TFF_Org</a> ahead of the Czechs. <a href="https://twitter.com/hashtag/EURO2016?src=hash">#EURO2016</a> <a href="https://t.co/1WV89VMkrT">https://t.co/1WV89VMkrT</a></p>  &mdash; Derek (@DTang0426) <a href="https://twitter.com/DTang0426/status/745337140846944256">June 21, 2016</a></blockquote>
		preg_match_all("/<blockquote[^>]+class=\"twitter-tweet\".*?<\/blockquote>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				preg_match_all("/(http[s]*:\/\/twitter\.com\/[^\/]+\/status\/[0-9]+)/", $item[0], $matches2, PREG_SET_ORDER);
				$link = $matches2[0][1];
                $key = preg_replace("/.*\/status\/([0-9]+).*/i", "\\1", $link);

				$new_text = str_replace($item[0], '<p class="embeded-post" data-source="twitter" data-id="' . $key . '">' . $link . '</p>', $new_text);
			}
			$new_text = preg_replace("/(<script[^>]+src=\"\/\/platform\.twitter\.com\/widgets.js\".*?<\/script>)/i", "", $new_text);
		}


		// Youtube
		// <iframe width="640" height="360" src="https://www.youtube.com/embed/yTCDVfMz15M?rel=0&amp;controls=0&amp;showinfo=0" frameborder="0" allowfullscreen></iframe>
		preg_match_all("/<iframe[^>]+src=\"(http[s]*:)*(\/\/www\.youtube\.com.*?)[\?\"].*?<\/iframe>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				$link = urldecode(preg_replace("/.*[\?\*]+href=(.*?)\&.*/i", "\\1", $item[2]));
				preg_match_all("/width=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$width = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				preg_match_all("/height=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$height = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				$key = preg_replace("/.*\/embed\/([0-9a-zA-Z_\-]+).*/i", "\\1", $link);

				$new_text = str_replace($item[0], '<p class="embeded-video" data-source="youtube" data-width="' . $width . '" data-height="' . $height . '" data-id="' . $key . '">' . $link . '</p>', $new_text);
			}
		}
		// just links in the text
		// https://www.youtube.com/watch?v=yTCDVfMz15M
		preg_match_all("/<(p|pre)[^>]*>(http[s]*:\/\/www\.youtube\.com.*?)<\/(p|pre)>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				if (!strpos($item[0], 'class="embeded-video"')) {
					$link = $item[2];
					$key = preg_replace("/.*[\?\&]+v=([0-9a-zA-Z_\-]+).*/i", "\\1", $link);

					$new_text = str_replace($item[0], '<p class="embeded-video" data-source="youtube" data-id="' . $key . '">' . $link . '</p>', $new_text);
				}
			}
		}
		// https://youtu.be/wlIDeHJcqrQ
		preg_match_all("/<(p|pre)[^>]*>(http[s]*:\/\/(www\.)*youtu\.be\/)([0-9a-zA-Z_\-]+).*?<\/(p|pre)>/ism", $text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				$link = $item[2] . $item[4];
				$key = $item[4];
				if (!strpos($link, 'class="embeded-video"')) {
					$new_text = str_replace($item[0], '<p class="embeded-video" data-source="youtube" data-id="' . $key . '">' . $link . '</p>', $new_text);
				}
			}
		}


		// Vine.co
		// <iframe src="https://vine.co/v/eDbVvx0qXjx/embed/simple" width="600" height="600" frameborder="0"></iframe>  <script src="https://platform.vine.co/static/scripts/embed.js"></script>
		preg_match_all("/<iframe[^>]+src=\"(http[s]*:)*(\/\/vine\.co.*?)<\/iframe>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				$link_text = '<p class="embeded-video" data-source="vine"';
				$link = urldecode(preg_replace("/(.*?)\".*/i", "\\1", $item[2]));
				preg_match_all("/width=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$width = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($width) {
					$link_text .= ' data-width="' . $width . '"';
				}
				preg_match_all("/height=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$height = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($height) {
					$link_text .= ' data-height="' . $height . '"';
				}
				$key = preg_replace("/.*\/v\/(.*?)\/.*/i", "\\1", $link);
				$link_text .= ' data-id="' . $key . '"';
				$link_text .= '>' . $link . '</p>';

				$new_text = str_replace($item[0], $link_text, $new_text);
				$new_text = str_replace('<script src="https://platform.vine.co/static/scripts/embed.js"></script>', '', $new_text);
			}
		}


		// Streamable
		// <iframe style="width: 100%; height: 100%; position: absolute;" src="https://streamable.com/e/vuvw" width="300" height="150" frameborder="0" scrolling="no" allowfullscreen="allowfullscreen"></iframe>
		preg_match_all("/<iframe[^>]+src=\"(http[s]*:)*(\/\/streamable\.com.*?)<\/iframe>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				$link_text = '<p class="embeded-video" data-source="streamable"';
				$link = urldecode(preg_replace("/(.*?)\".*/i", "\\1", $item[2]));
				preg_match_all("/width=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$width = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($width) {
					$link_text .= ' data-width="' . $width . '"';
				}
				preg_match_all("/height=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$height = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($height) {
					$link_text .= ' data-height="' . $height . '"';
				}
				$key = preg_replace("/.*\/([a-z0-9]+).*/i", "\\1", $link);
				$link_text .= ' data-id="' . $key . '"';
				$link_text .= '>' . $link . '</p>';

				$new_text = str_replace($item[0], $link_text, $new_text);
				$new_text = str_replace('<script src="https://platform.vine.co/static/scripts/embed.js"></script>', '', $new_text);
			}
		}


		// Instagram
		// <iframe style="display: block; margin-left: auto; margin-right: auto;" src="//instagram.com/p/rShs9FiTGW/embed/" width="612" height="710" frameborder="0" scrolling="no"></iframe>
		preg_match_all("/<iframe[^>]+src=\"(http[s]*:)*(\/\/instagram\.com\/.*?)<\/iframe>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				$link_text = '<p class="embeded-post" data-source="instagram"';
				$link = preg_replace("/(.*?)\".*/i", "\\1", $item[2]);
				preg_match_all("/width=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$width = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($width) {
					$link_text .= ' data-width="' . $width . '"';
				}
				preg_match_all("/height=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$height = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($height) {
					$link_text .= ' data-height="' . $height . '"';
				}
				$key = preg_replace("/.*?\/p\/(.*?)\/.*/i", "\\1", $link);

				$link_text .= ' data-id="' . $key . '"';
				$link_text .= '>' . $link . '</p>';

				$new_text = str_replace($item[0], $link_text, $new_text);
			}
		}
		// <blockquote class="instagram-media" style="background: #FFF; border: 0; border-radius: 3px; box-shadow: 0 0 1px 0 rgba(0,0,0,0.5),0 1px 10px 0 rgba(0,0,0,0.15); margin: 1px; max-width: 658px; padding: 0; width: calc(100% - 2px);" data-instgrm-version="7">  <div style="padding: 8px;">  <div style="background: #F8F8F8; line-height: 0; margin-top: 40px; padding: 62.5% 0; text-align: center; width: 100%;">&nbsp;</div>  <p style="color: #c9c8cd; font-family: Arial,sans-serif; font-size: 14px; line-height: 17px; margin-bottom: 0; margin-top: 8px; overflow: hidden; padding: 8px 0 7px; text-align: center; text-overflow: ellipsis; white-space: nowrap;"><a style="color: #c9c8cd; font-family: Arial,sans-serif; font-size: 14px; font-style: normal; font-weight: normal; line-height: 17px; text-decoration: none;" href="https://www.instagram.com/p/7mpxvag6Tz/" target="_blank">A photo posted by Laura Jay (@laura.jay)</a> on <time style="font-family: Arial,sans-serif; font-size: 14px; line-height: 17px;" datetime="2015-09-14T08:15:28+00:00">Sep 14, 2015 at 1:15am PDT</time></p>  </div>  </blockquote>  <script src="//platform.instagram.com/en_US/embeds.js" async="" defer="defer"></script>
		preg_match_all("/<blockquote[^>]+class=\"instagram-media\".*?>.*?<a[^>]+href=\"(http[s]*:)*(\/\/[w.]*instagram\.com\/.*?\").*?<\/blockquote>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				$link_text = '<p class="embeded-post" data-source="instagram"';
				$link = preg_replace("/(.*?)\".*/i", "\\1", $item[2]);
				$key = preg_replace("/.*?\/p\/(.*?)\/.*/i", "\\1", $link);

				$link_text .= ' data-id="' . $key . '"';
				$link_text .= '>' . $link . '</p>';

				$new_text = str_replace($item[0], $link_text, $new_text);
				$new_text = preg_replace("/<script[^>]+src=\"(http[s]*:)*\/\/platform\.instagram\.com\/.*?\/embeds\.js.*?<\/script>/", '', $new_text);
			}
		}


		// Infogram
		// <iframe src="//e.infogr.am/projected-spending-on-gay-weddings-with-legalization?src=embed" title="Projected spending on gay weddings with legalization" width="700" height="646" scrolling="no" frameborder="0" style="border:none;"></iframe>
		preg_match_all("/<iframe[^>]+src=\"(http[s]*:)*(\/\/e\.infogr\.am.*?)<\/iframe>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				$link_text = '<p class="embeded-post" data-source="infogram"';
				$link = urldecode(preg_replace("/(.*?)[\"\?].*/i", "\\1", $item[2]));
				preg_match_all("/width=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$width = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($width) {
					$link_text .= ' data-width="' . $width . '"';
				}
				preg_match_all("/height=\"(\d+?)\"/", $item[0], $matches2, PREG_SET_ORDER);
				$height = ($matches2 && $matches2[0][1]) ? $matches2[0][1] : 0;
				if ($height) {
					$link_text .= ' data-height="' . $height . '"';
				}
				$key = preg_replace("/.*\/([a-z0-9\-_]+).*/i", "\\1", $link);
				$link_text .= ' data-id="' . $key . '"';
				$link_text .= '>' . $link . '</p>';

				$new_text = str_replace($item[0], $link_text, $new_text);
			}
		}


		// playbuzz
		// <script type="text/javascript" src="//cdn.playbuzz.com/widget/feed.js"></script><div class="pb_feed" data-embed-by="6c805a9d-ccd7-4fb1-96e9-3c49f7a01f81" data-game="/jonasm12/kas-tur-t-u-imti-lietuvos-rinktin-s-trenerio-viet" ></div>
		// <script type="text/javascript" src="//cdn.playbuzz.com/widget/feed.js"></script><div class="pb_feed" data-embed-by="6c805a9d-ccd7-4fb1-96e9-3c49f7a01f81" data-game="/jonasm12/kas-tur-t-u-imti-lietuvos-rinktin-s-trenerio-viet" data-recommend="false" data-game-info="false" data-comments="false" data-shares="false" ></div>
		// <script type="text/javascript" src="//cdn.playbuzz.com/widget/feed.js"></script><div class="pb_feed" data-embed-by="23c3d63c-6010-4910-9d25-215874f82764" data-item="3dd01a17-6934-4eec-8f47-b9266fd6cd3d" data-recommend="false" data-game-info="false" data-comments="false" data-shares="false" ></div>
		//                        <script src="//cdn.playbuzz.com/widget/feed.js"></script><div class="pb_feed" data-item="65419320-4a6a-446d-814a-dc80cb2518d9" data-embed-by="23c3d63c-6010-4910-9d25-215874f82764" data-version="2" ></div>
		preg_match_all("/<div[^>]+class=\"pb_feed\".*?>.*?<\/div>/ism", $new_text, $matches, PREG_SET_ORDER);
		if ($matches && is_array($matches)) {
			foreach($matches as $item) {
				debug($matches);
				$link_text = '<p class="embeded-post" data-source="playbuzz"';
				preg_match("/.* data-version=\"(.*?)\".*/i", $item[0], $match_version);
				if ($match_version[1]) {
					$link_text .= ' data-version="' . $match_version[1] . '"';
				}

				preg_match("/.* data-game=\"(.*?)\".*/i", $item[0], $match_key);
				if ($match_key) {
					$key = $match_key[1];
				} else {
					preg_match("/.* data-item=\"(.*?)\".*/i", $item[0], $match_key);
					if ($match_key) {
						$key = $match_key[1];
					}
				}
				$link_text .= ' data-id="' . $key . '"';

				preg_match("/.* data-embed-by=\"(.*?)\".*/i", $item[0], $match_user);
				if ($match_user) {
					$author_id = $match_user[1];
					$link_text .= ' data-user="' . $author_id . '"';
				}

				$link_text .= '>//www.playbuzz.com/' . $key . '</p>';

				$new_text = str_replace($item[0], $link_text, $new_text);
				$new_text = str_replace('<script type="text/javascript" src="//cdn.playbuzz.com/widget/feed.js"></script>', '', $new_text);
				$new_text = str_replace('<script src="//cdn.playbuzz.com/widget/feed.js"></script>', '', $new_text);
			}
		}


		return $new_text;
	}



	// Video link to embeded code
	function video_link_to_embed_code($text, $size_x=false) {
		$new_text = $text;
		if (!$size_x) {
			$size_x = 620;
		}

		$replace_links = array();
		preg_match_all("/(<p[^>]+class=\"embeded-video\".*?<\/p>)/ism", $new_text, $matches, PREG_SET_ORDER);
		if (!$matches || !is_array($matches)) {
			$matches = array();
		}
		preg_match_all("/(<p[^>]+class=\"embeded-post\".*?<\/p>)/ism", $new_text, $matches2, PREG_SET_ORDER);
		if (!$matches2 || !is_array($matches2)) {
			$matches2 = array();
		}
		$matches = array_merge($matches, $matches2);
		foreach($matches as $item) {
			preg_match_all("/class=\"embeded\-(.*?)\"/ism", $item[0], $matches_type, PREG_SET_ORDER);
			preg_match_all("/(data\-[a-z]+)=\"(.+?)\"/ism", $item[0], $matches_params, PREG_SET_ORDER);
			$params = array();
			if ($matches_params && is_array($matches_params)) {
				foreach($matches_params as $item_param) {
					$params[$item_param[1]] = $item_param[2];
				}
			}
			$replace_links[] = array(
				'type'		=> $matches_type[0][1],
				'string'	=> $item[0],
				'params'	=> $params,
			);
		}

		foreach ($replace_links as $key => $value) {
			$width = $size_x;
			if ((int)$value['params']['data-width'] && (int)$value['params']['data-height']) {
				$coef = $value['params']['data-height'] / $value['params']['data-width'];
			} else {
				$coef = 9 / 16;
			}
			$height = round($width * $coef);

			$embed = '';
			switch ($value['type']) {
				// Video
				case 'video':
					switch ($value['params']['data-source']) {
						// Facebook
						case 'facebook':
							$width = max($width, 350);
							$embed = '<div class="embedded_video"><iframe src="//www.facebook.com/plugins/video.php?href=https%3A%2F%2Fwww.facebook.com%2Fghettogames%2Fvideos%2F' . $value['params']['data-id'] . '%2F&show_text=0&width=' . $width . '" width="' . $width . '" height="' . $height . '" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true" allowFullScreen="true"></iframe></div>';
							//$embed = '<div class="embedded_video"><div class="fb-video" data-href="https://www.facebook.com/facebook/videos/' . $value['params']['data-id'] . '/" data-width="' . $width . '" data-allowfullscreen="true" data-autoplay="false" data-show-captions="true"></div></div>';
							break;

						// Twitter
						case 'twitter':
							$embed = '<div style="clear: both;"></div><p><blockquote class="twitter-video"><a href="//twitter.com/a/status/' . $value['params']['data-id'] . '"></a></blockquote><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script></p>';
							break;

						// Youtube
						case 'youtube':
							$embed = '<div class="embedded_video"><iframe width="' . $width . '" height="' . $height . '" src="//www.youtube.com/embed/' . $value['params']['data-id'] . '?rel=0&amp;controls=1&amp;showinfo=1&amp;cc_lang_pref=lt&amp;cc_load_policy=1" frameborder="0" allowfullscreen></iframe></div>';
							break;

						// Vine.co
						case 'vine':
							$embed = '<iframe width="' . (int)$width . '" height="' . (int)$height . '" src="//vine.co/v/' . $value['params']['data-id'] . '/embed/simple" frameborder="0"></iframe> <script src="https://platform.vine.co/static/scripts/embed.js"></script>';
							break;

						// Streamable
						case 'streamable':
							$embed = '<iframe width="' . (int)$width . '" height="' . (int)$height . '" src="//streamable.com/e/' . $value['params']['data-id'] . '" scrolling="no" allowfullscreen="allowfullscreen"></iframe>';
							break;
					}
					break;

				// Post
				case 'post':
					switch ($value['params']['data-source']) {
						// Facebook
						case 'facebook':
							$width = max($width, 350);
							//$embed = '<iframe src="https://www.facebook.com/plugins/post.php?href=' . $value['params']['data-id'] . '&width=' . $width . '" width="' . $width . '" height="' . $height . '" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>';
							$embed = '<div class="fb-post" data-width="' . $width . '" data-href="' . urldecode($value['params']['data-id']) . '"></div>';
							break;

						// Instagram
						case 'instagram':
							//$embed = '<iframe style="display: block; margin-left: auto; margin-right: auto;" src="//instagram.com/p/' . $value['params']['data-id'] . '/embed/" width="' . $width . '" frameborder="0" scrolling="no"></iframe>';
							$embed = '<blockquote class="instagram-media" data-instgrm-captioned data-instgrm-version="7" style="width: ' . $width . 'px;"><a href="//www.instagram.com/p/' . $value['params']['data-id'] . '/" target="_blank"></a></blockquote> <script async defer src="//platform.instagram.com/en_US/embeds.js"></script>';
							break;

						// Twitter
						case 'twitter':
							$embed = '<blockquote class="twitter-tweet"><a href="//twitter.com/a/status/' . $value['params']['data-id'] . '"></a></blockquote><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>';
							break;

						// Infogram
						case 'infogram':
							// <div class="infogram-embed" data-id="projected-spending-on-gay-weddings-with-legalization" data-type="interactive" data-title="Projected spending on gay weddings with legalization"></div><script>!function(e,t,n,s){var i="InfogramEmbeds",o=e.getElementsByTagName(t),d=o[0],a=/^http:/.test(e.location)?"http:":"https:";if(/^\/{2}/.test(s)&&(s=a+s),window[i]&&window[i].initialized)window[i].process&&window[i].process();else if(!e.getElementById(n)){var r=e.createElement(t);r.async=1,r.id=n,r.src=s,d.parentNode.insertBefore(r,d)}}(document,"script","infogram-async","//e.infogr.am/js/dist/embed-loader-min.js");</script>
							$embed = '<div style="max-width:' . $width . 'px;" class="infogram-embed" data-id="' . $value['params']['data-id'] . '" data-type="interactive" data-title=""></div>';
							$embed .= '<script>!function(e,t,n,s){ var i="InfogramEmbeds",o=e.getElementsByTagName(t),d=o[0],a=/^http:/.test(e.location)?"http:":"https:";if(/^\/{2}/.test(s)&&(s=a+s),window[i]&&window[i].initialized)window[i].process&&window[i].process();else if(!e.getElementById(n)){ var r=e.createElement(t);r.async=1,r.id=n,r.src=s,d.parentNode.insertBefore(r,d) } }(document,"script","infogram-async","//e.infogr.am/js/dist/embed-loader-min.js");</script>';
							break;

						// Playbuzz
						case 'playbuzz':
							// <script type="text/javascript" src="//cdn.playbuzz.com/widget/feed.js"></script><div class="pb_feed" data-embed-by="6c805a9d-ccd7-4fb1-96e9-3c49f7a01f81" data-game="/jonasm12/kas-tur-t-u-imti-lietuvos-rinktin-s-trenerio-viet" data-recommend="false" data-game-info="false" data-comments="false" data-shares="false" ></div>
							//                        <script src="//cdn.playbuzz.com/widget/feed.js"></script><div class="pb_feed" data-item="65419320-4a6a-446d-814a-dc80cb2518d9" data-embed-by="23c3d63c-6010-4910-9d25-215874f82764" data-version="2" ></div>
							$embed = '<script src="//cdn.playbuzz.com/widget/feed.js"></script>';
							//$embed .= '<div class="pb_feed" data-embed-by="' . $value['params']['data-user'] . '" data-game="' . $value['params']['data-id'] . '" data-recommend="false" data-game-info="false" data-comments="false" data-shares="false" ></div>';
							$embed .= '<div class="pb_feed" data-embed-by="' . $value['params']['data-user'] . '" data-item="' . $value['params']['data-id'] . '" data-version="' . $value['params']['version'] . '" data-recommend="false" data-game-info="false" data-comments="false" data-shares="false" ></div>';
							break;
					}
					break;
			}

			if ($embed) {
				$replace_links[$key]['embed'] = $embed;
				$new_text = str_replace($value['string'], $embed, $new_text);
			}
		}

		//debug($replace_links);
		//die();
		return $new_text;
	}



	// Clear Xml code
	function clear_xml($text) {
		$text = preg_replace("/\[block \d+\]\[\/block\]/", "", $text);
		return $text;
	}
	function smarty_modifier_clear_xml($text) {
		return clear_xml($text);
	}







	// website address
	function this_website_address() {
		global $page_special_config;

		$address = ($page_special_config['protocol']) ? $page_special_config['protocol'] : 'http';
		$address .= '://' . $page_special_config['domain'];

		return $address;
	}



	// All GET and POST -> $url
	function all_params_to_url() {
		if (!empty($_SERVER['PATH_INFO'])) {
			$server_path_info = (substr($_SERVER['PATH_INFO'], 0, 1) == '/') ? substr($_SERVER['PATH_INFO'], 1) : $_SERVER['PATH_INFO'];
			$tmp = explode(';', $server_path_info);
			$i = 0;
			foreach ($tmp as $key => $value) {
				$tmp2 = explode('.', $value, 2);
				if ($key == 0) {
					$params['_module_'] = $tmp2[0];
					$params['_action_'] = (isset($tmp2[1])) ? $tmp2[1] : '';
					$params[0] = $params['_module_'];
					$params[1] = $params['_action_'];
					$i = 2;
				} else {
					$params[$tmp2[0]] = (isset($tmp2[1])) ? $tmp2[1] : '';
					$params[$i] = $params[$tmp2[0]];
					$i++;
				}
			}
			$url = array_merge($_POST, $params); // params overwrites POST
		} else {
			$url = array_merge($_POST, $_GET); // GET overwrites POST
			$url['_module_'] = $url['module'];
			$url['_action_'] = $url['action'];
		}
		
		return $url;
	}



	// Log data to file
	function log_to_file($content) {
		$log_path = CACHE_PATH . 'log_files/';
		$log_file = date("Ymd") . '.log';

		if (!file_exists($log_path)) {
			$path = substr($log_path, strlen(CACHE_PATH));
			$arr_path = explode('/', $path);
			$path = CACHE_PATH;
			foreach ($arr_path as $folder) {
				$path = $path . $folder . '/';
				if ($folder && !file_exists($path)) {
					mkdir($path, 0777);
					chmod($path, 0777);
				}
			}
		}

		if (!$content) {
			$content = '-empty-';
		}

		if (is_array($content)) {
			$content = json_encode($content);
		}

		$file_content = array();
		$file_content[] = "===========================================================================";
		$file_content[] = date("Y-m-d H:i:s");
		$file_content[] = 'Referer: ' . $_SERVER['HTTP_REFERER'];
		$file_content[] = 'Uri: ' . $_SERVER['REQUEST_URI'];
		$file_content[] = $content;
		$file_content[] = "";

		file_put_contents($log_path . $log_file, implode("\n", $file_content), FILE_APPEND | LOCK_EX);

		return true;
	}

    function sortArrayByArray(array $array, array $indexArray) {
        $ordered = array();
        foreach ($indexArray as $key) {
            if (array_key_exists($key, $array)) {
                $ordered[$key] = $array[$key];
            }
        }
        return $ordered;
    }



	function update_decimal_numbers($number) {
		if (!is_numeric($number)) {
			$number = str_replace(',', '.', $number);
		}

		return (float)$number;
	}



	// Price display
	function display_price($number) {
		$big = round($number * 100);
		$small = $big % 100;
		$big = ($big - $small) / 100;
		$small = str_pad($small, 2, '0', STR_PAD_LEFT);
		return $big . ',' . $small . '€';
	}

	function smarty_modifier_display_price($number) {
		return display_price($number);
	}



	// Phone display
	function display_phone($number) {
		$number = preg_replace("/[^0-9]/", "", $number);
		$new_number = '+';
		$new_number .= substr($number, 0, 3);
		$new_number .= ' ';
		$new_number .= substr($number, 3, 3);
		$new_number .= ' ';
		$new_number .= substr($number, 6);
		return $new_number;
	}

	function smarty_modifier_display_phone($number) {
		return display_phone($number);
	}


	function my_string_to_math($string) {
		return $string;
	}



	function smarty_function_actions_log($params, &$smarty) {
		global $ActionsLogs;

		$return = $ActionsLogs->html_list($params['table'], $params['id']);

		if (isset($params['assign'])) {
			$smarty->assign($params['assign'], $return);
			return null;
		} else {
			return $return;
		}
	}



	// admin url
	function admin_make_url($url) {
		global $Translate;

		// erase domain
		if (substr($url, 0, 4) == 'http') {
			$url = preg_replace("/^http[s]*:\/\/.*?\/(.*)/", "/\\1", $url);
		}

		// erase language
		if (preg_match("/^\/[a-z]{2}\/(.*)$/", $url, $matches)) {
			$url = '/' . $matches[1];
		}

		$string = '/admin';
		if (substr($url, 0, strlen($string)) == $string) {
			if ($Translate->language_default != $Translate->language && $Translate->language) {
				return '/' . $Translate->language . $url;
			}
		}

		return $url;
	}
	function smarty_modifier_admin_make_url($url) {
		return admin_make_url($url);
	}


	// Administrator permissions
	function check_permissions($module_name, $action='read') {
		global $Translate;

		if (!$_SESSION['user']['rights'][$module_name][$action]) {
			$_SESSION['main_messages'][] = $Translate->get_item('error action denied');
			Location($_SERVER['HTTP_REFERER']);
			die();
		}

		return true;
	}
	
	
	function get_ip() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		}
		
		if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
			return trim($ips[0]);
		}
		
		return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
	}

	function d(): void
	{
		global $adodb;
		$adodb->debug = true;
	}