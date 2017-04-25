<?php

	require_once 'applications/disponibil_data/BRMWebService.php';

	class Tfrm_LoginDLG extends TForm
	{
		function BRMLogin()
		{
			$LoginName = $this->ed_LoginName->Text;
			$LoginPassword = $this->ed_LoginPassword->Text;
			
			return TBRMWebService::instance($this->contextID)->Login($LoginName, $LoginPassword);
		}
		
		function btn_Login_OnClick()
		{
			switch ($this->BRMLogin())
			{
				case false:
					TQuark::instance()->browserAlert('login failed');
					break;
				case true:
					//TQuark::instance()->browserAlert('login success');
					$context = $this->getContext();
					if ($context == null) return;
					if (!is_object($context)) return;
					
					$context->application->CreateForms();
					$this->close();
					break;
			}
		}
	}

?>
