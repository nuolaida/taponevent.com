
<div class="login-box">
	<h1>{"choose festival"|translate}</h1>
    {if $list}
		<ul class="te-festival-list">
            {foreach $list as $item}
				<li>
					<a href="?module=festivals&action=selectAct&id={$item.festival_id}">
						<span class="te-fest-title">{$item.title}</span>
						<span class="te-fest-arrow">›</span>
					</a>
				</li>
            {/foreach}
		</ul>
    {else}
		<div class="te-no-data">{"no active festivals"|translate}</div>
    {/if}
</div>
