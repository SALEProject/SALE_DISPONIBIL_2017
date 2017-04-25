<?php 


	class Tfrm_OrderDetailsDLG extends TForm
	{
		var $ID_Order = 0;
		var $ID_REMIT_DataSourceHistory = 0;
		
		function newEntry()
		{
			$this->showModal();
		}
		
		function showOrderDetails($ID_Order)
		{
			$this->ID_Order = $ID_Order;
			$this->refreshData();
			$this->showModal();
		}
		
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getOrderDetails', array('Arguments' => array('ID_Order' => $this->ID_Order)));
				
			if ($ds == null) return;			
			if ($ds->RowsCount == 0) return;
			$row = $ds->Rows[0];
			
			$this->lbl_ContractID->Caption = $row['ContractID'];
			$this->lbl_ContractName->Caption = $row['ContractName'];
			
			$this->lbl_OrderID->Caption = $row['OrderID'];
			$this->lbl_Quantity->Caption = sprintf('%4.3f (%s)', round($row['NotionalQuantity'], 3), $row['NotionalQuantityMU']);
			$this->lbl_Price->Caption = sprintf('%4.2f (%s)', round($row['Price'], 2), $row['Currency']);
			$this->lbl_StartDeliveryDate->Caption = date('Y-m-d', strtotime($row['StartDeliveryDate']));
			$this->lbl_EndDeliveryDate->Caption = date('Y-m-d', strtotime($row['EndDeliveryDate']));
			
			$this->ID_REMIT_DataSourceHistory = $row['ID_REMIT_DataSourceHistory'];
			
			if ($row['hasReceipt']) $this->btn_DownloadReceipt->Visible = true;
			else $this->btn_DownloadReceipt->Visible = false;
		}
		
		function btn_DownloadXMLOnClick()
		{
			//TQuark::instance()->browserAlert($this->ID_REMIT_DataSourceHistory);
			if ($this->ID_REMIT_DataSourceHistory <= 0) return;
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getDataSourceOutputData', array('Arguments' => array('ID_DataSourceHistory' => $this->ID_REMIT_DataSourceHistory)));
				
			if ($ds == null) TQuark::instance()->browserAlert('error retrieving processed file');
			else
			{
				if ($ds->RowsCount > 0)
				{
					$b64 = $ds->Rows[0]["OutputData"];
					$str = base64_decode($b64);
					if ($str == '') TQuark::instance()->browserAlert('no file has been generated');
					else TQuark::instance()->browserDownload($ds->Rows[0]["OutputDataName"], $str);
				}
			}				
		}

		function btn_DownloadReceiptOnClick()
		{
			//TQuark::instance()->browserAlert($this->ID_REMIT_DataSourceHistory);
			if ($this->ID_REMIT_DataSourceHistory <= 0) return;
				
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getDataSourceReceiptData', array('Arguments' => array('ID_DataSourceHistory' => $this->ID_REMIT_DataSourceHistory)));
			
			if ($ds == null) TQuark::instance()->browserAlert('error retrieving receipt file');
			else
			{
				if ($ds->RowsCount > 0)
				{
					$b64 = $ds->Rows[0]["ReceiptData"];
					$str = base64_decode($b64);
					if ($str == '') TQuark::instance()->browserAlert('no file has been generated');
					else TQuark::instance()->browserDownload($ds->Rows[0]["ReceiptDataName"], $str);
				}
			}
		}
		
		function btn_CloseOnClick()
		{
			$this->close();
		}
	}
	
?>