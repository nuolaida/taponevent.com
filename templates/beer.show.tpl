
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
                    </div>
                </div>
                <div class="col-md-8">
                    <h1 class="text-uppercase text-gray-darkgray font-48 mt-0">{$data_festival.title}</h1>
                </div>
            </div>
	        {foreach $list_breweries as $item_brewery}
	            <div class="row mt-30">
	                <div class="col-md-12">
	                    <div class="Speaker-address">
	                        <div class="row">
	                            <div class="col-sm-4">
	                                <div class="media border-bottom p-15 mb-20 bg-light">
	                                    <div class="media-body">
	                                        <h4>{$item_brewery.title}</h4>
	                                    </div>
	                                </div>
	                            </div>
	                            <div class="col-sm-4">
	                                <div class="media border-bottom p-15 bg-light">
	                                    <div class="media-left">

	                                    </div>
	                                    <div class="media-body">
	                                        <h5 class="mt-0 mb-0">{"beer"|translate}</h5>
	                                        <p>
	                                            <ul>
	                                                {if $list_beer[$item_brewery.id]}
	                                                    {foreach $list_beer[$item_brewery.id] as $item_beer}
		                                                    <li>
		                                                        {"day `$item_beer.festival_session_number`"|translate}<br />
			                                                    <strong style="font-size: 1.2em;">{$item_beer.title}</strong>
		                                                        {if $item_beer.style}/ {$item_beer.style}{/if}
		                                                        {if $item_beer.abv}/ {$item_beer.abv}%{/if}
			                                                    {if $item_beer.untappd_id} <a href="https://untappd.com/b/a/{$item_beer.untappd_id}" target="_blank" style="background-color: #FFC000; color: #000; padding: 1px 10px; border-radius: 3px; margin-left: 10px; font-size: 0.9em;">untappd</a>{/if}
							                                    <br />
		                                                        {if $item_beer.description}
								                                    <em style="font-size: 0.8em;">{$item_beer.description}</em><br />
		                                                        {/if}
			                                                    <br />
		                                                    </li>
	                                                    {/foreach}
	                                                {/if}
	                                            </ul>
	                                        </p>
	                                    </div>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </div>
            {/foreach}
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




