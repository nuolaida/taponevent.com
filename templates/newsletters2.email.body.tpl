
<div class="body_top_image" style="margin-bottom: 30px;"><img src="{$config_domain}{"/index.php?module=images&action=show&id=`$data_email.gallery_id`&type=crop600x300"|make_url}" border="0" alt="" width="100%" /></div>

<p><strong>{$data_email.text_subject}</strong></p>

{$data_email.text_body}

{if $data_email && $data_user}
	<img src="{$config_domain}{"/index.php?module=newsletters2&action=track&id=`$data_email.id`&idd=`$data_user.id`"|make_url}" alt="" border="0" />
{/if}

<br /><br /><br />
{if $data_user}
	<p style="text-align: center;"><a href="{$config_domain}{"/index.php?module=newsletters2&action=unsubscribe&item_url=`$unsubscribe_id`"|make_url}">{"unsubscribe"|translate}</a></p>
{/if}
