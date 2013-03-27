<div id="config">
	<form action="index.php" method="POST">
		
		<input name="username" type="text" class="box user" onfocus="if (this.value=='username') this.value='';"
			value="<?php if (isset($_POST['username'])){ if (!empty($_POST['username'])) echo $_POST['username']; } else echo 'username'; ?>" />
		<input name="password" value="password" class="box pass" onfocus="if (this.value=='password') this.value=''; this.type='password'" />
		<input name="repeat_password" value="repeat password" class="box repeat" onfocus="if (this.value=='repeat password') this.value=''; this.type='password'" />		

		<input type="submit" value="Create Account" class="setup_button" />
		
	</form>
</div>

<?php 
	switch($config->status)
	{
		case ErrorType::InvalidInput:
			echo "<label class=\"error_msg\">Input is invalid.</label>";
			break;
		case ErrorType::PasswordsDontMatch:
			echo "<label class=\"error_msg\">Passwords do not match.</label>";
			break;
	}
?>