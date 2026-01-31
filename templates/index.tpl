{if !$display_layout}
	{$content}
{else}
	{$content}
{/if}

{if isset($site_footer_html)}
	{foreach item=item from=$site_footer_html}
		{$item}
	{/foreach}
{/if}

