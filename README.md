SolusVMan
=============

<img src="http://i.ftpbox.org/KqgLk.png"/>

About
--------------

A simple panel that uses the [SolusVM client API][SolusVM] to let you manage your virtual servers from anywhere.

Features
--------------

- View server status and resource usage
- Perform basic actions: reboot, boot or shutdown the server
- 2-Step Authentication
- Server status fields refresh automatically

Installation
--------------

### Step 1 : Upload files

Simply upload all files to a folder with browser access, preferably not on the server you want to monitor.

### Step 2: Account Setup

Open config.php with your text editor. Manually add the secret key used for 2-step login.
Also add the SolusVM API information needed (url, key, hash). 
To generate these, go to SolusVM Panel -> API Tab -> Generate

### Step 3: Account Setup

Use your browser to view your panel. You will be prompted to create an account.

### Step 4 : That's all

After you have created the account, login and you're ready to use the panel.

Credits
--------------

SolusVMan uses [Christian Stocker's GoogleAuthenticator.php][gauth] to support 2-step authentication


[SolusVM]: http://docs.solusvm.com/client_api
[gauth]: https://github.com/chregu/GoogleAuthenticator.php