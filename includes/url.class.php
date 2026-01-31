<?php

class Url {
	
	var $extension = null;
	var $search = [];
	var $parts = [];
	var $urls = [];
	var $redirect = [];
	var $translations = [];
	var $language = null;
	var $patterns;
	var $matches;

	function __construct($partsFromLang) {
		global $Translate;

		$this->language = $Translate->language;
		// Ulrs extension
		$this->extension = '.html';
		// Build patterns, search, redirect and urls arrays for url decryption, redirecting and etc.
		$this->translations = array(
				'%beer%'    		=>	$Translate->get_item('url beer'),
				'%breweries%'		=>	$Translate->get_item('url breweries'),
				'%catalogue%'		=>	$Translate->get_item('url catalogue'),
				'%competition%'	    =>	$Translate->get_item('url competition'),
				'%competitions%'	=>	$Translate->get_item('url competitions'),
				'%contacts%'		=>	$Translate->get_item('url contacts'),
				'%festivals%'		=>	$Translate->get_item('url festivals'),
				'%image%'			=>	$Translate->get_item('url image'),
				'%label%'			=>	$Translate->get_item('url label'),
				'%list%'			=>	$Translate->get_item('url list'),
				'%logout%'			=>	$Translate->get_item('url logout'),
				'%login%'			=>	$Translate->get_item('url login'),
				'%map%'	    		=>	$Translate->get_item('url map'),
				'%news%'            =>  $Translate->get_item('url news'),
				'%newsletters%'     =>  $Translate->get_item('url newsletters'),
				'%participants%'	=>	$Translate->get_item('url participants'),
				'%results%'			=>	$Translate->get_item('url results'),
				'%send%'			=>	$Translate->get_item('url send'),
				'%show%'			=>	$Translate->get_item('url show'),
				'%sommelier%'		=>	$Translate->get_item('url sommelier'),
				'%texts%'			=>	$Translate->get_item('url texts'),
				'%track%'			=>	$Translate->get_item('url track'),
				'%unsubscribe%'		=>	$Translate->get_item('url unsubscribe'),
				'%users%'			=>	$Translate->get_item('url users'),
			);
		$this->loadPatterns(array_values($this->translations));
}

	private function loadPatterns($partsFromLang) {

		// Where to save cashed version?
		//$filename = CORE . 'cache/' . $this->lang . ".patterns.php";
		// Get cashe from file
		//$array = unserialize(@ file_get_contents($filename));

		$this->getPatterns();
		// Do we have cashed version?
		if (empty($array)) {

			$this->urls = array_merge($this->patterns,$this->redirect);
			$itemName = '[\w \-]+';
			$this->parts['vars'] = array('%id%', '%name%');
			$this->parts['vals'] = array('\d+', $itemName);
			
			foreach ($this->parts['vals'] as $var => $val) {
				$this->parts['vals'][$var] = '('.$val.')';
			};
			
			$langParts['vars'] = array_keys($this->translations);
			//$langParts['vars'] = array('%about%', '%add%', '%advertisement%', '%authors%', '%blog%', '%edit%', '%info%', '%logout%', '%rss%', '%search%');
			$langParts['vals'] = $partsFromLang;
			$this->parts = array_merge_recursive($this->parts, $langParts);

			$staticParts['vars'] = array('%end%',           '%xml%',    '%img%',    '%json%',   '%gif%');
			$staticParts['vals'] = array($this->extension,  '.xml',     '.jpg',     '.json',    '.gif');
			$parts = array_merge_recursive($staticParts, $langParts);

			// Vertimus sukisame
			foreach ($this->urls as $var => $val) {
				$this->urls[$var] = '/' . str_replace($parts['vars'], $parts['vals'], $val);
			};

			$this->parts = array_merge_recursive($this->parts, $staticParts);

			$otherParts['vars'] = array('/',  '%id%',			'%pg%',			'%item_url%',		'%rec_url%', 		'%rec_url_name%',	'%rec_url_namee%',	'%type%',								'%idd%',			'%part_id%',	'%season%');
			$otherParts['vals'] = array('\/', '[\/]*(\d+)*',	'[\.]*(\d+)*',	'[\/]*([\w\-]+)*',	'[\/]*([\w\-]+)*',	'[\/]*([\w\-]+)*',	'[\/]*([\w\-]+)*',	'[\/]*([\w\-\%ąčęėįšųūĄČĘĖĮŠŲŪŽ]+)*',	'[\/]*(\d+)*',	'[\/]*(\d+)*',	'[\/]*(\d+)*');

			$this->parts = array_merge_recursive($this->parts, $otherParts);

			foreach ($this->patterns as $var => $val) {
				$this->search[$var] = '/^\/' . str_replace($this->parts['vars'], $this->parts['vals'], $val) . '$/';
			};

			$array = array('search' => $this->search, 'parts' => $this->parts, 'urls' => $this->urls);
/*
			if($f = fopen($filename, "w"))
			{
				if(fwrite($f, serialize($array)))
				{
					fclose($f);
				}
				else die("Could not write to file ".$filename);
			}
			else die("Could not open file ".$filename);
*/
		}

		$this->search = $array['search'];
		$this->parts = $array['parts'];
		$this->urls = $array['urls'];
	}
	
