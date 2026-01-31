<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />
<div class="login_container">
    {if !$user_info.id}
        <div class="form">
            <form action="/index.php" method="post">
                <input type="hidden" name="module" value="users" />
                <input type="hidden" name="action" value="login" />
                <input type="text" name="email" class="input user_login_input" />
                <input type="password" name="password" class="input user_login_input" />
                <input type="submit" value="{"login"|translate}" class="button" />
            </form>
        </div>
        {*
		<div class="links">
			<span><a href="{"/index.php?module=users&action=infoNew"|make_url}">{"register"|translate}</a></span>
			<a href="/index.php/users.forgot">{"password remind"|translate}</a>
		</div>
		*}
    {else}
        <div class="menu">
            <div>{$user_info.name}</div>
            <ul>
                {if $user_info.admin}<li><a href="/admin.php">{"administration"|translate}</a></li>{/if}
                <li><a href="{"/index.php?module=beerdraft&action=untappd"|make_url}">{"untappd"|translate}</a></li>
                <li class="logout"><a href="{"/index.php?module=users&action=logout"|make_url}">{"logout"|translate}</a></li>
            </ul>
        </div>
    {/if}
</div>
