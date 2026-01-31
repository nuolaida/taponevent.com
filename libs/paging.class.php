<?php
class Paging2 {
/*
	parametrai:
	$offset - nuo kurio elemento pradet puslapi, ne puslapio numeris bet
	pirmo elemento puslapyje
	$count - kiek ish viso elementu yra
	$ipp - items per page
	$link - linkas, kurio gale prideda $offset
*/
	function show($offset, $count, $ipp, $link) {
		global $dispatch;
		$display_skiped_steps = 6; // How many items display between first and last pages
		$display_before_and_after_current_page = 2; // How many items display before and after active page
		$display_last_page = true; // Display or not last page
		$display_first_page = true; // Display or not first page
		if (is_mobile_version()) {
			$display_skiped_steps = 0;
			$display_before_and_after_current_page = 4;
			$display_last_page = false;
			$display_first_page = false;
		}
		
		if ($make_url) {
			global $dispatch;
		}
		
		$step = ($display_skiped_steps) ? round($count / ($ipp * $display_skiped_steps)) : 0;
		$current_page = floor($offset / $ipp) + 1;
		$last =  floor(($count-1) / $ipp) + 1;
		
		$pages[0] = true;
		if ($display_first_page) {
			$pages[1] = true;
		}
		if ($display_last_page) {
			$pages[$last] = true;
		}

		for ($i = 1; $i <= $last; $i++) {
			if ($step && !($i % $step)) {
				$pages[$i] = true;
			}
		}

		for ($i = $current_page - $display_before_and_after_current_page; $i < $current_page + $display_before_and_after_current_page + 1; $i++) {
			$pages[$i] = true;
		}

		$link2 = $link . (($current_page - 2) * $ipp);
		$out = '';

		if (1 == $current_page) {
			$out = '';
		} else {
			//$out = "<a href='$link2'>&laquo;</a>";
			$link2 = (($current_page - 2) * $ipp) ? ($link . '.' . (($current_page - 2) * $ipp)) : $link;
			$out = "<a href='" . $dispatch->buildUrl($link2) . "'>&laquo;</a>&nbsp; ";
		}

		$number_displayed = false;
		for ($i = 1; $i <= $last; $i++) {
			if (isset($pages[$i]) && isset($pages[$i-1])) {
				if ($i == $current_page) {
					$out .= " <strong>$i</strong>";
				} else {
					$link2 = (($i - 1) * $ipp) ? ($link . '.' . (($i - 1) * $ipp)) : $link;
					$out .= " <a href='" . $dispatch->buildUrl($link2) . "'>$i</a>";
				}
				$number_displayed = true;
			} elseif(isset($pages[$i])) {
				$link2 = $link . '.' . (($i - 1) * $ipp);
				if ($number_displayed) {
					$out .= " &middot;&middot;&middot;";
				}
				$out .= " <a href='" . $dispatch->buildUrl($link2) . "'>$i</a>";
			}
		}

		$link2 = $link . (($current_page ) * $ipp);

		if (($last == $current_page) || !$count) {
			$out .= '';
		} else {
			$link2 = $link . '.' . (($current_page ) * $ipp);
			$out .= " &nbsp;<a href='" . $dispatch->buildUrl($link2) . "'>&raquo;</a>";
		}

		$out = '<span>' . $out . '</span>';
		//$out .= "<br>[iš viso:&nbsp;$count]";

		if ($count <= $ipp) {
			$out = '';
		}

		return $out;
	}
}
?>
