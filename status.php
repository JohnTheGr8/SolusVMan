<?php	
	if ( !isSessionSet() ) header("Location: index.php");

	require_once("func.php");	

	$solus = new SolusMan();
	$result = $solus->getInfo();
	$status = $result['statusmsg'];
	$mem_usage = $solus->getPercentage($result['mem']);
	$disk_usage = $solus->getPercentage($result['hdd']);
	$bw_usage = $solus->getPercentage($result['bw']);

	function isSessionSet(){
		session_start();
		return isset($_SESSION['sid'])
			&& isset($_SESSION['password']) 
			&& isset($_SESSION['username']);
	}
?>

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