	private function getPatterns() {
		$this->patterns['beer.show']    			=	'%beer%/%id%/%rec_url_name%%end%';
		$this->patterns['breweries.map']			=	'%breweries%/%map%%end%';
		$this->patterns['breweries.show']			=	'%breweries%/%id%/%rec_url_name%%end%';
		$this->patterns['catalogue.festivals']		=	'%catalogue%/%festivals%%end%';
		$this->patterns['catalogue.competitions']	=	'%catalogue%/%competitions%%end%';
		$this->patterns['catalogue.show']		    =	'%catalogue%/%id%/%rec_url_name%%end%';
		$this->patterns['competition.label']	    =	'%competition%/%label%/%item_url%%end%';
		$this->patterns['competition.results']	    =	'%competition%/%results%/%id%%end%';
		$this->patterns['festivals.show']			=	'%festivals%/%id%/%rec_url_name%%end%';
		$this->patterns['images.show']              =   '%image%-%id%-%type%%img%';
		$this->patterns['news.browse']			    =	'%news%%end%';
		$this->patterns['news.show']			    =	'%news%/%show%/%id%-%rec_url_name%%end%';
		$this->patterns['newsletters2.send'] 	    =	'%newsletters%/%send%-%id%%end%';
		$this->patterns['newsletters2.track'] 	    =	'%newsletters%/%track%-%id%-%idd%%gif%';
		$this->patterns['newsletters2.unsubscribeMessage']  =	'%newsletters%/%unsubscribe%%end%';
		$this->patterns['newsletters2.unsubscribe']  =	'%newsletters%/%unsubscribe%/%item_url%%end%';
		$this->patterns['participants.show']		=	'%participants%/%id%/%rec_url_name%%end%';
		$this->patterns['sommelier.show']  			=	'%sommelier%/%id%%end%';
		$this->patterns['texts.show']			    =	'%texts%/%id%/%rec_url_name%%end%';
		$this->patterns['users.logout']				=	'%users%/%logout%%end%';
		$this->patterns['users.loginForm']			=	'%users%/%login%%end%';
	}

	private function getAction($var) {

		// Build nice array of results
		unset($this->matches['0']);
		preg_match_all("/%([a-z_]+)%/",$this->urls[$var], $array);

		$i = 0;
		foreach ($array[1] as $part)
			$output[$part] = ($this->matches[++$i]) ? $this->matches[$i] : null;

		$this->matches = $output;
		
		$arr = explode('.', $var);
		$this->matches['_module_'] = $arr[0];
		$this->matches['_action_'] = $arr[1];
	}

	private function doRedirect($var) {
		Site::redirect($this->getUrl($var, $this->matches));
		exit();
	}

