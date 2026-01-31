
<section class="inner-header divider parallax layer-overlay overlay-dark-5" data-stellar-background-ratio="0.5" data-bg-img="{"/index.php?module=images&action=show&id=`$header_photo`&type=any1920x1281"|make_url}">
    <div class="container pt-90 pb-30">
        <div class="section-content pt-100">
            <div class="row">
                <div class="col-md-12">
                    <br /><br /><br />
                </div>
            </div>
        </div>
    </div>
</section>



<section>
    <div class="container">
        <div class="section-content">
            <div class="row">
                <div class="col-md-4">
                    <div class="thumb">
                        <img src="{"/index.php?module=images&action=show&id=`$data_participant.gallery_id`&type=crop400x400"|make_url}" alt="">
                    </div>
                </div>
                <div class="col-md-8">
                    <h3 class="name font-28 text-gray mb-0 mt-0">{* $data_participant.description_short *}</h3>
                    <h1 class="text-uppercase text-gray-darkgray font-48 mt-0">{$data_participant.title}</h1>
                    {$data_participant.description}
                </div>
            </div>
            <div class="row mt-30">
                <div class="col-md-12">
                    <div class="Speaker-address">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="media border-bottom p-15 bg-light">
                                    <div class="media-left">
                                        <i class="fa pe-7s-id text-theme-colored font-24 mt-5"></i>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-0">{"festivals"|translate}:</h5>
                                        <p>
                                            <ul>
                                                {foreach $list_festivals as $item}
                                                    <li><a href="{if $item.is_active}/{else}{"/index.php?module=festivals&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}{/if}">{$item.title}</a></li>
                                                {/foreach}
                                            </ul>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




{if $list_gallery}
	<section>
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div id="grid" class="portfolio-gallery grid-4 gutter clearfix">
						{foreach $list_gallery as $item}
							<div class="portfolio-item photography">
								<div class="thumb">
									<img class="img-fullwidth" src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop380x250"|make_url}" alt="project">
									<div class="overlay-shade"></div>
									<div class="icons-holder">
										<div class="icons-holder-inner">
											<div class="social-icons icon-sm icon-dark icon-circled icon-theme-colored">
												<a href="#"><i class="fa fa-plus"></i></a>
											</div>
										</div>
									</div>
									<a class="hover-link" data-lightbox="image" href="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=any1200x800"|make_url}">View more</a>
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
	</section>
{/if}




