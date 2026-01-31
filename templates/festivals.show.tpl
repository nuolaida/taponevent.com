
{* -- Top images -- *}
<section class="inner-header bg-black-222">
    <div class="container pt-90 pb-30">
        <!-- Section Content -->
        <div class="section-content pt-100">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-10 text-white">{$data_festival.title}</h2>
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



{if $list_breweries}
    <section id="breweries">
        <div class="divider parallax layer-overlay overlay-darkblue" data-stellar-background-ratio="0.5" data-bg-img="">
            <div class="container pt-50 pb-50">
                <div class="section-title">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 text-center">
                            <h2 class="title text-white mb-0">{"breweries"|translate}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white-light">
            <div class="container pt-80 pb-80">
                <div class="section-content">
                    <div class="row multi-row-clearfix">
                        {foreach $list_breweries as $item}
                            <div class="col-xs-12 col-sm-6 col-md-3 mb-30">
                                <div class="team-member clearfix">
                                    <div class="team-thumb"><img alt="{$item.title}" src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop400x400"|make_url}" class="img-fullwidth"></div>
                                    <div class="overlay">
                                        <div class="content text-center">
                                            <h4 class="author mb-0"><a href="{"/index.php?module=breweries&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}">{$item.title}</a></h4>
                                            <h6 class="title text-gray font-14 mt-5 mb-15">{$item.country_title}</h6>
                                        </div>
                                    </div>
                                    <ul class="social-icons flat icon-white square mt-10">
                                        <li class=""><a href="{"/index.php?module=breweries&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}">{"read more"|translate}</a></li>
                                    </ul>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}



{if $list_participants}
	<section id="breweries">
		<div class="divider parallax layer-overlay overlay-darkblue" data-stellar-background-ratio="0.5" data-bg-img="">
			<div class="container pt-50 pb-50">
				<div class="section-title">
					<div class="row">
						<div class="col-md-6 col-md-offset-3 text-center">
							<h2 class="title text-white mb-0">{"participants other"|translate}</h2>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="bg-white-light">
			<div class="container pt-80 pb-80">
				<div class="section-content">
					<div class="row multi-row-clearfix">
						{foreach $list_participants as $item}
							<div class="col-xs-12 col-sm-6 col-md-3 mb-30">
								<div class="team-member clearfix">
									<div class="team-thumb"><img alt="" src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop400x400"|make_url}" class="img-fullwidth"></div>
									<div class="overlay">
										<div class="content text-center">
											<h4 class="author mb-0"><a href="{"/index.php?module=participants&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}">{$item.title}</a></h4>
											<h6 class="title text-gray font-14 mt-5 mb-15">{$item.description_short}</h6>
										</div>
									</div>
									<ul class="social-icons flat icon-white square mt-10">
										<li class=""><a href="{"/index.php?module=participants&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}">{"read more"|translate}</a></li>
									</ul>
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			</div>
		</div>
	</section>
{/if}
