<?php

	require_once 'BRMDataClient.php';

	class TBRMWebService 
	{
		static $contextID = 0;
		static $FInstance = null;
		
		static function instance($contextID)
		{			
			if (static::$FInstance != null) return static::$FInstance;
			
			static::$contextID = $contextID;
			if ($contextID == 0)
			{
				static::$FInstance = new static();
				//static::$FInstance->Login();
			}
			else
			{
				$context = TQuark::instance()->getContext($contextID);
				if (!isset($context) || !isset($context->application)) return null;
				
				//  assume that application has a webservice property
				static::$FInstance = $context->application->webservice;
			}
			
			return static::$FInstance;
		}
		
		protected $WSURL = 'http://ip_to_webserver';
		
		protected $app_LoginName = 'appuser';
		protected $app_LoginPassword = 'appuser';
		
		protected $LoginURL = '%s/BRMLogin.svc';
		protected $ReaderURL = '%s/BRMRead.svc';
		protected $WriterURL = '%s/BRMWrite.svc';
		var $Login = null;
		var $Reader = null;
		var $Writer = null;
		var $user = null;
		var $clients = array();
		
		function __construct()
		{
			$this->LoginURL = sprintf($this->LoginURL, $this->WSURL);
			$this->ReaderURL = sprintf($this->ReaderURL, $this->WSURL);
			$this->WriterURL = sprintf($this->WriterURL, $this->WSURL);
			
			$this->Login = new TBRMDataClient(null, $this->LoginURL);
			$this->Login->setParameter('SessionId', session_id());
			$this->Login->setParameter('CurrentState', 'login');
				
			$this->Reader = new TBRMDataClient(null, $this->ReaderURL);
			$this->Reader->setParameter('SessionId', session_id());
			$this->Reader->setParameter('CurrentState', 'login');
				
			$this->Writer = new TBRMDataClient(null, $this->WriterURL);
			$this->Writer->setParameter('SessionId', session_id());
			$this->Writer->setParameter('CurrentState', 'login');
		}
		
		function Login($LoginName = '', $LoginPassword = '')
		{
			if ($this->Login == null) return false;
			if ($LoginName == '' && $LoginPassword == '' && isset($this->user)) return true;
			
			$loguid = $LoginName;
			$logpwd = $LoginPassword;
			if (static::$contextID == 0) 
			{
				$loguid = $this->app_LoginName;
				$logpwd = $this->app_LoginPassword;
			}			
			
			//TQuark::instance()->browserReportCallStack('logging as '.$loguid);
			$response = $this->Login->callMethod('login', array('Login' => array('LoginName' => $loguid, 'LoginPassword' => $logpwd, 'EntryPoint' => 'DISPONIBIL')));

			if ($response == null) return false;
			if (!is_array($response)) return false;
			if ($response['ResultType'] != 'LoginResult') return false;
			$result = $response['Result'];			
			if ((bool) $result['Success'] != true) return false;
			
			$this->user = $result['User'];
			TQuark::instance()->browserAlert(var_export($this->user, true));
			
			$this->updateClients();
			
			return true;
		}
		
		function updateClients()
		{
			if (!isset($this->user)) return;
			
			$ID_Agency = $this->user['ID_Agency'];
			$ds = $this->Reader->select('Agencies', 'getAgencyClients', array('Arguments' => array('ID_Agency' => (int) $ID_Agency)));
			if ($ds != null && $ds instanceof TDataSet) 
			{
				TQuark::instance()->browserAlert(var_export($ds, true));
				foreach ($ds->Rows as $row)
				{
					$this->clients[] = $row;
				}
			}
		}
	}


?>