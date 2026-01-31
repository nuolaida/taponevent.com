<div class="login-box">
	<h1>{"login"|translate}</h1>

	<form method="post" action="/app.php">
		<input type="hidden" name="module" value="users">
		<input type="hidden" name="action" value="loginAct">

		<label for="username">{"username"|translate}</label>
		<input type="text" id="username" name="email" required>

		<label for="password">{"password"|translate}</label>
		<input type="password" id="password" name="password" required>

		<button type="submit">{"login2"|translate}</button>
	</form>
</div>