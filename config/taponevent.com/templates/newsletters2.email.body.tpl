<div class="body_top_image" style="margin-bottom: 30px;"><img src="{$config_domain}{"/index.php?module=images&action=show&id=`$data_email.gallery_id`&type=crop600x300"|make_url}" border="0" alt="" width="100%" style="border-bottom: 3px solid #0b3254;" /></div>

<p><strong>{$data_email.text_subject}</strong></p>

{$data_email.text_body}

{if $data_email && $data_user}
	<img src="{$config_domain}{"/index.php?module=newsletters2&action=track&id=`$data_email.id`&idd=`$data_user.id`"|make_url}" alt="" border="0" />
{/if}

<br /><br /><br />
<p style="background-color: #0b3254; color: #ffffff; padding: 20px;">
	<strong>{"festival name short"|translate}</strong> - {"festival name"|translate}
	| <a href="{$config_domain}" style="color: #ffffff;">{$config_domain_plain}</a>
    {if $config_social.facebook.link}
		| <a href="{$config_social.facebook.link}" style="color: #ffffff;">{"facebook"|translate}</a>
    {/if}
    {if $config_social.instagram.link}
		| <a href="{$config_social.instagram.link}" style="color: #ffffff;">{"instagram"|translate}</a>
    {/if}
    {if $config_social.youtube.link}
		| <a href="{$config_social.youtube.link}" style="color: #ffffff;">{"youtube"|translate}</a>
    {/if}
    {if $data_user}
		| <a href="{$config_domain}{"/index.php?module=newsletters2&action=unsubscribe&item_url=`$unsubscribe_id`"|make_url}" style="color: #ffffff;">{"unsubscribe"|translate}</a>
	{/if}
</p>
