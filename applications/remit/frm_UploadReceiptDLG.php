<?php 


	class Tfrm_UploadReceiptDLG extends TForm
	{
		var $ID_DataSourceHistory = 0;
		
		function showReceiptDetails($ID_DataSourceHistory)
		{
			$this->ID_DataSourceHistory = $ID_DataSourceHistory;
			$this->refreshData();
			$this->showModal();
		}
		
		function refreshData()
		{
			$this->lbl_ReceiptStatus->Caption = 'status unknown';
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getDataSourceReceiptData', array('Arguments' => array('ID_DataSourceHistory' => $this->ID_DataSourceHistory)));
				
			if ($ds == null) TQuark::instance()->browserAlert('error retrieving receipt data');
			else
			{
				if ($ds->RowsCount > 0) 
				{
					$ReceiptDataName = $ds->Rows[0]['ReceiptDataName'];
					$this->lbl_ReceiptStatus->Caption = $ReceiptDataName;
					if ($ReceiptDataName != 'not uploaded') $this->btn_DownloadXML->Visible = true;
					else $this->btn_DownloadXML->Visible = false;
				}
			}
		}
		
		function btn_DownloadXMLOnClick()
		{
			//TQuark::instance()->browserAlert($this->ID_DataSourceHistory);
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
		
		function SaveData()
		{
			//TQuark::instance()->browserAlert($this->ID_DataSourceHistory);
			if ($this->ID_DataSourceHistory <= 0) return;
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			//  encode file contents			
			$file = TQuark::instance()->retrieveUploadedFile();
			$FileName = $file['name'];
			$tmp_name = $file['tmp_name'];
			
			$handle = fopen($tmp_name, "rb");
			$fsize = filesize($tmp_name);
			$binary = fread($handle, $fsize);
			$FileContent = base64_encode($binary);
				
			//  send the file & refresh
			$db = $context->application->Writer;
			$objects = array('Arguments' => array('ID_DataSourceHistory' => $this->ID_DataSourceHistory, 'FileName' => $FileName, 'FileContent' => $FileContent));
			$result = $db->execute('REMIT', 'uploadReceipt', $objects);
			
			if (!$result)
			{
				$s =	'HTTP Status: '.$db->LastHTTPStatus."\n".
						'Error Code: '.$db->LastErrorCode."\n".
						'Error Message: '.$db->LastErrorMsg;
				TQuark::instance()->browserAlert($s);
			}
		
			TQuark::instance()->getForm('frm_XLSHistory')->refreshData();
		}
		
		function btn_SaveOnClick()
		{
			$this->SaveData();
			$this->close();
		}
		
		function btn_CancelOnClick()
		{
			$this->close();
		}
	}
	
?>
