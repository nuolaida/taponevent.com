<header class="main-header dark-header fs-header sticky">
    <div class="header-inner">
        <div class="logo-holder">
            <a href="/"><img src="/web/images/logo.png" alt=""></a>
        </div>


        <div class="nav-holder main-menu">
            <nav>
                <ul>
                    <li>
                        <a {if $page == 'title'}class="act-link"{/if} href="/">{'home'|translate}</a>
                        <a {if $page == 'pubs'}class="act-link"{/if} href="{"/index.php?module=pubs&action=browse"|make_url}">{'bars'|translate}</a>
                        <a {if $page == 'texts' && $active_action == 'best'}class="act-link"{/if} href="{"/index.php?module=texts&action=best"|make_url}">{'best'|translate}</a>
                        {if $language_active == 'lt'}
                            <a {if $page == 'news'}class="act-link"{/if} href="{"/index.php?module=news&action=browse"|make_url}">{'news'|translate}</a>
                        {/if}
                        <a {if $page == 'texts' && $active_action == 'about'}class="act-link"{/if} href="{"/index.php?module=texts&action=about"|make_url}">{'about'|translate}</a>
                        <a href="{"/index.php?change_language=lt"|make_url}?change_language=lt" class="languages lt{if $language_active == 'lt'} active{/if}"></a>
                        <a href="{"/index.php?change_language=en"|make_url}?change_language=en" class="languages en{if $language_active == 'en'} active{/if}"></a>
                    </li>
                </ul>
            </nav>
        </div>
        <!-- navigation  end -->
    </div>
</header>