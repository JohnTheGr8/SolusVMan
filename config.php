<?php

if(!defined('Access'))
	die("You can't view this file");

/********************************
****** Google AUthenticator *****
********************************/

//	Your secret key used to generate the TOTPs
//	Your phone must use the same secret key.
$account_config['token'] = '';

/********************************
******* SolusVM API Info ********
********************************/

//	url to the client API, should look similar to:
//	https://<MASTER IP>:5656/api/client
$account_config['url'] = "";

//	the generated API key
$account_config['key'] = "";

//	and the generated API hash
$account_config['hash'] = "";

/********************************
********* Login Account *********
********************************/

//	Username in plain text
$account_config['username'] = '';

//	Password in SHA1, do not change this manually
//	unless you know what you're doing
$account_config['password'] = '';

?>