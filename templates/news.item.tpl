<!-- article> -->
<article>
    <div class="list-single-main-media fl-wrap">
        <div class="single-slider-wrapper fl-wrap">
            <div class="single-slider fl-wrap">
                <div class="slick-slide-item"><a href="{"/index.php?module=news&action=show&id=`$news_item.id`&rec_url_name=`$news_item.title`"|make_url}"><img src="{"/index.php?module=images&action=show&id=`$news_item.gallery_id`&type=crop800x530"|make_url}" alt=""></a></div>
            </div>
        </div>
    </div>
    <div class="list-single-main-item fl-wrap">
        <div class="list-single-main-item-title fl-wrap">
            <h3><a href="{"/index.php?module=news&action=show&id=`$news_item.id`&rec_url_name=`$news_item.title`"|make_url}">{$news_item.title}</a></h3>
        </div>
        {if $type == "full"}
            {$news_item.description}
        {else}
            {$news_item.short_description}
        {/if}
        <div class="post-opt">
            <ul>
                <li><i class="fa fa-calendar-check-o"></i> <span>{$news_item.rec_time|my_date_format:'middle'}</span></li>
            </ul>
        </div>
        {if $type != "full"}
        <span class="fw-separator"></span>
        <a href="{"/index.php?module=news&action=show&id=`$news_item.id`&rec_url_name=`$news_item.title`"|make_url}" class="btn transparent-btn float-btn">{"read more"|translate}<i class="fa fa-eye"></i></a>
        {/if}
    </div>
</article>
<!-- article end -->