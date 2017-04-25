<?php

	require_once 'BRMDataClient.php';

	class TRemitApp extends TApplication
	{
		//var $WSURL = 'http://10.0.0.100/Apollo';
		//var $WSURL = 'http://10.0.0.100/BRMDataReader';
		var $WSURL = 'http://192.168.21.107:51368';
		//var $WSURL = 'http://bitnova.ro/brm';
		
		var $LoginURL = '%s/BRMLogin.svc';
		var $ReaderURL = '%s/BRMRead.svc';
		var $WriterURL = '%s/BRMWrite.svc';
		var $Login;
		var $Reader;
		var $Writer;
		
		var $user = null;
		
		function main()
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
			
			$this->checkLogin(true);
		}
		
		function checkLogin($reset = false)
		{
			if ($reset)
			{
				$this->user = null;
				$frm = TQuark::instance()->firstForm();
				while ($frm != null)
				{
					$frm->close();
					$frm = TQuark::instance()->firstForm();
				}
			}
			
			if ($this->user == null) 
			{
				$frm = $this->CreateForm('frm_LoginDLG.xml');
				if ($frm != null) $frm->showModal();
			}
		}
		
		function CreateForms()
		{
			switch ((bool) $this->user['isAdministrator'])
			{
				case false:
					$frm_OrdersHistory = $this->CreateForm('frm_OrdersHistory.xml');
					$frm_NonStandardContractReports = $this->CreateForm('frm_NonStandardContracts.xml');
					$frm_NonStandardContractReports = $this->CreateForm('frm_NonStandardContractReports.xml');
					$frm_StorageReports = $this->CreateForm('frm_StorageReports.xml');
						
					if ($frm_OrdersHistory != null) $frm_OrdersHistory->show();
					break;
				case true:
					$frm_DataSources = $this->CreateForm('frm_DataSources.xml');
					$frm_Participants = $this->CreateForm('frm_Participants.xml');
					$frm_XLSHistory = $this->CreateForm('frm_XLSHistory.xml');
					$frm_OrdersHistory = $this->CreateForm('frm_OrdersHistory.xml');
					
					$frm_NonStandardContracts = $this->CreateForm('frm_NonStandardContracts.xml');
					$frm_NonStandardContractReports = $this->CreateForm('frm_NonStandardContractReports.xml');
					
					$frm_StorageReports = $this->CreateForm('frm_StorageReports.xml');
					
					$frm_ContractNames = $this->CreateForm('frm_ContractNames.xml');
					$frm_ContractTypes = $this->CreateForm('frm_ContractTypes.xml');
					//$frm_MeasuringUnits = $this->CreateForm('frm_MeasuringUnits.xml');
					$frm_Currencies = $this->CreateForm('frm_Currencies.xml');
					$frm_LoadTypes = $this->CreateForm('frm_LoadTypes.xml');						
						
					if ($frm_XLSHistory != null) $frm_XLSHistory->show();
					break;
			}
		}
		
		function processMessage($msg)
		{
			if ($msg == 'logout') $this->checkLogin(true);
		}
	}

?>
