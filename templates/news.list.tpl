<section class="gray-section" id="sec1">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="list-single-main-wrapper fl-wrap" id="sec2">
                    {foreach from=$news item=$news_item}
                        {include file="news.item.tpl"}
                        <span class="section-separator"></span>
                    {/foreach}

                    <div class="pagination">
                        {$paging}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
            </div>
        </div>
    </div>
</section>


<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/plugins.js"></script>
<script type="text/javascript" src="js/scripts.js"></script>