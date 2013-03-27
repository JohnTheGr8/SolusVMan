<?php
	require_once('func.php');
	
	$config = new Config();	
	$solus = new SolusMan();

	if (isset($_GET['logout']))	
		session_destroy();	
?>

<html>
	<head>
		<link href="style.css?param=35" rel="stylesheet" type="text/css">
		<script src="http://code.jquery.com/jquery-latest.js" type="text/javascript"></script>
		<title>SolusVM Manager</title>
		<head>
	</head>
	<body>

	<?php

		if ($config->GetViewMode() === ViewMode::Setup)
		{
			include('config-page.php');
		}
		else if ($config->GetViewMode() === ViewMode::Login)
		{
			include('login-page.php');		
		}
		else if ($config->GetViewMode() === ViewMode::Browse){
			$result = $solus->getInfo();
			$status = $result['statusmsg'];
			$mem_usage = $solus->getPercentage($result['mem']);
			$disk_usage = $solus->getPercentage($result['hdd']);
			$bw_usage = $solus->getPercentage($result['bw']);
			?>

			<label id="server_name"><?php echo $result['hostname']; ?></label>			
			<label class="server_ip"><?php echo $result['ipaddress']; ?></label>			
			
			<?php echo $solus->getStatus($result); ?>

			<ul id="server_info">
				<li>
					<label class="info">Status</label>
					<label class='info_value <?php echo $solus->getStatusColor($status); ?>'><?php echo $status; ?></label>
				</li>
				<li>
					<label class="info">Memory Usage</label>
					<label class='info_value <?php echo $solus->getUsageColor($mem_usage); ?>'><?php echo $mem_usage; ?> %</label>
				</li>
				<li>
					<label class="info">Disk Usage</label>
					<label class='info_value <?php echo $solus->getUsageColor($disk_usage); ?>'><?php echo $disk_usage ?> %</label>
				</li>
				<li>
					<label class="info">Bandwidth Usage</label>
					<label class='info_value <?php echo $solus->getUsageColor($bw_usage); ?>'><?php echo $bw_usage; ?> %</label>
				</li>				
			</ul>

			<a class="action" href="index.php?action=reboot" id="reboot">Reboot</a>
			<a class="action" href="index.php?action=boot" id="boot">Boot</a>
			<a class="action" href="index.php?action=shutdown" id="shutdown">Shutdown</a>
			<?php			
		}

	?>

	</body>

	<script type="text/javascript">
		//	Refresh the status fields
		$(document).ready(function(){
			$('#server_info').load("status.php");
			var refreshId = setInterval(function(){
				$('#server_info').load("status.php");
			}, 10000);
		$.ajaxSetup({cache:false});
		});
	</script>	
</html>