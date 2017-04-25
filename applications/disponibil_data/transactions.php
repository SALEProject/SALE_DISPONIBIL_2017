<?php
	
	require_once 'BRMWebService.php';
	require_once 'BRMDataCache.php';
	
	class TTransactions extends TBRMDataCache
	{
		protected static $FInstance = null;
		
		static function who()
		{
			return __CLASS__;
		}
		
		function __construct()
		{
			parent::__construct('cache/data/transactions');
		}
		
		function update($operation, $object)
		{
			//file_put_contents('cache/update', var_export($object, true));
			
			$ID_Transaction = $object['ID'];
			$this->setPath('.'.$ID_Transaction);
			$this->traversePath(true, false);
			
			foreach ($object as $key => $value) $this->set($key, $value);
		}
		
		function getTransactions($ID_Broker, &$ds, $updateCache = false)
		{			
			$ds = new TDataSet(null);
			$ds->FieldDefs = array('ID', 'Date', 'ID_Asset', 'Asset', 'Direction', 'Quantity', 'Price', 'ID_BuyOrder', 'ID_BuyBroker', 'ID_BuyClient', 					
					'ID_SellOrder', 'ID_SellBroker', 'ID_SellClient');

			if ($updateCache)
			{
				$webservice = TBRMWebService::instance(static::$contextID);
				if ($webservice == null) break;
			
				$ds_web = $webservice->Reader->select('Transactions', 'getTransactions', array('Arguments' => array('foo' => 'bar')));
				//TQuark::instance()->browserAlert(var_export($ds_web, true));
				if ($ds_web != null && $ds_web->RowsCount > 0)
				{
					//TQuark::instance()->browserAlert(var_export($ds, true));
					
					foreach ($ds_web->Rows as $row)
					{
						$this->setPath('.'.$row['ID']);
						$this->set('Date', $row['TransactionDate']);
						$this->set('ID_Asset', $row['ID_Asset']);
						$this->set('Asset', $row['Asset']);
						$this->set('Direction', ($row['Direction'] == 'S' ? true : false));
						$this->set('Quantity', $row['Quantity']);
						$this->set('Price', $row['Price']);
						$this->set('ID_BuyOrder', $row['ID_BuyOrder']);
						$this->set('ID_BuyBroker', $row['ID_BuyBroker']);
						$this->set('ID_BuyClient', $row['ID_BuyClient']);
						$this->set('ID_SellOrder', $row['ID_SellOrder']);
						$this->set('ID_SellBroker', $row['ID_SellBroker']);
						$this->set('ID_SellClient', $row['ID_SellClient']);
					}
				}
			}
								
			$this->setPath('.');
			if ($this->first())
			{
				do
				{
					$row = array();
					$row['ID'] = $this->getName();
					
					$this->open();
					$Date = explode('T', $this->get('Date'));
					$ID_BuyClient = $this->get('ID_BuyClient');
					$ID_SellClient = $this->get('ID_SellClient');
					
					if ($Date[0] == date('Y-m-d'))
					{
						if ($Date[0] == date('Y-m-d')) $row['Date'] = $Date[1];
						else $row['Date'] = $Date[0];
						//$row['Date'] = $this->get('Date');		
						$row['ID_Asset'] = $this->get('ID_Asset');
						$row['Asset'] = $this->get('Asset');
						$row['Direction'] = ($this->get('Direction') === true ? 'V' : 'C');
						$row['Quantity'] = $this->get('Quantity');
						$row['Price'] = $this->get('Price');
						$row['ID_BuyOrder'] = $this->get('ID_BuyOrder');
						$row['ID_BuyBroker'] = $this->get('ID_BuyBroker');
						$row['ID_BuyClient'] = $ID_BuyClient;
						$row['ID_SellOrder'] = $this->get('ID_SellOrder');
						$row['ID_SellBroker'] = $this->get('ID_SellBroker');
						$row['ID_SellClient'] = $ID_SellClient;
					
						$ds->addRow($row);
					}
					$this->close();
					
				} while ($this->next());
			}			
		}
			
	}
?>