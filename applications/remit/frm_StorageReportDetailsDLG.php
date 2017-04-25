<?php 


	class Tfrm_StorageReportDetailsDLG extends TForm
	{
		var $ID_StorageReport = 0;
		var $ID_DataSourceHistory = 0;
		
		function newEntry()
		{
			$this->showModal();
		}
		
		function showOrderDetails($ID_StorageReport)
		{
			$this->ID_StorageReport = $ID_StorageReport;
			$this->refreshData();
			$this->showModal();
		}
		
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getNGStorageReportDetails', array('Arguments' => array('ID_StorageReport' => $this->ID_StorageReport)));
				
			if ($ds == null || ($ds != null && $ds->RowsCount == 0))
			{
				TQuark::instance()->browserAlert($this->ID_StorageReport);
				TQuark::instance()->browserAlert('Error while retrieving details');
				$this->close();
				return;
			}
			$row = $ds->Rows[0];
			
			$this->lbl_Date->Caption = $row['Date'];
			$this->lbl_SubmitTimestamp->Caption = $row['SubmitTimestamp'];
			if ($row['isSubmitted']) $this->lbl_isSubmitted->Caption = 'yes'; else $this->lbl_isSubmitted->Caption = 'no';
						
			$this->ID_DataSourceHistory = $row['ID_DataSourceHistory'];

			if ($row['hasOutput']) $this->btn_DownloadXML->Visible = true;
			else $this->btn_DownloadXML->Visible = false;
			
			if ($row['hasReceipt']) $this->btn_DownloadReceipt->Visible = true;
			else $this->btn_DownloadReceipt->Visible = false;
		}
		
		function btn_DownloadXMLOnClick()
		{
			//TQuark::instance()->browserAlert($this->ID_REMIT_DataSourceHistory);
			if ($this->ID_DataSourceHistory <= 0) return;
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getDataSourceOutputData', array('Arguments' => array('ID_DataSourceHistory' => $this->ID_DataSourceHistory)));
				
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
			if ($this->ID_DataSourceHistory <= 0) return;
				
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getDataSourceReceiptData', array('Arguments' => array('ID_DataSourceHistory' => $this->ID_DataSourceHistory)));
			
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