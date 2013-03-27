<div id="login">
	<form action="index.php" method="POST">

		<input name="username" type="text" class="box user" onfocus="if (this.value=='username') this.value='';"
			value="<?php if (isset($_POST['username'])){ if (!empty($_POST['username'])) echo $_POST['username']; } else echo 'username'; ?>" />

		<input name="password" value="password" class="box pass" onfocus="if (this.value=='password') this.value=''; this.type='password'" />

		<input name="code" value="code" class="box code" onfocus="if (this.value=='code') this.value=''; this.type='code'" />
					
		<input type="submit" value="Login" class="login_button" />
		
	</form>
</div>

<?php
	if ($config->status === ErrorType::InvalidLogin)
		echo "<label class=\"error_msg\">Invalid account, you can try all day...</label>";
	else if ($config->status === ErrorType::InvalidCode)
		echo "<label class=\"error_msg\">Invalid code, you can't trick me...</label>";
?>