<?php

	class TLoginApp extends TApplication
	{
		function main()
		{
			$frm = $this->CreateForm('frm_MainLogin.xml');
			if ($frm != null) $frm->showModal();
		}
	}

?>