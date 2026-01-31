<?php
error_reporting(E_ALL ^ E_NOTICE);

function is_development_version() {
    $is_development_version = false;
    preg_match("/.*\.(.*)$/", $_SERVER['HTTP_HOST'], $matches);
    if ($matches[1] == 'dev') {
        $is_development_version = true;
    }

    return $is_development_version;
}
	require_once($_SERVER['DOCUMENT_ROOT'] . '/config.globals.php');
	require_once(INCLUDES_FILES_PATH . "url.class.php");

	$html = '';
	
	if ($_GET['type_id'] && $_GET['id']) {
		$title = ($_GET['name']) ? ' title="' . htmlspecialchars($_GET['name']) . "'" : '';
		$html = '<a href="' . $dispatch->buildUrl("/index.php?module=images&action=show&id={$_GET['id']}&type=vbg") . '"' . $title . ' class="lightbox"><img src="' . $dispatch->buildUrl("/index.php?module=images&action=show&id={$_GET['id']}&type={$_GET['type_id']}") . '" alt="' . $title . '" border="0" /></a>';
	}
	
	echo ($html) ? $html : false;
?>
<script language="javascript" type="text/javascript">
	$(".lightbox").lightbox();
</script>

