<?php

	
	class Tfrm_ContractTypes extends TForm
	{
		var $ID_ContractType;
		
		function OnLoad()
		{
			$this->dg_ContractTypes->Columns = array(
				array('Caption' => 'Code', 'DataType' => 'string', 'DataField' => 'Code'),
				array('Caption' => 'Name', 'DataType' => 'string', 'DataField' => 'Name'),
				array('Caption' => 'Table 1', 'DataType' => 'boolean', 'DataField' => 'enableTable1'),
				array('Caption' => 'Table 2', 'DataType' => 'boolean', 'DataField' => 'enableTable2'),
				array('Caption' => 'Edit', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Edit', 'OnClick' => 'btn_EditContractTypeOnClick'),
				array('Caption' => 'Delete', 'DataType' => 'hyperlink', 'DataField' => '', 'KeyField' => 'ID', 'Text' => 'Delete', 'OnClick' => 'btn_DeleteContractTypeOnClick'),	
			);
			$this->refreshData();
		}
		
		function btn_NewContractTypeOnClick()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$frm = $context->application->CreateForm('frm_ContractTypeDLG.xml');
			if ($frm != null) $frm->newEntry();
		}
		
		function btn_DeleteContractTypeOnClick($sender, $varname, $varvalue)
		{
			$this->ID_ContractType = $varvalue;
			TQuark::instance()->MessageDlg('Are you sure you want to delete the item?', 'Confirmation', array('mbYes', 'mbNo'), $this, 'btn_DeleteContractTypeOnConfirmation');			
		}
		
		function btn_DeleteContractTypeOnConfirmation($sender, $varName, $varValue)
		{
			if ($varValue != 'mrYes') return;
				
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Writer;
			$result = $db->execute('REMIT', 'deleteContractType', array('Arguments' => array('ID_ContractType' => $this->ID_ContractType)));
			$this->refreshData();				
		}
		
		function btn_EditContractTypeOnClick($sender, $varname, $varvalue)
		{
			//TQuark::instance()->browserAlert('sender: '.$sender."\n".'varname: '.$varname."\n".'varvalue: '.$varvalue);
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
			
			$frm = $context->application->CreateForm('frm_ContractTypeDLG.xml');
			if ($frm != null) $frm->editEntry($varvalue);	
		}
		
		function refreshData()
		{
			$context = $this->getContext();
			if ($context == null) return;
			if ($context->application == null) return;
				
			$db = $context->application->Reader;
			$ds = $db->select('REMIT', 'getContractTypes', array('Arguments' => array('none' => '')));
			
			$this->dg_ContractTypes->DataSet = $ds;	
		}
		
		function generateThumbHTML(){
			$html = '<img src="applications/remit/images/contractTypes.png" width="16px"/><span>'.$this->Caption.'</span>';
			return $html;
		}
	}