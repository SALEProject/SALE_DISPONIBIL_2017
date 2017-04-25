<?php 


	class Tfrm_XLSHistory extends TForm
	{
		var $timerCount = 0;
		var $filterInput = '';
		
		function OnLoad()
		{
			$this->dg_DataSourceHistory->Columns = array(
				array('Caption' => 'Date', 'DataType' => 'datetime', 'DataField' => 'Date'),
				array('Caption' => 'FileName', 'DataType' => 'string', 'DataField' => 'InputDataName'),
				array('Caption' => 'Status', 'DataType' => 'string', 'DataField' => 'Status'),
				array('Caption' => 'Processed', 'DataType' => 'boolean', 'DataField' => 'isProcessed'),
				array('Caption' => 'Error', 'DataType' => 'boolean', 'DataField' => 'hasError'),
				array('Caption' => 'Log', 'DataType' => 'hyperlink', 'DataField' => 'ProcessLog', 'KeyField' => 'ID', 'Text' => '', 'OnClick' => 'OnLogClick'),
				array('Caption' => 'Download XML', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'file.xml', 'OnClick' => 'OnDownloadClick'),
				array('Caption' => 'Receipt', 'DataType' => 'hyperlink', 'DataField' => 'ReceiptDataName', 'KeyField' => 'ID', 'Text' => 'receipt.xml', 'OnClick' => 'OnReceiptClick')
			);
			$this->refreshData();
			TQuark::instance()->registerTimer($this, 'OnTimer', 1000);			
		}
		
		function OnTimer()
		{
			$this->timerCount++;
			if (!$this->Visible || $this->timerCount % 2 == 1) return;
			$this->refreshData();
		}
		
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getDataSourceHistoryXLS', array('Arguments' => array('QueryKeyword' => $this->filterInput)));
				
			$this->dg_DataSourceHistory->DataSet = $ds;
		}
		
		function dg_DataSourceHistoryOnFilter($sender, $varName, $varValue)
		{
			$this->filterInput = $varValue;
			$this->refreshData();
		}
		
		function OnLogClick($sender, $varname, $varvalue)
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getDataSourceProcessLog', array('Arguments' => array('ID_DataSourceHistory' => $varvalue)));	
			
			if ($ds == null) TQuark::instance()->browserAlert('error retrieving process log');
			else 
			{
				$frm = $context->application->CreateForm('frm_ProcessLogDLG.xml');
				if ($ds->RowsCount > 0) $frm->ed_ProcessLog->Text = $ds->Rows[0]["ProcessLog"];
				$frm->showModal();
			}
		}
		
		function OnDownloadClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getDataSourceOutputData', array('Arguments' => array('ID_DataSourceHistory' => $varvalue)));
				
			if ($ds == null) TQuark::instance()->browserAlert('error retrieving processed file');
			else
			{
				if ($ds->RowsCount > 0)
				{
					$b64 = $ds->Rows[0]["OutputData"];
					$str = base64_decode($b64);
					
					TQuark::instance()->browserDownload($ds->Rows[0]["OutputDataName"], $str);					
				}
			}
		}
		
		function OnReceiptClick($sender, $varname, $varvalue)
		{
			$context = $this->getContext();
			$frm = $context->application->CreateForm('frm_UploadReceiptDLG.xml');
			if ($frm != null) $frm->showReceiptDetails($varvalue);	
		}
		
		function btn_UploadXLSOnClick()
		{
			$context = $this->getContext();
			$frm = $context->application->CreateForm('frm_UploadXLSDLG.xml');
			if ($frm != null) $frm->showModal();							
		}
		
		function generateThumbHTML()
		{
			$html = '<img src="applications/remit/images/upload.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
	}
	
?>
