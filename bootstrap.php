<?php
	//  bootstrap module id
	define('QOS_MOD_BOOTSTRAP', true);

	//  exit codes for quarkOS
	define('QOS_ERR_NOTFOUND', -1);
	define('QOS_ERR_OK', 0);
	define('QOS_ERR_LOADERROR', 1);
	define('QOS_ERR_FAIL', 2);

	//  this will attempt to successfuly load quarkOS or redirect control if not possible
	$qos_exit = QOS_ERR_NOTFOUND; //  assume the file does not exists	
	if (file_exists('quarkos.php')) 
	{
		$qos_exit = QOS_ERR_LOADERROR; //  assume it cannot be loaded
		require_once 'quarkos.php';
		
		$qos_exit = TQuarkOS::instance()->run(); //  retrieve the true exit code
	};
	
	define('QOS_EXITCODE', $qos_exit); //  save the exit code for command prompt
	switch ($qos_exit)
	{
		case QOS_ERR_NOTFOUND:
			echo 'quarkOS kernel not found'."\n";
			break;
		case QOS_ERR_LOADERROR:
			echo 'quarkOS code level error detected in kernel file.'."\n";
			break;
		case QOS_ERR_FAIL:
			echo 'quarkOS kernel run level error detected.'."\n";
			break;
		default:
			break;
	}
	
	if ($qos_exit != QOS_ERR_OK)
	{
		echo 'Redirecting to command prompt in unsafe mode...'."\n";
		if (!defined('QOS_MOD_CMD'))
		{
			if (file_exists('cmd.php')) require_once 'cmd.php';
			else echo 'Command Prompt not found. Please contact an RPC service.'."\n";
		}
		//  if QOS_MOD_CMD we assume that the command prompt was previously loaded
		//  and it will take over automatically from here
	}

?>
