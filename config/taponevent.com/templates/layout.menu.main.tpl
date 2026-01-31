<ul class="menuzord-menu">
	<li class="active"><a href="/#home">{"menu home"|translate}</a></li>
	<li><a href="/#breweries">{"breweries"|translate}</a></li>
	<li><a href="/#tickets">{"tickets"|translate}</a></li>
	<li><a href="/#place">{"place"|translate}</a></li>
	<li><a href="#">{"archive"|translate}</a>
		<ul class="dropdown">
			{foreach $list_festivals_archive as $item}
				<li><a href="{"/index.php?module=festivals&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}">{$item.title}</a></li>
			{/foreach}
			<li><a href="{"/index.php?module=breweries&action=map"|make_url}">{"breweries"|translate}</a></li>
		</ul>
	</li>
	<li><a href="#">{"more"|translate}..</a>
		<ul class="dropdown">
            {assign var="menutitle" value="contacts"|translate}
			<li><a href="{"/index.php?module=texts&action=show&id=1&rec_url_name=`$list_texts.1.title`"|make_url}">{"contacts"|translate}</a></li>
			<li><a href="{"/index.php?module=catalogue&action=festivals"|make_url}">{"festivals"|translate}</a></li>
			<li><a href="{"/index.php?module=catalogue&action=competitions"|make_url}">{"competitions"|translate}</a></li>
			<li><a href="{"/index.php?module=sommelier&action=show&id=2024"|make_url}">{"sommelier competition"|translate}</a></li>
		</ul>
	</li>
	<li><a href="#">{"menu language"|translate}</a>
		<ul class="dropdown">
			<li><a href="?change_language=lt">lietuviškai</a></li>
			<li><a href="?change_language=en">english</a></li>
		</ul>
	</li>
</ul>
