<?php
	//  declare the command module id
	define('QOS_MOD_CMD', true);
	
	class TQuarkCommand
	{
		static private $Finstance = null;
		
		static function instance()
		{
			if (self::$Finstance != null) return self::$Finstance;
				
			self::$Finstance = new TQuarkCommand();
			return self::$Finstance;
		}
		
		var $buf;
		var $lines = Array();
		
		//-----------------------------------------------------------------------
		//  internal commands definition
		var $internals = Array
		(
				''	=> Array('description' => 'null operation, with null input and null output', 'code' => 'internal_null'),
				'help' => Array('description' => 'prints this help message', 'code' => 'internal_help'),
				'clear' => Array('description' => 'clears screen', 'code' => 'internal_clear'),
				'date' => Array('description' => 'prints current system date', 'code' => 'internal_date'),
				'time' => Array('description' => 'prints current system time', 'code' => 'internal_time'),
				'timestamp' => Array('description' => 'prints current Unix timestamp', 'code' => 'internal_timestamp'),
				'echo' => Array('description' => 'prints the function argument on the standard output', 'code' => 'internal_echo')
		);
		
		function internal_null()
		{
			return;
		}
		
		function internal_help()
		{
			echo '<pre>';
			foreach ($this->internals as $key => $value)
			{
				echo str_pad($key, 16, ' ').$value['description']."\n";
			}
			echo '</pre>';
		}
		
		function internal_clear()
		{
			$this->lines = Array();
		}
		
		function internal_date()
		{
			echo date('F j, Y, g:i a');
		}
		
		function internal_time()
		{
			echo date('g:i a');
		}
		
		function internal_timestamp()
		{
			echo time();
		}
		
		function internal_echo($cmd_args)
		{
			$s = $cmd_args[1];
			if (is_string($s))
			{
				$l = strlen($s);
				if ($l >= 2 && $s[0] = '"' && $s[$l - 1] = '"') $s = substr($s, 1, -1);
				else if ($l >= 2 && $s[0] = "'" && $s[$l - 1] = "'") $s = substr($s, 1, -1);
			}
		
			echo $s;
		}
		
		function ob_callback($buffer)
		{
			$this->buf[] = $buffer;
			return '';
		}
		
		function unsafePrompt()
		{
			//-----------------------------------------------------------------------
			//  read input
			$cmd = '';
			if (isset($_REQUEST['lines']))
			{
				if (is_array($_REQUEST['lines']))
				{
					$this->lines = $_REQUEST['lines'];
				}
			}
			
			if (isset($_REQUEST['cmd']))
			{
				$cmd = $_REQUEST['cmd'];
				$this->lines[] = '>'.$cmd;
			}
			
			//-----------------------------------------------------------------------
			//  parse & execute command
			//$v = preg_split('/[\t\s]+/', $cmd);
			$v = '';
			preg_match_all('/(?<=^|[\t\s,])(?:([\'"]).*?\1|[^\s,\'"]+)(?=[\s,]|$)/', $cmd, $v);
			if (is_array($v))
			{
				$commands = Array();
			
				$cmd_args = Array();
				$next_cmd = Array();
				for ($i = 0; $i < count($v[0]); $i++)
				{
					switch ($v[0][$i])
					{
						case '>':
							$commands[] = $cmd_args;
							$cmd_args = Array();
							if (count($next_cmd) > 0)
							{
								$commands[] = $next_cmd;
								$next_cmd = Array();
							}
							break;
						case '<':
							$next_cmd = $cmd_args;
							$cmd_args = Array();
							break;
						default:
							$cmd_args[] = $v[0][$i];
						break;
					}
				}
				$commands[] = $cmd_args;
			
				fclose(STDIN);
				for ($k = 0; $k < count($commands); $k++)
				{
					$cmd_args = $commands[$k];
					
					if (count($cmd_args) == 0) $cmd_args = Array('');
					
					$STDIN = fopen('php://memory', 'r+');
					foreach ($buf as $val) fwrite($STDIN, $val);
					rewind($STDIN);
					$buf = Array();
			
					ob_start(array($this, 'ob_callback'));
					if (isset($this->internals[$cmd_args[0]]))
					{
						$code = $this->internals[$cmd_args[0]]['code'];
						if (method_exists($this, $code)) $this->$code($cmd_args);
					}
					else
					{
						//  this will search for a script and run it
						$filename = $cmd_args[0].'.php';
						if (file_exists($filename))
						{
							require_once $filename;
						}
						else
						{
							if ($k == count($commands) - 1)
							{
								//  we found that filename is not a script and that
								//  we are at the end of the foreach... then it must be an output file
				
								//$s = fread($STDIN);
								//rewind($STDIN);
								//echo $s;
								file_put_contents($cmd_args[0], $STDIN);
							}
							else echo $cmd_args[0].': command not found...';
						}
					}
					ob_end_flush();
					fclose($STDIN);
				}
			
				//  output buf to lines[]
				$this->lines = array_merge($this->lines, $this->buf);
			}
			
			//-----------------------------------------------------------------------
			//  write output
			
			echo '<body OnLoad="document.cmd.cmd.focus();">';
			echo '	<form name="cmd" method="post">';
			
			for($i = 0; $i < count($this->lines); $i++)
			{
				$line = $this->lines[$i];
				echo '<input type="hidden" name="lines['.$i.']" value="'.$line.'"></input>';
				echo '<span>'.$line.'</span></br>'."\n";
			}
			
			echo '		<div style="background-color: silver;">';
			echo '			<span>&gt;</span>';
			echo '			<input type="text" name="cmd" style="background-color: silver; border-style: none; width: 90%"></input>';
			echo '			<input type="submit" style="position: absolute; left: -100px; width: 1px; height: 1px;"></input>';
			echo '		</div>';
			echo '	</form>';
			echo '</body>';
		}		
	}
	
	if (!defined('QOS_MOD_BOOTSTRAP'))
	{
		//  the workflow passed over bootstrap, therefore attempt to load it 
		if (file_exists('bootstrap.php')) require_once 'bootstrap.php';
		else TQuarkCommand::instance()->unsafePrompt();
	}

	if (QOS_EXITCODE != QOS_ERR_OK) 
	{
		TQuarkCommand::instance()->unsafePrompt();
	}

	
	/*
	//  if there is a quarkOS bootstrap run it
	if (!defined('QOS') && file_exists('bootstrap.php')) 
	{
		require_once 'bootstrap.php';
		die;
	}
*/
	
?>