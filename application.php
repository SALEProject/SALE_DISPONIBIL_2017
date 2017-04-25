<?php

if (!defined('QOS_APPLICATION_QOS'))
{
	define('QOS_APPLICATION_QOS', true);

	class TApplication
	{
		var $ApplicationDirectory = '';
		var $WorkingDirectory = '';
		var $contextID = 0;
		
		function __construct()
		{
			$reflector = new ReflectionClass(get_class($this));
			$str_derived = dirname($reflector->getFileName());
			$str_base = dirname($_SERVER['SCRIPT_FILENAME']);
			$s = $str_derived;
			if (strpos($str_derived, $str_base) >= 0) $s = substr($str_derived, strlen($str_base));
			$this->ApplicationDirectory = $s;
		}
		
		function CreateForm($ref)
		{
			//  check if quark GUI is loaded
			if (!class_exists('TQuark')) return null;
			
			//  check if $ref is a class reference
			if (class_exists($ref)) return TQuark::instance()->loadForm($ref, $this->contextID);
			else 
			{
				//  try to create the form from file
				$filename = $ref;
				if (!strpos($filename, $this->WorkingDirectory))
				{
					$filename = $this->WorkingDirectory.DIRECTORY_SEPARATOR.$filename;
				}
				
				if (file_exists($filename)) return TQuark::instance()->loadForm($filename, $this->contextID);
			}
			
			return null;
		}
		
		function main()
		{
			
		}
		
		function processMessage($msg)
		{
			
		}
		
		function terminate()
		{
			//  search for the forms in the current context and close them
			if (class_exists('TQuark'))
			{
				if (TQuark::instance()->debugJS) TQuark::instance()->traceCallStack($this, 'terminate');
				
				$frm = TQuark::instance()->firstForm();
				while ($frm != null)
				{
					if ($frm->contextID == $this->contextID) 
					{
						$frm->close();
						$frm = TQuark::instance()->firstForm();
					}
					else $frm = TQuark::instance()->nextForm();
				}
			}
			
			TQuarkOS::instance()->removeContext($this->contextID);
		}
		
		function checkTerminate()
		{
			if (class_exists('TQuark') && TQuark::instance()->debugJS) TQuark::instance()->traceCallStack($this, 'checkTerminate');
			
			$found = false;
			if (class_exists('TQuark'))
			{
				$frm = TQuark::instance()->firstForm();
				while ($frm != null && !$found)
				{
					if ($frm->contextID == $this->contextID) $found = true;
					$frm = TQuark::instance()->nextForm();
				}
			}
			
			if (!$found) $this->terminate();
		}
	}

}

?>