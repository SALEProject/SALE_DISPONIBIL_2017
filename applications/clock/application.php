<?php

	class TClockApp extends TApplication
	{
		function main()
		{
			$frm = $this->CreateForm('frm_Clock.xml');
			if ($frm != null) $frm->show();
		}
	}

?>