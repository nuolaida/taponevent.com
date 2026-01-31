<?xml version="1.0" encoding="UTF-8"?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{$http_protocol}://{$http_domain}</loc>
    </url>
    <url>
        <loc>{$http_protocol}://{$http_domain}/en/</loc>
    </url>

    {foreach $festivals_list as $item}
        <url>
            <loc>{$http_protocol}://{$http_domain}{"/index.php?module=festivals&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}</loc>
        </url>
    {/foreach}


    {foreach $breweries_list as $item}
        <url>
            <loc>{$http_protocol}://{$http_domain}{"/index.php?module=breweries&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}</loc>
        </url>
    {/foreach}
    <url>
        <loc>{$http_protocol}://{$http_domain}{"/index.php?module=breweries&action=map"|make_url}</loc>
    </url>

    {foreach $participants_list as $item}
        <url>
            <loc>{$http_protocol}://{$http_domain}{"/index.php?module=participants&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}</loc>
        </url>
    {/foreach}

    {foreach $texts_list as $item}
        <url>
            <loc>{$http_protocol}://{$http_domain}{"/index.php?module=texts&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}</loc>
        </url>
    {/foreach}
</urlset>