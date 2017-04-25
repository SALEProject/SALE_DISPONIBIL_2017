<?php

	
	class Tfrm_ContractNames extends TForm
	{
		var $ID_ContractName;
		
		function OnLoad()
		{
			$this->dg_ContractNames->Columns = array(
				array('Caption' => 'Name', 'DataType' => 'string', 'DataField' => 'Name'),
				array('Caption' => 'Edit', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Edit', 'OnClick' => 'btn_EditContractNameOnClick'),
				array('Caption' => 'Delete', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Delete', 'OnClick' => 'btn_DeleteContractNameOnClick'),
						
				//array('Caption' => 'Test 3', 'DataType' => 'string', 'DataField' => 'test3')
			);
			$this->refreshData();
		}
		
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getContractNames', array('Arguments' => array('none' => '')));
			
			$this->dg_ContractNames->DataSet = $ds;	
		}
		
		function btn_NewContractNameOnClick()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$frm = $context->application->CreateForm('frm_ContractNameDLG.xml');
			if ($frm != null) $frm->newEntry();
		}
		
		function btn_DeleteContractNameOnClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);
			$this->ID_ContractName = $varvalue;
			TQuark::instance()->MessageDlg('Are you sure you want to delete the item?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'btn_DeleteContractNameOnConfirmation');
		}
		
		function btn_DeleteContractNameOnConfirmation($sender, $varName, $varValue)
		{
			if ($varValue != 'mrYes') return;
			
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$db = $context->application->Writer;
			$result = $db->execute('REMIT', 'deleteContractName', array('Arguments' => array('ID_ContractName' => $this->ID_ContractName)));
			$this->refreshData();
		}
		
		function btn_EditContractNameOnClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_ContractNameDLG.xml');
			if ($frm != null) $frm->editEntry($varvalue);	
		}
		
		function generateThumbHTML(){
			$html = '<img src="applications/remit/images/contractNames.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
	}