<?php

	class Tfrm_LoginDLG extends TForm
	{
		function BRMLogin()
		{
			$LoginName = $this->ed_LoginName->Text;
			$LoginPassword = $this->ed_LoginPassword->Text;
			
			$context = $this->getContext();
			if ($context == null) return false;
			if (!is_object($context)) return false;
			
			$db = $context->application->Login;
			$response = $db->callMethod('login', array('Login' => array('LoginName' => $LoginName, 'LoginPassword' => $LoginPassword, 'EntryPoint' => 'BTGN')));
			
			if ($response == null) return false;
			if (!is_array($response)) return false;
			if ($response['ResultType'] != 'LoginResult') return false;
			$result = $response['Result'];			
			if ((bool) $result['Success'] != true) return false;
			
			$user = $result['User'];
			$context->application->user = $user;
			TQuark::instance()->desktop->setLoginInfo($user['LoginName'], $user['FirstName'].' '.$user['LastName'], $user['CompanyName']);
			return true;
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
