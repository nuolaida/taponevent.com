<!--section -->
<section class="scroll-con-sec hero-section" data-scrollax-parent="true" id="sec1">
    <div class="bg"  data-bg="/web/images/intro.jpg?2" data-scrollax="properties: { translateY: '200px' }"></div>
    <div class="overlay"></div>
    <div class="hero-section-wrap fl-wrap">
        <div class="container">
            <div class="intro-item fl-wrap">
                <h2>{"title row 1"|translate}</h2>
                <h3>{"title row 2"|translate}</h3>
            </div>
            <div class="main-search-input-wrap">
                <div class="main-search-input fl-wrap">
                    <form method="get" action="{"/index.php?module=pubs&action=browse"|make_url}">
                        <div class="main-search-input-item">
                            <input type="text" placeholder="{"name"|translate}" name="name">
                        </div>
                        <div class="main-search-input-item">
                            <select name="city_id" data-placeholder="{"search..."|translate}" class="chosen-select" >
                                <option data-value="0" value="0">{"all cities"|translate}</option>
                                {foreach from=$cities item=city}
                                    <option data-value="{$city.id}" value="{$city.id}">{$city.name}</option>
                                {/foreach}
                            </select>
                        </div>
                        <button type="submit" class="main-search-button">{"search..."|translate}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="bubble-bg"> </div>
    <div class="header-sec-link">
        <div class="container"><a href="#sec2" class="custom-scroll-link">{"title row 3"|translate}</a></div>
    </div>
</section>