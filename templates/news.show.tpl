<section class="gray-section" id="sec1">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="list-single-main-wrapper fl-wrap" id="sec2">
                    {include file="news.item.tpl" type="full"}
                    <span class="section-separator"></span>
                </div>
            </div>
            <div class="col-md-4">
                <div class="box-widget-item fl-wrap">
                    <div class="box-widget-item-header">
                        <h3>{'latest news'|translate}</h3>
                    </div>
                    <div class="box-widget widget-posts blog-widgets">
                        <div class="box-widget-content">
                            <ul>
                                {foreach from=$latest_news item=$article}
                                <li class="clearfix">
                                    <a href="{"/index.php?module=news&action=show&id=`$article.id`&rec_url_name=`$article.title`"|make_url}" class="widget-posts-img"><img src="{"/index.php?module=images&action=show&id=`$article.gallery_id`&type=crop82x54"|make_url}" alt=""></a>
                                    <div class="widget-posts-descr">
                                        <a href="{"/index.php?module=news&action=show&id=`$article.id`&rec_url_name=`$article.title`"|make_url}" title="">{$article.title}</a>
                                    </div>
                                </li>
                                {/foreach}
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>