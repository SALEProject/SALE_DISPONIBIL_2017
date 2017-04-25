<?php

	class TTicTacToeApp extends TApplication
	{
		function main()
		{
			$frm = $this->CreateForm('frm_TicTacToe.xml');
			if ($frm != null) $frm->show();
		}
	}

?>