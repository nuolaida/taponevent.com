<section class="inner-header divider parallax layer-overlay overlay-dark-5" data-stellar-background-ratio="0.5" data-bg-img="{"/index.php?module=images&action=show&id=`$data.gallery_id`&type=any1920x1281"|make_url}">
    <div class="container pt-90 pb-30">
        <!-- Section Content -->
        <div class="section-content pt-100">
            <div class="row">
                <div class="col-md-12">
                    <h3 class="title text-white">{$data.title}</h3>
                </div>
                <div class="col-md-12">
                    <br /><br />
                </div>
            </div>
        </div>
    </div>
</section>


<section id="contact" class="divider" data-bg-color="#fff">
    <div class="container pb-30">
        <div class="row">
            <div class="col-sm-12 col-md-6">
                <div>
                    {$data.description}
	                {if $data.gallery_id}
		                <br /><br />
		                <img src="{"/index.php?module=images&action=show&id=`$data.gallery_id`&type=any1920x1281"|make_url}" alt=""/>
	                {/if}
                </div>
            </div>
            <div class="col-sm-7 col-md-6">
                <h4 class="text-uppercase">{"keep in tuch"|translate}</h4>
                <div class="line-bottom mb-30"></div>
                <ul class="list no-dot">
                    <li><i class="fa fa-phone"></i> +370 699 30031</li>
                    <li><i class="fa fa-phone"></i> +370 655 00880</li>
                    <li><i class="fa fa-envelope"></i> <a href="mailto:info@vafest.lt">info@vafest.lt</a></li>
                    <li><i class="fa fa-globe"></i> <a href="https://www.vafest.lt">www.vafest.lt</a></li>
                </ul>
                <ul class="social-icons icon-blue small m-0 mt-30 mb-30">
                    {if $social_links.facebook.link}<li><a href="{$social_links.facebook.link}" target="_blank"><i class="fa fa-facebook"></i></a></li>{/if}
                    {if $social_links.instagram.link}<li><a href="{$social_links.instagram.link}" target="_blank"><i class="fa fa-instagram"></i></a></li>{/if}
                    {if $social_links.twitter.link}<li><a href="{$social_links.twitter.link}" target="_blank"><i class="fa fa-twitter"></i></a></li>{/if}
                    {if $social_links.youtube.link}<li><a href="{$social_links.youtube.link}" target="_blank"><i class="fa fa-youtube"></i></a></li>{/if}
                </ul>
	            <div><img src="{"/index.php?module=images&action=show&id=152&type=any150x150"|make_url}" alt="" /></div>
                <div class="line-bottom mb-30"></div>
                <ul class="list no-dot">
                    <li>VšĮ Alaus festivaliai</li>
                    <li>Į.k.: 304566899</li>
                    <li>PVM k.: LT100011023517</li>
                    <li>Statybininkų g. 14, Molėtai</li>
                    </li>A.s.: LT513500010002983417</li>
                </ul>
            </div>
        </div>
    </div>
</section>

