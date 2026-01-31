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
	function show($offset, $count, $ipp, $link)
	{
		$step = round($count / ($ipp * 6));
		$current_page = floor($offset / $ipp) + 1;
		$last =  floor(($count-1) / $ipp) + 1;
		
		$pages[0] = true;
		$pages[1] = true;
		$pages[$last] = true;

		for ($i = 1; $i <= $last; $i++) {
			if (!$step || !($i % $step)) { $pages[$i] = true; }
		}

		for ($i = $current_page - 2; $i < $current_page + 3; $i++) {
			$pages[$i] = true;
		}

		$link2 = admin_make_url($link . (($current_page - 2) * $ipp));
		$out = '';

		if (1 == $current_page) {
			$out = '';
		} else {
			$out = "<a href='" . admin_make_url($link2) . "'>&laquo;</a>";
		}

		for ($i = 1; $i <= $last; $i++) {
			if (isset($pages[$i]) && isset($pages[$i-1])) {
				if ($i == $current_page) {
					$out .= " <b>$i</b>";
				} else {
					$link2 = $link . (($i - 1) * $ipp);
					$out .= " <a href='" . admin_make_url($link2) . "'>$i</a>";
				}
			}
			elseif(isset($pages[$i])) {
				$link2 = $link . (($i - 1) * $ipp);
				$out .= " &middot;&middot;&middot; <a href='" . admin_make_url($link2) . "'>$i</a>";
			}
		}

		$link2 = $link . (($current_page ) * $ipp);

		if (($last == $current_page) || !$count) {
			$out .= '';
		} else {
			$out .= " <a href='" . admin_make_url($link2) . "'>&raquo;</a>";
		}

		$out = '<span>' . $out . '</span>';
		$out .= "<br>[iš viso:&nbsp;$count]";

		if ($count <= $ipp) {
			$out = '';
		}

		return $out;
	}
}
?>
