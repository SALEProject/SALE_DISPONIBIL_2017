<?php

	if (file_exists('bootstrap.php')) require_once 'bootstrap.php';
	else
	{
		echo 'quarkOS bootstrap not found. Redirecting to command prompt in unsafe mode...'."\n";
		if (file_exists('cmd.php')) require_once 'cmd.php';
		else echo 'Command Prompt not found. Please contact an RPC service.'."\n";
	}
	

?>
