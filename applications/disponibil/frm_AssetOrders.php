<?php

	require_once 'frm_OrderDLG.php';

	class Tfrm_AssetOrders extends TForm
	{
		var $ID_Asset = 0;
		var $ID_SelectedOrder = 0;
		
		function OnLoad()
		{
			$this->dg_Bid->Columns = array(
				array('Caption' => 'Cantitate', 'DataType' => 'float', 'DataField' => 'Quantity'),
				array('Caption' => 'Pret', 'DataType' => 'float', 'DataField' => 'Price'),
				array('Caption' => 'T/P', 'DataType' => 'string', 'DataField' => 'PartialFlag'),
				array('Caption' => 'Valabilitate', 'DataType' => 'DateTime', 'DataField' => 'ExpirationDate'),
				array('Caption' => '', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'M', 'OnClick' => 'OnEditOrderClick'),
				array('Caption' => '', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'A', 'OnClick' => 'OnCancelOrderClick'),
			);

			$this->dg_Ask->Columns = array(
				array('Caption' => 'Cantitate', 'DataType' => 'float', 'DataField' => 'Quantity'),
				array('Caption' => 'Pret', 'DataType' => 'float', 'DataField' => 'Price'),
				array('Caption' => 'T/P', 'DataType' => 'string', 'DataField' => 'PartialFlag'),
				array('Caption' => 'Valabilitate', 'DataType' => 'DateTime', 'DataField' => 'ExpirationDate'),
				array('Caption' => '', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'M', 'OnClick' => 'OnEditOrderClick'),
				array('Caption' => '', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'A', 'OnClick' => 'OnCancelOrderClick'),
			);
			
			$this->resetOrders();
		}
		
		function OnAppTimer($event)
		{
			//$assets = TAssets::instance($this->contextID); if (!isset($assets)) return;			
			$ID_Order = $event['ID_Resource'];
			$ID_Asset = $event['ID_LinkedResource'];
			
			if ($ID_Asset != $this->ID_Asset) return;
			$this->refreshOrders($ID_Asset);
		}
		
		function resetOrders()
		{
			
		}
		
		function refreshOrders($ID_Asset = null, $updateCache = false)
		{
			$this->ID_SelectedOrder = 0;
			$assets = TAssets::instance($this->contextID); if (!isset($assets)) return;			
			
			if (isset($ID_Asset)) $this->ID_Asset = $ID_Asset;
				
			if ($this->ID_Asset == 0) $this->resetOrders();
			else
			{
				$ds_buy = null;
				$ds_sell = null;				
				$ds = $assets->getOrderDataSets($this->ID_Asset, $ds_buy, $ds_sell, $updateCache);
				
				//TQuark::instance()->browserAlert(var_export($ds, true));
				$this->dg_Bid->DataSet = $ds_buy;
				$this->dg_Ask->DataSet = $ds_sell;
			}
		}
		
		function btn_BuyOnClick($sender)
		{
			$frm = $this->getApplication()->CreateForm('frm_OrderDLG.xml');
			if ($frm != null) $frm->buy($this->ID_Asset, 0);
		}
		
		function btn_SellOnClick($sender)
		{
			$frm = $this->getApplication()->CreateForm('frm_OrderDLG.xml');
			if ($frm != null) $frm->sell($this->ID_Asset, 0);
		}
		
		function OnEditOrderClick($sender, $varName, $varValue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varName."\n".'varvalue: '.$varValue);
			$app = $this->getApplication(); if (!isset($app)) return;
				
			$frm = $this->getApplication()->CreateForm('frm_OrderDLG.xml');
			if ($frm != null) $frm->sell($this->ID_Asset, $varValue);
		}
		
		function OnCancelOrderClick($sender, $varName, $varValue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varName."\n".'varvalue: '.$varValue);
			$app = $this->getApplication(); if (!isset($app)) return;
				
			/*$frm = $context->application->CreateForm('frm_StorageReportDetailsDLG.xml');
			if ($frm != null) $frm->showOrderDetails($varvalue);*/
			$this->ID_SelectedOrder = $varValue;
			TQuark::instance()->MessageDlg('Esti sigur ca vrei sa anulezi ordinul?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'OnCancelOrderConfirmation');
		}
		
		function OnCancelOrderConfirmation($sender, $varName, $varValue)
		{
			//TQuark::instance()->browserAlert('varname: '.$varName."\n".'varvalue: '.$varValue);
			if ($varValue != 'mrYes') return;
		
			$webservice = TBRMWebService::instance($this->contextID);
			if (!isset($webservice)) return;
		
			//TQuark::instance()->browserAlert($this->ID_SelectedOrder);
			if ($webservice->Writer->execute('Orders', 'cancelOrder', array('Arguments' => array('ID_Order' => (int) $this->ID_SelectedOrder))) === false)
			{
				TQuark::instance()->browserAlert(var_export($webservice->Writer->LastErrorMessage, true));				
			}

			//$this->refreshOrders();
		}
		
		
	}

?>