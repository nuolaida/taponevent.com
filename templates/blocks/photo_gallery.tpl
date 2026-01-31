{if $block_gallery}
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div id="grid" class="portfolio-gallery grid-4 gutter clearfix">
                        {foreach $block_gallery as $item}
                            <div class="portfolio-item photography">
                                <div class="thumb">
                                    <img class="img-fullwidth" src="{"/index.php?module=images&action=show&id=`$item`&type=crop380x250"|make_url}" alt="project">
                                    <div class="overlay-shade"></div>
                                    <div class="icons-holder">
                                        <div class="icons-holder-inner">
                                            <div class="social-icons icon-sm icon-dark icon-circled icon-theme-colored">
                                                <a href="#"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="hover-link" data-lightbox="image" href="{"/index.php?module=images&action=show&id=`$item`&type=any1200x800"|make_url}">View more</a>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}