	function getMatches($url) {
        foreach ($this->search as $var => $val) {
            // If we have pattern for ulr...
            if (preg_match($val, $url, $this->matches)) {
                // Find where are we going (language independant)
                $this->getAction(str_replace('_missing', '', $var));

                // If it's not good url, redirect to new one
                if (isset($this->urls[$var]) && strpos($var, '_missing') !== false)
                    return $this->doRedirect($var);

                // Give out results array
                return $this->matches;
            }
        }

        return false;
    }

	function decodeUrl($url) {
		$url = $this->detectLanguage($url);

		$result = $this->getMatches($url);
		if ($result) {
		    return $result;
        }

		/*
		if ($this->language === 'lt') {
		    $this->changeLanguage('en');
        } else {
		    $this->changeLanguage('lt');
        }

        $result = $this->getMatches($url);
		if ($result === false) {
            if ($this->language === 'lt') {
                $this->changeLanguage('en');
            } else {
                $this->changeLanguage('lt');
            }
        }
		*/
		
		// We didint find anything
		return $result;
	}

	function getUrl($part, $modificators) {

		// Firstly we have empty array
		$mod['vars'] = $mod['vals'] = array();
		// Lets transform array, so what it can be used with str_replace

		if (isset($modificators['rec_url_name'])) {
			if (function_exists("friendly_url")) {
				$modificators['rec_url_name'] = friendly_url($modificators['rec_url_name']);
			}
		}
		if (isset($modificators['rec_url_namee'])) {
			if (function_exists("friendly_url")) {
				$modificators['rec_url_namee'] = friendly_url($modificators['rec_url_namee']);
			}
		}

		foreach ($modificators as $var => $val)
			{
				$val = str_replace("%", "||", $val);
				array_push($mod['vars'], '%' . $var . '%');
				array_push($mod['vals'], $val);
			}

		// Give out final array
		$link = str_replace($mod['vars'], $mod['vals'], $this->urls[$part]);

		$link = preg_replace('([\%]\w+[\%])', '', $link);
		$link = str_replace('||', '%', $link);

		return $link;
	}

	function buildUrl($url, $add_query_string = false) {
	    if (!is_array($url)) {
            $url = str_replace('/index.php/', '', $url);
            $url = str_replace('/index.php?', '', $url);
            $url = explode('&', $url);

            foreach ($url as $var => $match) {
                $match = explode('=', $match);
                $url[$match[0]] = $match[1];
                unset($url[$var]);
            }
        }

		if (!$url['module'] || !$url['action'])
			return false;
			
		$fuseaction = $url['module'] . '.' . $url['action'];
		unset($url['module']);
		unset($url['action']);

		return $this->getUrl($fuseaction, $url) . (($_SERVER['QUERY_STRING'] && $add_query_string) ? ('?'.$_SERVER['QUERY_STRING']) : '');
	}


	function changeLanguage($lang)
	{
		global $Translate;
		$_SESSION['language'] = $lang;
		$this->language = $lang;
		if ($Translate->language != $lang) {
			$Translate->change_language($lang);
		}
        $this->__construct([]);
    }

	function detectLanguage($url) {
		global $Translate;
		$path = explode('/', $url);
		$lang = $path[1];

		if ($_SESSION['language'] && $_SESSION['language'] !== 'lt' && !$lang) {
			Location($_SESSION['language'] . $url);
			exit();
		}
		if (strlen($lang) == 2) {
			if ($Translate->language != $lang) {
				$this->changeLanguage($lang);
			}
			return substr($url, 3);
		} else {
			return $url;
		}
	}
}

$dispatch = new Url([]);

//$attributes = $dispatch->decodeUrl($_SERVER['REDIRECT_URL']);
$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = preg_replace("/^(.*)?\?.*$/", "\\1", $request_uri);
$attributes = $dispatch->decodeUrl($request_uri);

//print_r($attributes);
// $dispatch->buildUrl('/index.php?fuseaction=blog2.browseNew')

