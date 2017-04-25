<?php

	class TMinesweeperApp extends TApplication
	{
		function main()
		{
			$frm = $this->CreateForm('frm_Minesweeper.xml');
			if ($frm != null) $frm->show();
		}
	}

?>