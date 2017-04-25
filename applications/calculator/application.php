<?php

	class TCalculatorApp extends TApplication
	{
		function main()
		{
			$frm = $this->CreateForm('frm_Calculator.xml');
			if ($frm != null) $frm->show();
		}
	}

?>