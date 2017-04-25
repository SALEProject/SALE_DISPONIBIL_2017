<?php

	require_once 'BRMWebService.php';
	require_once 'BRMDataCache.php';
	
	class TAssets extends TBRMDataCache
	{
		protected static $FInstance = null;
		
		static function who()
		{
			return __CLASS__;
		}
		
		function __construct()
		{
			parent::__construct('cache/data/assets');
		}
		
		function update($operation, $object)
		{
			//file_put_contents('cache/update', var_export($object, true));
			
			$ID_Asset = $object['ID'];
			$this->setPath('.'.$ID_Asset);
			$this->traversePath(true, false);
			
			foreach ($object as $key => $value) $this->set($key, $value);
		}
		
		function checkAsset($ID_Asset)
		{
			$this->setPath('.');
			$b = $this->keyExists($ID_Asset);
				
			$this->setPath($ID_Asset);
			$this->traversePath(true, false);
			
			if (!$b)
			{
				//  get asset details from the webservice
				//file_put_contents('cache/test', 'we check asset', FILE_APPEND);
				$webservice = TBRMWebService::instance(static::$contextID);
				if ($webservice->Login())
				{
					//file_put_contents('cache/test', 'loged in', FILE_APPEND);
					$objects = array('Arguments' => array('ID_Market' => (int) 3, 'ID_Asset' => (int) $ID_Asset));			
					$ds = $webservice->Reader->select('Rings', 'getAssetsDetailed', $objects);
					
					if ($ds->RowsCount > 0)
					{
						$row = $ds->get_Rows()[0];
						foreach ($row as $key => $value)
						{
							$this->set($key, $value);
						}
					}
					
					//  flag the asset for refresh in views. add it as an internal event
					//TSysLog::instance()->update('insert', array('ID' => , 'Date' => , 'Priority' => 0, 'Resource' => 'Assets', 'ID_Resource' => $ID_Asset, 'ID_LinkedResource' => 0));
					
					//file_put_contents('cache/test', var_export($ds, true), FILE_APPEND);
				}
			}
		}
		
		function updateSession($operation, $object)
		{
			$ID_Asset = $object['ID_Asset'];
			$this->checkAsset($ID_Asset);
			
			$this->setPath('session');
			$this->set('Date', $object['Date']);
			$this->set('Status', $object['Status']);
			$this->set('TransactionsCount', $object['TransactionsCount']);
			$this->set('TotalVolume', $object['TotalVolume']);
			$this->set('TotalValue', $object['TotalValue']);
			$this->set('OpeningPrice', $object['OpeningPrice']);
			$this->set('MinPrice', $object['MinPrice']);
			$this->set('MaxPrice', $object['MaxPrice']);
			$this->set('ClosingPrice', $object['ClosingPrice']);
			$this->set('PreOpeningTime', $object['PreOpeningTime']);
			$this->set('OpeningTime', $object['OpeningTime']);
			$this->set('PreClosingTime', $object['PreClosingTime']);
			$this->set('ClosingTime', $object['ClosingTime']);
		}
		
		function updateOrder($operation, $object)
		{
			//file_put_contents('cache/object', var_export($object, true));
			
			$ID_Asset = $object['ID_Asset'];
			$this->checkAsset($ID_Asset);
			
			$this->setPath('orders.'.$object['ID']);
			$this->set('Date', $object['Date']);
			$this->set('ID_Agency', $object['ID_Agency']);
			$this->set('ID_Broker', $object['ID_Broker']);
			$this->set('ID_Client', $object['ID_Client']);
			$this->set('Direction', $object['Direction']);
			$this->set('Quantity', $object['Quantity']);
			$this->set('Price', $object['Price']);
			$this->set('PartialFlag', $object['PartialFlag']);
			$this->set('ExpirationDate', $object['ExpirationDate']);
			$this->set('isTransacted', $object['isTransacted']);
			$this->set('isSuspended', $object['isSuspended']);
			$this->set('isActive', $object['isActive']);
			$this->set('isCanceled', $object['isCanceled']);
			$this->set('isApproved', $object['isApproved']);			
		}
		
		function getOrderDataSets($ID_Asset, &$ds_buy, &$ds_sell, $updateCache = false)
		{			
			$ds_buy = new TDataSet(null);
			$ds_buy->FieldDefs = array('ID', 'Date', 'ID_Agency', 'ID_Broker', 'ID_Client', 'Direction', 'Quantity', 'Price',					
					'PartialFlag', 'ExpirationDate', 'isActive', 'isTransacted', 'isSuspended', 'isCanceled');

			$ds_sell = new TDataSet(null);
			$ds_sell->FieldDefs = array('ID', 'Date', 'ID_Agency', 'ID_Broker', 'ID_Client', 'Direction', 'Quantity', 'Price',					
					'PartialFlag', 'ExpirationDate', 'isActive', 'isTransacted', 'isSuspended', 'isCanceled');

			$this->checkAsset($ID_Asset);

			if ($updateCache)
			{
				$webservice = TBRMWebService::instance(static::$contextID);
				if ($webservice == null) break;
			
				$ds = $webservice->Reader->select('Orders', 'getOrders', array('Arguments' => array('ID_Asset' => (int) $ID_Asset, 'all' => true)));
				if ($ds != null && $ds->RowsCount > 0)
				{
					//TQuark::instance()->browserAlert(var_export($ds, true));
					
					foreach ($ds->Rows as $row)
					{
						$this->setPath('.'.$ID_Asset.'.orders.'.$row['ID']);
						$this->set('Date', $row['Date']);
						$this->set('ID_Agency', $row['ID_Agency']);
						$this->set('ID_Broker', $row['ID_Broker']);
						$this->set('ID_Client', $row['ID_Client']);
						$this->set('Direction', ($row['Direction'] == 'S' ? true : false));
						$this->set('Quantity', $row['Quantity']);
						$this->set('Price', $row['Price']);
						$this->set('PartialFlag', $row['PartialFlag']);
						$this->set('ExpirationDate', $row['ExpirationDate']);
						$this->set('isTransacted', $row['isTransacted']);
						$this->set('isSuspended', $row['isSuspended']);
						$this->set('isActive', $row['isActive']);
						$this->set('isCanceled', $row['isCanceled']);
						//$this->set('isApproved', $row['isApproved']);						
					}
				}
			}
								
			$rows_buy = array();
			$rows_sell = array();			
			$this->setPath('.'.$ID_Asset.'.orders');

			if ($this->first())
			{
				do
				{
					$row = array();
					$row['ID'] = $this->getName();
					
					$this->open();
					$row['Date'] = $this->get('Date');					
					$row['ID_Agency'] = $this->get('ID_Agency');
					$row['ID_Broker'] = $this->get('ID_Broker');
					$row['ID_Client'] = $this->get('ID_Client');
					$row['Direction'] = $this->get('Direction');
					$row['Quantity'] = $this->get('Quantity');
					$row['Price'] = $this->get('Price');
					$row['PartialFlag'] = ($this->get('PartialFlag') ? 'P' : 'T');
					$ExpirationDate = explode('T', $this->get('ExpirationDate'));
					if ($ExpirationDate[0] == date('Y-m-d')) $row['ExpirationDate'] = $ExpirationDate[1];
					else $row['ExpirationDate'] = $ExpirationDate[0];
					//$row['ExpirationDate'] = $this->get('ExpirationDate');
					$row['isActive'] = $this->get('isActive');
					$row['isTransacted'] = $this->get('isTransacted');
					$row['isSuspended'] = $this->get('isSuspended');
					$row['isCanceled'] = $this->get('isCanceled');
					$this->close();
					
					if ($row['isCanceled'] || $row['isTransacted']) continue;
					
					if ($row['isActive'] == true)
					{
						switch ($row['Direction'])
						{
							case false:
								$rows_buy[] = $row;
								//$ds_buy->addRow($row);
								break;
							case true:
								$rows_sell[] = $row;
								//$ds_sell->addRow($row);
								break;
						}
					}
				} while ($this->next());
			}
			
			usort($rows_buy, array($this, 'cmp_buy'));
			usort($rows_sell, array($this, 'cmp_sell'));
			
			foreach ($rows_buy as $row) $ds_buy->addRow($row);
			foreach ($rows_sell as $row) $ds_sell->addRow($row);
		}
			
		function cmp_buy($row1, $row2)
		{
			if ($row1['Price'] == $row2['Price']) return 0;
			return ($row1['Price'] > $row2['Price']) ? -1 : 1; 
		}
		
		function cmp_sell($row1, $row2)
		{
			if ($row1['Price'] == $row2['Price']) return 0;
			return ($row1['Price'] < $row2['Price']) ? -1 : 1; 
		}
	}

?>