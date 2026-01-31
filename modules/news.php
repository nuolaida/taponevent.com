<?php
/**
 * Created by PhpStorm.
 * User: karolis
 * Date: 7/16/18
 * Time: 7:08 PM
 */

use BestPub\News\Model\News;

$PAGE_SIZE = 10;

switch ($url['_action_']) {
    case 'browse':
        $news = News::fetchAll(
            $PAGE_SIZE,
            isset($url['pg']) ? $url['pg'] : 0,
            ['n.rec_time DESC']
        );
        $smarty->assign('news', $news);
        $Paging = new paging;
        $smarty->assign('paging', $Paging->show(isset($url['pg']) ? $url['pg'] : 0, News::getNewsCount(), $PAGE_SIZE, '?pg='));
        $mod = $smarty->fetch('news.list.tpl');

        break;

    case 'show':
        $news_item = News::fetch($url['id']);
        if ($news_item === false) {
            my_header_code(301);
            Location('/');
            die();
        }

        $pageUrl = $dispatch->buildUrl(
            '/index.php?module=news&action=show&id=' . $news_item['id'] . '&rec_url_name=' . $news_item['title']
        );
        if ($_SERVER['REQUEST_URI'] !== $pageUrl) {
            my_header_code(301);
            Location($pageUrl);
            die();
        }

        $smarty->assign('page_url', $pageUrl);
        $smarty->assign('news_item', $news_item);
        $smarty->assign('latest_news', News::fetchAll(5));

        $mod = $smarty->fetch('news.show.tpl');

        // Headers
        $syspage['page_fb_image'] = $dispatch->buildUrl('/index.php?module=images&action=show&id=' . $news_item['gallery_id'] . '&type=crop1920x1024');
        $syspage['page_description'] = strip_tags($news_item['short_description']);
        $syspage['page_og_description'] = strip_tags($news_item['short_description']);
        $syspage['page_og_url'] = $pageUrl;
        $syspage['page_og_title'] = $news_item['title'];
        $syspage['page_og_type'] = 'article';
        $syspage['page_title'] = $news_item['title'];
        break;

}