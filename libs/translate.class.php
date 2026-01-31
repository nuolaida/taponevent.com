<?php
	class Translate {
		public string $language = '';
		var string $language_default = '';
		var array $keywords = [];
	
		function __construct($use_session = true) {
			global $page_special_config;
			
			$this->language_default = $page_special_config['default_site_language'];
	        if ($use_session && isset($_SESSION[get_side()]['language']) && !$this->language) {
	            $this->change_language($_SESSION[get_side()]['language']);
	        } elseif (!$this->language) {
				$this->change_language($this->language_default);
			} else {
				$this->load_translations();
	        }
		}
		
		
		function load_translations() {
			global $page_special_config;
			
			$words_path = TRANSLATIONS_PATH;
			$words_path .= ($this->language && file_exists($words_path . $this->language)) ? $this->language : $this->language_default;
			$words_path .= '/';
	
			// translations
			$file = $words_path . 'lang.' . get_side() . '.php';
			if (file_exists($file)) {
				require($file);
				$this->add_translations($lang);
			}
			
			// local translations
			if ($page_special_config['keyword']) {
				$file_arr = [
					SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/translations/' . $this->language . '/' . get_side() . '.php',
				];
				foreach ($file_arr as $file) {
					if (file_exists($file)) {
						require($file);
						$this->add_translations($lang);
					}
				}
			}
		}
		
		
		function change_language($lang=null) {
			$_SESSION['app']['language'] = $lang;
			$this->language = $lang;
			$this->load_translations();
		}
		
		
		function add_translations($lang) {
			$this->keywords = array_merge($this->keywords, $lang);
		}
	
	
		// Irasas
		function get_item($string, $return_empty_on_error = false) {
			if (isset($this->keywords[$string])) {
				return $this->replace_links($this->keywords[$string]);
			} elseif ($return_empty_on_error) {
				return "";
			}
	
			return 'empty: ' . $string;
		}
	
	
	
		// Nuorodas tekstuose pakeiciame i normalias
		function replace_links($string) {
			global $dispatch;
	
			preg_match_all("/\{link[\s=](.*?)\}(.*?)\{\/link\}/", $string, $matches, PREG_SET_ORDER);
			if ($matches && is_array($matches) && count($matches) > 0) {
				foreach ($matches as $l) {
					$string = str_replace($l[0], '<a href="' . $dispatch->buildUrl($l[1]) . '">' . $l[2] . '</a>', $string);
				}
			}
	
			return $string;
		}
	}

