<?php

	
	class Tfrm_NonStandardContractReports extends TForm
	{
		var $timerCount = 0;
		var $ID_Table2Report;
		var $filterInput = '';
		
		function OnLoad()
		{
			$this->dg_NonStandardContractReports->Columns = array(
				array('Caption' => 'Download', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID_Table2Report', 'Text' => 'download', 'OnClick' => 'OnDetailsClick'),	
				array('Caption' => 'Date', 'DataType' => 'datetime', 'DataField' => 'Date'),
				array('Caption' => 'Submit', 'DataType' => 'hyperlink', 'DataField' => 'Submit', 'KeyField' => 'ID_Table2Report', 'Text' => '', 'OnClick' => 'OnSubmitClick'),
				array('Caption' => 'Submit Timestamp', 'DataType' => 'datetime', 'DataField' => 'SubmitTimestamp'),
				array('Caption' => 'Status', 'DataType' => 'string', 'DataField' => 'Status'),
				array('Caption' => 'Processed', 'DataType' => 'boolean', 'DataField' => 'isProcessed'),
				array('Caption' => 'Error', 'DataType' => 'boolean', 'DataField' => 'hasError'),
				array('Caption' => 'Log', 'DataType' => 'hyperlink', 'DataField' => 'ProcessLog', 'KeyField' => 'ID', 'Text' => '', 'OnClick' => 'OnLogClick'),
				array('Caption' => 'Download XML', 'DataType' => 'hyperlink', 'DataField' => 'OutputDataName', 'KeyField' => 'ID', 'Text' => 'file.xml', 'OnClick' => 'OnDownloadClick'),
				array('Caption' => 'edit', 'DataType' => 'hyperlink', 'DataField' => 'Edit', 'KeyField' => 'ID_Table2Report', 'Text' => '', 'OnClick' => 'btn_EditNonStandardContractReportOnClick')				
			);
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			if ($context->application->user["isAdministrator"]) $this->dg_NonStandardContractReports->Columns[] = array('Caption' => 'Receipt', 'DataType' => 'hyperlink', 'DataField' => 'ReceiptDataName', 'KeyField' => 'ID', 'Text' => 'receipt.xml', 'OnClick' => 'OnReceiptClick');
				
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

            $ID_Agency = 0;
            if ($context->application->user["isAdministrator"]) $ID_Agency = -1;
            else if (isset($context->application->user["ID_Agency"])) $ID_Agency = $context->application->user["ID_Agency"];
            if ($ID_Agency == null) $ID_Agency = 0;

            $db = $context->application->Reader;
            $ds = $db->select('REMIT', 'getDataSourceHistoryTable2Reports', array('Arguments' => array('ID_Agency' => $ID_Agency, 'QueryKeyword' => $this->filterInput)));
			
            //$this->dg_StorageReports->DivRows = $ds->RowsCount;
            
            $this->dg_NonStandardContractReports->DataSet = $ds;
		}
		
		function OnDetailsClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_StorageReportDetailsDLG.xml');
			if ($frm != null) $frm->showOrderDetails($varvalue);
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
		
		function btn_NewNonStandardContractReportOnClick()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$frm = $context->application->CreateForm('frm_NonStandardContractReportDLG.xml');
			if ($frm != null) $frm->newEntry();
		}
		
		function btn_EditNonStandardContractReportOnClick($sender, $varname, $varvalue)
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_NonStandardContractReportDLG.xml');
			if ($frm != null) $frm->editEntry($varvalue);	
		}
		
		/*function btn_ErrorNonStandardContractReportOnClick($sender, $varname, $varvalue)
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_NonStandardContractReportDLG.xml');
			if ($frm != null) $frm->errorEntry($varvalue);	
		}
				
		function btn_CancelNonStandardContractReportOnClick($sender, $varname, $varvalue)
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_NonStandardContractReportDLG.xml');
			if ($frm != null) $frm->cancelEntry($varvalue);	
		}
		
		function btn_DeleteNonStandardContractReportOnClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_NonStandardContractReportDeleteDLG.xml');
			if ($frm != null) $frm->deleteEntry($varvalue);
		}*/
		
		function OnSubmitClick($sender, $varName, $varValue)
		{
			$this->ID_Table2Report = $varValue;
			TQuark::instance()->MessageDlg('Are you sure you want to submit the report?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'OnSubmitConfirmation');
		}

		function OnSubmitConfirmation($sender, $varName, $varValue)
		{
			if ($varValue != 'mrYes') return;

			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Writer;
			$result = $db->execute('REMIT', 'submitTable2Report', array('Arguments' => array('ID_Table2Report' => $this->ID_Table2Report)));
			$this->refreshData();
		}
		
		function OnFilter($sender, $varName, $varValue)
		{
			// 	TQuark::instance()->browserAlert($varValue);
			$this->filterInput = $varValue;
			//TQuark::instance()->browserUpdate('filtered_rows', $this->FDataSet->RowsCount);
			$this->refreshData();
		}
		
		function generateThumbHTML(){
			$html = '<img src="applications/remit/images/nonStandardContracts.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
	}