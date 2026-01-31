<section class="inner-header divider parallax layer-overlay overlay-dark-5" data-stellar-background-ratio="0.5" data-bg-img="{"/index.php?module=images&action=show&id=`$data_settings.gallery_id`&type=any1920x1281"|make_url}">
    <div class="container pt-90 pb-30">
        <!-- Section Content -->
        <div class="section-content pt-100">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="title text-white">{$data_settings.title}</h3>
                </div>
                <div class="col-md-12">
                    <br /><br />
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bg-solid-color">
    <div class="container pb-80">
        <div class="row">
            <div class="col-md-12">
                <h3 class="sub-title font-28 text-gray-darkgray m-0 mt-0 mt-md-0">{* Overview *}</h3>
                <h2 class="title font-40 text-gray mt-0 mb-20">{* About the Conference *}</h2>
                {$data_settings.description}
            </div>
        </div>
    </div>
</section>



{if $list_judges}
    <section>
        <div class="divider parallax layer-overlay overlay-darkblue" data-stellar-background-ratio="0.5" data-bg-img="http://placehold.it/1920x1280">
            <div class="container pt-50 pb-50">
                <div class="section-title">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 text-center">
                            <h2 class="title text-white mb-0">{"judges"|translate}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white-light">
            <div class="container pt-80 pb-50">
                <div class="row multi-row-clearfix">
                    {foreach $list_judges as $item}
                        <div class="col-xs-12 col-sm-6 col-md-3 mb-30">
                            <div class="team-member clearfix">
                                {if $item.gallery_id}
                                    {assign var="photo_id" value=$item.gallery_id}
                                {else}
                                    {assign var="photo_id" value=$default_judge_photo_id}
                                {/if}
                                <div class="team-thumb"><img alt="{$item.name}" src="{"/index.php?module=images&action=show&id=`$photo_id`&type=crop400x400"|make_url}" class="img-fullwidth"></div>
                                <div class="overlay">
                                    <div class="content text-center">
                                        <h4 class="author mb-0"><a href="#">{$item.name}</a></h4>
                                        <h6 class="title text-gray font-14 mt-5 mb-15">{$item.description}</h6>
                                    </div>
                                </div>
                                <ul class="social-icons flat icon-white square mt-10">
                                    {if $item.social_linkedin}<li><a href="{$item.social_linkedin}" target="_blank"><i class="fa fa-linkedin pr-10 pl-10"></i></a></li>{/if}
                                    {if $item.social_facebook}<li class=""><a href="{$item.social_facebook}" target="_blank"><i class="fa fa-facebook pr-10 pl-10"></i></a></li>{/if}
                                    {if $item.social_instagram}<li class=""><a href="{$item.social_instagram}" target="_blank"><i class="fa fa-instagram pr-10 pl-10"></i></a></li>{/if}
                                    {if $item.social_youtube}<li class=""><a href="{$item.social_youtube}" target="_blank"><i class="fa fa-youtube-play pr-10 pl-10"></i></a></li>{/if}
                                    {if $item.social_web}<li class=""><a href="{$item.social_web}" target="_blank"><i class="fa fa-globe pr-10 pl-10"></i></a></li>{/if}
                                </ul>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </section>
{/if}

{if $list_medals}
    <section>
        <div class="divider parallax layer-overlay overlay-darkblue" data-stellar-background-ratio="0.5" data-bg-img="http://placehold.it/1920x1280">
            <div class="container pt-50 pb-50">
                <div class="section-title">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 text-center">
                            <h2 class="title text-white mb-0">{"medals"|translate}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="divider">
        <div class="container pt-60 pb-60">
            {foreach $list_medals as $key => $value}
                <div class="section-title mb-60">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="esc-heading small-border text-center">
                                <h3>{$list_medal_categories_assoc[$key]}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="section-content">
                    <div class="row">
                        {foreach $value.medals as $medal}
                            <div class="col-sm-12 col-md-4">
                                <div class="contact-info text-center">
                                    <i class="fa fa-trophy font-36 mb-10 medal-color-{$medal.medal}"></i>
                                    <h4>{$medal.owner_title}</h4>
                                    <h6 class="text-gray">{$medal.title}{if $medal.abv} | {$medal.abv}%{/if}{if $medal.sweetness} | {"competition sweetness `$medal.sweetness`"|translate}{/if}</h6>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
                <br /><br /><br /><br /><br /><br />
            {/foreach}
        </div>
    </section>
{/if}