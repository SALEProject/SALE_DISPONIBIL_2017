<?php

	class Tfrm_MainLogin extends TForm
	{
		function btn_Login_OnClick()
		{
			TQuark::instance()->addAjaxStack('', 'alert', 'press');
		}
	}

?>