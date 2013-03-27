<?php
	include_once("gauth/GoogleAuthenticator.php");
	define('Access', TRUE);

	class ViewMode {
		const Setup = 0;
		const Login = 1;
		const Browse = 2;
	}

	class ErrorType{
		const None = 0;
		const InvalidInput = 1;
		const PasswordsDontMatch = 2;
		const InvalidLogin = 3;
		const InvalidCode = 4;
	}

	class Config{

		public $config = array();
		public $status;

		public $code;
		public $token;

		public function __construct(){
			session_start();
			$this->getConfig();
			$this->getStatus();
			$this->code = isset($_POST["code"]) ? $_POST["code"] : "0";			
		}

		function getConfig() {			
			require('config.php');
			$this->config = $account_config;	
			$this->token = $account_config['token'];		
		}

		public function GetViewMode(){		

			if ($this->isSessionSet() && $this->isConfigSet())
			{
				return ViewMode::Browse;
			}
			else if ($this->postMatchesConfig() && $this->isConfigSet())
			{
				$this->startSession();
				return ViewMode::Browse;
			}
			
			if ($this->checkConfig()){
				return ViewMode::Login;
			}
			else{
				if($this->isPostProperSet()){
					$this->setConfig();
					return ViewMode::Login;
				}
				else{	
					return ViewMode::Setup;
				}					
			}
		}

		private function isPostProperSet(){

			if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['repeat_password']))
				return false;
						
			$ga = new GoogleAuthenticator();
			return $_POST['password'] === $_POST['repeat_password'];
		}

		private function getStatus(){
			$ga = new GoogleAuthenticator();

			if (!isset($_POST['username']) || !isset($_POST['password']))
				$this->status = ErrorType::None;
			else if (empty($_POST['username']) || empty($_POST['password']))
				$this->status = ErrorType::InvalidInput;
			else if (isset($_POST['repeat_password']) && $_POST['password'] !== $_POST['repeat_password'])
				$this->status = ErrorType::PasswordsDontMatch;			
			else if ($_POST['username'] !== $this->config['username'] || sha1($_POST['password']) !== $this->config['password'])
				$this->status = ErrorType::InvalidLogin;
			else if (!$ga->checkCode($this->token, $this->code))
				$this->status = ErrorType::InvalidCode;
			else 
				$this->status = ErrorType::None;
		}

		/////////////////////////////////////////////////////////////////
		/////////////////////////    Session    /////////////////////////
		/////////////////////////////////////////////////////////////////

		private function isSessionSet(){
			if (!isset($_SESSION['sid']) || !isset($_SESSION['password']) || !isset($_SESSION['username']) )
				return false;
			
			return $_SESSION['sid'] == session_id() 
				&& $_SESSION['username'] === $this->config['username'] 
				&& $_SESSION['password'] === $this->config['password'];
		}

		private function startSession() {
			$_SESSION['sid'] = session_id();
			$_SESSION['username'] = $this->config['username'];
			$_SESSION['password'] = $this->config['password'];
		}

		private function postMatchesConfig() {
			if (!isset($_POST['username']) || !isset($_POST['password']))
				return false;

			$ga = new GoogleAuthenticator();

			return $_POST['username'] === $this->config['username'] 
				&& sha1($_POST['password']) === $this->config['password']
				&& $ga->checkCode($this->token, $this->code);
		}

		/////////////////////////////////////////////////////////////////
		/////////////////////////  Config file  /////////////////////////
		/////////////////////////////////////////////////////////////////

		private function isConfigSet(){
			return !empty($this->config['username']) || !empty($this->config['password']);
		}

		private function checkConfig(){

			if (!$this->isConfigSet())
				return False;

			return isset($this->config['username']) 
				&& isset($this->config['password']);
		}

		public function setConfig() {
			$array = array('username' => $_POST['username'], 
					'password' => $_POST['password'], 
					'repeat_password' => $_POST['repeat_password']);
		
			if(!is_writable('config.php')) {
				return 'You are not allowed to change the config';
			}
			if(empty($array['password']) || empty($array['username'])) {
				return 'Username/password fields cannot be empty';
			}
			if($array['password'] !== $array['repeat_password']) {
				return 'Please enter the same password';
			}
			
			unset($array['repeat_password']);
			$lines = file('config.php');
			foreach($array as $key => $value) {
				if($this->config[$key] != $value && !empty($value)) {
					if($key == 'password') {
						$value = sha1($value);
					}
					$lines = $this->writeConfig($key, $value, $lines);
				}
			}
			$handle = fopen('config.php', 'w');
			fwrite($handle, implode($lines));
			fclose($handle);
			Header("Location: index.php");
		}

		private function writeConfig($key, $value, $lines) {
			foreach($lines as $linekey => $line) {
				if(strstr($line, '$account_config[\''.$key.'\']')) {
					$lines[$linekey] = '$account_config[\''.$key.'\'] = \''.$value.'\';'."\n";
					$this->config[$key] = $value;
				}
			}
			return $lines;
		}
	}	

	class SolusMan {
 
		private $url;
		private $postfields;

	 	public function __construct(){
	 		require('config.php');
			$this->url = $account_config['url'];
	 		$this->postfields["key"] = $account_config['key'];
			$this->postfields["hash"] = $account_config['hash'];
 		}

 		private function getResponse(){
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $this->url . "/command.php?key=".$this->postfields['key']."&hash=".$this->postfields['hash'].$this->postfields['action']);
		    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		    curl_setopt($ch, CURLOPT_HEADER, 0);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    $data = curl_exec($ch);
		    curl_close($ch);

		    preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $match);
		    $result = array();
		    foreach ($match[1] as $x => $y)
		    {
		      $result[$y] = $match[2][$x];
		    }
		    return $result;
 		}

	 	public function getInfo(){
	 		$this->postfields["action"] = "&action=status&ipaddr=true&bw=true&mem=true&hdd=true";
			
			if ($this->isActionSet())
				$this->postfields["action"] .= "&action=".$_GET['action'];		

			return $this->getResponse();
			
		}

		public function getStatus($result){

			// Check for any errors
			if ($result["status"] == "error")
			{
				echo "<label id='server_status' class='prob'>".$result["statusmsg"]."</label>";
				exit();
			}

			// Display a message on successful return
			if ($result["status"] == "success")
			{
				if ($result["statusmsg"] == "online")
				{
					echo "<label id='server_status' class='good'>The virtual server is online!</label>";
				} elseif ($result["statusmsg"] == "offline")
				{
					echo "<label id='server_status' class='good'>The virtual server is offline!</label>";
				} elseif ($result["statusmsg"] == "rebooted")
				{
					echo "<label id='server_status' class='good'>The virtual server has been rebooted!</label>";
				} elseif ($result["statusmsg"] == "shutdown")
				{
					echo "<label id='server_status' class='good'>The virtual server has been shutdown!</label>";
				} elseif ($result["statusmsg"] == "booted")
				{
					echo "<label id='server_status' class='good'>The virtual server has been booted!</label>";
				}else
				{
					echo "<label id='server_status' class='prob'>Status message unknown!</label>";
				}
			}
		}

		public function getPercentage($values){
			$value = explode(',', $values);

			return $value[3];
		}

		public function getUsageColor($value){
			if ($value < 75)
				return "normal";
			else if ($value < 90)
				return "warning";
			else
				return "attention";
		}

		public function getStatusColor($status){
			if (!$status || $status !== "online")
				return "offline";
			else
				return "online";
		}

		public function performServerAction($action){
			if (!isset($_GET['action'])) return;
			$action = $_GET['action'];
			if (!$action) return;

			$this->postfields["action"] = "&action=".$action;			
			return $this->getResponse();
		}

		public function isActionSet()
		{			
			return isset($_GET['action']) && ( $_GET['action'] == "boot" || $_GET['action'] == "reboot" || $_GET['action'] == "shutdown");
		}
	}


?>