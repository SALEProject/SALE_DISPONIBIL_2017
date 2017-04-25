<?php

if (!defined('Q_UTILS_SYSINFO_Q'))
{
	define('Q_UTILS_SYSINFO_Q', true);
	
	class TSysInfo extends TApplication
	{
		function main()
		{
			$frm = $this->CreateForm('Tfrm_SysInfoView');
		}
	}
	
	
	class Tfrm_SysInfoView extends TForm
	{
		function OnLoad()
		{
			
		}
	}
	
}

?>