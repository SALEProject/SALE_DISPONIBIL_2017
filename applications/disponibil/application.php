<?php

	require_once 'applications/disponibil_data/BRMDataClient.php';
	require_once 'applications/disponibil_data/BRMWebService.php';
	require_once 'applications/disponibil_data/syslog.php';
	require_once 'applications/disponibil_data/alerts.php';
	require_once 'applications/disponibil_data/messages.php';
	require_once 'applications/disponibil_data/assets.php';
	require_once 'applications/disponibil_data/transactions.php';
	require_once 'applications/disponibil_data/translations.php';
	
	class TDisponibilApp extends TApplication
	{		
		var $webservice = null;
		
		var $handle = null;
		
		var $Syslog = null;
		protected $last_id = 0;
		protected $last_date = 0;
		protected $timerCount = 0;
		var $Alerts = null;
		var $Messages = null;
		var $Assets = null;
		var $Transactions = null;
		var $Translations = null;
		
		function main()
		{
			$this->webservice = new TBRMWebService($this->contextID);
			
			$this->checkLogin(true);
			//$this->CreateForms();
		}
		
		function OnTimer()
		{
			if (!isset($this->Syslog) || !isset($this->Alerts) || !isset($this->Messages) || 
				!isset($this->Assets) || !isset($this->Transactions) || !isset($this->Translations)) return;
			$this->Assets->logOperations = true;
			TSysLog::setInstance($this->Syslog, $this->contextID);
			TAlerts::setInstance($this->Alerts, $this->contextID);
			TMessages::setInstance($this->Messages, $this->contextID);
			TAssets::setInstance($this->Assets, $this->contextID);
			TTransactions::setInstance($this->Transactions, $this->contextID);
			TTranslations::setInstance($this->Translations, $this->contextID);
						
			$events = array();
			
			$t0 = microtime(true);			
			$this->Syslog->checkRevisionNumber();
			$this->Syslog->buildLatest();
			
			$this->Syslog->setPath('.');
			if ($this->Syslog->first())
			{
				$this->Syslog->locate($this->last_id);
				$maxdate = $this->last_date;
				
				do 
				{
					$id = (int) $this->Syslog->getName();
					if ($id > $this->last_id)
					{
						$this->Syslog->open();
						$evdate = $this->Syslog->get('Date');
						if ($evdate >= $this->last_date)
						{
							$event = array();
							$event['Date'] = $evdate;
							$event['Resource'] 			= $this->Syslog->get('Resource');
							$event['EventType'] 		= $this->Syslog->get('EventType');
							$event['ID_Resource'] 		= $this->Syslog->get('ID_Resource');
							$event['ID_LinkedResource'] = $this->Syslog->get('ID_LinkedResource');
							$events[$id] = $event;
							
							if ($evdate > $maxdate) $maxdate = $evdate;
						}
						$this->Syslog->close();
						$this->last_id = $id;
					}
				} while ($this->Syslog->next());
				
				$this->last_date = $maxdate;
			}
			
			//TQuark::instance()->browserAlert(var_export($events, true));
			foreach ($events as $id => $event)
			{
				switch ($event['Resource'])
				{
					case 'Messages':
						$frm = TQuark::instance()->getForm('frm_ChatView');
						if ($frm != null) $frm->OnAppTimer($event);
						break;
					case 'Assets':
						$frm = TQuark::instance()->getForm('frm_AssetsView');
						if ($frm != null) $frm->OnAppTimer($event);
						break;
					case 'AssetSessions':
						$frm = TQuark::instance()->getForm('frm_AssetsView');
						if ($frm != null) $frm->OnAppTimer($event);
						
						$frm = TQuark::instance()->getForm('frm_AssetDetails');
						if ($frm != null) $frm->OnAppTimer($event);
						break;
					case 'Orders':
						$frm = TQuark::instance()->getForm('frm_AssetOrders');
						if ($frm != null) $frm->OnAppTimer($event);
						
						$frm = TQuark::instance()->getForm('frm_AssetDetails');
						if ($frm != null) $frm->OnAppTimer($event);
						break;
					case 'DeltaT1':
						$frm = TQuark::instance()->getForm('frm_AssetDetails');
						if ($frm != null) $frm->OnAppTimer($event);
						break;
					case 'Transactions':
						$frm = TQuark::instance()->getForm('frm_TransactionsView');
						if ($frm != null) $frm->OnAppTimer($event);
						break;
				}
				
				$t1 = microtime(true);
				$frm = TQuark::instance()->getForm('frm_AppDebug');
				if ($frm != null) $frm->OnAppTimer($event, ($t1 - $t0) * 1000);
			}
			
			//  at the end of all other events, we have to synch clocks
			if ($this->timerCount % 30 == 0) //  every 30 seconds or so
			{
				$this->timerCount = 0;
				
				$ds_time = $this->webservice->Reader->select('Events', 'getDBTime', array('Arguments' => array('null' => null)));
				if (isset($ds_time) && $ds_time instanceof  TDataSet)
				{
					if ($ds_time->RowsCount > 0)
					{
						$a = explode('T', $ds_time->Rows[0]['DBTime']);
						$time = $a[1];
						//TQuark::instance()->browserAlert($time);
						
						$frm = TQuark::instance()->desktop;
						if ($frm != null) $frm->SynchClock($time);
						
						$frm = TQuark::instance()->getForm('frm_AssetDetails');
						if ($frm != null) $frm->SynchClock($time);
					}
				}
			}
			$this->timerCount++;
		}
		
		function checkLogin($reset = false)
		{
			if ($reset)
			{
				unset($this->Syslog);
				unset($this->Alerts);
				unset($this->Messages);
				unset($this->Assets);
				unset($this->Transactions);
				unset($this->Translations);
				
				$this->Syslog = null;
				$this->Alerts = null;
				$this->Messages = null;
				$this->Assets = null;
				$this->Transactions = null;
				$this->Translations = null;
				
				TSysLog::setInstance(null, $this->contextID);
				TAlerts::setInstance(null, $this->contextID);
				TMessages::setInstance(null, $this->contextID);
				TAssets::setInstance(null, $this->contextID);
				TTransactions::setInstance(null, $this->contextID);
				TTranslations::setInstance(null, $this->contextID);
				
				$this->last_id = 0;
				$this->webservice->user = null;
				
				$frm = TQuark::instance()->firstForm();
				while ($frm != null)
				{
					$frm->close();
					$frm = TQuark::instance()->firstForm();
				}
			}
			
			if ($this->webservice->user == null) 
			{
				$frm = $this->CreateForm('frm_LoginDLG.xml');
				if ($frm != null) $frm->showModal();
			}
		}
		
		function CreateForms()
		{
			//  get server DB time to skip non relevant events
			$ds_time = $this->webservice->Reader->select('Events', 'getDBTime', array('Arguments' => array('null' => null)));
			//file_put_contents('cache/date', var_export($ds_time, true));
			if (isset($ds_time) && $ds_time instanceof TDataSet)
			{
				//file_put_contents('cache/date', 'something');
				if ($ds_time->RowsCount > 0) 
				{
					$this->last_date = $ds_time->Rows[0]["DBTime"];
					//file_put_contents('cache/date', $this->last_date);
				}
			}
			
			$this->Syslog = new TSysLog();
			$this->Alerts = new TAlerts();
			$this->Messages = new TMessages();
			$this->Assets = new TAssets();
			$this->Transactions = new TTransactions();
			$this->Translations = new TTranslations();
			
			$frm_AppDebug = $this->CreateForm('frm_AppDebug.xml');
			$frm_AppDebug->show();
			
			$frm_AssetsView = $this->CreateForm('frm_AssetsView.xml');
			$frm_AssetsView->show();
			
			$frm_AssetOrders = $this->CreateForm('frm_AssetOrders.xml');
			$frm_AssetOrders->show();
			
			$frm_AssetDetails = $this->CreateForm('frm_AssetDetails.xml');
			$frm_AssetDetails->show();
			
			$frm_ChatView = $this->CreateForm('frm_ChatView.xml');
			$frm_ChatView->show();

			$frm_TransactionsView = $this->CreateForm('frm_TransactionsView.xml');
			$frm_TransactionsView->show();
			
			/*switch ((bool) $this->user['isAdministrator'])
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
			}*/
		}
		
		function processMessage($msg)
		{
			if ($msg == 'logout') $this->checkLogin(true);
		}
		
		function checkTerminate()
		{
			
		}
	}

?>
