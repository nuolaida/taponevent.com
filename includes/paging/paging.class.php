<?php
class paging {
/*
	parametrai:
	$offset - nuo kurio elemento pradet puslapi, ne puslapio numeris bet
	pirmo elemento puslapyje
	$count - kiek ish viso elementu yra
	$ipp - items per page
	$link - linkas, kurio gale prideda $offset
*/
	function show($offset, $count, $ipp, $link, $make_url = false) {
		if ($make_url) {
			global $dispatch;
		}
		
		$step = round($count / ($ipp * 6));
		$current_page = floor($offset / $ipp) + 1;
		$last =  floor(($count-1) / $ipp) + 1;
		
		$pages[0] = true;
		$pages[1] = true;
		$pages[$last] = true;

		for ($i = 1; $i <= $last; $i++) {
			if ($step && !($i % $step)) { $pages[$i] = true; }
		}

		for ($i = $current_page - 2; $i < $current_page + 3; $i++) {
			$pages[$i] = true;
		}

		$link2 = $link . (($current_page - 2) * $ipp);
		$out = '';

		if (1 == $current_page) {
			$out = '';
		} else {
			//$out = "<a href='$link2'>&laquo;</a>";
			if ($make_url) {
				$link2 = (($current_page - 2) * $ipp) ? ($link . '.' . (($current_page - 2) * $ipp)) : $link;
				$out = "<a href='" . smarty_modifier_make_url($link2) . "'>&laquo;</a>&nbsp; ";
			} else {
				$out = "<a href='$link2'>&laquo;</a>&nbsp; ";
			}
		}

		for ($i = 1; $i <= $last; $i++) {
			if (isset($pages[$i]) && isset($pages[$i-1])) {
				if ($i == $current_page) {
					$out .= " <a class=\"current-page\">$i</a>";
				} else {
					if ($make_url) {
						$link2 = (($i - 1) * $ipp) ? ($link . '.' . (($i - 1) * $ipp)) : $link;
						$out .= " <a href='" . smarty_modifier_make_url($link2) . "'>$i</a>";
					} else {
						$link2 = $link . (($i - 1) * $ipp);
						$out .= " <a href='$link2'>$i</a>";
					}
				}
			} elseif(isset($pages[$i])) {
				if ($make_url) {
					$link2 = $link . '.' . (($i - 1) * $ipp);
					$out .= " &middot;&middot;&middot; <a href='" . smarty_modifier_make_url($link2) . "'>$i</a>";
				} else {
					$link2 = $link . (($i - 1) * $ipp);
					$out .= " &middot;&middot;&middot; <a href='$link2'>$i</a>";
				}
			}
		}

		$link2 = $link . (($current_page ) * $ipp);

		if (($last == $current_page) || !$count) {
			$out .= '';
		} else {
			if ($make_url) {
				$link2 = $link . '.' . (($current_page ) * $ipp);
				$out .= " &nbsp;<a href='" . smarty_modifier_make_url($link2) . "'>&raquo;</a>";
			} else {
				$out .= " &nbsp;<a href='$link2'>&raquo;</a>";
			}
		}

		$out = '<span>' . $out . '</span>';
		//$out .= "<br>[iš viso:&nbsp;$count]";

		if ($count <= $ipp) {
			$out = '';
		}

		return $out;
	}
	
	
	//	===============================================================
	//	$params['id'] butinas
	//	===============================================================
	function show2($from, $count, $items, $module, $params) {
		$step = ($items) ? round($count / ($items * 6)) : 0;
		$current_page = ($items) ? floor($from / $items) + 1 : 0;
		$last =  ($items) ? floor(($count-1) / $items) + 1 : 0;
		$this_params = $params;
		unset($this_params['id']);
		unset($this_params['name']);
		
		$pages[0] = true;
		$pages[1] = true;
		$pages[$last] = true;

		for ($i = 1; $i <= $last; $i++) {
			if ($step && !($i % $step)) { $pages[$i] = true; }
		}

		for ($i = $current_page - 2; $i < $current_page + 3; $i++) {
			$pages[$i] = true;
		}
		
		$this_params['pg'] = ($current_page - 2) * $items;
		$link2 = scramble_url($module, $params['id'], $params['name'], $this_params);
		//$link2 = $link . (($current_page - 2) * $items);
		$out = '';

		if (1 == $current_page) {
			$out = '';
		} else {
			//$out = "<a href='$link2'>&laquo;</a>";
			$out = "<a href='$link2'>&laquo;</a>&nbsp;";
		}

		for ($i = 1; $i <= $last; $i++) {
			if (isset($pages[$i]) && isset($pages[$i-1])) {
				if ($i == $current_page) {
					$out .= " <b>$i</b>";
				} else {
					$this_params['pg'] = ($i - 1) * $items;
					$link2 = scramble_url($module, $params['id'], $params['name'], $this_params);
					//$link2 = $link . (($i - 1) * $items);
					$out .= " <a href='$link2'>$i</a>";
				}
			} elseif(isset($pages[$i])) {
				$this_params['pg'] = ($i - 1) * $items;
				$link2 = scramble_url($module, $params['id'], $params['name'], $this_params);
				//$link2 = $link . (($i - 1) * $items);
				$out .= " &middot;&middot;&middot; <a href='$link2'>$i</a>";
			}
		}

		$this_params['pg'] = $current_page * $items;
		$link2 = scramble_url($module, $params['id'], $params['name'], $this_params);
		//$link2 = $link . (($current_page ) * $items);

		if (($last == $current_page) || !$count) {
			$out .= '';
		} else {
			//$out .= " <a href='$link2'>&raquo;</a>";
			$out .= " &nbsp;<a href='$link2'>&raquo;</a>";
		}

		$out = '<span>' . $out . '</span>';
		//$out .= "<br>[iš viso:&nbsp;$count]";

		if ($count <= $items) {
			$out = '';
		}

		return $out;
	}
}
?>
